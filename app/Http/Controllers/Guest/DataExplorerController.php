<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;

class DataExplorerController extends Controller
{
    public function index()
    {
        return view('guest.data-explorer');
    }
}
