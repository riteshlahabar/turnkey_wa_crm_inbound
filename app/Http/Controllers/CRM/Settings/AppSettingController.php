<?php

namespace App\Http\Controllers\CRM\Settings;

use App\Http\Controllers\Controller;
use App\Models\CRM\CrmAppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class AppSettingController extends Controller
{
    public function index()
    {
        $setting = CrmAppSetting::firstOrCreateDefault();
        return view('crm.settings.app-settings.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'app_name' => 'required|string|max:255',
            'primary_color' => 'nullable|string|max:20',
            'secondary_color' => 'nullable|string|max:20',
            'app_logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'login_logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'splash_logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'default_profile_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $setting = CrmAppSetting::firstOrCreateDefault();

        foreach (['app_logo', 'login_logo', 'splash_logo', 'default_profile_image'] as $field) {
            if ($request->hasFile($field)) {
                $data[$field] = $this->uploadPublicFile($request->file($field), $field);
            } else {
                unset($data[$field]);
            }
        }

        $setting->update($data);

        return back()->with('success', 'App settings updated successfully.');
    }

    public function apiSettings()
    {
        return response()->json([
            'success' => true,
            'data' => CrmAppSetting::firstOrCreateDefault()->toMobileArray(),
        ]);
    }

    private function uploadPublicFile($file, string $prefix): string
    {
        $directory = public_path('uploads/app-settings');

        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $filename = $prefix . '-' . time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($directory, $filename);

        return 'uploads/app-settings/' . $filename;
    }
}
