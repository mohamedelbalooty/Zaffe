<?php

namespace App\Livewire;

use App\Models\Invitation;
use App\Models\InvitationPhoto;
use App\Models\InvitationSection;
use App\Models\InvitationTemplate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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

        $styles = $invitation->effective_styles;
        $this->primary_color = $styles['primary_color'] ?? '#C6A76B';
        $this->secondary_color = $styles['secondary_color'] ?? '#292521';
        $this->background_color = $styles['background_color'] ?? '#FAF7F2';
        $this->font_family = $styles['font_family'] ?? 'Tajawal';

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
        $this->saveStatus = 'جاري الحفظ...';
        $this->saveChanges();
    }

    public function updatedNewCoverPhoto()
    {
        $this->getAuthorizedInvitation();

        $this->validate([
            'new_cover_photo' => 'image|max:5120', // 5MB
        ]);

        $path = $this->new_cover_photo->store('invitations/covers', 'public');
        $this->cover_photo = Storage::disk('public')->url($path);
        
        $this->saveChanges();
    }

    public function updatedNewGalleryPhoto()
    {
        $invitation = $this->getAuthorizedInvitation();

        $this->validate([
            'new_gallery_photo' => 'image|max:5120',
        ]);

        $path = $this->new_gallery_photo->store('invitations/gallery', 'public');
        
        InvitationPhoto::create([
            'invitation_id' => $invitation->id,
            'image_path' => Storage::disk('public')->url($path),
            'caption' => 'صورة من ألبوم الفرح',
            'sort_order' => InvitationPhoto::where('invitation_id', $invitation->id)->count() + 1,
        ]);

        $this->saveStatus = 'تمت إضافة الصورة بنجاح ✓';
    }

    public function deleteGalleryPhoto(int $photoId)
    {
        $invitation = $this->getAuthorizedInvitation();
        $photo = InvitationPhoto::where('invitation_id', $invitation->id)->findOrFail($photoId);
        
        // Clean up storage file if stored locally
        if ($photo->image_path && !str_starts_with($photo->image_path, 'http')) {
            $relativePath = str_replace(Storage::disk('public')->url(''), '', $photo->image_path);
            Storage::disk('public')->delete(ltrim($relativePath, '/'));
        }

        $photo->delete();
        $this->saveStatus = 'تم حذف الصورة ✓';
    }

    public function changeTemplate(int $templateId)
    {
        $this->getAuthorizedInvitation();
        $this->template_id = $templateId;
        $template = InvitationTemplate::findOrFail($templateId);
        
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

        $customStyles = [
            'primary_color' => $this->primary_color,
            'secondary_color' => $this->secondary_color,
            'background_color' => $this->background_color,
            'font_family' => $this->font_family,
        ];

        $invitation->update([
            'groom_name' => $this->groom_name,
            'bride_name' => $this->bride_name,
            'title' => $this->title ?: 'حفل زفاف ' . $this->groom_name . ' و ' . $this->bride_name,
            'wedding_date' => $this->wedding_date,
            'wedding_time' => $this->wedding_time ?: null,
            'venue_name' => $this->venue_name,
            'venue_address' => $this->venue_address ?: null,
            'venue_map_url' => $this->venue_map_url ?: null,
            'welcome_message' => $this->welcome_message ?: null,
            'invitation_message' => $this->invitation_message ?: null,
            'cover_photo' => $this->cover_photo,
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
        $templates = InvitationTemplate::where('is_active', true)->orderBy('sort_order')->get();

        // Build dynamic live preview object combining active state
        $previewObject = (object) [
            'id' => $invitation->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'groom_name' => $this->groom_name ?: 'اسم العريس',
            'bride_name' => $this->bride_name ?: 'اسم العروس',
            'wedding_date' => $this->wedding_date ?: now()->addDays(30),
            'wedding_time' => $this->wedding_time,
            'venue_name' => $this->venue_name ?: 'اسم القاعة أو الفندق',
            'venue_address' => $this->venue_address,
            'venue_map_url' => $this->venue_map_url,
            'welcome_message' => $this->welcome_message,
            'invitation_message' => $this->invitation_message,
            'cover_photo' => $this->cover_photo,
            'music_url' => $this->music_url,
            'is_rsvp_enabled' => $this->is_rsvp_enabled,
            'max_guests_per_rsvp' => $this->max_guests_per_rsvp,
            'template' => $invitation->template,
            'effective_styles' => [
                'primary_color' => $this->primary_color,
                'secondary_color' => $this->secondary_color,
                'background_color' => $this->background_color,
                'card_color' => '#FFFFFF',
                'font_family' => $this->font_family,
            ],
            'sections' => $invitation->sections,
            'photos' => $invitation->photos,
            'public_url' => $invitation->public_url,
        ];

        return view('livewire.invitation-editor', [
            'invitation' => $invitation,
            'previewObject' => $previewObject,
            'templates' => $templates,
        ])->layout('layouts.base', ['title' => 'محرر الدعوة: ' . $invitation->title . ' — زفّة']);
    }
}

