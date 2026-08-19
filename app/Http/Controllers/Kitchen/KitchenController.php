<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class KitchenController extends Controller
{
    /**
     * Show the kitchen display.
     */
    public function index(): View
    {
        return view('kitchen.index');
    }
}
