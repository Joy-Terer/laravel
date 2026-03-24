<?php


namespace App\Policies;

use App\Models\Loan;
use App\Models\User;

class LoanPolicy
{
    public function manage(User $user, Loan $loan) : bool
    {
        return in_array($user->role, ['treasures' , 'admin']) && $user->chama_id === $loan->chama_id;
    }

    public function repay(User $user, Loan $loan) : bool
    {
        return in_array($user->role, ['treasures' , 'admin']) && $user->chama_id === $loan->chama_id;
    }
}