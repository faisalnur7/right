<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GeneralSetting;

class GeneralSettingController extends Controller
{
    public function edit()
    {
        $settings = GeneralSetting::first();
        return view('admin.settings.general.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name'             => 'nullable|string|max:255',
            'site_title'            => 'nullable|string|max:255',
            'business_start_date'   => 'nullable|date',
            'email'                 => 'nullable|email|max:255',
            'phone'                 => 'nullable|string|max:255',
            'address'               => 'nullable|string|max:1000',

            'facebook'              => 'nullable|url|max:255',
            'twitter'               => 'nullable|url|max:255',
            'instagram'             => 'nullable|url|max:255',
            'linkedin'              => 'nullable|url|max:255',
            'youtube'               => 'nullable|url|max:255',

            'site_logo'             => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
            'site_favicon'          => 'nullable|image|mimes:png,ico|max:1024',
        ]);

        $data = $validated;

        // Upload site_logo
        if ($request->hasFile('site_logo')) {
            $logo = $request->file('site_logo');
            $logoName = time() . '_' . $logo->getClientOriginalName();
            $logo->move(public_path('settings/site_logo'), $logoName);
            $data['site_logo'] = 'settings/site_logo/' . $logoName;
        }

        // Upload site_favicon
        if ($request->hasFile('site_favicon')) {
            $favicon = $request->file('site_favicon');
            $faviconName = time() . '_' . $favicon->getClientOriginalName();
            $favicon->move(public_path('settings/site_favicon'), $faviconName);
            $data['site_favicon'] = 'settings/site_favicon/' . $faviconName;
        }

        // Update or create the settings row
        GeneralSetting::updateOrCreate(['id' => 1], $data);

        return back()->with('success', 'General settings updated successfully.');
    }

}
