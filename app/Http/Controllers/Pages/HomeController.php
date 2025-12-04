<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {
        $title = 'Verified. Connected. Hired.';
        $description = 'Kickstart your career with Looksharp — Ghana’s trusted platform for verified internships, early-career jobs, and employer connections. Build your profile, get matched to real opportunities, and launch your future with confidence.';

        return view('pages.home.index', compact('title'));
    }
}
