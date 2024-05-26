<?php

namespace App\Policies;

use App\Models\User;
use App\Models\TaskCheckpoint;

class TaskCheckpointPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TaskCheckpoint $checkpoint): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TaskCheckpoint $checkpoint): bool
    {
        return $user->isDev();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TaskCheckpoint $checkpoint): bool
    {
        return $user->isDev();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TaskCheckpoint $checkpoint): bool
    {
        return $user->isDev();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TaskCheckpoint $checkpoint): bool
    {
        return $user->isDev();
    }
}
