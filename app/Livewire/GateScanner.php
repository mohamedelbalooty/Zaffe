<?php

namespace App\Livewire;

use App\Models\InvitationGate;
use App\Models\InvitationGuest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Locked;
use Livewire\Component;

class GateScanner extends Component
{
    #[Locked]
    public int $gateId;

    #[Locked]
    public int $invitationId;

    #[Locked]
    public string $gateToken;

    public string $passCodeInput = '';

    public string $search = '';

    public ?string $scanState = null; // success, duplicate, invalid, declined

    public string $scanMessage = '';

    public ?string $scanGuestName = null;

    public ?string $scanGuestDetail = null;

    public function mount(string $gateToken): void
    {
        $gate = InvitationGate::query()
            ->with('invitation')
            ->where('access_token', $gateToken)
            ->firstOrFail();

        if (! $gate->is_active) {
            abort(403, 'تم إيقاف رابط هذه البوابة من قبل منظم الحفل.');
        }

        if (! $gate->invitation || ! $gate->invitation->isPublished()) {
            abort(403, 'الدعوة غير منشورة أو غير متاحة حالياً.');
        }

        if (! $gate->invitation->supportsQrPass()) {
            abort(403, 'نظام بطاقات الدخول غير مفعّل في نمط هذه الدعوة.');
        }

        $this->gateId = $gate->id;
        $this->invitationId = $gate->invitation_id;
        $this->gateToken = $gateToken;
    }

    protected function resolveActiveGate(): InvitationGate
    {
        $gate = InvitationGate::query()
            ->with('invitation')
            ->where('id', $this->gateId)
            ->where('invitation_id', $this->invitationId)
            ->where('access_token', $this->gateToken)
            ->first();

        if (! $gate || ! $gate->is_active) {
            abort(403, 'تم إيقاف صلاحية هذه البوابة.');
        }

        if (! $gate->invitation || ! $gate->invitation->isPublished()) {
            abort(403, 'الدعوة غير منشورة حالياً.');
        }

        if (! $gate->invitation->supportsQrPass()) {
            abort(403, 'نظام بطاقات الدخول غير مفعّل في نمط هذه الدعوة.');
        }

        return $gate;
    }

    public function scanPayload(string $payload): void
    {
        $gate = $this->resolveActiveGate();
        $failRateKey = 'gate-fail:'.$gate->id.':'.request()->ip();

        if (RateLimiter::tooManyAttempts($failRateKey, 20)) {
            $this->setFeedback('invalid', 'تم تسجيل محاولات كثيرة غير صحيحة — يرجى الانتظار دقيقة قبل المحاولة مجدداً.');

            return;
        }

        $trimmed = trim($payload);

        if ($trimmed === '') {
            $this->setFeedback('invalid', 'الرجاء مسح رمز QR أو إدخال كود الدخول.');

            return;
        }

        $qrToken = null;
        if (preg_match('~\/pass\/([A-Za-z0-9_-]{12,64})~', $trimmed, $matches)) {
            $qrToken = $matches[1];
        }

        $guest = InvitationGuest::query()
            ->where('invitation_id', $gate->invitation_id)
            ->where(function ($q) use ($trimmed, $qrToken): void {
                if ($qrToken !== null) {
                    $q->where('qr_token', $qrToken);
                } else {
                    $q->where('qr_token', $trimmed)
                        ->orWhere('pass_code', strtoupper($trimmed));
                }
            })
            ->first();

        if (! $guest) {
            RateLimiter::hit($failRateKey, 60);
            $this->setFeedback('invalid', 'هذا الرمز غير مسجل في قائمة ضيوف هذا الحفل.');

            return;
        }

        RateLimiter::clear($failRateKey);
        $this->performAtomicCheckIn($gate, $guest);
    }

    public function checkInByCode(): void
    {
        $gate = $this->resolveActiveGate();
        $failRateKey = 'gate-fail:'.$gate->id.':'.request()->ip();

        if (RateLimiter::tooManyAttempts($failRateKey, 20)) {
            $this->passCodeInput = '';
            $this->setFeedback('invalid', 'تم تسجيل محاولات كثيرة غير صحيحة — يرجى الانتظار دقيقة قبل المحاولة مجدداً.');

            return;
        }

        $rawInput = trim($this->passCodeInput);
        $code = strtoupper($rawInput);

        if ($code === '') {
            $this->setFeedback('invalid', 'يرجى إدخال كود التحقق المكوّن من 6 أحرف.');

            return;
        }

        $guest = InvitationGuest::query()
            ->where('invitation_id', $gate->invitation_id)
            ->where(function ($q) use ($code, $rawInput): void {
                $q->where('pass_code', $code)
                    ->orWhere('qr_token', $rawInput);
            })
            ->first();

        $this->passCodeInput = '';

        if (! $guest) {
            RateLimiter::hit($failRateKey, 60);
            $this->setFeedback('invalid', 'لم يتم العثور على ضيف بهذا الكود في هذا الحفل.');

            return;
        }

        RateLimiter::clear($failRateKey);
        $this->performAtomicCheckIn($gate, $guest);
    }

