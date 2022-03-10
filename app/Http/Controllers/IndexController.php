<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class IndexController extends Controller
{
    /**
     * Index might change in the future, in case there is a main view before logging in
     */
    public function index(): View
    {
        return view('main');
    }
}
