<?php

namespace App\Services;

use App\Models\SendingServer;
use Exception;
use Illuminate\Support\Facades\Log;

class ServerBalanceChecker
{
    /**
     * Check balance for a single sending server.
     *
     * @param SendingServer $server
     * @return array{balance: string|float|null, currency: string, status: string, raw_response: string|null}
     */
    public function checkBalance(SendingServer $server): array
    {
        // Skip inactive servers
        if (!$server->status) {
            return [
                'balance'      => null,
                'currency'     => '',
                'status'       => 'skipped',
                'raw_response' => 'Server is inactive (status: ' . var_export($server->status, true) . ')',
            ];
        }

        // Skip servers without credentials
        if (!$this->hasCredentials($server)) {
            return [
                'balance'      => null,
                'currency'     => '',
                'status'       => 'skipped',
                'raw_response' => 'No API credentials configured (Checked api_key, api_token, username, password, etc.)',
            ];
        }

        try {
            $type = strtoupper($server->settings ?? $server->type);

            return match ($type) {
                'GREENWEBBD'         => $this->checkGreenWebBD($server),
                'BULKSMSBD'          => $this->checkBulkSMSBD($server),
                'ELITBUZZBD'         => $this->checkElitBuzzBD($server),
                'TWILIO',
                'TWILIOCOPILOT'      => $this->checkTwilio($server),
                'VONAGE'             => $this->checkVonage($server),
                'PLIVO',
                'PLIVOPOWERPACK'     => $this->checkPlivo($server),
                'INFOBIP'            => $this->checkInfobip($server),
                'CLICKSEND'          => $this->checkClickSend($server),
                'SMSGLOBAL'          => $this->checkSMSGlobal($server),
                'BUDGETSMS'          => $this->checkBudgetSMS($server),
                'BULKSMS'            => $this->checkBulkSMS($server),
                'AFRICASTALKING'     => $this->checkAfricasTalking($server),
                'TELNYX',
                'TELNYXNUMBERPOOL'   => $this->checkTelnyx($server),
                'SMSTO'              => $this->checkSmsTo($server),
                'TERMII'             => $this->checkTermii($server),
                'D7NETWORKS'         => $this->checkD7Networks($server),
                'EASYSENDSMS'        => $this->checkEasySendSMS($server),
                default              => [
                    'balance'      => null,
                    'currency'     => '',
                    'status'       => 'unsupported',
                    'raw_response' => 'Balance check not supported for: ' . $type,
                ],
            };
        } catch (Exception $e) {
            Log::error('ServerBalanceChecker error', [
                'server_id'   => $server->id,
                'server_name' => $server->name,
                'type'        => $server->settings ?? $server->type,
                'error'       => $e->getMessage(),
            ]);

            return [
                'balance'      => null,
                'currency'     => '',
                'status'       => 'error',
                'raw_response' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check if a server has the minimum required credentials.
     */
    protected function hasCredentials(SendingServer $server): bool
    {
        // Check main table credentials
        $fields = [
            $server->api_key, $server->api_token, $server->api_secret,
            $server->username, $server->password, $server->account_sid,
            $server->auth_token, $server->auth_id, $server->auth_key,
            $server->access_key, $server->access_token, $server->user_token,
            $server->c1, $server->c2, $server->c3,
        ];

        foreach ($fields as $field) {
            if (!empty($field)) {
                return true;
            }
        }

        // Check custom server credentials if it's a custom server
        if ($server->customSendingServer) {
            $custom = $server->customSendingServer;
            $customFields = [
                $custom->username_value,
                $custom->password_value,
                $custom->custom_one_value,
                $custom->custom_two_value,
                $custom->custom_three_value,
            ];

            foreach ($customFields as $field) {
                if (!empty($field)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Make a cURL request and return the response.
     */
    protected function curlRequest(string $url, array $options = []): array
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        foreach ($options as $key => $value) {
            curl_setopt($ch, $key, $value);
        }

        $response  = curl_exec($ch);
        $httpCode  = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        return [
            'body'      => $response,
            'http_code' => $httpCode,
            'error'     => $curlError,
        ];
    }

    // ─────────────────────────────────────────────
    // Provider-specific balance check methods
    // ─────────────────────────────────────────────

    protected function checkGreenWebBD(SendingServer $server): array
    {
        // GreenWeb often uses api.php for balance check
        $url      = 'http://api.greenweb.com.bd/api.php';
        $response = $this->curlRequest($url . '?' . http_build_query([
                'token'   => $server->api_token,
                'balance' => 'true', // pass as string 'true'
            ]));

        if ($response['error']) {
            throw new Exception('cURL error: ' . $response['error']);
        }

        $body = trim($response['body']);
        
        // Remove any non-numeric characters except decimal point (e.g., "Your balance is: 100.50" -> "100.50")
        if (preg_match('/(\d+(\.\d+)?)/', $body, $matches)) {
            return [
                'balance'      => (float) $matches[1],
                'currency'     => 'BDT',
                'status'       => 'success',
                'raw_response' => $body,
            ];
        }

        return [
            'balance'      => null,
            'currency'     => 'BDT',
            'status'       => 'error',
            'raw_response' => $body,
        ];
    }

    protected function checkBulkSMSBD(SendingServer $server): array
    {
        $baseUrl = rtrim($server->api_link ?: 'https://bulksmsbd.net/api', '/');
        
        // Get API Key from main field or custom server field
        $apiKey = $server->api_token ?: $server->api_key;
        if (empty($apiKey) && $server->customSendingServer) {
            $apiKey = $server->customSendingServer->username_value;
        }

        // Try the standard balance endpoint
        $url      = str_replace('/smsapi', '/getBalanceApi', $baseUrl);

        $response = $this->curlRequest($url . '?' . http_build_query([
                'api_key' => $apiKey,
            ]));

        if ($response['error']) {
            throw new Exception('cURL error: ' . $response['error']);
        }

        $decoded = json_decode($response['body'], true);

        if (is_array($decoded) && isset($decoded['balance'])) {
            return [
                'balance'      => (float) $decoded['balance'],
                'currency'     => 'BDT',
                'status'       => 'success',
                'raw_response' => $response['body'],
            ];
        }

        return [
            'balance'      => null,
            'currency'     => 'BDT',
            'status'       => 'error',
            'raw_response' => $response['body'],
        ];
    }

    protected function checkElitBuzzBD(SendingServer $server): array
    {
        $baseUrl  = rtrim($server->api_link ?: 'https://elitbuzzbd.com/sms/api', '/');
        $url      = $baseUrl . '?' . http_build_query([
                'api_key' => $server->api_key,
                'action'  => 'check-balance',
            ]);

        $response = $this->curlRequest($url);

        if ($response['error']) {
            throw new Exception('cURL error: ' . $response['error']);
        }

        $decoded = json_decode($response['body'], true);

        if (is_array($decoded) && isset($decoded['balance'])) {
            return [
                'balance'      => (float) $decoded['balance'],
                'currency'     => 'BDT',
                'status'       => 'success',
                'raw_response' => $response['body'],
            ];
        }

        return [
            'balance'      => null,
            'currency'     => 'BDT',
            'status'       => 'error',
            'raw_response' => $response['body'],
        ];
    }

    protected function checkTwilio(SendingServer $server): array
    {
        $url = "https://api.twilio.com/2010-04-01/Accounts/{$server->account_sid}.json";

        $response = $this->curlRequest($url, [
            CURLOPT_USERPWD => $server->account_sid . ':' . $server->auth_token,
        ]);

        if ($response['error']) {
            throw new Exception('cURL error: ' . $response['error']);
        }

        $decoded = json_decode($response['body'], true);

        if (isset($decoded['balance'])) {
            return [
                'balance'      => (float) $decoded['balance'],
                'currency'     => $decoded['currency'] ?? 'USD',
                'status'       => 'success',
                'raw_response' => $response['body'],
            ];
        }

        // Twilio Balance API (newer)
        $balUrl = "https://api.twilio.com/2010-04-01/Accounts/{$server->account_sid}/Balance.json";
        $response2 = $this->curlRequest($balUrl, [
            CURLOPT_USERPWD => $server->account_sid . ':' . $server->auth_token,
        ]);

        $decoded2 = json_decode($response2['body'], true);
        if (isset($decoded2['balance'])) {
            return [
                'balance'      => (float) $decoded2['balance'],
                'currency'     => $decoded2['currency'] ?? 'USD',
                'status'       => 'success',
                'raw_response' => $response2['body'],
            ];
        }

        return [
            'balance'      => null,
            'currency'     => 'USD',
            'status'       => 'error',
            'raw_response' => $response['body'],
        ];
    }

    protected function checkVonage(SendingServer $server): array
    {
        $url = 'https://rest.nexmo.com/account/get-balance?' . http_build_query([
                'api_key'    => $server->api_key,
                'api_secret' => $server->api_secret,
            ]);

        $response = $this->curlRequest($url);

        if ($response['error']) {
            throw new Exception('cURL error: ' . $response['error']);
        }

        $decoded = json_decode($response['body'], true);

        if (isset($decoded['value'])) {
            return [
                'balance'      => (float) $decoded['value'],
                'currency'     => 'EUR',
                'status'       => 'success',
                'raw_response' => $response['body'],
            ];
        }

        return [
            'balance'      => null,
            'currency'     => 'EUR',
            'status'       => 'error',
            'raw_response' => $response['body'],
        ];
    }

    protected function checkPlivo(SendingServer $server): array
    {
        $url = "https://api.plivo.com/v1/Account/{$server->auth_id}/";

        $response = $this->curlRequest($url, [
            CURLOPT_USERPWD  => $server->auth_id . ':' . $server->auth_token,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        ]);

        if ($response['error']) {
            throw new Exception('cURL error: ' . $response['error']);
        }

        $decoded = json_decode($response['body'], true);

        if (isset($decoded['cash_credits'])) {
            return [
                'balance'      => (float) $decoded['cash_credits'],
                'currency'     => 'USD',
                'status'       => 'success',
                'raw_response' => $response['body'],
            ];
        }

        return [
            'balance'      => null,
            'currency'     => 'USD',
            'status'       => 'error',
            'raw_response' => $response['body'],
        ];
    }

    protected function checkInfobip(SendingServer $server): array
    {
        $baseUrl = rtrim($server->api_link ?: 'https://api.infobip.com', '/');
        $url     = $baseUrl . '/account/1/balance';

        $response = $this->curlRequest($url, [
            CURLOPT_HTTPHEADER => [
                'Authorization: App ' . $server->api_key,
                'Accept: application/json',
            ],
        ]);

        if ($response['error']) {
            throw new Exception('cURL error: ' . $response['error']);
        }

        $decoded = json_decode($response['body'], true);

        if (isset($decoded['balance'])) {
            return [
                'balance'      => (float) $decoded['balance'],
                'currency'     => $decoded['currency'] ?? 'EUR',
                'status'       => 'success',
                'raw_response' => $response['body'],
            ];
        }

        return [
            'balance'      => null,
            'currency'     => 'EUR',
            'status'       => 'error',
            'raw_response' => $response['body'],
        ];
    }

    protected function checkClickSend(SendingServer $server): array
    {
        $url = 'https://rest.clicksend.com/v3/account';

        $response = $this->curlRequest($url, [
            CURLOPT_USERPWD  => $server->username . ':' . $server->api_key,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        ]);

        if ($response['error']) {
            throw new Exception('cURL error: ' . $response['error']);
        }

        $decoded = json_decode($response['body'], true);

        if (isset($decoded['data']['balance'])) {
            return [
                'balance'      => (float) $decoded['data']['balance'],
                'currency'     => $decoded['data']['currency']['currency_name_short'] ?? 'USD',
                'status'       => 'success',
                'raw_response' => $response['body'],
            ];
        }

        return [
            'balance'      => null,
            'currency'     => 'USD',
            'status'       => 'error',
            'raw_response' => $response['body'],
        ];
    }

    protected function checkSMSGlobal(SendingServer $server): array
    {
        $url = 'https://api.smsglobal.com/http-api.php?' . http_build_query([
                'action'   => 'balancesms',
                'user'     => $server->username,
                'password' => $server->password,
            ]);

        $response = $this->curlRequest($url);

        if ($response['error']) {
            throw new Exception('cURL error: ' . $response['error']);
        }

        $body = trim($response['body']);

        if (str_starts_with($body, 'BALANCE:')) {
            $balance = str_replace('BALANCE: ', '', $body);
            return [
                'balance'      => (float) $balance,
                'currency'     => 'AUD',
                'status'       => 'success',
                'raw_response' => $body,
            ];
        }

        return [
            'balance'      => null,
            'currency'     => 'AUD',
            'status'       => 'error',
            'raw_response' => $body,
        ];
    }

    protected function checkBudgetSMS(SendingServer $server): array
    {
        $url = 'https://api.budgetsms.net/balance/?' . http_build_query([
                'username' => $server->username,
                'userid'   => $server->c1,
                'handle'   => $server->password,
            ]);

        $response = $this->curlRequest($url);

        if ($response['error']) {
            throw new Exception('cURL error: ' . $response['error']);
        }

        $decoded = json_decode($response['body'], true);

        if (isset($decoded['balance'])) {
            return [
                'balance'      => (float) $decoded['balance'],
                'currency'     => 'EUR',
                'status'       => 'success',
                'raw_response' => $response['body'],
            ];
        }

        return [
            'balance'      => null,
            'currency'     => 'EUR',
            'status'       => 'error',
            'raw_response' => $response['body'],
        ];
    }

    protected function checkBulkSMS(SendingServer $server): array
    {
        $url = 'https://api.bulksms.com/v1/profile';

        $response = $this->curlRequest($url, [
            CURLOPT_USERPWD  => $server->username . ':' . $server->password,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        ]);

        if ($response['error']) {
            throw new Exception('cURL error: ' . $response['error']);
        }

        $decoded = json_decode($response['body'], true);

        if (isset($decoded['credits']['balance'])) {
            return [
                'balance'      => (float) $decoded['credits']['balance'],
                'currency'     => 'Credits',
                'status'       => 'success',
                'raw_response' => $response['body'],
            ];
        }

        return [
            'balance'      => null,
            'currency'     => 'Credits',
            'status'       => 'error',
            'raw_response' => $response['body'],
        ];
    }

    protected function checkAfricasTalking(SendingServer $server): array
    {
        $url = 'https://api.africastalking.com/version1/user?' . http_build_query([
                'username' => $server->username,
            ]);

        $response = $this->curlRequest($url, [
            CURLOPT_HTTPHEADER => [
                'apiKey: ' . $server->api_key,
                'Accept: application/json',
            ],
        ]);

        if ($response['error']) {
            throw new Exception('cURL error: ' . $response['error']);
        }

        $decoded = json_decode($response['body'], true);

        if (isset($decoded['UserData']['balance'])) {
            $balanceStr = $decoded['UserData']['balance']; // e.g. "KES 150.00"
            preg_match('/[\d.]+/', $balanceStr, $matches);
            $balance = $matches[0] ?? null;

            return [
                'balance'      => $balance ? (float) $balance : null,
                'currency'     => preg_replace('/[\d.\s]+/', '', $balanceStr) ?: 'KES',
                'status'       => $balance ? 'success' : 'error',
                'raw_response' => $response['body'],
            ];
        }

        return [
            'balance'      => null,
            'currency'     => 'KES',
            'status'       => 'error',
            'raw_response' => $response['body'],
        ];
    }

    protected function checkTelnyx(SendingServer $server): array
    {
        $url = 'https://api.telnyx.com/v2/balance';

        $response = $this->curlRequest($url, [
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $server->api_key,
                'Content-Type: application/json',
            ],
        ]);

        if ($response['error']) {
            throw new Exception('cURL error: ' . $response['error']);
        }

        $decoded = json_decode($response['body'], true);

        if (isset($decoded['data']['balance'])) {
            return [
                'balance'      => (float) $decoded['data']['balance'],
                'currency'     => $decoded['data']['currency'] ?? 'USD',
                'status'       => 'success',
                'raw_response' => $response['body'],
            ];
        }

        return [
            'balance'      => null,
            'currency'     => 'USD',
            'status'       => 'error',
            'raw_response' => $response['body'],
        ];
    }

    protected function checkSmsTo(SendingServer $server): array
    {
        $url = 'https://api.sms.to/balance';

        $response = $this->curlRequest($url, [
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $server->api_key,
                'Content-Type: application/json',
            ],
        ]);

        if ($response['error']) {
            throw new Exception('cURL error: ' . $response['error']);
        }

        $decoded = json_decode($response['body'], true);

        if (isset($decoded['balance'])) {
            return [
                'balance'      => (float) $decoded['balance'],
                'currency'     => $decoded['currency'] ?? 'EUR',
                'status'       => 'success',
                'raw_response' => $response['body'],
            ];
        }

        return [
            'balance'      => null,
            'currency'     => 'EUR',
            'status'       => 'error',
            'raw_response' => $response['body'],
        ];
    }

    protected function checkTermii(SendingServer $server): array
    {
        $url = 'https://api.ng.termii.com/api/get-balance?api_key=' . $server->api_key;

        $response = $this->curlRequest($url);

        if ($response['error']) {
            throw new Exception('cURL error: ' . $response['error']);
        }

        $decoded = json_decode($response['body'], true);

        if (isset($decoded['balance'])) {
            return [
                'balance'      => (float) $decoded['balance'],
                'currency'     => $decoded['currency'] ?? 'NGN',
                'status'       => 'success',
                'raw_response' => $response['body'],
            ];
        }

        return [
            'balance'      => null,
            'currency'     => 'NGN',
            'status'       => 'error',
            'raw_response' => $response['body'],
        ];
    }

    protected function checkD7Networks(SendingServer $server): array
    {
        $url = 'https://api.d7networks.com/messages/v1/balance';

        $response = $this->curlRequest($url, [
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $server->api_token,
                'Accept: application/json',
            ],
        ]);

        if ($response['error']) {
            throw new Exception('cURL error: ' . $response['error']);
        }

        $decoded = json_decode($response['body'], true);

        if (isset($decoded['balance'])) {
            return [
                'balance'      => (float) $decoded['balance'],
                'currency'     => $decoded['currency'] ?? 'USD',
                'status'       => 'success',
                'raw_response' => $response['body'],
            ];
        }

        return [
            'balance'      => null,
            'currency'     => 'USD',
            'status'       => 'error',
            'raw_response' => $response['body'],
        ];
    }

    protected function checkEasySendSMS(SendingServer $server): array
    {
        $url = 'https://api.easysendsms.com/bulksms?' . http_build_query([
            'username' => $server->username,
            'password' => $server->password,
            'action'   => 'balance',
        ]);

        $response = $this->curlRequest($url);

        if ($response['error']) {
            throw new Exception('cURL error: ' . $response['error']);
        }

        $body = trim($response['body']);

        if (is_numeric($body)) {
            return [
                'balance'      => (float) $body,
                'currency'     => 'Credits',
                'status'       => 'success',
                'raw_response' => $body,
            ];
        }

        $decoded = json_decode($body, true);
        if (is_array($decoded) && isset($decoded['balance'])) {
            return [
                'balance'      => (float) $decoded['balance'],
                'currency'     => 'Credits',
                'status'       => 'success',
                'raw_response' => $body,
            ];
        }

        return [
            'balance'      => null,
            'currency'     => 'Credits',
            'status'       => 'error',
            'raw_response' => $body,
        ];
    }
}
