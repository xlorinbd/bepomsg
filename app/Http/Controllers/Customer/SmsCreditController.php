<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Invoices;
use App\Models\PaymentMethods;
use App\Models\SmsPricingTier;
use App\Models\SmsPurchase;
use App\Services\SMSCreditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SmsCreditController extends Controller
{
    protected SMSCreditService $creditService;

    public function __construct(SMSCreditService $creditService)
    {
        $this->creditService = $creditService;
    }

    /**
     * Show the Buy SMS Credits page.
     */
    public function index()
    {
        $user = Auth::user();
        $tiers = SmsPricingTier::where('status', true)->orderBy('min_qty')->get();
        $payment_methods = PaymentMethods::where('status', true)->get();

        $breadcrumbs = [
            ['link' => url('dashboard'), 'name' => __('locale.menu.Dashboard')],
            ['name' => 'Buy SMS'],
        ];

        $monthSpend = (float) SmsPurchase::forUser($user->id)->completed()
            ->where('created_at', '>=', now()->startOfMonth())
            ->sum('total_price');

        $outstandingInvoices = Invoices::where('user_id', $user->id)
            ->whereIn('status', [Invoices::STATUS_UNPAID, Invoices::STATUS_PENDING])
            ->count();

        $orderCount   = SmsPurchase::where('user_id', $user->id)->count();
        $recentOrders = SmsPurchase::where('user_id', $user->id)->latest()->take(5)->get();

        return view('customer.sms_credits.index', [
            'user' => $user,
            'balance' => $user->sms_balance,
            'tiers' => $tiers,
            'payment_methods' => $payment_methods,
            'breadcrumbs' => $breadcrumbs,
            'monthSpend' => $monthSpend,
            'outstandingInvoices' => $outstandingInvoices,
            'orderCount' => $orderCount,
            'recentOrders' => $recentOrders,
        ]);
    }

    /**
     * Get price quote for a given quantity (AJAX).
     */
    public function getQuote(Request $request): JsonResponse
    {
        $request->validate(['quantity' => 'required|integer|min:1']);

        $quantity = (int) $request->input('quantity');
        $tier = SmsPricingTier::getTierForQuantity($quantity);

        if (!$tier) {
            return response()->json([
                'status' => 'error',
                'message' => 'No pricing available for this quantity.',
            ], 422);
        }

        $rate = (float) $tier->rate;
        $totalPrice = round($quantity * $rate, 2);

        return response()->json([
            'status' => 'success',
            'quantity' => $quantity,
            'rate' => $rate,
            'total_price' => $totalPrice,
            'tier_label' => "{$tier->min_qty}–" . ($tier->max_qty ?? '∞') . " SMS @ {$rate} BDT/SMS",
        ]);
    }

    /**
     * Show checkout page with payment options (like Top Up checkout).
     */
    public function checkout(Request $request)
    {
        if (!$request->has('quantity')) {
            return redirect()->route('customer.buy_sms.index')->with([
                'status' => 'error',
                'message' => 'Please select a quantity first.',
            ]);
        }

        $request->validate(['quantity' => 'required|integer|min:1']);

        $quantity = (int) $request->input('quantity');
        $tier = SmsPricingTier::getTierForQuantity($quantity);

        if (!$tier) {
            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'No pricing available for this quantity.',
            ]);
        }

        $rate = (float) $tier->rate;
        $amount = round($quantity * $rate, 2);
        $payment_methods = PaymentMethods::where('status', true)->get();

        $breadcrumbs = [
            ['link' => url('dashboard'), 'name' => __('locale.menu.Dashboard')],
            ['link' => route('customer.buy_sms.index'), 'name' => 'Buy SMS'],
            ['name' => __('locale.labels.checkout')],
        ];

        $pageConfigs = [
            'bodyClass' => 'ecommerce-application',
        ];

        return view('customer.sms_credits.checkout', compact(
            'breadcrumbs',
            'amount',
            'quantity',
            'rate',
            'pageConfigs',
            'payment_methods'
        ));
    }

    /**
     * Show purchase history.
     */
    public function history(Request $request)
    {
        $user = Auth::user();
        $purchases = SmsPurchase::where('user_id', $user->id)
            ->latest()
            ->paginate(15);

        $breadcrumbs = [
            ['link' => url('dashboard'), 'name' => __('locale.menu.Dashboard')],
            ['link' => route('customer.buy_sms.index'), 'name' => 'Buy SMS'],
            ['name' => 'My Orders'],
        ];

        return view('customer.sms_credits.history', [
            'user' => $user,
            'balance' => $user->sms_balance,
            'purchases' => $purchases,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    /**
     * Return current balance as JSON (for dashboard widget refresh).
     */
    public function balance(): JsonResponse
    {
        $user = Auth::user();

        return response()->json([
            'status' => 'success',
            'sms_balance' => $user->sms_balance,
        ]);
    }

    /**
     * Get all pricing tiers as JSON (for API / frontend use).
     */
    public function pricingTiers(): JsonResponse
    {
        $tiers = SmsPricingTier::where('status', true)
            ->orderBy('min_qty')
            ->get(['min_qty', 'max_qty', 'rate']);

        return response()->json([
            'status' => 'success',
            'tiers' => $tiers,
        ]);
    }

    /**
     * Confirm a manual / offline payment purchase (Admin-approved flow).
     */
    public function confirmOfflinePurchase(Request $request): JsonResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'transaction_id' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $quantity = (int) $request->input('quantity');
        $tier = SmsPricingTier::getTierForQuantity($quantity);

        if (!$tier) {
            return response()->json([
                'status' => 'error',
                'message' => 'No pricing available for this quantity.',
            ], 422);
        }

        $rate = (float) $tier->rate;
        $totalPrice = round($quantity * $rate, 2);

        // Create a pending purchase record (Admin must approve)
        SmsPurchase::create([
            'user_id' => $user->id,
            'sms_quantity' => $quantity,
            'credits_added' => 0,
            'rate' => $rate,
            'total_price' => $totalPrice,
            'payment_method' => 'offline',
            'transaction_id' => $request->input('transaction_id'),
            'status' => 'pending',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Purchase request submitted. Credits will be added after admin approval.',
        ]);
    }
}
