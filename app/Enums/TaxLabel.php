<?php

namespace App\Enums;

enum TaxLabel : string {
    case IGST = 'IGST';
    case CGST = 'CGST'; 
    case SGST = 'SGST'; 
}