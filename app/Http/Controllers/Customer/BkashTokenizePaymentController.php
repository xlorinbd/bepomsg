<?php

namespace App\Http\Controllers\Customer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Karim007\LaravelBkashTokenize\Facade\BkashPaymentTokenize;
use Karim007\LaravelBkashTokenize\Facade\BkashRefundTokenize;

class BkashTokenizePaymentController extends Controller
{
    public function index()
    {
        return view('bkashT::bkash-payment');
    }
    private function setBkashConfig()
    {
        $paymentMethod = \App\Models\PaymentMethods::where('status', true)->where('type', \App\Models\PaymentMethods::TYPE_BKASH)->first();
        if ($paymentMethod) {
            $credentials = json_decode($paymentMethod->options);
            config([
                'bkash.sandbox' => $credentials->environment == 'sandbox',
                'bkash.bkash_app_key' => $credentials->bkash_app_key,
                'bkash.bkash_app_secret' => $credentials->bkash_app_secret,
                'bkash.bkash_username' => $credentials->bkash_username,
                'bkash.bkash_password' => $credentials->bkash_password,
                'bkash.bkash_transaction_charge' => $credentials->bkash_transaction_charge ?? 0,
                'bkash.callbackURL' => route('customer.bkash.callback'),
            ]);
            \Log::info('bKash Config Set:', [
                'sandbox' => config('bkash.sandbox'),
                'app_key' => config('bkash.bkash_app_key'),
                'username' => config('bkash.bkash_username'),
            ]);
        } else {
            \Log::error('bKash Payment Method not found or inactive');
        }
    }

    public function createPayment(Request $request)
    {
        $this->setBkashConfig();

        $inv = uniqid();
        $request['intent'] = 'sale';
        $request['mode'] = '0011'; //0011 for checkout
        $request['payerReference'] = $inv;
        $request['currency'] = 'BDT';
        $request['amount'] = $request->get('amount', 10);
        $transaction_charge_rate = config('bkash.bkash_transaction_charge');
        $transaction_charge = 0;
        if ($transaction_charge_rate > 0) {
            $transaction_charge = ($request['amount'] * $transaction_charge_rate) / 100;
        }
        $request['amount'] = number_format((float) $request['amount'] + $transaction_charge, 2, '.', '');
        
        $request['merchantInvoiceNumber'] = $inv;
        $request['callbackURL'] = config("bkash.callbackURL");

        // Store data in session for callback
        session([
            'bkash_sms_unit' => $request->sms_unit,
            'bkash_price' => $request->price,
            'bkash_tax_amount' => $request->tax_amount,
            'bkash_transaction_charge' => $transaction_charge,
            'bkash_transaction_charge_rate' => $transaction_charge_rate,
            'bkash_total_amount' => $request->amount,
            'bkash_user_id' => $request->user_id,
            'bkash_email' => $request->email,
            'bkash_purchase_type' => $request->purchase_type,
        ]);

        $request['callbackURL'] = route('customer.bkash.callback', [
            'user_id' => $request->user_id,
            'email' => $request->email,
            'sms_unit' => $request->sms_unit,
            'price' => $request->price,
            'tax_amount' => $request->tax_amount,
            'transaction_charge' => $transaction_charge,
            'transaction_charge_rate' => $transaction_charge_rate,
            'total_amount' => $request->amount,
            'purchase_type' => $request->purchase_type,
        ]);

        \Log::info('bKash Payment Request Data:', [
            'request_all' => $request->all(),
            'transaction_charge' => $transaction_charge,
            'final_amount' => $request['amount']
        ]);

        $request_data_json = json_encode($request->all());

        $response = BkashPaymentTokenize::cPayment($request_data_json);

        if (isset($response['bkashURL']))
            return redirect()->away($response['bkashURL']);
        else
            return redirect()->route('customer.buy_sms.index')->with([
                'status' => 'error',
                'message' => 'bKash Error: ' . ($response['statusMessage'] ?? $response['msg'] ?? 'Payment initialization failed.')
            ]);
    }

    public function callBack(Request $request)
    {
        $this->setBkashConfig();

        if ($request->status == 'success') {
            $response = BkashPaymentTokenize::executePayment($request->paymentID);
            if (!$response) {
                $response = BkashPaymentTokenize::queryPayment($request->paymentID);
            }

            if (isset($response['statusCode']) && $response['statusCode'] == "0000" && $response['transactionStatus'] == "Completed") {

                // Payment success logic
                return redirect()->route('customer.top_up.payment_success', [
                    'payment_method' => \App\Models\PaymentMethods::TYPE_BKASH,
                    'user_id' => $request->user_id ?: session('bkash_user_id'),
                    'email' => $request->email ?: session('bkash_email'),
                    'sms_unit' => $request->sms_unit ?: session('bkash_sms_unit'),
                    'price' => $request->price ?: session('bkash_price'),
                    'tax_amount' => $request->tax_amount ?: session('bkash_tax_amount'),
                    'transaction_charge' => $request->transaction_charge ?: session('bkash_transaction_charge'),
                    'transaction_charge_rate' => $request->transaction_charge_rate ?: session('bkash_transaction_charge_rate'),
                    'total_amount' => $request->total_amount ?: session('bkash_total_amount') ?: ($response['amount'] ?? null),
                    'transaction_id' => $response['trxID'],
                    'purchase_type' => $request->purchase_type ?: session('bkash_purchase_type'),
                ]);
            }
            return redirect()->route('customer.buy_sms.index')->with([
                'status' => 'error',
                'message' => $response['statusMessage'] ?? 'bKash Payment failed or rejected.'
            ]);
        } else if ($request->status == 'cancel') {
            return redirect()->route('customer.buy_sms.index')->with([
                'status' => 'error',
                'message' => 'bKash Payment was canceled.'
            ]);
        } else {
            return redirect()->route('customer.buy_sms.index')->with([
                'status' => 'error',
                'message' => 'bKash Payment failed.'
            ]);
        }
    }

    public function searchTnx($trxID)
    {
        //response
        return BkashPaymentTokenize::searchTransaction($trxID);
        //return BkashPaymentTokenize::searchTransaction($trxID,1); //last parameter is your account number for multi account its like, 1,2,3,4,cont..
    }

    public function refund(Request $request)
    {
        $paymentID = 'Your payment id';
        $trxID = 'your transaction no';
        $amount = 5;
        $reason = 'this is test reason';
        $sku = 'abc';
        //response
        return BkashRefundTokenize::refund($paymentID, $trxID, $amount, $reason, $sku);
        //return BkashRefundTokenize::refund($paymentID,$trxID,$amount,$reason,$sku, 1); //last parameter is your account number for multi account its like, 1,2,3,4,cont..
    }
    public function refundStatus(Request $request)
    {
        $paymentID = 'Your payment id';
        $trxID = 'your transaction no';
        return BkashRefundTokenize::refundStatus($paymentID, $trxID);
        //return BkashRefundTokenize::refundStatus($paymentID,$trxID, 1); //last parameter is your account number for multi account its like, 1,2,3,4,cont..
    }
}
