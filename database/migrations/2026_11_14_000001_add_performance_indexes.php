<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Named composite indexes to accelerate high-traffic queries without duplicating base indexes.
     */
    public function up(): void
    {
        $hasInvUserStatus = Schema::hasIndex('invitations', ['user_id', 'status']);
        $hasInvUserCreated = Schema::hasIndex('invitations', ['user_id', 'created_at']);
        $hasInvStatusDate = Schema::hasIndex('invitations', ['status', 'wedding_date']);

        Schema::table('invitations', function (Blueprint $table) use ($hasInvUserStatus, $hasInvUserCreated, $hasInvStatusDate): void {
            if (! $hasInvUserStatus) {
                $table->index(['user_id', 'status'], 'idx_invitations_user_status');
            }
            if (! $hasInvUserCreated) {
                $table->index(['user_id', 'created_at'], 'idx_invitations_user_created');
            }
            if (! $hasInvStatusDate) {
                $table->index(['status', 'wedding_date'], 'idx_invitations_status_wedding_date');
            }
        });

        $hasRsvpStatus = Schema::hasIndex('rsvp_responses', ['invitation_id', 'attendance_status']);
        $hasRsvpPhone = Schema::hasIndex('rsvp_responses', ['invitation_id', 'phone']);
        $hasRsvpCreated = Schema::hasIndex('rsvp_responses', ['invitation_id', 'created_at']);

        Schema::table('rsvp_responses', function (Blueprint $table) use ($hasRsvpStatus, $hasRsvpPhone, $hasRsvpCreated): void {
            if (! $hasRsvpStatus) {
                $table->index(['invitation_id', 'attendance_status'], 'idx_rsvp_invitation_status');
            }
            if (! $hasRsvpPhone) {
                $table->index(['invitation_id', 'phone'], 'idx_rsvp_invitation_phone');
            }
            if (! $hasRsvpCreated) {
                $table->index(['invitation_id', 'created_at'], 'idx_rsvp_invitation_created');
            }
        });
    }

    public function down(): void
    {
        $dropInvUserStatus = Schema::hasIndex('invitations', 'idx_invitations_user_status');
        $dropInvUserCreated = Schema::hasIndex('invitations', 'idx_invitations_user_created');
        $dropInvStatusDate = Schema::hasIndex('invitations', 'idx_invitations_status_wedding_date');

        Schema::table('invitations', function (Blueprint $table) use ($dropInvUserStatus, $dropInvUserCreated, $dropInvStatusDate): void {
            if ($dropInvUserStatus) {
                $table->dropIndex('idx_invitations_user_status');
            }
            if ($dropInvUserCreated) {
                $table->dropIndex('idx_invitations_user_created');
            }
            if ($dropInvStatusDate) {
                $table->dropIndex('idx_invitations_status_wedding_date');
            }
        });

        $dropRsvpStatus = Schema::hasIndex('rsvp_responses', 'idx_rsvp_invitation_status');
        $dropRsvpPhone = Schema::hasIndex('rsvp_responses', 'idx_rsvp_invitation_phone');
        $dropRsvpCreated = Schema::hasIndex('rsvp_responses', 'idx_rsvp_invitation_created');

        Schema::table('rsvp_responses', function (Blueprint $table) use ($dropRsvpStatus, $dropRsvpPhone, $dropRsvpCreated): void {
            if ($dropRsvpStatus) {
                $table->dropIndex('idx_rsvp_invitation_status');
            }
            if ($dropRsvpPhone) {
                $table->dropIndex('idx_rsvp_invitation_phone');
            }
            if ($dropRsvpCreated) {
                $table->dropIndex('idx_rsvp_invitation_created');
            }
        });
    }
};
