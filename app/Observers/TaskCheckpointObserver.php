<?php

namespace App\Observers;

use App\Models\User;
use App\Models\TaskCheckpoint;
use Filament\Notifications\Notification;

class TaskCheckpointObserver
{
    /**
     * Handle the TaskCheckpoint "created" event.
     */
    public function created(TaskCheckpoint $taskCheckpoint): void
    {
        $user = User::find($taskCheckpoint->user_id);
        $logAddedBy = $user ? $user->name : "User";
        $sendTo = $user->isAuditor() || $user->isDev() ? $taskCheckpoint->task->assigned_user_id : $taskCheckpoint->task->client->auditor_group_id;

        Notification::make()
            ->title('New Work Log Added')
            ->body('From '.$logAddedBy.' for task '.$taskCheckpoint->task->code)
            ->success()
            ->icon('heroicon-o-exclamation-circle')
            ->iconColor('warning')
            ->duration(5000)
            ->sendToDatabase(User::find($sendTo) ?? User::find(2) );
    }

    /*

    // Handle the TaskCheckpoint "updated" event.
    
    public function updated(TaskCheckpoint $taskCheckpoint): void
    {
        //
    }

    //
    public function deleted(TaskCheckpoint $taskCheckpoint): void
    {
        //
    }

    // Handle the TaskCheckpoint "restored" event.
    public function restored(TaskCheckpoint $taskCheckpoint): void
    {
        //
    }

    // Handle the TaskCheckpoint "force deleted" event.

    public function forceDeleted(TaskCheckpoint $taskCheckpoint): void
    {
        //
    }

    */
}
