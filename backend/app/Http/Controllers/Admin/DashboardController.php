<?php

namespace App\Http\Controllers\Admin;

use App\Models\Entity;
use App\Models\Novel;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends AdminController
{
    public function index(): View
    {
        $stats = [
            'novels'         => Novel::count(),
            'active_novels'  => Novel::where('is_active', true)->count(),
            'entities'       => Entity::count(),
            'active_entities'=> Entity::where('is_active', true)->count(),
            'users'          => User::count(),
            'active_users'   => User::where('is_active', true)->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}