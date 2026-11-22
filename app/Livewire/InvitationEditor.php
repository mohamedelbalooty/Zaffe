<?php

namespace App\Livewire;

use App\Models\Invitation;
use App\Models\InvitationPhoto;
use App\Models\InvitationSection;
use App\Models\InvitationTemplate;
use App\Services\ImageOptimizationService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;

class InvitationEditor extends Component
{
    use WithFileUploads;

    #[Locked]
    public int $invitationId;

    public string $activeTab = 'basics'; // basics, content, design, media, sections, share

    public string $previewDevice = 'mobile'; // mobile, tablet, desktop

    public string $saveStatus = 'تم الحفظ تلقائياً ✓';

    public bool $showPublishModal = false;

    // Basic Wedding Details
    public string $groom_name = '';

    public ?string $bride_name = '';

    public string $title = '';

    public string $slug = '';

    public string $wedding_date = '';

    public string $wedding_time = '';

    public string $timezone = 'Asia/Riyadh';

    public string $venue_name = '';

    public string $venue_address = '';

    public string $venue_map_url = '';

    public string $welcome_message = '';

    public string $invitation_message = '';

    #[Locked]
    public string $status = 'draft';

    public bool $is_rsvp_enabled = false;

    public string $invitation_mode = Invitation::MODE_ANNOUNCEMENT;

    public int $max_guests_per_rsvp = 5;

    public ?string $rsvp_deadline = '';

    #[Locked]
    public ?string $music_url = null;

    public int $template_id;

    // Design Customization
    public string $primary_color = '#C6A76B';

    public string $secondary_color = '#292521';

    public string $background_color = '#FAF7F2';

    public string $font_family = 'Tajawal';

    public string $invitation_locale = 'ar';

    public string $digits = 'latin';

    public bool $show_hijri = true;

    // Media
    public ?string $cover_photo = null;

    public $new_cover_photo;

    public $new_gallery_photo;

    // Sections toggles
    public bool $section_countdown = true;

    public bool $section_timeline = true;

    public bool $section_gallery = true;

    public bool $section_dress_code = true;

    public function mount(int $id)
    {
        $invitation = Invitation::with(['template', 'sections', 'photos'])->findOrFail($id);

        // Ownership authorization check
        if ($invitation->user_id !== auth()->id() && ! auth()->user()->isAdmin()) {
            abort(403, 'غير مصرح لك بتعديل هذه الدعوة.');
        }

        $this->invitationId = $invitation->id;
        $this->groom_name = $invitation->groom_name;
        $this->bride_name = (string) ($invitation->bride_name ?? '');
        $this->title = $invitation->title;
        $this->slug = $invitation->slug;
        $this->wedding_date = $invitation->wedding_date ? $invitation->wedding_date->format('Y-m-d') : '';
        $this->wedding_time = $invitation->wedding_time ? substr($invitation->wedding_time, 0, 5) : '';
        $this->timezone = $invitation->timezone ?: 'Asia/Riyadh';
        $this->venue_name = $invitation->venue_name;
        $this->venue_address = $invitation->venue_address ?? '';
        $this->venue_map_url = $invitation->venue_map_url ?? '';
        $this->welcome_message = $invitation->welcome_message ?? '';
        $this->invitation_message = $invitation->invitation_message ?? '';
        $this->status = $invitation->status;
        $this->invitation_mode = $invitation->effectiveMode();
        $this->is_rsvp_enabled = $invitation->allowsRsvp();
        $this->max_guests_per_rsvp = $invitation->max_guests_per_rsvp;
        $this->rsvp_deadline = $invitation->rsvp_deadline ? $invitation->rsvp_deadline->format('Y-m-d') : '';
        $this->music_url = $invitation->music_url;
        $this->template_id = $invitation->template_id;
        $this->cover_photo = $invitation->cover_photo;

        $styles = $invitation->effective_styles;
        $this->primary_color = $styles['primary_color'] ?? '#C6A76B';
        $this->secondary_color = $styles['secondary_color'] ?? '#292521';
        $this->background_color = $styles['background_color'] ?? '#FAF7F2';
        $this->font_family = $styles['font_family'] ?? 'Tajawal';
        $this->invitation_locale = $styles['locale'] ?? $styles['language'] ?? 'ar';
        $this->digits = $styles['digits'] ?? 'latin';
        $this->show_hijri = (bool) ($styles['show_hijri'] ?? true);

        // Check active sections
        $this->section_countdown = (bool) ($invitation->sections->where('section_type', 'countdown')->first()?->is_enabled ?? true);
        $this->section_timeline = (bool) ($invitation->sections->where('section_type', 'timeline')->first()?->is_enabled ?? false);
        $this->section_gallery = (bool) ($invitation->sections->where('section_type', 'gallery')->first()?->is_enabled ?? true);
        $this->section_dress_code = (bool) ($invitation->sections->where('section_type', 'dress_code')->first()?->is_enabled ?? false);
    }

