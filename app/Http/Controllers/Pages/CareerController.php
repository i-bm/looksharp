<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;

class CareerController extends Controller
{
    public function index()
    {
        $title = 'Careers';

        return view('pages.careers.index', compact('title'));
    }

    public function show($id)
    {
        $title = 'Career Details';

        return view('pages.careers.show', compact('title', 'id'));
    }
}
