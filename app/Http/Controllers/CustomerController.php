<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function dashboard(): View
    {
        $user = Auth::user();

        return view('dashboard.customer', compact('user'));
    }
}
