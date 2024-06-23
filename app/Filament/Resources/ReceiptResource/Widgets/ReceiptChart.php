<?php

namespace App\Filament\Resources\ReceiptResource\Widgets;

use App\Models\User;
use App\Models\Receipt;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ReceiptChart extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 2;

    protected static ?string $maxHeight = '120px';

    protected function getData(): array
    {
        //Auditors
        $sk = User::where('name', 'SK')->first();
        $hk = User::where('name', 'HK')->first();
        $mk = User::where('name', 'MK')->first();

        $totalData = Trend::model(Receipt::class)
            ->dateColumn('payment_date')
            ->between(
                start: now()->subMonths(11),
                end: now()->addMonths(1)
            )
            ->perMonth()
            ->sum('amount_paid');

        $mkData = Trend::query(Receipt::where('auditor_id', '=', $mk->id))
            ->dateColumn('payment_date')
            ->between(
                start: now()->subMonths(11),
                end: now()->addMonths(1)
            )
            ->perMonth()
            ->sum('amount_paid');

        $skData = Trend::query(Receipt::where('auditor_id', '=', $sk->id))
            ->dateColumn('payment_date')
            ->between(
                start: now()->subMonths(11),
                end: now()->addMonths(1)
            )
            ->perMonth()
            ->sum('amount_paid');

        $hkData = Trend::query(Receipt::where('auditor_id', '=', $hk->id))
            ->dateColumn('payment_date')
            ->between(
                start: now()->subMonths(11),
                end: now()->addMonths(1)
            )
            ->perMonth()
            ->sum('amount_paid');
    

        return [
            'datasets' => [
                [
                    'label' => 'Total Receipt Amount',
                    'data' => $totalData->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#888888',
                    'borderColor' => '#888888',
                ],
                [
                    'label' => 'MK Receipts',
                    'data' => $mkData->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#03adfc',
                    'borderColor' => '#03adfc',
                ],
                [
                    'label' => 'SK Receipts',
                    'data' => $skData->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#f36efa',
                    'borderColor' => '#f36efa',
                ],
                [
                    'label' => 'HK Receipts',
                    'data' => $hkData->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#affa89',
                    'borderColor' => '#affa89',
                ],
            ],
            'labels' => $totalData->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
