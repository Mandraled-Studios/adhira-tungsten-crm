<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Models\Task;
use Filament\Resources\Pages\Page;
use App\Filament\Resources\TaskResource;

class CompletedTasks extends Page
{
    protected static string $resource = TaskResource::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationGroup = "Tasks";

    protected static string $view = 'filament.resources.task-resource.pages.completed-tasks';

    public function mount(): void
    {
        static::authorizeResourceAccess();
    }

    protected function getViewData(): array
    {
        $completed = Task::where('status', 'completed')
                            ->whereNull('invoice_id')
                            ->get();
        return ['completed' => $completed];
    }
}
