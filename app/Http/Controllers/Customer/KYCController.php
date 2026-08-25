<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\UserVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KYCController extends Controller
{
    /**
     * Resubmit verification documents
     */
    public function resubmit(Request $request)
    {
        $request->validate([
            'nid_upload' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'trade_license' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Handle file uploads
        $nid_upload = null;
        if ($request->hasFile('nid_upload')) {
            $file = $request->file('nid_upload');
            $filename = time() . '_nid_' . $user->id . '.' . $file->getClientOriginalExtension();
            $file->move(storage_path('app/private/nid'), $filename);
            $nid_upload = 'nid/' . $filename;
        }

        $trade_license = null;
        if ($request->hasFile('trade_license')) {
            $file = $request->file('trade_license');
            $filename = time() . '_trade_' . $user->id . '.' . $file->getClientOriginalExtension();
            $file->move(storage_path('app/private/trade_license'), $filename);
            $trade_license = 'trade_license/' . $filename;
        }

        // Create new verification record
        UserVerification::create([
            'user_id' => $user->id,
            'nid_document' => $nid_upload,
            'trade_license_document' => $trade_license,
            'status' => 'pending',
        ]);

        // Update user status
        $user->update([
            'verification_status' => 'pending'
        ]);

        // Notify Admin
        try {
            $adminUsers = \App\Models\User::where('is_admin', true)->get();
            foreach ($adminUsers as $admin) {
                $admin->notify(new \App\Notifications\KYCSubmitted($user));
            }
        } catch (\Exception $e) {
            \Log::error('KYC Notification Error: ' . $e->getMessage());
        }

        return redirect()->route('user.home')->with('flash_success', 'Verification documents resubmitted successfully. Please wait for admin review.');

    }
}
