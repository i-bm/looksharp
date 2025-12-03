<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;

class EmployerController extends Controller
{
    public function index()
    {
        $title = 'For Employers';

        return view('pages.employers.index', compact('title'));
    }
}
