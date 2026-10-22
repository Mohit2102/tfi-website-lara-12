<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        $stats = [
            'total_customers' => User::where('role', 'customer')->count(),
            'active_customers' => User::where('role', 'customer')->where('is_active', true)->count(),
            'inactive_customers' => User::where('role', 'customer')->where('is_active', false)->count(),
        ];

        $recentCustomers = User::where('role', 'customer')->latest()->take(8)->get();

        return view('dashboard.admin', compact('stats', 'recentCustomers'));
    }
}
