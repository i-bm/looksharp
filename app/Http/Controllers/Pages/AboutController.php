<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;

class AboutController extends Controller
{
    public function index()
    {
        $title = 'About Us';

        return view('pages.about.index', compact('title'));
    }
}