    protected function getAuthorizedInvitation(): Invitation
    {
        $invitation = Invitation::findOrFail($this->invitationId);
        if ($invitation->user_id !== auth()->id() && ! auth()->user()?->isAdmin()) {
            abort(403, 'غير مصرح لك بتعديل هذه الدعوة.');
        }

        return $invitation;
    }

    public function updatedInvitationMode(string $value): void
    {
        if (! in_array($value, Invitation::modes(), true)) {
            $this->invitation_mode = Invitation::MODE_ANNOUNCEMENT;
        }

        $this->is_rsvp_enabled = in_array(
            $this->invitation_mode,
            [Invitation::MODE_RSVP_QR, Invitation::MODE_RSVP_ONLY],
            true
        );
    }

    public function updatedIsRsvpEnabled(bool $value): void
    {
        if (! $value && in_array($this->invitation_mode, [Invitation::MODE_RSVP_QR, Invitation::MODE_RSVP_ONLY], true)) {
            $this->invitation_mode = Invitation::MODE_ANNOUNCEMENT;
        } elseif ($value && ! in_array($this->invitation_mode, [Invitation::MODE_RSVP_QR, Invitation::MODE_RSVP_ONLY], true)) {
            $this->invitation_mode = Invitation::MODE_RSVP_ONLY;
        }
    }

    public function clearLegacyMusicUrl(): void
    {
        $invitation = $this->getAuthorizedInvitation();
        $invitation->update(['music_url' => null]);
        $this->music_url = null;
        $this->saveStatus = 'تمت إزالة رابط الصوت السابق ✓';
    }

    public function updated($propertyName)
    {
        $this->saveStatus = 'جاري الحفظ...';
        $this->saveChanges();
    }

    public function updatedNewCoverPhoto()
    {
        $invitation = $this->getAuthorizedInvitation();

        $this->validate([
            'new_cover_photo' => ['required', 'file', 'mimes:jpeg,jpg,png,webp,gif', 'max:5120'],
        ]);

        $previousCover = $invitation->cover_photo;

        try {
            $path = app(ImageOptimizationService::class)->storeAsWebp($this->new_cover_photo, 'invitations/covers');
        } catch (ValidationException $e) {
            $this->new_cover_photo = null;
            throw ValidationException::withMessages([
                'new_cover_photo' => collect($e->errors())->flatten()->first() ?: 'تعذر معالجة صورة الغلاف المرفوعة.',
            ]);
        }

        $this->cover_photo = Storage::disk('public')->url($path);
        $this->new_cover_photo = null;

        try {
            $this->saveChanges();
        } catch (ValidationException $e) {
            $this->deleteStorageFile($this->cover_photo);
            $this->cover_photo = $previousCover;
            throw $e;
        }

        // Clean up previous cover photo only after optimized WebP save commits cleanly
        if ($previousCover && $previousCover !== $this->cover_photo) {
            $this->deleteStorageFile($previousCover);
        }
    }

    public function updatedNewGalleryPhoto()
    {
        $invitation = $this->getAuthorizedInvitation();

        $this->validate([
            'new_gallery_photo' => ['required', 'file', 'mimes:jpeg,jpg,png,webp,gif', 'max:5120'],
        ]);

        try {
            $path = app(ImageOptimizationService::class)->storeAsWebp($this->new_gallery_photo, 'invitations/gallery');
        } catch (ValidationException $e) {
            $this->new_gallery_photo = null;
            throw ValidationException::withMessages([
                'new_gallery_photo' => collect($e->errors())->flatten()->first() ?: 'تعذر معالجة صورة الألبوم المرفوعة.',
            ]);
        }

        InvitationPhoto::create([
            'invitation_id' => $invitation->id,
            'image_path' => Storage::disk('public')->url($path),
            'caption' => 'صورة من ألبوم الفرح',
            'sort_order' => InvitationPhoto::where('invitation_id', $invitation->id)->count() + 1,
        ]);

        $this->new_gallery_photo = null;
        $this->saveStatus = 'تمت إضافة الصورة بنجاح ✓';
    }

    public function deleteGalleryPhoto(int $photoId)
    {
        $invitation = $this->getAuthorizedInvitation();
        $photo = InvitationPhoto::where('invitation_id', $invitation->id)->findOrFail($photoId);

        $this->deleteStorageFile($photo->image_path);

        $photo->delete();
        $this->saveStatus = app()->getLocale() === 'en' ? 'Photo deleted ✓' : 'تم حذف الصورة ✓';
    }

