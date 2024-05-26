<?php

namespace App\Enums;

enum UserRole : string {
    case Developer = 'Developer';
    case Auditor = 'Auditor'; 
    case Staff = 'Staff'; 
    case Admin = 'Admin'; 
}