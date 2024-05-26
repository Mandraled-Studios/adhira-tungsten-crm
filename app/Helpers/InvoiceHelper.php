<?php

namespace App\Helpers;

use App\Models\Invoice;

class InvoiceHelper {
    public static function getInvoiceValue($invoice_id) {
        $invoice = Invoice::findOrFail($invoice_id);
        return $invoice->total;
    }

    public static function getClientDetails($invoice_id, $type) {
        $invoice = Invoice::findOrFail($invoice_id);
        $client = $invoice->task->client;
        if($type == "name") {
            return $client->company_name;
        } else {
            return $client->client_code;
        }
    }
}