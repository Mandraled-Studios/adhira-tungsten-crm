<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use Filament\Actions;
use App\Models\Invoice;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\InvoiceResource;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function afterCreate(): void
    {
       $invoice = Invoice::latest()->first();
       $invoice_task = $invoice->task;
       $invoice_task->update([
            'invoice_id' => $invoice->id,
       ]);
       $invoice->save();
    }
}
