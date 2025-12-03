<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;

class UniversityController extends Controller
{
    public function index()
    {
        $title = 'For Universities';

        return view('pages.universities.index', compact('title'));
    }
}