    public function deleteCoverPhoto(): void
    {
        $this->getAuthorizedInvitation();
        $this->new_cover_photo = null;

        if ($this->cover_photo) {
            $this->deleteStorageFile($this->cover_photo);
            $this->cover_photo = null;
            $this->saveChanges();
            $this->saveStatus = app()->getLocale() === 'en' ? 'Cover photo deleted ✓' : 'تم حذف صورة الغلاف ✓';
        }
    }

    protected function deleteStorageFile(?string $pathOrUrl): void
    {
        $relativePath = Invitation::resolvePublicDiskPath($pathOrUrl);
        if ($relativePath === null) {
            return;
        }

        $disk = Storage::disk('public');
        if ($disk->exists($relativePath)) {
            $disk->delete($relativePath);
        }
    }

    public function changeTemplate(int $templateId)
    {
        $this->getAuthorizedInvitation();
        $template = InvitationTemplate::where('id', $templateId)
            ->availableForSelection()
            ->firstOrFail();
        $this->template_id = $template->id;

        if (! empty($template->default_styles)) {
            $this->primary_color = $template->default_styles['primary_color'] ?? $this->primary_color;
            $this->secondary_color = $template->default_styles['secondary_color'] ?? $this->secondary_color;
            $this->background_color = $template->default_styles['background_color'] ?? $this->background_color;
            $this->font_family = $template->default_styles['font_family'] ?? $this->font_family;
        }

        $this->saveChanges();
        $this->saveStatus = 'تم تغيير القالب بنجاح ✓';
    }

    public function togglePublish()
    {
        $this->getAuthorizedInvitation();
        $previousStatus = $this->status;
        $this->status = ($previousStatus === 'published') ? 'draft' : 'published';

        try {
            $this->saveChanges();
        } catch (ValidationException $e) {
            $this->status = $previousStatus;
            $this->showPublishModal = false;
            throw $e;
        }

        $this->showPublishModal = ($this->status === 'published');
        $this->saveStatus = ($this->status === 'published')
            ? 'تم نشر الدعوة وأصبحت متاحة للجميع 🎉'
            : 'تم تحويل الدعوة إلى مسودة';
    }

    public function closePublishModal(): void
    {
        $this->showPublishModal = false;
    }

