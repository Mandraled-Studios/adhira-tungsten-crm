<?php

namespace App\Filament\Resources\TaskResource\Widgets;

use App\Models\Task;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class TaskOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $month_start = date('Y-m-d', strtotime('first day of this month', time()));
        $month_end = date('Y-m-d', strtotime('last day of this month', time()));

        $totalTasks = Task::count();
        $onHold = Task::where('status', 'On Hold')->count();
        $completed = Task::where('status', 'Completed')->count();
        $overdueTasks = Task::where('status', '!=', 'completed')->where('duedate', '<', now())->count();
        $nondueTasks = Task::where('status', '!=', 'completed')->where('duedate', '>', now())->count();
        $tasksDue = Task::whereBetween('duedate', [$month_start, $month_end])->count();
        $dueTasksCompleted = Task::where('status', 'completed')->whereBetween('duedate', [$month_start, $month_end])->count();
        if($overdueTasks == 0) {
            $overdueColor = 'success';
        } else {
            if($overdueTasks < 5) {
                $overdueColor = 'warning';
            } else {
                $overdueColor = 'danger';
            }
        }

        return [
            Stat::make('Total Tasks', $totalTasks)
                ->description('Completed: '.$completed.' | On Hold: '.$onHold),
            Stat::make('Non-dues', $nondueTasks)
                ->description('Overdues: '. $overdueTasks)
                ->color($overdueColor),
            Stat::make('Tasks Due This Month', $tasksDue),
            Stat::make('Due Tasks Completed This Month', $dueTasksCompleted),
        ];
    }
}
