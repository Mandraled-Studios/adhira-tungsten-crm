<?php

namespace App\Filament\Resources\ReceiptResource\Widgets;

use App\Models\Receipt;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class ReceiptStats extends BaseWidget
{
    protected function getStats(): array
    {
        $month_start = date('Y-m-d', strtotime('first day of this month', time()));
        $month_end = date('Y-m-d', strtotime('last day of this month', time()));

        if(date('m') == '1' || date('m') == '2' || date('m') == '3') {
            $year_start = date('Y-m-d', strtotime('first day of April last year', time()));
            $year_end = date('Y-m-d', strtotime('last day of March this year', time()));
        } else {
            $year_start = date('Y-m-d', strtotime('first day of April this year', time()));
            $year_end = date('Y-m-d', strtotime('last day of March next year', time()));
        }

        $monthlyPayment = Receipt::whereBetween('payment_date', [$month_start, $month_end])->sum('amount_paid');
        $yearlyPayment = Receipt::whereBetween('payment_date', [$year_start, $year_end])->sum('amount_paid');

        return [
            Stat::make('Total Payment This Month', $monthlyPayment),
            Stat::make('Total Payment This Year', $yearlyPayment)
        ];
    }
}
