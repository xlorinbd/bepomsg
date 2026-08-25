<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Karim007\LaravelBkashTokenize\Facade\BkashPaymentTokenize;
use App\Models\PaymentMethods;

$paymentMethod = PaymentMethods::where('status', true)->where('type', PaymentMethods::TYPE_BKASH)->first();
if ($paymentMethod) {
    $credentials = json_decode($paymentMethod->options);
    config([
        'bkash.sandbox' => $credentials->environment == 'sandbox',
        'bkash.bkash_app_key' => $credentials->bkash_app_key,
        'bkash.bkash_app_secret' => $credentials->bkash_app_secret,
        'bkash.bkash_username' => $credentials->bkash_username,
        'bkash.bkash_password' => $credentials->bkash_password,
    ]);

    echo "Config App Key: " . config('bkash.bkash_app_key') . "\n";

    // Attempting to resolve the service and check its internal state or call a method
    try {
        $request_data = [
            'amount' => 10,
            'intent' => 'sale',
            'mode' => '0011',
            'payerReference' => 'test',
            'merchantInvoiceNumber' => 'test_inv',
            'callbackURL' => 'http://localhost/callback',
        ];
        $response = BkashPaymentTokenize::cPayment(json_encode($request_data));
        print_r($response);
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "Payment Method not found\n";
}