    public function saveChanges()
    {
        $invitation = $this->getAuthorizedInvitation();

        $this->validate([
            'groom_name' => ['required', 'string', 'max:255'],
            'bride_name' => ['nullable', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'wedding_date' => ['required', 'date'],
            'wedding_time' => ['nullable', 'string', 'max:20'],
            'timezone' => ['required', 'string', 'in:Africa/Cairo,Asia/Riyadh'],
            'venue_name' => ['required', 'string', 'max:255'],
            'venue_address' => ['nullable', 'string', 'max:500'],
            'venue_map_url' => ['nullable', 'url:http,https', 'max:500'],
            'welcome_message' => ['nullable', 'string', 'max:2000'],
            'invitation_message' => ['nullable', 'string', 'max:2000'],
            'max_guests_per_rsvp' => ['required', 'integer', 'min:1', 'max:20'],
            'rsvp_deadline' => ['nullable', 'date'],
            'primary_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'secondary_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'background_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'font_family' => ['required', 'string', 'in:Tajawal,Cairo,Amiri,Playfair Display'],
        ]);

        $customStyles = array_merge($invitation->custom_styles ?? [], [
            'primary_color' => $this->primary_color,
            'secondary_color' => $this->secondary_color,
            'background_color' => $this->background_color,
            'font_family' => $this->font_family,
            'locale' => in_array($this->invitation_locale, ['ar', 'en'], true) ? $this->invitation_locale : 'ar',
            'digits' => in_array($this->digits, ['latin', 'arabic_indic'], true) ? $this->digits : 'latin',
            'show_hijri' => $this->show_hijri,
        ]);

        $cleanBride = filled(trim((string) $this->bride_name)) ? trim((string) $this->bride_name) : null;
        $fallbackTitle = $cleanBride
            ? 'حفل زفاف '.$this->groom_name.' و '.$cleanBride
            : 'حفل زفاف '.$this->groom_name;

        $invitation->update([
            'groom_name' => $this->groom_name,
            'bride_name' => $cleanBride,
            'title' => $this->title ?: $fallbackTitle,
            'wedding_date' => $this->wedding_date,
            'wedding_time' => $this->wedding_time ?: null,
            'timezone' => $this->timezone ?: 'Asia/Riyadh',
            'venue_name' => $this->venue_name,
            'venue_address' => $this->venue_address ?: null,
            'venue_map_url' => $this->venue_map_url ?: null,
            'welcome_message' => $this->welcome_message ?: null,
            'invitation_message' => $this->invitation_message ?: null,
            'cover_photo' => $this->cover_photo,
            'status' => $this->status,
            'is_rsvp_enabled' => $this->is_rsvp_enabled,
            'invitation_mode' => $this->invitation_mode,
            'max_guests_per_rsvp' => $this->max_guests_per_rsvp,
            'rsvp_deadline' => $this->rsvp_deadline ?: null,
            'template_id' => $this->template_id,
            'custom_styles' => $customStyles,
        ]);

        // Update sections
        InvitationSection::updateOrCreate(
            ['invitation_id' => $invitation->id, 'section_type' => 'countdown'],
            ['is_enabled' => $this->section_countdown, 'content' => ['target_date' => $this->wedding_date]]
        );

        if ($this->section_timeline || $invitation->sections()->where('section_type', 'timeline')->exists()) {
            InvitationSection::updateOrCreate(
                ['invitation_id' => $invitation->id, 'section_type' => 'timeline'],
                ['is_enabled' => $this->section_timeline]
            );
        }

        InvitationSection::updateOrCreate(
            ['invitation_id' => $invitation->id, 'section_type' => 'gallery'],
            ['is_enabled' => $this->section_gallery]
        );

        if ($this->section_dress_code || $invitation->sections()->where('section_type', 'dress_code')->exists()) {
            InvitationSection::updateOrCreate(
                ['invitation_id' => $invitation->id, 'section_type' => 'dress_code'],
                ['is_enabled' => $this->section_dress_code]
            );
        }

        $this->saveStatus = 'تم الحفظ تلقائياً ✓';
    }

    public function render()
    {
        $invitation = $this->getAuthorizedInvitation();
        $invitation->load(['template', 'sections', 'photos']);
        $templates = InvitationTemplate::availableForSelection()->orderBy('sort_order')->get();

        $previewStyles = [
            'primary_color' => $this->primary_color,
            'secondary_color' => $this->secondary_color,
            'background_color' => $this->background_color,
            'card_color' => '#FFFFFF',
            'font_family' => $this->font_family,
            'locale' => in_array($this->invitation_locale, ['ar', 'en'], true) ? $this->invitation_locale : 'ar',
            'digits' => in_array($this->digits, ['latin', 'arabic_indic'], true) ? $this->digits : 'latin',
            'show_hijri' => $this->show_hijri,
        ];

        $timelineSection = $invitation->sections->where('section_type', 'timeline')->first();
        $timelineEvents = is_array($timelineSection?->content['events'] ?? null)
            ? array_filter($timelineSection->content['events'], static fn (mixed $e): bool => is_array($e) && (! empty($e['title']) || ! empty($e['time'])))
            : [];
        $hasCustomTimeline = $timelineEvents !== [];

        $dressCodeSection = $invitation->sections->where('section_type', 'dress_code')->first();
        $dressCodeContent = is_array($dressCodeSection?->content ?? null) ? $dressCodeSection->content : [];
        $hasCustomDressCode = ! empty($dressCodeContent['title']) || ! empty($dressCodeContent['notes']);

        // Build dynamic live preview object combining active state
        $cleanPreviewBride = filled(trim((string) $this->bride_name)) ? trim((string) $this->bride_name) : null;
        $previewObject = (object) [
            'id' => $invitation->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'groom_name' => $this->groom_name ?: 'اسم العريس',
            'bride_name' => $cleanPreviewBride,
            'wedding_date' => $this->wedding_date ?: now()->addDays(30),
            'wedding_time' => $this->wedding_time,
            'timezone' => $this->timezone ?: 'Asia/Riyadh',
            'venue_name' => $this->venue_name ?: 'اسم القاعة أو الفندق',
            'venue_address' => $this->venue_address,
            'venue_map_url' => $this->venue_map_url,
            'welcome_message' => $this->welcome_message,
            'invitation_message' => $this->invitation_message,
            'cover_photo' => $this->cover_photo,
            'music_url' => $invitation->music_url,
            'is_rsvp_enabled' => $this->is_rsvp_enabled,
            'invitation_mode' => $this->invitation_mode,
            'max_guests_per_rsvp' => $this->max_guests_per_rsvp,
            'template' => $invitation->template,
            'custom_styles' => $previewStyles,
            'effective_styles' => $previewStyles,
            'sections' => $invitation->sections,
            'photos' => $invitation->photos,
            'public_url' => $invitation->public_url,
        ];

        return view('livewire.invitation-editor', [
            'invitation' => $invitation,
            'previewObject' => $previewObject,
            'templates' => $templates,
            'hasLegacyRoster' => $invitation->hasLegacyRosterAccess(),
            'hasCustomTimeline' => $hasCustomTimeline,
            'hasCustomDressCode' => $hasCustomDressCode,
            'hasLegacyMusic' => filled($invitation->music_url),
        ])->layout('layouts.base', ['title' => 'محرر الدعوة: '.$invitation->title.' — زفّة']);
    }
}
