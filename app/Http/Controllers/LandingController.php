<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function index(): View
    {
        $settings = SiteSetting::getValue('landing', config('landing'));

        return view('landing', compact('settings'));
    }
}
