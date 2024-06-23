<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Support\Number;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class InvoiceSplit extends BaseWidget
{
    protected static ?string $heading = 'Invoices';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 12;

    protected static ?string $maxHeight = '200px';

    protected function getStats(): array
    {
        if(date('m') == '1' || date('m') == '2' || date('m') == '3') {
            $year_start = date('Y-m-d', strtotime('first day of April last year', time()));
            $year_end = date('Y-m-d', strtotime('last day of March this year', time()));
        } else {
            $year_start = date('Y-m-d', strtotime('first day of April this year', time()));
            $year_end = date('Y-m-d', strtotime('last day of March next year', time()));
        }

        $month_start = date('Y-m-d', strtotime('first day of this month', time()));
        $month_end = date('Y-m-d', strtotime('last day of this month', time()));

        $invoice_total = Invoice::whereBetween('invoice_date', [$year_start, $year_end])->sum('total');
        $this_month_total = Invoice::whereBetween('invoice_date', [$month_start, $month_end])->sum('total');

        //Auditors
        $sk = User::where('name', 'SK')->first();
        $hk = User::where('name', 'HK')->first();
        $mk = User::where('name', 'MK')->first();
        
        $sk_query = DB::table('clients')
                        ->join('tasks', 'clients.id', '=', 'tasks.client_id')
                        ->join('invoices', 'tasks.id', '=', 'invoices.task_id')
                        ->select('invoices.total', 'invoices.invoice_date')
                        ->where('clients.auditor_group_id', '=', $sk->id)
                        ->whereNull('invoices.deleted_at')
                        ->get();
        $total_sk = $sk_query->whereBetween('invoice_date', [$year_start, $year_end])
                        ->sum('total');
        $month_sk = $sk_query->whereBetween('invoice_date', [$month_start, $month_end])
                            ->sum('total');

        $hk_query = DB::table('clients')
                        ->join('tasks', 'clients.id', '=', 'tasks.client_id')
                        ->join('invoices', 'tasks.id', '=', 'invoices.task_id')
                        ->select('invoices.total', 'invoices.invoice_date')
                        ->where('clients.auditor_group_id', '=', $hk->id)
                        ->whereNull('invoices.deleted_at')
                        ->get();
        $total_hk = $hk_query->whereBetween('invoice_date', [$year_start, $year_end])
                        ->sum('total');
        $month_hk = $hk_query->whereBetween('invoice_date', [$month_start, $month_end])
                            ->sum('total');

        $mk_query = DB::table('clients')
                        ->join('tasks', 'clients.id', '=', 'tasks.client_id')
                        ->join('invoices', 'tasks.id', '=', 'invoices.task_id')
                        ->select('invoices.total', 'invoices.invoice_date')
                        ->where('clients.auditor_group_id', '=', $mk->id)
                        ->whereNull('invoices.deleted_at')
                        ->get();
        $total_mk = $mk_query->whereBetween('invoice_date', [$year_start, $year_end])
                        ->sum('total');
        $month_mk = $mk_query->whereBetween('invoice_date', [$month_start, $month_end])
                            ->sum('total');

        if($this_month_total < 50000) {
            $thisMonthColor = 'danger';
        } elseif($this_month_total < 100000) {
            $thisMonthColor = 'warning';
        } else {
            $thisMonthColor = 'success';
        }

        if($month_sk < 10000) {
            $skMonthColor = 'danger';
        } elseif($month_sk < 20000) {
            $skMonthColor = 'warning';
        } else {
            $skMonthColor = 'success';
        }

        if($month_hk < 10000) {
            $hkMonthColor = 'danger';
        } elseif($month_hk < 20000) {
            $hkMonthColor = 'warning';
        } else {
            $hkMonthColor = 'success';
        }

        if($month_mk < 10000) {
            $mkMonthColor = 'danger';
        } elseif($month_mk < 20000) {
            $mkMonthColor = 'warning';
        } else {
            $mkMonthColor = 'success';
        }
        
        
        //$this_month_total_sk = $sk_invoices->sum('total');
        
        return [
            Stat::make('Total Invoice Amount (this FY)', Number::currency($invoice_total, 'INR'))
                  ->description('This Month: '.$this_month_total )
                  ->descriptionColor($thisMonthColor),
            Stat::make('Invoice Amount This FY By MK', Number::currency($total_mk, 'INR'))
                  ->description('This Month: '.Number::currency($month_mk, 'INR'))
                  ->descriptionColor($mkMonthColor),
            Stat::make('Invoice Amount This FY By SK', Number::currency($total_sk, 'INR'))
                  ->description('This Month: '.Number::currency($month_sk, 'INR'))
                  ->descriptionColor($skMonthColor),
            Stat::make('Invoice Amount This FY By HK', Number::currency($total_hk, 'INR'))
                  ->description('This Month: '.Number::currency($month_hk, 'INR'))
                  ->descriptionColor($hkMonthColor),
        ];
    }
}
