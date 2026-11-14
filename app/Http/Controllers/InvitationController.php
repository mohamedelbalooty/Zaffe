<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvitationController extends Controller
{
    public function show(string $slug)
    {
        $invitation = Invitation::with(['template', 'sections', 'photos', 'rsvps'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Check publishing access: published invitations are public; drafts are only viewable by owner or admin
        if (!$invitation->isPublished()) {
            if (!Auth::check() || (Auth::id() !== $invitation->user_id && !Auth::user()->isAdmin())) {
                return response()->view('pages.unpublished', ['invitation' => $invitation], 403);
            }
        }

        // Increment view count safely with session-based deduplication
        $sessionKey = 'viewed_invitation_' . $invitation->id;
        if (!session()->has($sessionKey)) {
            session()->put($sessionKey, true);
            $invitation->increment('view_count');
        }

        $viewName = $invitation->template?->view_name ?? 'templates.royal_gold';

        return view('pages.invitation-show', [
            'invitation' => $invitation,
            'viewName' => $viewName,
        ]);
    }
}

