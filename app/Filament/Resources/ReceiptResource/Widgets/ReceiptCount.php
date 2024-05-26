<?php

namespace App\Filament\Resources\ReceiptResource\Widgets;

use App\Models\User;
use App\Models\Receipt;
use Illuminate\Support\Number;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class ReceiptCount extends BaseWidget
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

        $payment_total = Receipt::whereBetween('payment_date', [$year_start, $year_end])->sum('amount_paid');
        $payment_monthly = Receipt::whereBetween('payment_date', [$month_start, $month_end])->sum('amount_paid');

        //Auditors
        $sk = User::where('name', 'SK')->first();
        $hk = User::where('name', 'HK')->first();
        $mk = User::where('name', 'MK')->first();
        
        $sk_query = DB::table('clients')
                        ->join('tasks', 'clients.id', '=', 'tasks.client_id')
                        ->join('invoices', 'tasks.id', '=', 'invoices.task_id')
                        ->join('receipts', 'invoices.id', '=', 'receipts.invoice_id')
                        ->select('receipts.amount_paid', 'receipts.payment_date', 'clients.auditor_group_id')
                        ->where('clients.auditor_group_id', '=', $sk->id)
                        ->whereNull('invoices.deleted_at')
                        ->get();
        $total_sk = $sk_query->whereBetween('payment_date', [$year_start, $year_end])
                        ->sum('amount_paid');
        $month_sk = $sk_query->whereBetween('payment_date', [$month_start, $month_end])
                        ->sum('amount_paid');

        $hk_query = DB::table('clients')
                        ->join('tasks', 'clients.id', '=', 'tasks.client_id')
                        ->join('invoices', 'tasks.id', '=', 'invoices.task_id')
                        ->join('receipts', 'invoices.id', '=', 'receipts.invoice_id')
                        ->select('receipts.amount_paid', 'receipts.payment_date', 'clients.auditor_group_id')
                        ->where('clients.auditor_group_id', '=', $hk->id)
                        ->whereNull('invoices.deleted_at')
                        ->get();
        $total_hk = $hk_query->whereBetween('payment_date', [$year_start, $year_end])
                        ->sum('amount_paid');
        $month_hk = $hk_query->whereBetween('payment_date', [$month_start, $month_end])
                        ->sum('amount_paid');

        $mk_query = DB::table('clients')
                        ->leftjoin('tasks', 'clients.id', '=', 'tasks.client_id')
                        ->leftjoin('invoices', 'tasks.id', '=', 'invoices.task_id')
                        ->leftjoin('receipts', 'invoices.id', '=', 'receipts.invoice_id')
                        ->select('receipts.amount_paid', 'receipts.payment_date', 'clients.auditor_group_id')
                        ->where('clients.auditor_group_id', '=', $mk->id)
                        ->whereNull('invoices.deleted_at')
                        ->get();
        $total_mk = $mk_query->whereBetween('payment_date', [$year_start, $year_end])
                        ->sum('amount_paid');
        $month_mk = $mk_query->whereBetween('payment_date', [$month_start, $month_end])
                        ->sum('amount_paid');

        return [
            Stat::make('Payments This FY', Number::currency($payment_total, 'INR'))
                  ->description('SK: '.Number::currency($total_sk, 'INR').' / HK: '.Number::currency($total_hk, 'INR').' / MK: '.Number::currency($total_mk, 'INR')),
            
            Stat::make('Payments This Month', Number::currency($payment_monthly, 'INR'))
                  ->description('SK: '.Number::currency($month_sk, 'INR').' / HK: '.Number::currency($month_hk, 'INR').' / MK: '.Number::currency($month_mk, 'INR')),
        ];
    }
}
