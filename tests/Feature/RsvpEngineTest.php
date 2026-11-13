<?php

namespace Tests\Feature;

use App\Livewire\RsvpForm;
use App\Models\Invitation;
use App\Models\InvitationTemplate;
use App\Models\RsvpResponse;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use Tests\TestCase;

class RsvpEngineTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;
    private InvitationTemplate $template;
    private Invitation $invitation;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();

        $this->owner = User::factory()->create();

        $this->template = InvitationTemplate::create([
            'name_ar' => 'الملكي الذهبي',
            'name_en' => 'Royal Gold',
            'slug' => 'royal-gold',
            'view_name' => 'templates.royal_gold',
            'category' => 'classic',
            'is_premium' => false,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->invitation = Invitation::create([
            'user_id' => $this->owner->id,
            'template_id' => $this->template->id,
            'title' => 'فرح أحمد وسارة',
            'slug' => 'ahmed-sara-rsvp',
            'groom_name' => 'أحمد',
            'bride_name' => 'سارة',
            'wedding_date' => now()->addDays(20)->toDateString(),
            'venue_name' => 'فندق الفورسيزونز',
            'status' => 'published',
            'is_rsvp_enabled' => true,
            'max_guests_per_rsvp' => 5,
            'rsvp_deadline' => now()->addDays(10)->toDateString(),
        ]);
    }

    public function test_guest_can_submit_rsvp_with_egypt_phone_normalization(): void
    {
        Livewire::test(RsvpForm::class, ['invitationId' => $this->invitation->id])
            ->set('guest_name', 'م. حسام الدين')
            ->set('phone', '01012345678')
            ->set('phone_country', 'EG')
            ->set('attendance_status', 'attending')
            ->set('guest_count', 2)
            ->call('submit')
            ->assertHasNoErrors();

        $rsvp = RsvpResponse::where('invitation_id', $this->invitation->id)->first();
        $this->assertNotNull($rsvp);
        $this->assertSame('+201012345678', $rsvp->phone);
        $this->assertSame(2, $rsvp->guest_count);
        $this->assertSame('attending', $rsvp->attendance_status);
    }

    public function test_guest_can_submit_rsvp_with_saudi_phone_normalization(): void
    {
        Livewire::test(RsvpForm::class, ['invitationId' => $this->invitation->id])
            ->set('guest_name', 'فهد بن خالد')
            ->set('phone', '0501234567')
            ->set('phone_country', 'SA')
            ->set('attendance_status', 'attending')
            ->set('guest_count', 3)
            ->call('submit')
            ->assertHasNoErrors();

        $rsvp = RsvpResponse::where('invitation_id', $this->invitation->id)->first();
        $this->assertNotNull($rsvp);
        $this->assertSame('+966501234567', $rsvp->phone);
        $this->assertSame(3, $rsvp->guest_count);
    }

    public function test_invalid_phone_fails_validation(): void
    {
        Livewire::test(RsvpForm::class, ['invitationId' => $this->invitation->id])
            ->set('guest_name', 'سامي محمود')
            ->set('phone', '123')
            ->set('phone_country', 'EG')
            ->call('submit')
            ->assertHasErrors(['phone']);

        $this->assertDatabaseCount('rsvp_responses', 0);
    }

    public function test_declining_attendance_forces_guest_count_to_zero(): void
    {
        Livewire::test(RsvpForm::class, ['invitationId' => $this->invitation->id])
            ->set('guest_name', 'محمد شريف')
            ->set('attendance_status', 'declined')
            ->set('guest_count', 4)
            ->call('submit')
            ->assertHasNoErrors();

        $rsvp = RsvpResponse::where('invitation_id', $this->invitation->id)->first();
        $this->assertNotNull($rsvp);
        $this->assertSame('declined', $rsvp->attendance_status);
        $this->assertSame(0, $rsvp->guest_count);
    }

    public function test_duplicate_phone_updates_existing_rsvp_record(): void
    {
        // First submission
        Livewire::test(RsvpForm::class, ['invitationId' => $this->invitation->id])
            ->set('guest_name', 'كريم عادل')
            ->set('phone', '01099887766')
            ->set('phone_country', 'EG')
            ->set('attendance_status', 'attending')
            ->set('guest_count', 2)
            ->call('submit')
            ->assertHasNoErrors();

        $this->assertDatabaseCount('rsvp_responses', 1);

        // Second submission from same phone updates to 5 guests
        Livewire::test(RsvpForm::class, ['invitationId' => $this->invitation->id])
            ->set('guest_name', 'كريم عادل وعائلته')
            ->set('phone', '01099887766')
            ->set('phone_country', 'EG')
            ->set('attendance_status', 'attending')
            ->set('guest_count', 5)
            ->call('submit')
            ->assertHasNoErrors();

        $this->assertDatabaseCount('rsvp_responses', 1);
        $rsvp = RsvpResponse::where('invitation_id', $this->invitation->id)->first();
        $this->assertSame('كريم عادل وعائلته', $rsvp->guest_name);
        $this->assertSame(5, $rsvp->guest_count);
    }

    public function test_structured_dietary_notes_are_saved(): void
    {
        Livewire::test(RsvpForm::class, ['invitationId' => $this->invitation->id])
            ->set('guest_name', 'ياسمين طارق')
            ->set('selected_dietary', ['وجبة نباتية', 'خالٍ من الجلوتين'])
            ->set('dietary_notes', 'بدون مكسرات')
            ->call('submit')
            ->assertHasNoErrors();

        $rsvp = RsvpResponse::where('invitation_id', $this->invitation->id)->first();
        $this->assertNotNull($rsvp);
        $this->assertStringContainsString('وجبة نباتية', $rsvp->dietary_notes);
        $this->assertStringContainsString('خالٍ من الجلوتين', $rsvp->dietary_notes);
        $this->assertStringContainsString('بدون مكسرات', $rsvp->dietary_notes);
    }

    public function test_cannot_rsvp_when_rsvp_is_disabled(): void
    {
        $this->invitation->update(['is_rsvp_enabled' => false]);

        Livewire::test(RsvpForm::class, ['invitationId' => $this->invitation->id])
            ->set('guest_name', 'طارق مصطفى')
            ->call('submit')
            ->assertHasErrors(['guest_name']);

        $this->assertDatabaseCount('rsvp_responses', 0);
    }

    public function test_cannot_rsvp_when_deadline_has_passed(): void
    {
        $this->invitation->update(['rsvp_deadline' => now()->subDay()->toDateString()]);

        Livewire::test(RsvpForm::class, ['invitationId' => $this->invitation->id])
            ->set('guest_name', 'طارق مصطفى')
            ->call('submit')
            ->assertHasErrors(['guest_name']);

        $this->assertDatabaseCount('rsvp_responses', 0);
    }

    public function test_cannot_rsvp_to_draft_invitation(): void
    {
        $this->invitation->update(['status' => 'draft']);

        Livewire::test(RsvpForm::class, ['invitationId' => $this->invitation->id])
            ->set('guest_name', 'طارق مصطفى')
            ->call('submit')
            ->assertHasErrors(['guest_name']);

        $this->assertDatabaseCount('rsvp_responses', 0);
    }

    public function test_rate_limiter_blocks_excessive_submissions(): void
    {
        RateLimiter::clear('rsvp:127.0.0.1:' . $this->invitation->id);

        for ($i = 0; $i < 10; $i++) {
            RateLimiter::hit('rsvp:127.0.0.1:' . $this->invitation->id, 60);
        }

        Livewire::test(RsvpForm::class, ['invitationId' => $this->invitation->id])
            ->set('guest_name', 'زائر مكرر')
            ->call('submit')
            ->assertHasErrors(['guest_name']);

        $this->assertDatabaseCount('rsvp_responses', 0);
    }
}
