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

        $total = max(1, (int) $stats['total_users']);
        $admins = (int) $stats['total_admins'];
        $customers = (int) $stats['total_customers'];
        $active = (int) $stats['active_users'];
        $others = max(0, $total - $admins - $customers);
        $activePct = min(100, (int) round($active / $total * 100));

        return view('dashboard.superadmin', [
            'stats' => $stats,
            'recentUsers' => $recentUsers,
            'admins' => $admins,
            'customers' => $customers,
            'others' => $others,
            'activePct' => $activePct,
            'ringOff' => round(326.7 * (1 - $activePct / 100), 1),
        ]);
    }
}
