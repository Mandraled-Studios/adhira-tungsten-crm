<?php

use App\Models\Invoice;
use App\Enums\BillingAt;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/print-invoice/{id}', function ($id) {
    $invoice = Invoice::findOrFail($id);
    $client = $invoice->task->client;
    
    $auditor = \App\Models\User::findOrFail($client->auditor_group_id);
    $billing_at = ($client->billing_at || !empty($client->billing_at)) ? $client->billing_at : BillingAt::ADHIRA;
    
    $qrcode = "";
    $logo = "";
    $address = "39-11, 2nd Floor, Ramani's Kalpaviusha Appartment Tatabad, 11th St, Ganthipuram, Coimbatore, Tamil Nadu 641012";

    if($billing_at == BillingAt::ADHIRA) {
        $qrcode = "images/adhira-associates-qr-code.jpeg";
        $logo = "images/adhira-associates-logo.jpg";
        $phone = "+919750835150";
        $email = "info@adhiraassociates.com";
    } elseif($billing_at == BillingAt::PERFECT) {
        $logo = "images/perfect-tax-consultancy-logo.jpg";
        switch($auditor->name) {
            case "HK":  $qrcode = "images/perfect-tax-consultancy-qr-code-hk.jpeg";  
                        $phone  = "+919944699337";
                        $email  = "harish@adhiraassociates.com";
                        break;
            case "MK":  $qrcode = "images/perfect-tax-consultancy-qr-code-mk.jpeg";
                        $phone  = "+919944699775";  
                        $email  = "manoj@adhiraassociates.com";
                        break;
            default: $qrcode = "images/perfect-tax-consultancy-qr-code-mk.jpeg";
                     $phone = "+919750835150";
                     $email = "info@adhiraassociates.com"; 
                     break;
        }
    } elseif($billing_at == BillingAt::PERFECT_GLOBAL) {
        $logo = "images/perfect-global-services-logo.jpg";
        $qrcode = "images/perfect-tax-consultancy-qr-code-sk.jpeg";
        $phone = "9944699455";
        $email = "suresh@adhiraassociates.com";
    } else {
        $qrcode = "images/adhira-associates-qr-code.jpeg";
        $logo = "images/adhira-associates-logo.jpg";
        $phone = "+919750835150";
        $email = "info@adhiraassociates.com";
    }

    $emailDetails = [
        "billing_at" => $billing_at, 
        "qrcode" => $qrcode, 
        "logo" => $logo,
        "address" => $address,
        "phone" => $phone,
        "email" => $email,
    ];

    return new App\Mail\SendInvoice($invoice, $client, $emailDetails);

})->name('print-invoice');