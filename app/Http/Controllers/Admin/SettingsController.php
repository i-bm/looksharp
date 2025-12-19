<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    /**
     * Display system settings page.
     */
    public function index()
    {
        $user = auth()->user();

        Log::info('Settings page accessed', [
            'user_id' => $user->id,
        ]);

        $title = 'System Settings';

        // Placeholder for future settings functionality
        $settings = [
            'system_name' => config('app.name'),
            'system_email' => config('mail.from.address'),
        ];

        return view('pages.dashboard.admin.settings.index', compact('settings', 'title'));
    }

    /**
     * Update system settings.
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        Log::info('Settings update requested', [
            'user_id' => $user->id,
            'settings_keys' => array_keys($request->all()),
        ]);

        try {
            // Placeholder for future settings update logic
            // This will be implemented when settings model/config is added

            Log::info('Settings updated successfully', [
                'user_id' => $user->id,
            ]);

            return back()->with('success', 'Settings updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update settings', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Failed to update settings. Please try again.']);
        }
    }
}
