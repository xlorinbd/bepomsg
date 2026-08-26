<?php

namespace App\Http\Controllers;

use App\Models\Plan;

class WebsiteController extends Controller
{
    /**
     * Public landing page.
     */
    public function home()
    {
        if (config('app.stage') == 'new') {
            return redirect('install');
        }

        $plans = Plan::where('status', true)
            ->where('show_in_customer', true)
            ->orderBy('custom_order')
            ->take(3)
            ->get();

        return view('website.home', compact('plans'));
    }

    /**
     * Public packages / pricing page.
     */
    public function packages()
    {
        $plans = Plan::where('status', true)
            ->where('show_in_customer', true)
            ->orderBy('custom_order')
            ->get();

        return view('website.packages', compact('plans'));
    }
}
