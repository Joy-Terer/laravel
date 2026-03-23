<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AuditLog;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminController extends Controller
{
    public function __construct(private NotificationService $notifications) {}

    // ── Members list ──────────────────────────────────────────────
    public function members()
    {
        $members = User::where('chama_id', Auth::user()->chama_id)
            ->orderByRaw("FIELD(status, 'pending', 'active', 'rejected', 'inactive')")
            ->orderBy('name')
            ->get();

        return view('admin.members', compact('members'));
    }

    // ── Approve member ────────────────────────────────────────────
    public function approveMember(Request $request, User $user)
    {
        $user->update(['status' => 'active']);

        // Notify the member their account is approved
        $this->notifications->memberApproved($user);

        AuditLog::log('member.approved',
            Auth::user()->name . " approved {$user->name}'s account."
        );

        return back()->with('success', "{$user->name}'s account has been approved. They can now log in.");
    }

    // ── Reject member ─────────────────────────────────────────────
    public function rejectMember(Request $request, User $user)
    {
        $user->update(['status' => 'rejected']);

        AuditLog::log('member.rejected',
            Auth::user()->name . " rejected {$user->name}'s registration."
        );

        return back()->with('success', "{$user->name}'s registration has been rejected.");
    }

    // ── Toggle active/inactive ────────────────────────────────────
    public function toggleMember(Request $request, User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->withErrors(['member' => 'You cannot deactivate your own account.']);
        }

        $newStatus = $user->status === 'active' ? 'inactive' : 'active';
        $user->update(['status' => $newStatus]);

        AuditLog::log('member.status_changed',
            Auth::user()->name . " set {$user->name}'s account to {$newStatus}."
        );

        return back()->with('success', "{$user->name}'s account is now {$newStatus}.");
    }

    // ── Update role (AJAX) ────────────────────────────────────────
    public function updateRole(Request $request, User $user)
    {
        $request->validate(['role' => ['required', 'in:member,treasurer,admin']]);

        if ($user->id === Auth::id()) {
            return response()->json(['success' => false, 'message' => 'You cannot change your own role.'], 403);
        }

        $user->update(['role' => $request->role]);

        AuditLog::log('member.role_changed',
            Auth::user()->name . " changed {$user->name}'s role to {$request->role}."
        );

        return response()->json(['success' => true]);
    }

    // ── Audit logs ────────────────────────────────────────────────
    public function auditLogs(Request $request)
    {
        $query = AuditLog::with('user')->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) =>
                $q->where('description', 'like', "%{$s}%")
                  ->orWhere('action', 'like', "%{$s}%")
            );
        }
        if ($request->filled('from'))   $query->whereDate('created_at', '>=', $request->from);
        if ($request->filled('to'))     $query->whereDate('created_at', '<=', $request->to);
        if ($request->filled('action')) $query->where('action', 'like', "%{$request->action}%");

        $logs = $query->paginate(30)->withQueryString();

        return view('admin.audit-logs', compact('logs'));
    }

    // ── Export audit logs PDF ─────────────────────────────────────
    public function exportAuditLogs()
    {
        $logs = AuditLog::with('user')->orderBy('created_at', 'desc')->get();

        $pdf = Pdf::loadView('admin.audit-logs-pdf', compact('logs'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('audit-logs-' . now()->format('Y-m-d') . '.pdf');
    }
}


