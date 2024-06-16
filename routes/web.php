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

    if($billing_at == BillingAt::ADHIRA) {
        $qrcode = "images/adhira-associates-qr-code.jpeg";
        $logo = "images/adhira-associates-logo.jpg";
        $address = "39-11, 2nd Floor, Ramani's Kalpaviusha Appartment Tatabad, 11th St, Ganthipuram, Coimbatore, Tamil Nadu 641012";
        $phone = "+919750835150";
        $email = "info@adhiraassociates.com";
    } else {
        if($auditor) {
            switch($auditor->name) {
                case "SK": $qrcode = "images/perfect-tax-consultancy-qr-code-sk.jpeg";
                            $logo = "images/perfect-global-services-logo.jpg";
                            $address = "";
                            $phone = "";
                            $email = "";
                            break;
                case "HK": $qrcode = "images/perfect-tax-consultancy-qr-code-hk.jpeg";
                            $logo = "images/perfect-tax-consultancy-logo.jpg";
                            $address = "2nd Floor, NGN Street, New Sidhapudhur, Coimbatore, Tamil Nadu 641044";
                            $phone = "";
                            $email = "";
                            break;
                case "MK": $qrcode = "images/perfect-tax-consultancy-qr-code-mk.jpeg";
                            $logo = "images/perfect-tax-consultancy-logo.jpg";
                            $address = "2nd Floor, NGN Street, New Sidhapudhur, Coimbatore, Tamil Nadu 641044";
                            $phone = "";
                            $email = "";
                            break;
                default: $qrcode = "images/adhira-associates-qr-code.jpeg"; 
                            $logo = "images/adhira-associates-logo.jpg";
                            $address = "39-11, 2nd Floor, Ramani's Kalpaviusha Appartment Tatabad, 11th St, Ganthipuram, Coimbatore, Tamil Nadu 641012";
                            $phone = "+919750835150";
                            $email = "info@adhiraassociates.com";
                            break;
            }
        }
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