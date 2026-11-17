<?php

namespace App\Livewire;

use App\Models\Invitation;
use App\Models\InvitationGate;
use App\Models\InvitationGuest;
use App\Services\PhoneService;
use App\Services\QrCodeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;

class GuestRosterManager extends Component
{
    use WithFileUploads;

    #[Locked]
    public int $invitationId;

    // Add single guest fields
    public string $guestName = '';

    public string $guestPhone = '';

    public string $guestPhoneCountry = 'EG';

    public string $guestCategory = InvitationGuest::CATEGORY_GENERAL;

    public int $maxAdults = 2;

    public int $maxChildren = 0;

    public string $guestNotes = '';

    // Bulk / CSV import fields
    public bool $showImportModal = false;

    public string $bulkText = '';

    public $csvFile;

    public ?array $importSummary = null;

    // Filters
    public string $search = '';

    public string $filterStatus = 'all';

    public string $filterCategory = 'all';

    // Preview pass modal
    public ?int $previewGuestId = null;

    // Gate management
    public string $gateName = '';

    public function mount(int $invitationId): void
    {
        $this->invitationId = $invitationId;
        $invitation = $this->getAuthorizedInvitation();
        $this->guestPhoneCountry = $invitation->timezone === 'Asia/Riyadh' ? 'SA' : 'EG';
    }

    protected function getAuthorizedInvitation(): Invitation
    {
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }

        $invitation = Invitation::findOrFail($this->invitationId);

        if ($invitation->user_id !== $user->id && ! $user->isAdmin()) {
            abort(403, 'غير مصرح لك بالوصول إلى قائمة ضيوف هذه الدعوة.');
        }

