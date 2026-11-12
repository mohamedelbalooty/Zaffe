<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\InvitationSection;
use App\Models\InvitationTemplate;
use App\Models\RsvpResponse;
use App\Services\PhoneService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $invitations = $user->invitations()->with(['template', 'rsvps'])->latest()->get();

        // Calculate statistics
        $totalInvitations = $invitations->count();
        $publishedInvitations = $invitations->where('status', 'published')->count();

        // Aggregated RSVP stats across user's invitations
        $allInvitationIds = $invitations->pluck('id');
        $allRsvps = RsvpResponse::whereIn('invitation_id', $allInvitationIds)->get();

        $confirmedAttendees = $allRsvps->where('attendance_status', 'attending')->sum('guest_count');
        $declinedCount = $allRsvps->where('attendance_status', 'declined')->count();
        $totalResponses = $allRsvps->count();

        return view('dashboard.index', compact(
            'invitations',
            'totalInvitations',
            'publishedInvitations',
            'confirmedAttendees',
            'declinedCount',
            'totalResponses'
        ));
    }

    public function invitations()
    {
        $invitations = Auth::user()->invitations()
            ->with(['template', 'rsvps'])
            ->latest()
            ->paginate(10);

        return view('dashboard.invitations', compact('invitations'));
    }

    public function create(Request $request)
    {
        $selectedTemplateId = $request->query('template_id');
        $templates = InvitationTemplate::where('is_active', true)->orderBy('sort_order')->get();

        return view('dashboard.create', compact('templates', 'selectedTemplateId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'template_id' => [
                'required',
                Rule::exists('invitation_templates', 'id')->where('is_active', true),
            ],
            'groom_name' => ['required', 'string', 'max:255'],
            'bride_name' => ['required', 'string', 'max:255'],
            'wedding_date' => ['required', 'date'],
            'venue_name' => ['required', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
        ], [
            'template_id.required' => 'الرجاء اختيار قالب للدعوة.',
            'groom_name.required' => 'اسم العريس مطلوب.',
            'bride_name.required' => 'اسم العروس مطلوب.',
            'wedding_date.required' => 'تاريخ الزفاف مطلوب.',
            'venue_name.required' => 'اسم قاعة الحفل مطلوب.',
        ]);

        $template = InvitationTemplate::findOrFail($validated['template_id']);

        // Generate unique slug
        $baseSlug = Str::slug($validated['groom_name'] . '-and-' . $validated['bride_name']);
        if (empty($baseSlug)) {
            $baseSlug = 'wedding-' . Str::random(6);
        }
        $slug = $baseSlug;
        $counter = 1;
        while (Invitation::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        $invitation = Invitation::create([
            'user_id' => Auth::id(),
            'template_id' => $template->id,
            'title' => ($validated['title'] ?? null) ?: ('حفل زفاف ' . $validated['groom_name'] . ' و ' . $validated['bride_name']),
            'slug' => $slug,
            'groom_name' => $validated['groom_name'],
            'bride_name' => $validated['bride_name'],
            'wedding_date' => $validated['wedding_date'],
            'wedding_time' => '20:00',
            'venue_name' => $validated['venue_name'],
            'welcome_message' => 'بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ\n«وَمِنْ آيَاتِهِ أَنْ خَلَقَ لَكُم مِّنْ أَنفُسِكُمْ أَزْوَاجًا لِّتَسْكُنُوا إِلَيْهَا وَجَعَلَ بَيْنَكُم مَّوَدَّةً وَرَحْمَةً»',
            'invitation_message' => 'يسرنا دعوتكم لمشاركتنا فرحة العمر وتتويج قصة حبنا في ليلة لا تكتمل إلا بوجودكم.',
            'status' => 'draft',
            'is_rsvp_enabled' => true,
            'max_guests_per_rsvp' => 5,
            'custom_styles' => $template->default_styles ?? [],
        ]);

        // Default sections
        InvitationSection::create([
            'invitation_id' => $invitation->id,
            'section_type' => 'countdown',
            'title_ar' => 'العد التنازلي',
            'content' => ['target_date' => $validated['wedding_date']],
            'sort_order' => 1,
            'is_enabled' => true,
        ]);

        return redirect()->route('dashboard.invitations.editor', $invitation->id)
            ->with('success', 'تم إنشاء الدعوة بنجاح! يمكنك الآن تخصيص كافة التفاصيل والصور من المحرر المباشر.');
    }

    public function destroy(int $id)
    {
        $invitation = Auth::user()->invitations()->findOrFail($id);
        $invitation->delete();

        return redirect()->route('dashboard.invitations.index')
            ->with('success', 'تم حذف الدعوة بنجاح.');
    }

    public function guests(int $id, Request $request)
    {
        $invitation = Auth::user()->invitations()->with('template')->findOrFail($id);

        $query = $invitation->rsvps();

        if ($status = $request->query('status')) {
            $query->where('attendance_status', $status);
        }

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('guest_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $rsvps = $query->paginate(20)->withQueryString();

        $stats = [
            'total' => $invitation->rsvps()->count(),
            'attending' => $invitation->rsvps()->where('attendance_status', 'attending')->count(),
            'total_guests' => $invitation->rsvps()->where('attendance_status', 'attending')->sum('guest_count'),
            'declined' => $invitation->rsvps()->where('attendance_status', 'declined')->count(),
        ];

        return view('dashboard.guests', compact('invitation', 'rsvps', 'stats'));
    }

    public function exportGuestsCsv(int $id)
    {
        $invitation = Auth::user()->invitations()->findOrFail($id);
        $rsvps = $invitation->rsvps()->get();

        $filename = 'rsvp-guests-' . $invitation->slug . '-' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($rsvps) {
            $file = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel Arabic support
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, [
                'اسم الضيف',
                'رقم الهاتف',
                'البريد الإلكتروني',
                'حالة الحضور',
                'عدد الحضور / المرافقين',
                'الرسالة أو التهنئة',
                'ملاحظات غذائية',
                'تاريخ التأكيد',
            ]);

            $sanitize = function ($value) {
                if (is_string($value) && strlen($value) > 0 && in_array($value[0], ['=', '+', '-', '@'])) {
                    return "'" . $value;
                }
                return $value;
            };

            foreach ($rsvps as $rsvp) {
                fputcsv($file, [
                    $sanitize($rsvp->guest_name),
                    $sanitize($rsvp->phone ?? '—'),
                    $sanitize($rsvp->email ?? '—'),
                    $rsvp->attendance_status === 'attending' ? 'حضور مؤكد' : ($rsvp->attendance_status === 'declined' ? 'اعتذار' : 'غير محدد'),
                    $rsvp->guest_count,
                    $sanitize($rsvp->message ?? '—'),
                    $sanitize($rsvp->dietary_notes ?? '—'),
                    $rsvp->created_at->format('Y-m-d H:i'),
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function profile()
    {
        $user = Auth::user();
        return view('dashboard.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = !empty($validated['phone']) ? PhoneService::normalize($validated['phone']) : null;

        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }

        $user->save();

        return back()->with('success', 'تم تحديث بيانات الحساب بنجاح.');
    }
}
