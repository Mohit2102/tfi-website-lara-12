<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class SuperAdminController extends Controller
{
    public function dashboard(): View
    {
        $stats = [
            'total_users'     => User::count(),
            'total_admins'    => User::where('role', 'admin')->count(),
            'total_customers' => User::where('role', 'customer')->count(),
            'active_users'    => User::where('is_active', true)->count(),
        ];

        $recentUsers = User::latest()->take(8)->get();

        return view('dashboard.superadmin', compact('stats', 'recentUsers'));
    }
}
