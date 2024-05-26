<?php

namespace App\Enums;

enum TaskStatus : string {
    case Assigned = 'Assigned';
    case InProgress = 'In Progress'; 
    case OnHold = 'On Hold'; 
    case WaitingToInvoice = 'Waiting To Invoice'; 
    case Completed = 'Completed'; 
}