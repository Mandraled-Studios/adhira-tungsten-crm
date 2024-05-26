<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CustomController extends Controller
{
    public function createInvoice($taskid) {
        return redirect()->back();
    }
}
