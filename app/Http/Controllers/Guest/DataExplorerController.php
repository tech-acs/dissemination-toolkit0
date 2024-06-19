<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Story;
use App\Models\Topic;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class DataExplorerController extends Controller
{
    public function index(Request $request)
    {
        return view('guest.data-explorer');
    }
}
