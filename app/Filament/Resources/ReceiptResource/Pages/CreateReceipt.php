<?php

namespace App\Filament\Resources\ReceiptResource\Pages;

use Filament\Actions;
use App\Models\Receipt;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\ReceiptResource;

class CreateReceipt extends CreateRecord
{
    protected static string $resource = ReceiptResource::class;

    protected function afterCreate(): void
    {
        $receipt = $this->record;
        $client = $receipt->invoice->task->client;
        $auditor = null;

        if($client->auditor_group_id) {
            $auditor = $client->auditor_group_id;

            if($auditor) {
                $receipt->update([
                    'auditor_id' => $auditor,
                ]);
                $receipt->save();
            }
        } else {
            $receipt->update([
                'auditor_id' => 1,
            ]);
            $receipt->save();
        }

    }
}
