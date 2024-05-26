<?php

namespace App\Enums;

enum PaymentMethods : string {
    case Bank = 'Net Banking';
    case GPay = 'Google Pay'; 
    case UPI = 'Other UPI'; 
    case CARD = 'Card'; 
    case CASH = 'Cash'; 

    public function getLabel(): ?string
    {
        return $this->name;
    }
}