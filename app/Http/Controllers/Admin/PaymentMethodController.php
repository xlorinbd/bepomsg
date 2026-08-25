<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\GeneralException;
use App\Http\Requests\Settings\UpdatePaymentMethods;
use App\Models\PaymentMethods;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PaymentMethodController extends AdminBaseController
{
    /**
     * view all payment gateways
     *
     * @return Application|Factory|View
     * @throws AuthorizationException
     */
    public function index(): Factory|View|Application
    {
        $this->authorize('view payment_gateways');

        $breadcrumbs = [
            ['link' => url(config('app.admin_path') . "/dashboard"), 'name' => __('locale.menu.Dashboard')],
            ['link' => url(config('app.admin_path') . "/dashboard"), 'name' => __('locale.menu.Settings')],
            ['name' => __('locale.menu.Payment Gateways')],
        ];

        $payment_gateways = PaymentMethods::all();

        return \view('admin.settings.PaymentMethods.index', compact('payment_gateways', 'breadcrumbs'));
    }


    /**
     *
     * change status
     *
     * @param  PaymentMethods  $gateway
     *
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws GeneralException
     */
    public function activeToggle(PaymentMethods $gateway): JsonResponse
    {
        if (config('app.stage') == 'demo') {
            return response()->json([
                'status' => 'error',
                'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        try {

            $this->authorize('view payment_gateways');

            if ($gateway->update(['status' => !$gateway->status])) {
                return response()->json([
                    'status' => 'success',
                    'message' => __('locale.settings.status_successfully_change'),
                ]);
            }

            throw new GeneralException(__('locale.exceptions.something_went_wrong'));

        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ]);
        }
    }

    /**
     * manage payment gateway
     *
     * @param  PaymentMethods  $gateway
     *
     * @return Application|Factory|View
     * @throws AuthorizationException
     */
    public function show(PaymentMethods $gateway): Factory|View|Application
    {
        $this->authorize('update payment_gateways');

        $breadcrumbs = [
            ['link' => url(config('app.admin_path') . "/dashboard"), 'name' => __('locale.menu.Dashboard')],
            ['link' => url(config('app.admin_path') . "/payment-gateways"), 'name' => __('locale.menu.Payment Gateways')],
            ['name' => $gateway->name],
        ];

        return \view('admin.settings.PaymentMethods.show', compact('gateway', 'breadcrumbs'));
    }


    /**
     * update payment gateway information
     *
     * @param  PaymentMethods  $payment_gateway
     * @param  UpdatePaymentMethods  $request
     *
     * @return RedirectResponse
     * @throws GeneralException
     */
    public function update(PaymentMethods $payment_gateway, UpdatePaymentMethods $request): RedirectResponse
    {
        if (config('app.stage') == 'demo') {
            return redirect()->route('admin.payment-gateways.show', $payment_gateway->uid)->with([
                'status' => 'error',
                'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $options = $request->except('_token', '_method', 'name', 'type', 'gateway_logo');

        // Handle Image Upload
        if ($request->hasFile('gateway_logo')) {
            $image = $request->file('gateway_logo');
            $name = $payment_gateway->type . '_logo_' . time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/uploads/gateways');
            
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            // Delete old logo if exists
            $oldLogoPath = $payment_gateway->getOption('gateway_logo');
            if ($oldLogoPath && file_exists(public_path($oldLogoPath))) {
                unlink(public_path($oldLogoPath));
            }

            $image->move($destinationPath, $name);
            $options['gateway_logo'] = 'uploads/gateways/' . $name;
        } else {
            // Keep existing logo if no new one is uploaded
            $options['gateway_logo'] = $payment_gateway->getOption('gateway_logo');
        }

        $payment_gateway->name = $request->input('name');
        $payment_gateway->options = json_encode($options);

        if (!$payment_gateway->save()) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        return redirect()->route('admin.payment-gateways.show', $payment_gateway->uid)->with([
            'status' => 'success',
            'message' => __('locale.payment_gateways.gateway_was_updated'),
        ]);

    }

    public function testBkashConnection(PaymentMethods $gateway): JsonResponse
    {
        if (config('app.stage') == 'demo') {
            return response()->json([
                'status' => 'error',
                'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        try {
            $options = json_decode($gateway->options);

            // Following bKash Official Doc: POST /checkout/token/grant
            $post_token = [
                'app_key' => trim($options->bkash_app_key),
                'app_secret' => trim($options->bkash_app_secret),
            ];

            $url = ($options->environment === 'sandbox')
                ? 'https://tokenized.sandbox.bka.sh/v1.2.0-beta/tokenized/checkout/token/grant'
                : 'https://tokenized.pay.bka.sh/v1.2.0-beta/tokenized/checkout/token/grant';

            $header = [
                'Content-Type:application/json',
                "username: " . trim($options->bkash_username),
                "password: " . trim($options->bkash_password),
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_token));
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Important for local environments

            $resultdata = curl_exec($ch);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($resultdata === false) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Curl Error: ' . $curlError,
                ]);
            }

            $response = json_decode($resultdata, true);

            if (isset($response['id_token'])) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Connection Successful! bKash Token Generated Successfully.',
                ]);
            }

            $errorMessage = $response['statusMessage'] ?? ($response['message'] ?? 'Unknown Error');
            return response()->json([
                'status' => 'error',
                'message' => 'bKash Error: ' . $errorMessage,
                'debug' => $response
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'System Error: ' . $e->getMessage(),
            ]);
        }
    }

}
