<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\InvitationGuest;
use App\Support\TemplateStyleCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvitationController extends Controller
{
    public function show(Request $request, string $slug)
    {
        $invitation = Invitation::with(['template', 'sections', 'photos', 'rsvps'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Check publishing access: published invitations are public; drafts are only viewable by owner or admin
        if (! $invitation->isPublished()) {
            if (! Auth::check() || (Auth::id() !== $invitation->user_id && ! Auth::user()->isAdmin())) {
                return response()->view('pages.unpublished', ['invitation' => $invitation], 403);
            }
        }

        // Resolve optional personalized guest strictly scoped to this invitation (prevents cross-event IDOR)
        $inviteToken = trim((string) $request->query('g', ''));
        $personalGuest = null;
        if ($inviteToken !== '') {
            $personalGuest = InvitationGuest::query()
                ->where('invitation_id', $invitation->id)
                ->where('invite_token', $inviteToken)
                ->first();
        }

        // Increment view count safely with session-based deduplication
        $sessionKey = 'viewed_invitation_'.$invitation->id;
        if (! session()->has($sessionKey)) {
            session()->put($sessionKey, true);
            $invitation->increment('view_count');
        }

        $viewName = TemplateStyleCatalog::resolveBladeView($invitation->template?->view_name);

        return view('pages.invitation-show', [
            'invitation' => $invitation,
            'viewName' => $viewName,
            'personalGuest' => $personalGuest,
        ]);
    }
}
