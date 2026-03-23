<?php

namespace App\Services;

use App\Models\ChamaNotification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send an in-app notification to a user.
     */
    public function sendInApp(
        User   $user,
        string $title,
        string $message,
        string $type = 'info'  // info | success | warning | alert
    ): void {
        ChamaNotification::create([
            'user_id' => $user->id,
            'title'   => $title,
            'message' => $message,
            'type'    => $type,
            'is_read' => false,
        ]);
    }

    /**
     * Send an email notification.
     */
    public function sendEmail(
        User   $user,
        string $subject,
        string $message
    ): void {
        try {
            Mail::send([], [], function ($mail) use ($user, $subject, $message) {
                $mail->to($user->email, $user->name)
                     ->subject($subject)
                     ->html("
                        <div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto'>
                            <div style='background:#1d4ed8;padding:20px;text-align:center'>
                                <h1 style='color:white;margin:0;font-size:22px'>SmartChama</h1>
                            </div>
                            <div style='padding:30px;background:#f9f9f9'>
                                <p style='font-size:15px;color:#333'>Dear {$user->name},</p>
                                <p style='font-size:14px;color:#555;line-height:1.6'>{$message}</p>
                                <p style='font-size:13px;color:#888;margin-top:30px'>
                                    Smart Chama Funding and Contribution System<br>
                                    This is an automated message, please do not reply.
                                </p>
                            </div>
                        </div>
                     ");
            });
        } catch (\Exception $e) {
            Log::error("Email notification failed for {$user->email}: " . $e->getMessage());
        }
    }

    // ── Contribution confirmed ─────────────────────────────────────
    public function contributionConfirmed(User $user, float $amount, string $ref): void
    {
        $formattedAmount = number_format($amount, 0);

        $this->sendInApp(
            $user,
            'Contribution Confirmed',
            "Your contribution of KES {$formattedAmount} has been confirmed. Reference: {$ref}",
            'success'
        );

        $this->sendEmail(
            $user,
            'Contribution Confirmed — SmartChama',
            "Your contribution of KES {$formattedAmount} to {$user->chama->name} has been confirmed successfully. Transaction reference: {$ref}."
        );
    }

    // ── Loan application received ──────────────────────────────────
    public function loanApplicationReceived(User $user, float $amount): void
    {
        $formattedAmount = number_format($amount, 0);

        $this->sendInApp(
            $user,
            'Loan Application Submitted',
            "Your loan application of KES {$formattedAmount} has been submitted and is awaiting review.",
            'info'
        );

        $this->sendEmail(
            $user,
            'Loan Application Received — SmartChama',
            "Your loan application of KES {$formattedAmount} has been received. The administrator will review it shortly and you will be notified of the decision."
        );

        // Notify admin/treasurer
        $admins = User::where('chama_id', $user->chama_id)
            ->whereIn('role', ['admin', 'treasurer'])
            ->get();

        foreach ($admins as $admin) {
            $this->sendInApp(
                $admin,
                'New Loan Request',
                "{$user->name} has applied for a loan of KES {$formattedAmount}. Please review.",
                'warning'
            );
        }
    }

    // ── Loan approved ──────────────────────────────────────────────
    public function loanApproved(User $user, float $amount, string $dueDate): void
    {
        $formattedAmount = number_format($amount, 0);

        $this->sendInApp(
            $user,
            'Loan Approved! 🎉',
            "Your loan of KES {$formattedAmount} has been approved. Repayment due by {$dueDate}.",
            'success'
        );

        $this->sendEmail(
            $user,
            'Loan Approved — SmartChama',
            "Great news! Your loan of KES {$formattedAmount} from {$user->chama->name} has been approved. Please ensure repayment is completed by {$dueDate}."
        );
    }

    // ── Loan declined ──────────────────────────────────────────────
    public function loanDeclined(User $user, float $amount, string $reason = ''): void
    {
        $formattedAmount = number_format($amount, 0);
        $reasonText      = $reason ? " Reason: {$reason}" : '';

        $this->sendInApp(
            $user,
            'Loan Request Declined',
            "Your loan request of KES {$formattedAmount} has been declined.{$reasonText}",
            'alert'
        );

        $this->sendEmail(
            $user,
            'Loan Request Declined — SmartChama',
            "We regret to inform you that your loan request of KES {$formattedAmount} has been declined.{$reasonText} Please contact your group administrator for more information."
        );
    }

    // ── Member approved ────────────────────────────────────────────
    public function memberApproved(User $user): void
    {
        $this->sendInApp(
            $user,
            'Account Approved! 🎉',
            "Your Smart Chama account has been approved. You can now log in and start contributing.",
            'success'
        );

        $this->sendEmail(
            $user,
            'Account Approved — SmartChama',
            "Welcome to {$user->chama->name}! Your account has been approved. You can now log in at " . config('app.url') . " using your registered email and password."
        );
    }

    // ── Repayment reminder ─────────────────────────────────────────
    public function repaymentReminder(User $user, float $balance, string $dueDate): void
    {
        $formattedBalance = number_format($balance, 0);

        $this->sendInApp(
            $user,
            'Loan Repayment Reminder',
            "Reminder: Your loan balance of KES {$formattedBalance} is due on {$dueDate}. Please make a repayment soon.",
            'warning'
        );

        $this->sendEmail(
            $user,
            'Loan Repayment Reminder — SmartChama',
            "This is a friendly reminder that your outstanding loan balance of KES {$formattedBalance} is due on {$dueDate}. Please log in to make a repayment."
        );
    }

    // ── Contribution due reminder ──────────────────────────────────
    public function contributionDueReminder(User $user, float $amount, string $dueDate): void
    {
        $formattedAmount = number_format($amount, 0);

        $this->sendInApp(
            $user,
            'Contribution Due Soon',
            "Your monthly contribution of KES {$formattedAmount} is due on {$dueDate}. Don't forget to contribute!",
            'warning'
        );
    }

    // ── Get unread count for a user ────────────────────────────────
    public function unreadCount(User $user): int
    {
        return ChamaNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();
    }

    // ── Mark all as read ───────────────────────────────────────────
    public function markAllRead(User $user): void
    {
        ChamaNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }
}