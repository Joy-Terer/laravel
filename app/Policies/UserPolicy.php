<?php

namespace App\Policies;

use App\Models\Loan;
use App\Models\User;

class UserPolicy{
    public function view(User $authUser, User $targetUser): bool
    {
        return $authUser->chama_id === $targetUser->chama_id;
    } 
    
    public function update(User $authUser, User $targetUser): bool
    {
        return in_array($authUser->role, ['treasures', 'admin']) && $authUser->chama_id === $targetUser->chama_id;
    }

    public function delete(User $authUser, User $targetUser): bool
    {
        return in_array($authUser->role, ['treasures', 'admin']) && $authUser->chama_id === $targetUser->chama_id;
    }

    public function create(User $authUser): bool
        {
            return in_array($authUser->role, ['treasures', 'admin']);
        }
}

