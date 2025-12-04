<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function index()
    {
        $title = 'Dashboard';

        return view('pages.dashboard.index', compact('title'));
    }
}
