<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;

class StudentController extends Controller
{
    public function index()
    {
        $title = 'For Students';

        return view('pages.students.index', compact('title'));
    }
}
