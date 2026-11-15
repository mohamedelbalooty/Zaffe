<?php

namespace App\Http\Controllers;

use App\Models\InvitationGuest;
use App\Services\QrCodeService;
use Illuminate\Support\Facades\Auth;

class GuestPassController extends Controller
{
    public function show(string $qrToken, QrCodeService $qrCodeService)
    {
        $guest = InvitationGuest::query()
            ->with(['invitation.template', 'checkedInGate'])
            ->where('qr_token', $qrToken)
            ->firstOrFail();

        $invitation = $guest->invitation;

        if (! $invitation->isPublished()) {
            if (! Auth::check() || (Auth::id() !== $invitation->user_id && ! Auth::user()->isAdmin())) {
                return response()->view('pages.unpublished', ['invitation' => $invitation], 403);
            }
        }

        if (! $invitation->supportsQrPass()) {
            abort(404);
        }

        $qrSvg = $guest->canReceiveQrPass()
            ? $qrCodeService->renderSvg(
                $guest->getPassUrl(),
                232,
                '#111827',
                '#FFFFFF',
                'بطاقة الدخول الإلكترونية '.$guest->name
            )
            : null;

        return view('pages.guest-pass', [
            'guest' => $guest,
            'invitation' => $invitation,
            'qrSvg' => $qrSvg,
        ]);
    }
}
