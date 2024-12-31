<?php

namespace App\Filament\Resources\InvoiceResource\Widgets;

use App\Models\User;
use App\Models\Invoice;
use Illuminate\Support\Number;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class InvoiceCountOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 2;

    protected function getStats(): array
    {
        $week_start = date('Y-m-d', strtotime('first day of last week', time()));
        $week_end = date('Y-m-d', strtotime('last day of last week', time()));

        $month_start = date('Y-m-d', strtotime('first day of this month', time()));
        $month_end = date('Y-m-d', strtotime('last day of this month', time()));

        if(date('m') == '1' || date('m') == '2' || date('m') == '3') {
            $year_start = date('Y-m-d', strtotime('first day of April last year', time()));
            $year_end = date('Y-m-d', strtotime('last day of March this year', time()));
        } else {
            $year_start = date('Y-m-d', strtotime('first day of April this year', time()));
            $year_end = date('Y-m-d', strtotime('last day of March next year', time()));
        }

        $thisMonth = Invoice::whereBetween('invoice_date', [$month_start, $month_end])->count();
        $thisWeek = Invoice::whereBetween('invoice_date', [$week_start, $week_end])->count();

        if($thisWeek < 10) {
            $thisWeekColor = 'danger';
        } elseif($thisWeek < 25) {
            $thisWeekColor = 'warning';
        } else {
            $thisWeekColor = 'success';
        }

        $invoice_total = Invoice::whereBetween('invoice_date', [$year_start, $year_end])->count();
        $invoice_monthly = Invoice::whereBetween('invoice_date', [$month_start, $month_end])->count();

        //Split

        //Auditors
        $sk = User::where('name', 'SK')->first();
        //$hk = User::where('name', 'HK')->first();
        $mk = User::where('name', 'MK')->first();
        
        $sk_query = DB::table('clients')
                        ->join('tasks', 'clients.id', '=', 'tasks.client_id')
                        ->join('invoices', 'tasks.id', '=', 'invoices.task_id')
                        ->select('invoices.total', 'invoices.invoice_date')
                        ->where('clients.auditor_group_id', '=', $sk->id)
                        ->whereNull('invoices.deleted_at')
                        ->get();
        $total_sk = $sk_query->whereBetween('invoice_date', [$year_start, $year_end])
                        ->count();
        $month_sk = $sk_query->whereBetween('invoice_date', [$month_start, $month_end])
                        ->count();
        $unpaid_sk = $sk_query->whereBetween('invoice_date', [$year_start, $year_end])
                        ->where('invoices.status', '!=', 'Paid' )
                        ->count();

        /*
        $hk_query = DB::table('clients')
                        ->join('tasks', 'clients.id', '=', 'tasks.client_id')
                        ->join('invoices', 'tasks.id', '=', 'invoices.task_id')
                        ->select('invoices.total', 'invoices.invoice_date')
                        ->where('clients.auditor_group_id', '=', $hk->id)
                        ->whereNull('invoices.deleted_at')
                        ->get();
        $total_hk = $hk_query->whereBetween('invoice_date', [$year_start, $year_end])
                        ->count();
        $month_hk = $hk_query->whereBetween('invoice_date', [$month_start, $month_end])
                        ->count();
        $unpaid_hk = $hk_query->whereBetween('invoice_date', [$year_start, $year_end])
                        ->where('invoices.status', '!=', 'Paid' )
                        ->count();
        */

        $mk_query = DB::table('clients')
                        ->join('tasks', 'clients.id', '=', 'tasks.client_id')
                        ->join('invoices', 'tasks.id', '=', 'invoices.task_id')
                        ->select('invoices.total', 'invoices.invoice_date')
                        ->where('clients.auditor_group_id', '=', $mk->id)
                        ->whereNull('invoices.deleted_at')
                        ->get();
        $total_mk = $mk_query->whereBetween('invoice_date', [$year_start, $year_end])
                        ->count();
        $month_mk = $mk_query->whereBetween('invoice_date', [$month_start, $month_end])
                        ->count();
        $unpaid_mk = $mk_query->whereBetween('invoice_date', [$year_start, $year_end])
                        ->where('invoices.status', '!=', 'Paid' )
                        ->count();
        
        return [
            Stat::make('Invoices This FY', $invoice_total )
                  ->description('MK: '.$total_mk.' / SK: '.$total_sk),

            Stat::make('Invoices This Month', $invoice_monthly )
                  ->description('MK: '.$month_mk.' / SK: '.$month_sk),
            
            Stat::make('Invoices Not Paid', Invoice::where('invoice_status', '!=', 'Paid')->count())
                  ->description('MK: '.$unpaid_mk.' / SK: '.$unpaid_sk),
        ];
    }
}
