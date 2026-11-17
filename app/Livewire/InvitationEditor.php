<?php

namespace App\Livewire;

use App\Jobs\ProcessInvitationImage;
use App\Models\Invitation;
use App\Models\InvitationPhoto;
use App\Models\InvitationSection;
use App\Models\InvitationTemplate;
use App\Services\InvitationImageService;
use App\Services\OgImageService;
use Illuminate\Support\Facades\Validator;
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

    // Basic Wedding Details
    public string $groom_name = '';
    public string $bride_name = '';
    public string $title = '';
    public string $slug = '';
    public string $wedding_date = '';
    public string $wedding_time = '';
    public string $timezone = 'Africa/Cairo';
    public string $venue_name = '';
    public string $venue_address = '';
    public string $venue_map_url = '';
    public string $welcome_message = '';
    public string $invitation_message = '';
    public string $status = 'draft';
    public bool $is_rsvp_enabled = true;
    public int $max_guests_per_rsvp = 5;
    public ?string $rsvp_deadline = '';
    public ?string $music_url = '';
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
    public ?int $cover_photo_width = null;
    public ?int $cover_photo_height = null;
    public bool $coverProcessing = false;
    public $new_cover_photo;
    public $new_gallery_photo;
    public array $new_gallery_photos = [];

    // Sections toggles
    public bool $section_countdown = true;
    public bool $section_timeline = true;
    public bool $section_gallery = true;
    public bool $section_dress_code = true;

    public function mount(int $id)
    {
        $invitation = Invitation::with(['template', 'sections', 'photos'])->findOrFail($id);
        
        // Ownership authorization check
        if ($invitation->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'غير مصرح لك بتعديل هذه الدعوة.');
        }

        $this->invitationId = $invitation->id;
        $this->groom_name = $invitation->groom_name;
        $this->bride_name = $invitation->bride_name;
        $this->title = $invitation->title;
        $this->slug = $invitation->slug;
        $this->wedding_date = $invitation->wedding_date ? $invitation->wedding_date->format('Y-m-d') : '';
        $this->wedding_time = $invitation->wedding_time ? substr($invitation->wedding_time, 0, 5) : '';
        $this->timezone = $invitation->timezone ?? 'Africa/Cairo';
        $this->venue_name = $invitation->venue_name;
        $this->venue_address = $invitation->venue_address ?? '';
        $this->venue_map_url = $invitation->venue_map_url ?? '';
        $this->welcome_message = $invitation->welcome_message ?? '';
        $this->invitation_message = $invitation->invitation_message ?? '';
        $this->status = $invitation->status;
        $this->is_rsvp_enabled = $invitation->is_rsvp_enabled;
        $this->max_guests_per_rsvp = $invitation->max_guests_per_rsvp;
        $this->rsvp_deadline = $invitation->rsvp_deadline ? $invitation->rsvp_deadline->format('Y-m-d') : '';
        $this->music_url = $invitation->music_url ?? '';
        $this->template_id = $invitation->template_id;
        $this->cover_photo = $invitation->cover_photo;
        $this->cover_photo_width = $invitation->cover_photo_width;
        $this->cover_photo_height = $invitation->cover_photo_height;

        $styles = $invitation->effective_styles;
        $this->primary_color = $styles['primary_color'] ?? '#C6A76B';
        $this->secondary_color = $styles['secondary_color'] ?? '#292521';
        $this->background_color = $styles['background_color'] ?? '#FAF7F2';
        $this->font_family = $styles['font_family'] ?? 'Tajawal';
        $this->invitation_locale = $styles['locale'] ?? $styles['language'] ?? 'ar';
        $this->digits = $styles['digits'] ?? 'latin';
        $this->show_hijri = (bool) ($styles['show_hijri'] ?? true);

        // Check active sections
        $this->section_countdown = (bool) $invitation->sections->where('section_type', 'countdown')->first()?->is_enabled ?? true;
        $this->section_timeline = (bool) $invitation->sections->where('section_type', 'timeline')->first()?->is_enabled ?? true;
        $this->section_gallery = (bool) $invitation->sections->where('section_type', 'gallery')->first()?->is_enabled ?? true;
        $this->section_dress_code = (bool) $invitation->sections->where('section_type', 'dress_code')->first()?->is_enabled ?? true;
    }

    protected function getAuthorizedInvitation(): Invitation
    {
        $invitation = Invitation::findOrFail($this->invitationId);
        if ($invitation->user_id !== auth()->id() && !auth()->user()?->isAdmin()) {
            abort(403, 'غير مصرح لك بتعديل هذه الدعوة.');
        }
        return $invitation;
    }

    public function updated($propertyName)
    {
        if (
            str_starts_with($propertyName, 'new_cover_photo')
            || str_starts_with($propertyName, 'new_gallery_photo')
        ) {
            return;
        }

        $this->saveStatus = 'جاري الحفظ...';
        $this->saveChanges();
    }

    public function updatedNewCoverPhoto()
    {
        $invitation = $this->getAuthorizedInvitation();

        $maxKb = (int) config('invitations.images.max_file_size_kb', 5120);
        $mimes = implode(',', config('invitations.images.allowed_extensions', ['jpeg', 'jpg', 'png', 'webp']));
        $mimeTypes = implode(',', config('invitations.images.allowed_mime_types', ['image/jpeg', 'image/png', 'image/webp']));

        $this->validate(
            [
                'new_cover_photo' => "required|file|mimes:{$mimes}|mimetypes:{$mimeTypes}|max:{$maxKb}",
            ],
            [
                'new_cover_photo.max' => 'الحد الأقصى ٥ ميجابايت لصورة الغلاف.',
                'new_cover_photo.mimes' => 'صيغة الصورة غير مدعومة. الصيغ المسموح بها: JPG و PNG و WebP.',
                'new_cover_photo.mimetypes' => 'صيغة الصورة غير مدعومة. الصيغ المسموح بها: JPG و PNG و WebP.',
                'new_cover_photo.file' => 'يرجى اختيار ملف صورة صالح.',
            ]
        );

        $tempDisk = (string) config('invitations.images.temp_disk', 'local');
        $tempDir = (string) config('invitations.images.temp_dir', 'invitations/tmp');
        $tempPath = $this->new_cover_photo->store($tempDir, $tempDisk);

        $this->new_cover_photo = null;
        $this->coverProcessing = config('queue.default') !== 'sync';

        ProcessInvitationImage::dispatch($invitation->id, $tempDisk, $tempPath, 'cover');

        $invitation->refresh();
        $this->cover_photo = $invitation->cover_photo;
        $this->cover_photo_width = $invitation->cover_photo_width;
        $this->cover_photo_height = $invitation->cover_photo_height;
        $this->saveStatus = $this->coverProcessing ? 'جاري معالجة صورة الغلاف...' : 'تم تحديث صورة الغلاف بنجاح ✓';
    }

    public function removeCoverPhoto()
    {
        $invitation = $this->getAuthorizedInvitation();

        app(InvitationImageService::class)->deleteStorageFile($invitation->cover_photo);
        app(OgImageService::class)->cleanupForInvitation($invitation->id);

        $invitation->update([
            'cover_photo' => null,
            'cover_photo_width' => null,
            'cover_photo_height' => null,
        ]);

        $this->cover_photo = null;
        $this->cover_photo_width = null;
        $this->cover_photo_height = null;
        $this->coverProcessing = false;
        $this->new_cover_photo = null;
        $this->saveStatus = 'تم حذف صورة الغلاف ✓';
    }

    public function updatedNewGalleryPhoto()
    {
        if (! $this->new_gallery_photo) {
            return;
        }

        $file = $this->new_gallery_photo;
        $this->new_gallery_photo = null;
        $this->handleGalleryUploads([$file], 'new_gallery_photo');
    }

    public function updatedNewGalleryPhotos()
    {
        if (empty($this->new_gallery_photos)) {
            return;
        }

        $files = $this->new_gallery_photos;
        $this->new_gallery_photos = [];
        $this->handleGalleryUploads($files, 'new_gallery_photos');
    }

    protected function handleGalleryUploads(array $files, string $errorBagKey): void
    {
        $invitation = $this->getAuthorizedInvitation();
        $files = array_values(array_filter($files));
        if (empty($files)) {
            return;
        }

        $maxPhotos = (int) config('invitations.images.max_gallery_photos', 20);
        $currentCount = InvitationPhoto::where('invitation_id', $invitation->id)->count();
        if ($currentCount + count($files) > $maxPhotos) {
            $remaining = max(0, $maxPhotos - $currentCount);
            $message = $remaining === 0
                ? "تم الوصول للحد الأقصى ({$maxPhotos} صورة) لألبوم هذه الدعوة."
                : "الحد الأقصى لصور الألبوم هو {$maxPhotos} صورة (المتبقي {$remaining}).";
            $this->addError($errorBagKey, $message);

            return;
        }

        $maxKb = (int) config('invitations.images.max_file_size_kb', 5120);
        $mimes = implode(',', config('invitations.images.allowed_extensions', ['jpeg', 'jpg', 'png', 'webp']));
        $mimeTypes = implode(',', config('invitations.images.allowed_mime_types', ['image/jpeg', 'image/png', 'image/webp']));

        $validator = Validator::make(
            ['files' => $files],
            [
                'files.*' => "required|file|mimes:{$mimes}|mimetypes:{$mimeTypes}|max:{$maxKb}",
            ],
            [
                'files.*.max' => 'الحد الأقصى ٥ ميجابايت لحجم الصورة الواحدة.',
                'files.*.mimes' => 'صيغة الصورة غير مدعومة. الصيغ المسموح بها: JPG و PNG و WebP.',
                'files.*.mimetypes' => 'صيغة الصورة غير مدعومة. الصيغ المسموح بها: JPG و PNG و WebP.',
                'files.*.file' => 'يرجى اختيار ملفات صور صالحة.',
            ]
        );

        if ($validator->fails()) {
            $this->addError($errorBagKey, $validator->errors()->first());

            return;
        }

        $tempDisk = (string) config('invitations.images.temp_disk', 'local');
        $tempDir = (string) config('invitations.images.temp_dir', 'invitations/tmp');
        $nextOrder = (int) InvitationPhoto::where('invitation_id', $invitation->id)->max('sort_order') + 1;

        foreach ($files as $file) {
            $tempPath = $file->store($tempDir, $tempDisk);
            ProcessInvitationImage::dispatch($invitation->id, $tempDisk, $tempPath, 'gallery', $nextOrder++);
        }

        $this->saveStatus = count($files) > 1
            ? 'تمت إضافة الصور إلى الألبوم بنجاح ✓'
            : 'تمت إضافة الصورة بنجاح ✓';
    }

    public function deleteGalleryPhoto(int $photoId)
    {
        $invitation = $this->getAuthorizedInvitation();
        $photo = InvitationPhoto::where('invitation_id', $invitation->id)->findOrFail($photoId);

        $this->deleteStorageFile($photo->image_path, $photo->thumbnail_path);

        $photo->delete();
        $this->saveStatus = 'تم حذف الصورة ✓';
    }

    protected function deleteStorageFile(?string $pathOrUrl, ?string $thumbnailPathOrUrl = null): void
    {
        app(InvitationImageService::class)->deleteStorageFile($pathOrUrl, $thumbnailPathOrUrl);
    }

    public function changeTemplate(int $templateId)
    {
        $this->getAuthorizedInvitation();
        $template = InvitationTemplate::where('id', $templateId)
            ->where('is_active', true)
            ->firstOrFail();
        $this->template_id = $template->id;
        
        if (!empty($template->default_styles)) {
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
        $this->status = ($this->status === 'published') ? 'draft' : 'published';
        $this->saveChanges();
        $this->saveStatus = ($this->status === 'published') ? 'تم نشر الدعوة وأصبحت متاحة للجميع 🎉' : 'تم تحويل الدعوة إلى مسودة';
    }

    public function saveChanges()
    {
        $invitation = $this->getAuthorizedInvitation();

        $customStyles = array_merge($invitation->custom_styles ?? [], [
            'primary_color' => $this->primary_color,
            'secondary_color' => $this->secondary_color,
            'background_color' => $this->background_color,
            'font_family' => $this->font_family,
            'locale' => in_array($this->invitation_locale, ['ar', 'en'], true) ? $this->invitation_locale : 'ar',
            'digits' => in_array($this->digits, ['latin', 'arabic_indic'], true) ? $this->digits : 'latin',
            'show_hijri' => $this->show_hijri,
        ]);

        $invitation->update([
            'groom_name' => $this->groom_name,
            'bride_name' => $this->bride_name,
            'title' => $this->title ?: 'حفل زفاف ' . $this->groom_name . ' و ' . $this->bride_name,
            'wedding_date' => $this->wedding_date,
            'wedding_time' => $this->wedding_time ?: null,
            'timezone' => $this->timezone ?: 'Africa/Cairo',
            'venue_name' => $this->venue_name,
            'venue_address' => $this->venue_address ?: null,
            'venue_map_url' => $this->venue_map_url ?: null,
            'welcome_message' => $this->welcome_message ?: null,
            'invitation_message' => $this->invitation_message ?: null,
            'music_url' => $this->music_url ?: null,
            'status' => $this->status,
            'is_rsvp_enabled' => $this->is_rsvp_enabled,
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

        InvitationSection::updateOrCreate(
            ['invitation_id' => $invitation->id, 'section_type' => 'timeline'],
            ['is_enabled' => $this->section_timeline]
        );

        InvitationSection::updateOrCreate(
            ['invitation_id' => $invitation->id, 'section_type' => 'gallery'],
            ['is_enabled' => $this->section_gallery]
        );

        InvitationSection::updateOrCreate(
            ['invitation_id' => $invitation->id, 'section_type' => 'dress_code'],
            ['is_enabled' => $this->section_dress_code]
        );

        $this->saveStatus = 'تم الحفظ تلقائياً ✓';
    }

    public function render()
    {
        $invitation = $this->getAuthorizedInvitation();
        $invitation->load(['template', 'sections', 'photos']);

        if ($this->coverProcessing && $invitation->cover_photo && $invitation->cover_photo !== $this->cover_photo) {
            $this->cover_photo = $invitation->cover_photo;
            $this->cover_photo_width = $invitation->cover_photo_width;
            $this->cover_photo_height = $invitation->cover_photo_height;
            $this->coverProcessing = false;
        }

        $templates = InvitationTemplate::where('is_active', true)->orderBy('sort_order')->get();

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

        // Build dynamic live preview object combining active state
        $previewObject = (object) [
            'id' => $invitation->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'code' => $invitation->code,
            'groom_name' => $this->groom_name ?: 'اسم العريس',
            'bride_name' => $this->bride_name ?: 'اسم العروس',
            'wedding_date' => $this->wedding_date ?: now()->addDays(30),
            'wedding_time' => $this->wedding_time,
            'timezone' => $this->timezone ?: 'Africa/Cairo',
            'venue_name' => $this->venue_name ?: 'اسم القاعة أو الفندق',
            'venue_address' => $this->venue_address,
            'venue_map_url' => $this->venue_map_url,
            'welcome_message' => $this->welcome_message,
            'invitation_message' => $this->invitation_message,
            'cover_photo' => $this->cover_photo,
            'cover_photo_width' => $this->cover_photo_width,
            'cover_photo_height' => $this->cover_photo_height,
            'music_url' => $this->music_url,
            'is_rsvp_enabled' => $this->is_rsvp_enabled,
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
            'maxGalleryPhotos' => (int) config('invitations.images.max_gallery_photos', 20),
            'maxImageSizeMb' => (int) ceil(((int) config('invitations.images.max_file_size_kb', 5120)) / 1024),
        ])->layout('layouts.base', ['title' => 'محرر الدعوة: ' . $invitation->title . ' — زفّة']);
    }
}

