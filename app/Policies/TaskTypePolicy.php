<?php

namespace App\Policies;

use App\Models\TaskType;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskTypePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isDev() || $user->isAuditor();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TaskType $taskType): bool
    {
        return $user->isDev() || $user->isAuditor();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isDev() || $user->isAuditor();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TaskType $taskType): bool
    {
        return $user->isDev() || $user->isAuditor();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TaskType $taskType): bool
    {
        return $user->isDev() || $user->isAuditor();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TaskType $taskType): bool
    {
        return $user->isDev();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TaskType $taskType): bool
    {
        return $user->isDev();
    }
}
