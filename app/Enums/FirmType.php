<?php

namespace App\Enums;

enum FirmType : string {
    case Individual = 'Individual';
    case Proprietorship = 'Proprietorship'; 
    case Partnership = 'Partnership'; 
    case Company = 'Company'; 
    case Firm = 'Firm'; 
    case Others = 'Others'; 
    case NA = 'N/A'; 
    
}