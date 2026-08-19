<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Show the customer menu / catalog.
     */
    public function index(): View
    {
        return view('customer.menu');
    }
}
