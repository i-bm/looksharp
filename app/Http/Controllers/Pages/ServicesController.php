<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;

class ServicesController extends Controller
{
    public function index()
    {
        $title = 'Services';

        return view('pages.services.index', compact('title'));
    }

    public function consulting()
    {
        $title = 'Strategic Business Consulting';

        return view('pages.services.consulting.index', compact('title'));
    }

    public function software()
    {
        $title = 'Custom Software & Web Solutions';

        return view('pages.services.software.index', compact('title'));
    }

    public function itInfrastructure()
    {
        $title = 'IT Infrastructure & Support';
        // $parentRoute = route('services');

        return view('pages.services.it-infrastructure.index', compact('title'));
    }

    public function cybersecurity()
    {
        $title = 'Cybersecurity & Compliance';

        return view('pages.services.cybersecurity.index', compact('title'));
    }

    public function aiAnalytics()
    {
        $title = 'AI-Powered Business Analytics';

        return view('pages.services.ai-analytics.index', compact('title'));
    }
}
