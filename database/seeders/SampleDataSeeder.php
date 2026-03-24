<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Chama;
use App\Models\Contribution;
use App\Models\Loan;
use App\Models\AuditLog;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $chama        = Chama::first();
        $activeMembers = User::where('status', 'active')->get();
        $admin        = User::where('role', 'admin')->first();
        $totalBalance = 0;

        // 3 months of contributions for every active member
        foreach ($activeMembers as $member) {
            for ($month = 2; $month >= 0; $month--) {
                $amount = $chama->contribution_amount;
                $date   = now()->subMonths($month)->setDay(5);

                Contribution::create([
                    'user_id'          => $member->id,
                    'chama_id'         => $chama->id,
                    'amount'           => $amount,
                    'payment_method'   => 'mpesa',
                    'transaction_code' => 'NLJ' . strtoupper(substr(md5(uniqid()), 0, 8)),
                    'status'           => 'completed',
                    'notes'            => $date->format('F Y') . ' contribution',
                    'created_at'       => $date,
                    'updated_at'       => $date,
                ]);

                $totalBalance += $amount;
            }
        }

        // Update chama balance with all contributions
        $chama->update(['balance' => $totalBalance]);

        // Sample approved loan for first regular member
        $member1 = User::where('role', 'member')
            ->where('status', 'active')
            ->first();

        if ($member1) {
            Loan::create([
                'user_id'          => $member1->id,
                'chama_id'         => $chama->id,
                'amount'           => 10000.00,
                'balance'          => 6000.00,
                'status'           => 'approved',
                'purpose'          => 'School fees',
                'repayment_period' => 6,
                'due_date'         => now()->addMonths(3)->toDateString(),
                'approved_by'      => $admin->id,
            ]);

            // Deduct loan from chama balance
            $chama->decrement('balance', 10000);
        }

        // Sample pending loan for second member
        $member2 = User::where('role', 'member')
            ->where('status', 'active')
            ->skip(1)->first();

        if ($member2) {
            Loan::create([
                'user_id'          => $member2->id,
                'chama_id'         => $chama->id,
                'amount'           => 5000.00,
                'balance'          => 5000.00,
                'status'           => 'pending',
                'purpose'          => 'Medical expenses',
                'repayment_period' => 3,
            ]);
        }

        // Sample audit logs
        $logs = [
            ['action' => 'member.approved',        'description' => "{$admin->name} approved Ann Wangari's account."],
            ['action' => 'contribution.completed',  'description' => 'Peter Mwangi contributed KES 2,000 via M-Pesa.'],
            ['action' => 'loan.approved',           'description' => "{$admin->name} approved loan of KES 10,000 for Ann Wangari."],
            ['action' => 'contribution.completed',  'description' => 'Joy Tracy contributed KES 2,000 via M-Pesa.'],
            ['action' => 'loan.applied',            'description' => 'James Ouma applied for a loan of KES 5,000.'],
            ['action' => 'member.registered',       'description' => 'Diana Chebet registered and is awaiting approval.'],
        ];

        foreach ($logs as $i => $log) {
            AuditLog::create([
                'user_id'     => $admin->id,
                'action'      => $log['action'],
                'description' => $log['description'],
                'ip_address'  => '127.0.0.1',
                'created_at'  => now()->subHours($i * 3 + 1),
                'updated_at'  => now()->subHours($i * 3 + 1),
            ]);
        }
    }
}