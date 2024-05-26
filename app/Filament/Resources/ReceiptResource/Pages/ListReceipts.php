<?php

namespace App\Filament\Resources\ReceiptResource\Pages;

use App\Filament\Resources\ReceiptResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReceipts extends ListRecords
{
    protected static string $resource = ReceiptResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            //ReceiptResource\Widgets\ReceiptStats::class,
            ReceiptResource\Widgets\ReceiptChart::class,
            ReceiptResource\Widgets\ReceiptCount::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
