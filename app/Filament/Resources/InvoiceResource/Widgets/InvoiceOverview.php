<?php

namespace App\Filament\Resources\InvoiceResource\Widgets;

use App\Models\Invoice;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;

class InvoiceOverview extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 2;

    protected static ?string $maxHeight = '120px';

    protected function getData(): array
    {
        $data = Trend::model(Invoice::class)
        ->dateColumn('invoice_date')
        ->between(
            start: now()->subMonths(11),
            end: now()->addMonths(1),
        )
        ->perMonth()
        ->sum('total');

        return [
            'datasets' => [
                [
                    'label' => 'Invoice Amount',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),

        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
