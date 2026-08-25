<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Senderid;
use App\Models\SendingServer;
use App\Models\CustomerBasedSendingServer;
use App\Models\User;
use App\Models\UserVerification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class KYCController extends AdminBaseController
{
    /**
     * List verification requests
     */
    public function index()
    {
        $status = request()->get('status', 'pending');
        
        $query = User::where('is_customer', true);
        
        if ($status) {
            $query->where('verification_status', $status);
        } else {
            $query->whereIn('verification_status', ['pending', 'approved', 'rejected']);
        }
        
        $users = $query->with('latestVerification')->latest()->paginate(20);

        $breadcrumbs = [
            ['link' => url(config('app.admin_path') . '/dashboard'), 'name' => __('locale.menu.Dashboard')],
            ['name' => 'Verification Requests'],
        ];

        return view('admin.verifications.index', compact('users', 'breadcrumbs', 'status'));
    }

    /**
     * Show verification details
     */
    public function show(User $user)
    {
        $user->load('latestVerification', 'verifications');
        
        $sending_servers = SendingServer::where('status', true)->get();
        $sender_ids = Senderid::with('sendingServers')->get()->unique('sender_id');

        $breadcrumbs = [
            ['link' => url(config('app.admin_path') . '/dashboard'), 'name' => __('locale.menu.Dashboard')],
            ['link' => route('admin.verifications.index'), 'name' => 'Verification Requests'],
            ['name' => $user->displayName()],
        ];

        return view('admin.verifications.show', compact('user', 'breadcrumbs', 'sending_servers', 'sender_ids'));
    }

    /**
     * Approve verification
     */
    public function approve(Request $request, User $user)
    {
        $verification = $user->latestVerification;
        
        if ($verification) {
            $verification->update([
                'status' => 'approved',
                'verified_by' => Auth::id(),
                'verified_at' => Carbon::now(),
            ]);
        }

        $user->update([
            'verification_status' => 'approved',
            'status' => true // Ensure account is active
        ]);

        // Assign Sending Server
        if ($request->filled('sending_server')) {
            CustomerBasedSendingServer::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'sending_server' => $request->sending_server,
                    'status' => true
                ]
            );
        }

        // Assign Sender ID/Number
        if ($request->filled('sender_id')) {
            $senderId = Senderid::with('sendingServers')->find($request->sender_id);
            if ($senderId) {
                if ($senderId->allocation_type == 'shared') {
                    // Check if this user already has this sender_id
                    $existing = Senderid::where('user_id', $user->id)
                        ->where('sender_id', $senderId->sender_id)
                        ->first();

                    if (!$existing) {
                        // Create a new record for this user based on the selected one
                        $newSenderId = $senderId->replicate();
                        $newSenderId->user_id = $user->id;
                        $newSenderId->status = 'active';
                        $newSenderId->save();

                        // Sync sending servers
                        if ($senderId->sendingServers->count() > 0) {
                            $newSenderId->sendingServers()->sync($senderId->sendingServers->pluck('id'));
                        }
                    } else {
                        $existing->update(['status' => 'active']);
                    }
                } else {
                    // Dedicated: Transfer ownership
                    $senderId->update([
                        'user_id' => $user->id,
                        'status' => 'active'
                    ]);
                }
            }
        }

        // Notify User
        try {
            $user->notify(new \App\Notifications\KYCApproved());
        } catch (\Exception $e) {
            \Log::error('KYC Approval Notification Error: ' . $e->getMessage());
        }

        return redirect()->route('admin.verifications.index')->with('flash_success', 'User verification approved and resources assigned successfully.');
    }

    /**
     * Reject verification
     */
    public function reject(Request $request, User $user)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $verification = $user->latestVerification;
        
        if ($verification) {
            $verification->update([
                'status' => 'rejected',
                'admin_notes' => $request->reason,
                'verified_by' => Auth::id(),
                'verified_at' => Carbon::now(),
            ]);
        }

        $user->update([
            'verification_status' => 'rejected'
        ]);

        // Notify User
        try {
            $user->notify(new \App\Notifications\KYCRejected($request->reason));
        } catch (\Exception $e) {
            \Log::error('KYC Rejection Notification Error: ' . $e->getMessage());
        }

        return redirect()->route('admin.verifications.index')->with('flash_success', 'User verification rejected.');
    }

    /**
     * View private documents
     */
    public function viewDocument($path)
    {
        $fullPath = storage_path('app/private/' . $path);
        
        if (!file_exists($fullPath)) {
            abort(404);
        }

        return response()->file($fullPath);
    }
}
