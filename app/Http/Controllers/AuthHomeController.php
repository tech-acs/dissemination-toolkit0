<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthHomeController extends Controller
{
    public function __invoke()
    {
        return view('manage.home');
    }
}
