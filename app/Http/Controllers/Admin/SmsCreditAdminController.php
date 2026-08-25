<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Invoices;
use App\Models\PaymentMethods;
use App\Models\SmsPricingTier;
use App\Models\SmsPurchase;
use App\Models\User;
use App\Services\SMSCreditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SmsCreditAdminController extends Controller
{
    protected SMSCreditService $creditService;

    public function __construct(SMSCreditService $creditService)
    {
        $this->creditService = $creditService;
    }

    /*
    |--------------------------------------------------------------------------
    | Pricing Tiers Management
    |--------------------------------------------------------------------------
    */

    public function tierIndex()
    {
        $tiers = SmsPricingTier::orderBy('min_qty')->paginate(15);
        return view('admin.sms_credits.tiers.index', compact('tiers'));
    }

    public function tierStore(Request $request): JsonResponse
    {
        $request->validate([
            'min_qty' => 'required|integer|min:1',
            'max_qty' => 'nullable|integer|gt:min_qty',
            'rate' => 'required|numeric|min:0.0001',
            'status' => 'boolean',
        ]);

        SmsPricingTier::create([
            'min_qty' => $request->min_qty,
            'max_qty' => $request->max_qty,
            'rate' => $request->rate,
            'status' => $request->boolean('status', true),
        ]);

        return response()->json(['status' => 'success', 'message' => 'Pricing tier created successfully.']);
    }

    public function tierUpdate(Request $request, SmsPricingTier $tier): JsonResponse
    {
        $request->validate([
            'min_qty' => 'required|integer|min:1',
            'max_qty' => 'nullable|integer',
            'rate' => 'required|numeric|min:0.0001',
            'status' => 'boolean',
        ]);

        $tier->update([
            'min_qty' => $request->min_qty,
            'max_qty' => $request->max_qty,
            'rate' => $request->rate,
            'status' => $request->boolean('status', true),
        ]);

        return response()->json(['status' => 'success', 'message' => 'Pricing tier updated.']);
    }

    public function tierDestroy(SmsPricingTier $tier): JsonResponse
    {
        $tier->delete();
        return response()->json(['status' => 'success', 'message' => 'Pricing tier deleted.']);
    }

    public function tierToggle(SmsPricingTier $tier): JsonResponse
    {
        $tier->update(['status' => !$tier->status]);
        return response()->json(['status' => 'success', 'message' => 'Status toggled.', 'active' => $tier->status]);
    }

    /*
    |--------------------------------------------------------------------------
    | Purchase Logs & Approvals
    |--------------------------------------------------------------------------
    */

    public function purchaseIndex(Request $request)
    {
        $purchases = SmsPurchase::with('user')
            ->when($request->input('status'), fn($q, $s) => $q->where('status', $s))
            ->when($request->input('search'), function ($q, $search) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%"))
                    ->orWhere('transaction_id', 'like', "%$search%");
            })
            ->latest()
            ->paginate(20);

        return view('admin.sms_credits.purchases.index', compact('purchases'));
    }

    public function purchaseApprove(Request $request, SmsPurchase $purchase): JsonResponse
    {
        if ($purchase->status !== 'pending') {
            return response()->json(['status' => 'error', 'message' => 'Only pending purchases can be approved.'], 422);
        }

        $request->validate([
            'credits_added' => 'nullable|integer|min:0',
        ]);

        $user = User::findOrFail($purchase->user_id);
        $creditsToAdd = $request->input('credits_added', $purchase->sms_quantity);
        $bonusStr = '';

        if ($creditsToAdd !== $purchase->sms_quantity) {
            $bonus = $creditsToAdd - $purchase->sms_quantity;
            $bonusStr = $bonus > 0 ? " (+{$bonus} bonus)" : '';
        }

        // Add credits to user balance
        $this->creditService->addCredits($user, $creditsToAdd);

        // Update purchase record
        $purchase->update([
            'status' => 'completed',
            'credits_added' => $creditsToAdd - $purchase->sms_quantity, // store bonus separately
        ]);

        // Create Invoice
        $paymentMethod = PaymentMethods::where('type', 'offline')->first();
        
        Invoices::create([
            'user_id' => $user->id,
            'currency_id' => 8, // BDT currency ID found via tinker
            'payment_method' => $paymentMethod ? $paymentMethod->id : 1,
            'amount' => $purchase->total_price,
            'type' => Invoices::TYPE_SMS_CREDIT,
            'qty' => $purchase->sms_quantity,
            'rate' => $purchase->rate,
            'description' => "Purchase of {$purchase->sms_quantity} SMS Credits @ {$purchase->rate} BDT",
            'transaction_id' => $purchase->transaction_id ?? $purchase->uid,
            'status' => Invoices::STATUS_PAID,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => "Purchase approved. {$creditsToAdd} SMS credits added to {$user->name}.",
        ]);
    }

    public function purchaseReject(SmsPurchase $purchase): JsonResponse
    {
        if ($purchase->status !== 'pending') {
            return response()->json(['status' => 'error', 'message' => 'Only pending purchases can be rejected.'], 422);
        }

        $purchase->update(['status' => 'rejected']);

        return response()->json(['status' => 'success', 'message' => 'Purchase request rejected.']);
    }

    /*
    |--------------------------------------------------------------------------
    | Manual Balance Adjustment
    |--------------------------------------------------------------------------
    */

    public function balanceAdjustment()
    {
        return view('admin.sms_credits.tiers.balance_adjustment');
    }

    public function adjustBalance(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'amount' => 'required|integer',
            'note' => 'nullable|string|max:255',
        ]);

        $user = User::findOrFail($request->user_id);
        $amount = (int) $request->amount;
        $note = $request->input('note', 'Manual admin adjustment');

        if ($amount > 0) {
            $this->creditService->addCredits($user, $amount);
        } elseif ($amount < 0) {
            $absAmount = abs($amount);
            if (!$user->hasEnoughCredits($absAmount)) {
                return response()->json([
                    'status' => 'error',
                    'message' => "User only has {$user->sms_balance} credits. Cannot deduct {$absAmount}.",
                ], 422);
            }
            $this->creditService->deductCredits($user, $absAmount);
        }

        return response()->json([
            'status' => 'success',
            'message' => "Balance adjusted. {$user->name} now has {$user->fresh()->sms_balance} SMS credits.",
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | User Search for Balance Adjustment
    |--------------------------------------------------------------------------
    */

    public function searchUsers(Request $request): JsonResponse
    {
        $query = trim($request->input('q', ''));

        if (strlen($query) < 1) {
            return response()->json([]);
        }

        $users = User::where('is_customer', true)
            ->where(function ($q) use ($query) {
                // Search by user ID (exact)
                if (is_numeric($query)) {
                    $q->where('id', $query);
                }

                // Search by email
                $q->orWhere('email', 'like', "%{$query}%");

                // Search by name
                $q->orWhere('first_name', 'like', "%{$query}%")
                  ->orWhere('last_name', 'like', "%{$query}%");

                // Search by phone number in customers table
                $q->orWhereHas('customer', function ($sub) use ($query) {
                    $sub->where('phone', 'like', "%{$query}%");
                });
            })
            ->with('customer:id,user_id,phone,company')
            ->limit(10)
            ->get(['id', 'first_name', 'last_name', 'email', 'status', 'sms_balance']);

        $results = $users->map(function ($user) {
            return [
                'id'          => $user->id,
                'name'        => $user->first_name . ' ' . $user->last_name,
                'email'       => $user->email,
                'phone'       => $user->customer->phone ?? 'N/A',
                'company'     => $user->customer->company ?? 'N/A',
                'sms_balance' => $user->sms_balance,
                'status'      => $user->status ? 'Active' : 'Inactive',
            ];
        });

        return response()->json($results);
    }
}
