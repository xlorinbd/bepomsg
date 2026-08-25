<?php

namespace App\Http\Controllers\Admin;

use App\Models\ApiFeature;
use Illuminate\Http\Request;

class ApiFeatureController extends AdminBaseController
{
    public function index()
    {
        $this->authorize('general settings');

        $breadcrumbs = [
            ['link' => url(config('app.admin_path') . "/dashboard"), 'name' => __('locale.menu.Dashboard')],
            ['link' => url(config('app.admin_path') . "/settings"), 'name' => __('locale.menu.Settings')],
            ['name' => 'API Documentation Settings'],
        ];

        $features = ApiFeature::all();

        return view('admin.settings.ApiFeatures.index', compact('breadcrumbs', 'features'));
    }

    public function update(Request $request)
    {
        $this->authorize('general settings');

        if (config('app.stage') == 'demo') {
            return redirect()->back()->with([
                'status'  => 'error',
                'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $features = $request->input('features', []);

        // Reset all statuses to 0 first
        ApiFeature::query()->update(['status' => 0]);

        // Enable checked features
        foreach ($features as $slug => $status) {
            ApiFeature::where('slug', $slug)->update(['status' => 1]);
        }

        return redirect()->back()->with([
            'status'  => 'success',
            'message' => 'API Documentation settings updated successfully',
        ]);
    }
}
