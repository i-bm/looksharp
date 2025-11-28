<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;

class ContactController extends Controller
{
    public function index()
    {
        $title = 'Contact Us';

        return view('pages.contact.index', compact('title'));
    }
}
