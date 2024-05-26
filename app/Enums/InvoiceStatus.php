<?php

namespace App\Enums;

enum InvoiceStatus : string {
    case Generated = 'Generated';
    case Emailed = 'Emailed'; 
    case Paid = 'Paid'; 
    case Partial = 'Partially Paid'; 
    case Refunded = 'Refunded'; 
}