        return $invitation;
    }

    public function addGuest(): void
    {
        $invitation = $this->getAuthorizedInvitation();

        $this->validate([
            'guestName' => ['required', 'string', 'min:2', 'max:120'],
            'guestPhone' => ['nullable', 'string', 'max:30'],
            'guestPhoneCountry' => ['required', 'in:EG,SA'],
            'guestCategory' => ['required', 'in:'.implode(',', InvitationGuest::categories())],
            'maxAdults' => ['required', 'integer', 'min:1', 'max:20'],
            'maxChildren' => ['required', 'integer', 'min:0', 'max:20'],
            'guestNotes' => ['nullable', 'string', 'max:500'],
        ], [
            'guestName.required' => 'يرجى إدخال اسم الضيف.',
            'guestName.min' => 'اسم الضيف يجب أن يكون حرفين على الأقل.',
        ]);

        $normalizedPhone = null;
        if (trim($this->guestPhone) !== '') {
            if (! PhoneService::isValid($this->guestPhone, $this->guestPhoneCountry)) {
                $this->addError('guestPhone', 'رقم الهاتف غير صالح. يرجى التأكد من الرقم والدولة.');

                return;
            }
            $normalizedPhone = PhoneService::normalize($this->guestPhone, $this->guestPhoneCountry);
        }

        InvitationGuest::create([
            'invitation_id' => $invitation->id,
            'name' => trim($this->guestName),
            'phone' => $normalizedPhone,
            'category' => $this->guestCategory,
            'max_adults' => $this->maxAdults,
            'max_children' => $this->maxChildren,
            'notes' => trim($this->guestNotes) !== '' ? trim($this->guestNotes) : null,
        ]);

        $this->reset(['guestName', 'guestPhone', 'guestNotes']);
        $this->guestCategory = InvitationGuest::CATEGORY_GENERAL;
        $this->maxAdults = 2;
        $this->maxChildren = 0;

        session()->flash('roster_notice', 'تمت إضافة الضيف وإصدار رابطه وبطاقته بنجاح.');
    }

    public function importBulkGuests(): void
    {
        $invitation = $this->getAuthorizedInvitation();

        $this->validate([
            'csvFile' => ['nullable', 'file', 'mimes:csv,txt', 'max:2048'],
            'bulkText' => ['nullable', 'string', 'max:50000'],
        ]);

        $rawContent = '';
        if ($this->csvFile) {
            $rawContent = (string) file_get_contents($this->csvFile->getRealPath());
        } elseif (trim($this->bulkText) !== '') {
            $rawContent = $this->bulkText;
        }

        if (trim($rawContent) === '') {
            $this->addError('bulkText', 'يرجى رفع ملف CSV أو لصق قائمة الضيوف أولاً.');

            return;
        }

        // Strip UTF-8 BOM
        $rawContent = preg_replace('/^\xEF\xBB\xBF/', '', $rawContent) ?? $rawContent;
        $lines = preg_split('/\r\n|\r|\n/', trim($rawContent)) ?: [];

        $defaultCountry = $invitation->timezone === 'Asia/Riyadh' ? 'SA' : 'EG';
        $added = 0;
        $skipped = 0;

        DB::transaction(function () use ($lines, $invitation, $defaultCountry, &$added, &$skipped): void {
            foreach ($lines as $idx => $line) {
                $line = trim($line);
                if ($line === '') {
                    continue;
                }

                $cols = str_getcsv($line);
                $rawName = trim((string) ($cols[0] ?? ''));
                if ($rawName === '') {
                    $skipped++;

                    continue;
                }

                // Skip common CSV header row
                $lowerFirst = Str::lower($rawName);
                if ($idx === 0 && in_array($lowerFirst, ['name', 'guest', 'guest_name', 'الاسم', 'اسم الضيف'], true)) {
                    continue;
                }

                // Neutralize dangerous formula prefixes on text inputs
                $cleanName = $this->sanitizeImportedText($rawName);
                if (mb_strlen($cleanName) < 2) {
                    $skipped++;

                    continue;
                }

                $rawPhone = trim((string) ($cols[1] ?? ''));
                $normalizedPhone = null;
                if ($rawPhone !== '') {
                    $country = str_starts_with($rawPhone, '+966') || str_starts_with($rawPhone, '966')
                        ? 'SA'
                        : (str_starts_with($rawPhone, '+20') || str_starts_with($rawPhone, '20') ? 'EG' : $defaultCountry);

                    if (PhoneService::isValid($rawPhone, $country)) {
                        $normalizedPhone = PhoneService::normalize($rawPhone, $country);
                    }
                }

                $adults = isset($cols[2]) && is_numeric(trim((string) $cols[2]))
                    ? max(1, min(20, (int) trim((string) $cols[2])))
                    : 2;

                $children = isset($cols[3]) && is_numeric(trim((string) $cols[3]))
                    ? max(0, min(20, (int) trim((string) $cols[3])))
                    : 0;

                $rawCategory = Str::lower(trim((string) ($cols[4] ?? InvitationGuest::CATEGORY_GENERAL)));
                $category = in_array($rawCategory, InvitationGuest::categories(), true)
                    ? $rawCategory
                    : InvitationGuest::CATEGORY_GENERAL;

                InvitationGuest::create([
                    'invitation_id' => $invitation->id,
                    'name' => mb_substr($cleanName, 0, 120),
                    'phone' => $normalizedPhone,
                    'category' => $category,
                    'max_adults' => $adults,
                    'max_children' => $children,
                ]);

                $added++;
            }
        });

        $this->reset(['bulkText', 'csvFile', 'showImportModal']);
        $this->importSummary = ['added' => $added, 'skipped' => $skipped];
    }

    public function markInvited(int $guestId): void
    {
        $invitation = $this->getAuthorizedInvitation();
        $guest = InvitationGuest::query()
            ->where('invitation_id', $invitation->id)
            ->findOrFail($guestId);

        if ($guest->invited_at === null) {
            $guest->update(['invited_at' => now()]);
        } else {
            $guest->update(['reminded_at' => now()]);
        }
    }

    public function toggleCheckIn(int $guestId): void
    {
        $invitation = $this->getAuthorizedInvitation();
        $guest = InvitationGuest::query()
            ->where('invitation_id', $invitation->id)
            ->findOrFail($guestId);

        if ($guest->isCheckedIn()) {
            $guest->update([
                'checked_in_at' => null,
                'checked_in_count' => 0,
                'checked_in_by_gate_id' => null,
            ]);
        } else {
            $guest->update([
                'checked_in_at' => now(),
                'checked_in_count' => $guest->getExpectedPartySize(),
                'checked_in_by_gate_id' => null,
            ]);
        }
    }

    public function deleteGuest(int $guestId): void
    {
        $invitation = $this->getAuthorizedInvitation();
        InvitationGuest::query()
            ->where('invitation_id', $invitation->id)
            ->whereKey($guestId)
            ->delete();

        if ($this->previewGuestId === $guestId) {
            $this->previewGuestId = null;
        }
    }

    public function createGate(): void
    {
        $invitation = $this->getAuthorizedInvitation();

        $this->validate([
            'gateName' => ['required', 'string', 'min:2', 'max:100'],
        ], [
            'gateName.required' => 'يرجى كتابة اسم البوابة (مثال: البوابة الرئيسية أو قاعة النساء).',
        ]);

        InvitationGate::create([
            'invitation_id' => $invitation->id,
            'name' => trim($this->gateName),
            'is_active' => true,
            'created_by_user_id' => Auth::id(),
        ]);

        $this->gateName = '';
        session()->flash('gate_notice', 'تم إنشاء رابط البوابة بنجاح ويمكن مشاركته مع مستقبل الضيوف فوراً.');
    }

    public function toggleGate(int $gateId): void
    {
        $invitation = $this->getAuthorizedInvitation();
        $gate = InvitationGate::query()
            ->where('invitation_id', $invitation->id)
            ->findOrFail($gateId);

        $gate->update(['is_active' => ! $gate->is_active]);
    }

    public function deleteGate(int $gateId): void
    {
        $invitation = $this->getAuthorizedInvitation();
        InvitationGate::query()
            ->where('invitation_id', $invitation->id)
            ->whereKey($gateId)
            ->delete();
    }

    public function openPassPreview(int $guestId): void
    {
        $invitation = $this->getAuthorizedInvitation();
        $guest = InvitationGuest::query()
            ->where('invitation_id', $invitation->id)
            ->findOrFail($guestId);

        $this->previewGuestId = $guest->id;
    }

    public function closePassPreview(): void
    {
        $this->previewGuestId = null;
    }

    private function sanitizeImportedText(string $value): string
    {
        $trimmed = trim($value);
        if ($trimmed !== '' && preg_match('/^[=+\-@\t\r]/', $trimmed)) {
            $trimmed = ltrim($trimmed, "=+-@\t\r ");
        }

        return strip_tags($trimmed);
    }

    public function render()
    {
        $invitation = $this->getAuthorizedInvitation();

        $query = $invitation->guests()->with('checkedInGate');

        if ($this->filterStatus === 'pending') {
            $query->where('rsvp_status', InvitationGuest::STATUS_PENDING);
        } elseif ($this->filterStatus === 'attending') {
            $query->where('rsvp_status', InvitationGuest::STATUS_ATTENDING);
        } elseif ($this->filterStatus === 'declined') {
            $query->where('rsvp_status', InvitationGuest::STATUS_DECLINED);
        } elseif ($this->filterStatus === 'checked_in') {
            $query->whereNotNull('checked_in_at');
        } elseif ($this->filterStatus === 'not_checked_in') {
            $query->whereNull('checked_in_at');
        }

        if ($this->filterCategory !== 'all') {
            $query->where('category', $this->filterCategory);
        }

        if (trim($this->search) !== '') {
            $term = '%'.trim($this->search).'%';
            $query->where(function ($q) use ($term): void {
                $q->where('name', 'like', $term)
                    ->orWhere('phone', 'like', $term)
                    ->orWhere('pass_code', 'like', $term);
            });
        }

        $guests = $query->get();
        $gates = $invitation->gates()->get();
        $stats = $invitation->guestCheckInStats();

        $previewGuest = $this->previewGuestId
            ? $invitation->guests()->find($this->previewGuestId)
            : null;
        $previewQrSvg = $previewGuest
            ? app(QrCodeService::class)->renderSvg(
                $previewGuest->getPassUrl(),
                200,
                '#111827',
                '#FFFFFF',
                'بطاقة الدخول '.$previewGuest->name
            )
            : null;

        return view('livewire.guest-roster-manager', [
            'invitation' => $invitation,
            'guests' => $guests,
            'gates' => $gates,
            'stats' => $stats,
            'previewGuest' => $previewGuest,
            'previewQrSvg' => $previewQrSvg,
        ]);
    }
}
