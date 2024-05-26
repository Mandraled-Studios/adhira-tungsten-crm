<?php

namespace App\Filament\Resources\ClientResource\Widgets;

use App\Models\Client;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $month_start = date('Y-m-d', strtotime('first day of this month', time()));
        $month_end = date('Y-m-d', strtotime('last day of this month', time()));

        $active = Client::where('client_status', 1)->count();
        $inactive = Client::where('client_status', 0)->count();
        
        $sk = Client::where('auditor_group_id', 3)->whereBetween('created_at', [$month_start, $month_end])->count();
        $mk = Client::where('auditor_group_id', 4)->whereBetween('created_at', [$month_start, $month_end])->count();
        $hk = Client::where('auditor_group_id', 5)->whereBetween('created_at', [$month_start, $month_end])->count();
        
        return [
            Stat::make('Total Clients', Client::count())
                ->description('Active: '.$active.' | Inactive: '.$inactive),
            Stat::make('SK Clients', Client::where('auditor_group_id', 3)->count())
                ->description('This Month: '.$sk),
            Stat::make('MK Clients', Client::where('auditor_group_id', 4)->count())
                ->description('This Month: '.$mk),
            Stat::make('HK Clients', Client::where('auditor_group_id', 5)->count())
                ->description('This Month: '.$hk),
        ];
    }
}