    public function checkInGuest(int $guestId): void
    {
        $gate = $this->resolveActiveGate();

        $guest = InvitationGuest::query()
            ->where('invitation_id', $gate->invitation_id)
            ->find($guestId);

        if (! $guest) {
            $this->setFeedback('invalid', 'هذا الضيف غير مسجل ضمن هذا الحفل.');

            return;
        }

        $this->performAtomicCheckIn($gate, $guest);
    }

    protected function performAtomicCheckIn(InvitationGate $gate, InvitationGuest $guest): void
    {
        if ($guest->rsvp_status === InvitationGuest::STATUS_DECLINED) {
            $this->setFeedback(
                'declined',
                'هذا الضيف مسجل كمعتذر عن الحضور.',
                $guest->name
            );

            return;
        }

        $arrivedHeads = $guest->getExpectedPartySize();
        $now = now();

        $updated = 0;
        DB::transaction(function () use ($guest, $gate, $arrivedHeads, $now, &$updated): void {
            $updated = InvitationGuest::query()
                ->whereKey($guest->id)
                ->where('invitation_id', $gate->invitation_id)
                ->whereNull('checked_in_at')
                ->update([
                    'checked_in_at' => $now,
                    'checked_in_count' => $arrivedHeads,
                    'checked_in_by_gate_id' => $gate->id,
                ]);

            if ($updated === 1) {
                InvitationGate::query()
                    ->whereKey($gate->id)
                    ->update([
                        'scan_count' => DB::raw('scan_count + 1'),
                        'last_scan_at' => $now,
                    ]);
            }
        });

        $freshGuest = $guest->fresh(['checkedInGate']);

        if ($updated === 0) {
            $tz = $gate->invitation->timezone ?: 'Africa/Cairo';
            $timeStr = $freshGuest?->checked_in_at?->timezone($tz)->format('Y-m-d H:i') ?? '';
            $gateLabel = $freshGuest?->checkedInGate?->name ?? 'إدارة الحفل';

            $this->setFeedback(
                'duplicate',
                "تم استخدام هذه البطاقة مسبقاً بتاريخ {$timeStr} عبر: {$gateLabel}",
                $guest->name,
                InvitationGuest::categoryLabel($guest->category, 'ar')
            );

            return;
        }

        $hasConfirmedParty = ($guest->confirmed_adults + $guest->confirmed_children) > 0;
        $adults = $guest->rsvp_status === InvitationGuest::STATUS_ATTENDING && $hasConfirmedParty
            ? $guest->confirmed_adults
            : $guest->max_adults;
        $children = $guest->rsvp_status === InvitationGuest::STATUS_ATTENDING && $hasConfirmedParty
            ? $guest->confirmed_children
            : $guest->max_children;

        $detail = InvitationGuest::categoryLabel($guest->category, 'ar')
            ." · البالغون: {$adults}"
            .($children > 0 ? " · الأطفال: {$children}" : '');

        $this->setFeedback(
            'success',
            'تم تأكيد الدخول بنجاح — أهلاً وسهلاً!',
            $guest->name,
            $detail
        );
    }

    private function setFeedback(
        string $state,
        string $message,
        ?string $guestName = null,
        ?string $guestDetail = null
    ): void {
        $this->scanState = $state;
        $this->scanMessage = $message;
        $this->scanGuestName = $guestName;
        $this->scanGuestDetail = $guestDetail;
    }

    public function render()
    {
        $gate = $this->resolveActiveGate();
        $invitation = $gate->invitation;
        $stats = $invitation->guestCheckInStats();

        $searchQuery = InvitationGuest::query()
            ->where('invitation_id', $invitation->id)
            ->orderByRaw('checked_in_at IS NOT NULL')
            ->latest('id');

        if (trim($this->search) !== '') {
            $term = '%'.trim($this->search).'%';
            $searchQuery->where(function ($q) use ($term): void {
                $q->where('name', 'like', $term)
                    ->orWhere('phone', 'like', $term)
                    ->orWhere('pass_code', 'like', strtoupper($term));
            });
        }

        $guests = $searchQuery->limit(25)->get();

        return view('livewire.gate-scanner', [
            'gate' => $gate,
            'invitation' => $invitation,
            'stats' => $stats,
            'guests' => $guests,
        ])->layout('layouts.base', ['title' => 'بوابة الاستقبال: '.$gate->name.' — زفّة']);
    }
}
