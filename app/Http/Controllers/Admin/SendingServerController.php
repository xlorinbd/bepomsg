<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SendingServer\StoreCustomServer;
use App\Http\Requests\SendingServer\StoreSendingServerRequest;
use App\Models\CustomSendingServer;
use App\Models\SendingServer;
use App\Repositories\Contracts\SendingServerRepository;
use Generator;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application as ApplicationAlias;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use JetBrains\PhpStorm\NoReturn;
use OpenSpout\Common\Exception\InvalidArgumentException;
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Common\Exception\UnsupportedTypeException;
use OpenSpout\Writer\Exception\WriterNotOpenedException;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SendingServerController extends AdminBaseController
{
    protected SendingServerRepository $sendingServers;

    /**
     * SendingServerController constructor.
     *
     * @param  SendingServerRepository  $sendingServers
     */

    public function __construct(SendingServerRepository $sendingServers)
    {
        $this->sendingServers = $sendingServers;
    }


    /**
     * @return ApplicationAlias|Factory|View
     * @throws AuthorizationException
     */

    public function index(): Factory|View|ApplicationAlias
    {

        $this->authorize('view sending_servers');

        $breadcrumbs = [
                [
                        'link' => url(config('app.admin_path')."/dashboard"),
                        'name' => __('locale.menu.Dashboard'),
                ],
                [
                        'link' => url(config('app.admin_path')."/dashboard"),
                        'name' => __('locale.menu.Sending'),
                ],
                ['name' => __('locale.menu.Sending Servers')],
        ];


        return view('admin.SendingServer.index', compact('breadcrumbs'));
    }


    /**
     * @param  Request  $request
     *
     * @return void
     * @throws AuthorizationException
     */
    #[NoReturn] public function search(Request $request): void
    {

        $this->authorize('view sending_servers');

        $columns = [
                0 => 'responsive_id',
                1 => 'uid',
                2 => 'uid',
                3 => 'name',
                4 => 'type',
                5 => 'quota_value',
                6 => 'status',
                7 => 'action',
        ];

        $totalData = SendingServer::count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $sending_servers = SendingServer::limit($limit)
                                            ->offset($start)
                                            ->orderBy($order, $dir)
                                            ->get();
        } else {
            $search = $request->input('search.value');

            $sending_servers = SendingServer::whereLike(['uid', 'name', 'type'], $search)
                                            ->limit($limit)
                                            ->orderBy($order, $dir)
                                            ->get();

            $totalFiltered = SendingServer::whereLike(['uid', 'name', 'type'], $search)
                                          ->count();
        }

        $data = [];
        if ( ! empty($sending_servers)) {
            foreach ($sending_servers as $sending_server) {
                $show = route('admin.sending-servers.show', $sending_server->uid);

                if ($sending_server->status === true) {
                    $status = 'checked';
                } else {
                    $status = '';
                }


                if ($sending_server->settings == 'Whatsender') {
                    $nestedData['devices'] = route('admin.sending-servers.devices', $sending_server->uid);
                }
                $nestedData['settings'] = $sending_server->settings;

                $nestedData['responsive_id'] = '';
                $nestedData['uid']           = $sending_server->uid;
                $nestedData['name']          = $sending_server->name;
                $nestedData['type']          = $sending_server->getCapabilities();
                $nestedData['quota_value']   = "<div> <p class='text-capitalize'>"
                        .__('locale.sending_servers.sending_limit')
                        ." <span class='text-danger'>$sending_server->quota_value </span> "
                        .__('locale.sending_servers.per')
                        ." <span class='text-info'> $sending_server->quota_base $sending_server->quota_unit</span></p>  </div>";


                $nestedData['status'] = "<div class='form-check form-switch form-check-primary'>
                <input type='checkbox' class='form-check-input get_status' id='status_$sending_server->uid' data-id='$sending_server->uid' name='status' $status>
                <label class='form-check-label' for='status_$sending_server->uid'>
                  <span class='switch-icon-left'><i data-feather='check'></i> </span>
                  <span class='switch-icon-right'><i data-feather='x'></i> </span>
                </label>
              </div>";

                $nestedData['edit'] = $show;
                $data[]             = $nestedData;

            }
        }

        $json_data = [
                "draw"            => intval($request->input('draw')),
                "recordsTotal"    => $totalData,
                "recordsFiltered" => $totalFiltered,
                "data"            => $data,
        ];

        echo json_encode($json_data);
        exit();

    }


    /**
     * Get all sending servers
     *
     * @return ApplicationAlias|Factory|View
     *
     * @throws AuthorizationException
     */

    public function select(): Factory|View|ApplicationAlias
    {

        $user = auth()->user();
        if ( ! $user || ( ! $user->can('create sending_servers') && ! $user->can('edit sending_servers'))) {
            abort(403);
        }

        $breadcrumbs = [
                [
                        'link' => url(config('app.admin_path')."/dashboard"),
                        'name' => __('locale.menu.Dashboard'),
                ],
                [
                        'link' => url(config('app.admin_path')."/sending-servers"),
                        'name' => __('locale.menu.Sending Servers'),
                ],
                [
                        'name' => __('locale.sending_servers.select_sending_server'),
                ],
        ];

        $sending_servers = $this->sendingServers->allSendingServer();

        return view('admin.SendingServer.list', compact('breadcrumbs', 'sending_servers'));
    }

    /**
     * Create New Server
     *
     * @param $type
     *
     * @return ApplicationAlias|Factory|View
     *
     * @throws AuthorizationException
     */

    protected function create($type): Factory|View|ApplicationAlias
    {

        $user = auth()->user();
        if ( ! $user || ( ! $user->can('create sending_servers') && ! $user->can('edit sending_servers'))) {
            abort(403);
        }

        $breadcrumbs = [
                [
                        'link' => url(config('app.admin_path')."/dashboard"),
                        'name' => __('locale.menu.Dashboard'),
                ],
                [
                        'link' => url(config('app.admin_path')."/sending-servers"),
                        'name' => __('locale.menu.Sending Servers'),
                ],
                [
                        'link' => url(config('app.admin_path')."/sending-servers/select"),
                        'name' => __('locale.sending_servers.select_sending_server'),
                ],
        ];

        if ($type == 'custom') {

            $breadcrumbs[] = [
                    'name' => __('locale.sending_servers.create_own_server'),
            ];

            return view('admin.SendingServer.create_custom', compact('breadcrumbs'));
        }

        $server = $this->sendingServers->allSendingServer()[$type];

        $breadcrumbs[] = ['name' => $server['name']];

        return view('admin.SendingServer.create', compact('server', 'breadcrumbs'));

    }


    /**
     * Store Sending Server
     *
     * @param  StoreSendingServerRequest  $request
     *
     * @return RedirectResponse
     *
     * @throws AuthorizationException
     */
    public function store(StoreSendingServerRequest $request): RedirectResponse
    {

        if (config('app.stage') == 'demo') {
            return redirect()->route('admin.sending-servers.index')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }


        $this->authorize('create sending_servers');

        $this->sendingServers->store($request->input());

        return redirect()->route('admin.sending-servers.index')->with([
                'status'  => 'success',
                'message' => __('locale.sending_servers.sending_server_successfully_added'),
        ]);
    }

    /**
     * Show existing sending server
     *
     * @param  SendingServer  $server
     *
     * @return ApplicationAlias|Factory|RedirectResponse|View
     * @throws AuthorizationException
     */

    public function show(SendingServer $server): Factory|View|RedirectResponse|ApplicationAlias
    {
        $this->authorize('edit sending_servers');

        $server = $server->toArray();
        if ( ! is_array($server)) {
            return redirect()->route('admin.sending-servers.index')->with([
                    'status'  => 'error',
                    'message' => __('locale.sending_servers.sending_server_not_found'),
            ]);
        }

        $breadcrumbs = [
                [
                        'link' => url(config('app.admin_path')."/dashboard"),
                        'name' => __('locale.menu.Dashboard'),
                ],
                [
                        'link' => url(config('app.admin_path')."/sending-servers"),
                        'name' => __('locale.menu.Sending Servers'),
                ],
        ];

        if ($server['custom']) {

            $custom_info = CustomSendingServer::where('server_id', $server['id'])->first();

            if ($custom_info) {
                $breadcrumbs[] = [
                        'name' => __('locale.sending_servers.create_own_server'),
                ];

                $data = $custom_info->toArray();

                return view('admin.SendingServer.edit_custom', compact('server', 'data', 'breadcrumbs'));
            }

            return redirect()->route('admin.sending-servers.index')->with([
                    'status'  => 'error',
                    'message' => __('locale.sending_servers.sending_server_not_found'),
            ]);
        }

        $breadcrumbs[] = ['name' => $server['name']];

        return view('admin.SendingServer.create', compact('server', 'breadcrumbs'));
    }


    /**
     * Update existing sending server
     *
     * @param  SendingServer  $sendingServer
     * @param  StoreSendingServerRequest  $request
     *
     * @return RedirectResponse
     * @throws AuthorizationException
     */

    public function update(SendingServer $sendingServer, StoreSendingServerRequest $request): RedirectResponse
    {

        if (config('app.stage') == 'demo') {
            return redirect()->route('admin.sending-servers.index')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }


        $this->authorize('edit sending_servers');

        $this->sendingServers->update($sendingServer, $request->input());

        return redirect()->route('admin.sending-servers.index')->with([
                'status'  => 'success',
                'message' => __('locale.sending_servers.sending_server_successfully_updated'),
        ]);
    }

    /**
     * Add Customer Server
     *
     * @param  StoreCustomServer  $request
     *
     * @return RedirectResponse
     * @throws AuthorizationException
     */

    public function addCustomServer(StoreCustomServer $request): RedirectResponse
    {
        if (config('app.stage') == 'demo') {
            return redirect()->route('admin.sending-servers.index')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }


        $this->authorize('create sending_servers');

        $this->sendingServers->storeCustom($request->input());

        return redirect()->route('admin.sending-servers.index')->with([
                'status'  => 'success',
                'message' => __('locale.sending_servers.sending_server_successfully_added'),
        ]);
    }

    /**
     * Update existing sending server
     *
     * @param  SendingServer  $sendingServer
     * @param  StoreCustomServer  $request
     *
     * @return RedirectResponse
     * @throws AuthorizationException
     */

    public function updateCustomServer(SendingServer $sendingServer, StoreCustomServer $request): RedirectResponse
    {
        if (config('app.stage') == 'demo') {
            return redirect()->route('admin.sending-servers.index')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->authorize('edit sending_servers');

        $this->sendingServers->updateCustom($sendingServer, $request->input());

        return redirect()->route('admin.sending-servers.index')->with([
                'status'  => 'success',
                'message' => __('locale.sending_servers.sending_server_successfully_updated'),
        ]);
    }

    public function testCustomServerConnection(Request $request): JsonResponse
    {
        if (config('app.stage') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ], 403);
        }

        $this->authorize('create sending_servers');

        $validated = $request->validate([
                'api_link'                     => 'required|string',
                'success_keyword'              => 'nullable|string',
                'http_request_method'          => 'required|in:get,post',
                'json_encoded_post'            => 'required|in:0,1',
                'content_type'                 => 'required|string',
                'content_type_accept'          => 'required|string',
                'character_encoding'           => 'required|string',
                'ssl_certificate_verification' => 'required|in:0,1',
                'authorization'                => 'required|in:no_auth,bearer_token,basic_auth',
                'username_param'               => 'required|string',
                'username_value'               => 'required|string',
                'password_param'               => 'nullable|string',
                'password_value'               => 'nullable|string',
                'password_status'              => 'nullable|in:0,1',
                'action_param'                 => 'nullable|string',
                'action_value'                 => 'nullable|string',
                'action_status'                => 'nullable|in:0,1',
                'source_param'                 => 'nullable|string',
                'source_value'                 => 'nullable|string',
                'source_status'                => 'nullable|in:0,1',
                'destination_param'            => 'required|string',
                'message_param'                => 'required|string',
                'unicode_param'                => 'nullable|string',
                'unicode_value'                => 'nullable|string',
                'unicode_status'               => 'nullable|in:0,1',
                'route_param'                  => 'nullable|string',
                'route_value'                  => 'nullable|string',
                'route_status'                 => 'nullable|in:0,1',
                'language_param'               => 'nullable|string',
                'language_value'               => 'nullable|string',
                'language_status'              => 'nullable|in:0,1',
                'custom_one_param'             => 'nullable|string',
                'custom_one_value'             => 'nullable|string',
                'custom_one_status'            => 'nullable|in:0,1',
                'custom_two_param'             => 'nullable|string',
                'custom_two_value'             => 'nullable|string',
                'custom_two_status'            => 'nullable|in:0,1',
                'custom_three_param'           => 'nullable|string',
                'custom_three_value'           => 'nullable|string',
                'custom_three_status'          => 'nullable|in:0,1',
                'test_destination'             => 'nullable|string',
                'test_message'                 => 'nullable|string',
                'test_sender_id'               => 'nullable|string',
        ]);

        $sendCustomData = [];
        $usernameValue  = $validated['username_value'];
        $passwordValue  = $validated['password_value'] ?? null;

        if ($validated['authorization'] == 'no_auth') {
            $sendCustomData[$validated['username_param']] = $usernameValue;
        }

        if (($validated['password_status'] ?? '0') === '1' && ! empty($validated['password_param'])) {
            if ($validated['authorization'] == 'no_auth') {
                $sendCustomData[$validated['password_param']] = $passwordValue;
            }
        }

        if (($validated['action_status'] ?? '0') === '1' && ! empty($validated['action_param'])) {
            $sendCustomData[$validated['action_param']] = $validated['action_value'] ?? '';
        }

        if (($validated['source_status'] ?? '0') === '1' && ! empty($validated['source_param'])) {
            $sendCustomData[$validated['source_param']] = $validated['test_sender_id'] ?? ($validated['source_value'] ?? '');
        }

        $sendCustomData[$validated['destination_param']] = $validated['test_destination'] ?? '8801000000000';
        $sendCustomData[$validated['message_param']]     = $validated['test_message'] ?? 'Beposms SMS gateway connection test';

        if (($validated['unicode_status'] ?? '0') === '1' && ! empty($validated['unicode_param'])) {
            $sendCustomData[$validated['unicode_param']] = $validated['unicode_value'] ?? '';
        }

        if (($validated['route_status'] ?? '0') === '1' && ! empty($validated['route_param'])) {
            $sendCustomData[$validated['route_param']] = $validated['route_value'] ?? '';
        }

        if (($validated['language_status'] ?? '0') === '1' && ! empty($validated['language_param'])) {
            $sendCustomData[$validated['language_param']] = $validated['language_value'] ?? '';
        }

        if (($validated['custom_one_status'] ?? '0') === '1' && ! empty($validated['custom_one_param'])) {
            $sendCustomData[$validated['custom_one_param']] = $validated['custom_one_value'] ?? '';
        }

        if (($validated['custom_two_status'] ?? '0') === '1' && ! empty($validated['custom_two_param'])) {
            $sendCustomData[$validated['custom_two_param']] = $validated['custom_two_value'] ?? '';
        }

        if (($validated['custom_three_status'] ?? '0') === '1' && ! empty($validated['custom_three_param'])) {
            $sendCustomData[$validated['custom_three_param']] = $validated['custom_three_value'] ?? '';
        }

        $isJson     = (int) $validated['json_encoded_post'] === 1;
        $parameters = $isJson ? json_encode($sendCustomData) : http_build_query($sendCustomData);

        $headers = [];
        if ($validated['content_type'] !== 'none') {
            $headers[] = 'Content-Type: ' . $validated['content_type'];
        }
        if ($validated['content_type_accept'] !== 'none') {
            $headers[] = 'Accept: ' . $validated['content_type_accept'];
        }
        if ($validated['character_encoding'] !== 'none') {
            $headers[] = 'charset=' . $validated['character_encoding'];
        }

        if ($validated['authorization'] == 'bearer_token') {
            $headers[] = 'Authorization: Bearer ' . $usernameValue;
        } else if ($validated['authorization'] == 'basic_auth') {
            $headers[] = 'Authorization: Basic ' . base64_encode($usernameValue . ':' . ($passwordValue ?? ''));
        }

        $ch = curl_init();

        if ($validated['http_request_method'] == 'get') {
            $url = $validated['api_link'] . '?' . $parameters;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPGET, 1);
        } else {
            $url = $validated['api_link'];
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        if ((int) $validated['ssl_certificate_verification'] === 1) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        if (count($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $responseBody = curl_exec($ch);
        $curlError    = curl_error($ch);
        $curlErrNo    = curl_errno($ch);
        $httpCode     = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $info         = curl_getinfo($ch);
        curl_close($ch);

        $debugPayload = [
                'url'         => $url,
                'method'      => strtoupper($validated['http_request_method']),
                'request'     => $sendCustomData,
                'headers'     => $headers,
                'http_code'   => $httpCode,
                'curl_errno'  => $curlErrNo,
                'curl_error'  => $curlError,
                'curl_info'   => $info,
                'raw_response'=> (string) $responseBody,
        ];

        $successKeyword = trim((string) ($validated['success_keyword'] ?? ''));
        if ($successKeyword !== '') {
            $debugPayload['success_keyword'] = $successKeyword;
            $debugPayload['keyword_matched'] = substr_count(strtolower((string) $responseBody), strtolower($successKeyword)) == 1;
        }

        Log::channel('daily')->info('sending_server_custom_test_connection', $debugPayload);

        if ($curlErrNo !== 0) {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Connection failed: ' . $curlError,
                    'debug'   => $debugPayload,
            ], 422);
        }

        if (($debugPayload['keyword_matched'] ?? null) === false) {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'HTTP request ok, but success keyword did not match response.',
                    'debug'   => $debugPayload,
            ], 422);
        }

        return response()->json([
                'status'  => 'success',
                'message' => 'Connection test completed',
                'debug'   => $debugPayload,
        ]);
    }

    /**
     * change sending server status
     *
     * @param  SendingServer  $server
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */

    public function activeToggle(SendingServer $server): JsonResponse
    {

        if (config('app.stage') == 'demo') {

            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }


        $this->authorize('edit sending_servers');

        $server->update(['status' => ! $server->status]);

        return response()->json([
                'status'  => 'success',
                'message' => __('locale.sending_servers.sending_server_successfully_change'),
        ]);

    }


    /**
     * Delete sending server
     *
     * @param  SendingServer  $sendingServer
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(SendingServer $sendingServer): JsonResponse
    {

        if (config('app.stage') == 'demo') {

            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }


        $this->authorize('delete sending_servers');

        $this->sendingServers->destroy($sendingServer);

        return response()->json([
                'status'  => 'success',
                'message' => __('locale.sending_servers.sending_server_successfully_deleted'),
        ]);

    }


    /**
     * Bulk Action with Enable, Disable and Delete
     *
     * @param  Request  $request
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */

    public function batchAction(Request $request): JsonResponse
    {

        if (config('app.stage') == 'demo') {

            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $action = $request->get('action');
        $ids    = $request->get('ids');

        switch ($action) {
            case 'destroy':
                $this->authorize('delete sending_servers');

                $this->sendingServers->batchDestroy($ids);

                return response()->json([
                        'status'  => 'success',
                        'message' => __('locale.sending_servers.sending_servers_deleted'),
                ]);

            case 'enable':
                $this->authorize('edit sending_servers');

                $this->sendingServers->batchActive($ids);

                return response()->json([
                        'status'  => 'success',
                        'message' => __('locale.sending_servers.sending_servers_enabled'),
                ]);

            case 'disable':

                $this->authorize('edit sending_servers');

                $this->sendingServers->batchDisable($ids);

                return response()->json([
                        'status'  => 'success',
                        'message' => __('locale.sending_servers.sending_servers_disabled'),
                ]);
        }

        return response()->json([
                'status'  => 'error',
                'message' => __('locale.exceptions.invalid_action'),
        ]);

    }


    /**
     *
     * @return Generator
     */

    public function sendingServerGenerator(): Generator
    {
        foreach (SendingServer::cursor() as $sendingServer) {
            yield $sendingServer;
        }
    }


    /**
     * @return RedirectResponse|BinaryFileResponse
     * @throws AuthorizationException
     */
    public function export(): BinaryFileResponse|RedirectResponse
    {
        if (config('app.stage') == 'demo') {
            return redirect()->route('admin.sending-servers.index')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->authorize('view sending_servers');

        try {
            $file_name = (new FastExcel($this->sendingServerGenerator()))->export(storage_path('SendingServers_'.time().'.xlsx'));

            return response()->download($file_name);

        } catch (IOException|InvalidArgumentException|UnsupportedTypeException|WriterNotOpenedException $e) {
            return redirect()->route('admin.sending-servers.index')->with([
                    'status'  => 'error',
                    'message' => $e->getMessage(),
            ]);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | For WhatSender Only
    |--------------------------------------------------------------------------
    |
    | This Controller only contains whatsender info
    |
    */


    /**
     * Show existing device details
     *
     * @param  SendingServer  $server
     *
     * @return ApplicationAlias|Factory|RedirectResponse|View
     * @throws AuthorizationException
     */

    public function devices(SendingServer $server)
    {
        $this->authorize('edit sending_servers');

        $server = $server->toArray();
        if ( ! is_array($server)) {
            return redirect()->route('admin.sending-servers.index')->with([
                    'status'  => 'error',
                    'message' => __('locale.sending_servers.sending_server_not_found'),
            ]);
        }

        $breadcrumbs = [
                [
                        'link' => url(config('app.admin_path')."/dashboard"),
                        'name' => __('locale.menu.Dashboard'),
                ],
                [
                        'link' => url(config('app.admin_path')."/sending-servers"),
                        'name' => __('locale.menu.Sending Servers'),
                ],
        ];

        $breadcrumbs[] = ['name' => $server['name']];


        $ch = curl_init();

        curl_setopt_array($ch, [
                CURLOPT_URL            => "https://api.whatsender.io/v1/devices/".$server['device_id'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING       => "",
                CURLOPT_MAXREDIRS      => 10,
                CURLOPT_TIMEOUT        => 30,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST  => "GET",
                CURLOPT_HTTPHEADER     => [
                        "Token: ".$server['api_token'],
                ],
        ]);

        $response = curl_exec($ch);
        $err      = curl_error($ch);

        curl_close($ch);

        if ($err) {
            return redirect()->route('admin.sending-servers.index')->with([
                    'status'  => 'error',
                    'message' => $err,
            ]);
        }

        $device = json_decode($response, true);

        if (is_array($device) && array_key_exists('status', $device)) {
            if ($device['status'] == 'operative') {
                return view('admin.SendingServer.device_details', compact('server', 'breadcrumbs', 'device'));
            }

            return redirect()->route('admin.sending-servers.index')->with([
                    'status'  => 'error',
                    'message' => $device['message'],
            ]);
        }

        return redirect()->route('admin.sending-servers.index')->with([
                'status'  => 'error',
                'message' => 'Device not found',
        ]);

    }


    /**
     * reboot session
     *
     * @param  SendingServer  $server
     *
     * @return JsonResponse
     */
    public function reboot(SendingServer $server): JsonResponse
    {

        $ch = curl_init();

        curl_setopt_array($ch, [
                CURLOPT_URL            => "https://api.whatsender.io/v1/devices/".$server['device_id']."/reboot",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING       => "",
                CURLOPT_MAXREDIRS      => 10,
                CURLOPT_TIMEOUT        => 30,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST  => "POST",
                CURLOPT_POSTFIELDS     => json_encode(['wait' => true]),
                CURLOPT_HTTPHEADER     => [
                        "Content-Type: application/json",
                        "Token: ".$server['api_token'],
                ],
        ]);

        $response = curl_exec($ch);
        $err      = curl_error($ch);

        curl_close($ch);

        if ($err) {
            return response()->json([
                    'status'  => 'success',
                    'message' => $err,
            ]);
        }

        $reboot = json_decode($response, true);

        if (is_array($reboot) && array_key_exists('status', $reboot)) {
            if ($reboot['status'] == 'operative') {
                return response()->json([
                        'status'  => 'success',
                        'message' => 'Session was successfully rebooted',
                ]);
            }

            return response()->json([
                    'status'  => 'error',
                    'message' => $reboot['message'],
            ]);
        }

        return response()->json([
                'status'  => 'error',
                'message' => 'Device info not found',
        ]);
    }

    /**
     * reset sessions
     *
     * @param  SendingServer  $server
     *
     * @return JsonResponse
     */
    public function reset(SendingServer $server): JsonResponse
    {
        $ch = curl_init();

        curl_setopt_array($ch, [
                CURLOPT_URL            => "https://api.whatsender.io/v1/devices/".$server['device_id']."/reset",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING       => "",
                CURLOPT_MAXREDIRS      => 10,
                CURLOPT_TIMEOUT        => 30,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST  => "POST",
                CURLOPT_POSTFIELDS     => json_encode([
                        'wait'       => 'false',
                        'emptyQueue' => 'true',
                ]),
                CURLOPT_HTTPHEADER     => [
                        "Content-Type: application/json",
                        "Token: ".$server['api_token'],
                ],
        ]);

        $response = curl_exec($ch);
        $err      = curl_error($ch);

        curl_close($ch);

        if ($err) {
            return response()->json([
                    'status'  => 'success',
                    'message' => $err,
            ]);
        }

        $reboot = json_decode($response, true);

        if (is_array($reboot) && array_key_exists('status', $reboot)) {
            if ($reboot['status'] == 'operative') {
                return response()->json([
                        'status'  => 'success',
                        'message' => 'Session was successfully recreated',
                ]);
            }

            return response()->json([
                    'status'  => 'error',
                    'message' => $reboot['message'],
            ]);
        }

        return response()->json([
                'status'  => 'error',
                'message' => 'Device info not found',
        ]);
    }

    /**
     * scan qr code
     *
     * @param  SendingServer  $server
     *
     * @return JsonResponse
     */
    public function scan(SendingServer $server): JsonResponse
    {

        $ch = curl_init();

        curl_setopt_array($ch, [
                CURLOPT_URL            => "https://api.whatsender.io/v1/devices/".$server['device_id']."/scan",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING       => "",
                CURLOPT_MAXREDIRS      => 10,
                CURLOPT_TIMEOUT        => 30,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST  => "GET",
                CURLOPT_HTTPHEADER     => [
                        "Token: ".$server['api_token'],
                ],
        ]);

        $response = curl_exec($ch);
        $err      = curl_error($ch);

        curl_close($ch);

        if ($err) {
            return response()->json([
                    'status'  => 'success',
                    'message' => $err,
            ]);
        }

        $data = json_decode($response);

        if (json_last_error() != JSON_ERROR_NONE) {
            return response()->json([
                    'status' => 'success',
                    'image'  => $response,
            ]);
        }

        return response()->json([
                'status'  => 'error',
                'message' => $data->message,
        ]);

    }


    /**
     * start new session
     *
     * @param  SendingServer  $server
     *
     * @return JsonResponse
     */
    public function start(SendingServer $server): JsonResponse
    {

        $ch = curl_init();

        curl_setopt_array($ch, [
                CURLOPT_URL            => "https://api.whatsender.io/v1/devices/".$server['device_id']."/start",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING       => "",
                CURLOPT_MAXREDIRS      => 10,
                CURLOPT_TIMEOUT        => 30,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST  => "POST",
                CURLOPT_POSTFIELDS     => json_encode([
                        'wait' => 'true',
                ]),
                CURLOPT_HTTPHEADER     => [
                        "Content-Type: application/json",
                        "Token: ".$server['api_token'],
                ],
        ]);

        $response = curl_exec($ch);
        $err      = curl_error($ch);

        curl_close($ch);

        if ($err) {
            return response()->json([
                    'status'  => 'error',
                    'message' => $err,
            ]);
        }

        $start = json_decode($response, true);

        if (is_array($start) && array_key_exists('status', $start)) {
            if ($start['status'] == 'operative') {
                return response()->json([
                        'status'  => 'success',
                        'message' => 'Session was successfully started',
                ]);
            }

            return response()->json([
                    'status'  => 'error',
                    'message' => $start['message'],
            ]);
        }

        return response()->json([
                'status'  => 'error',
                'message' => 'Device info not found',
        ]);
    }

    /**
     * sync the sessions
     *
     * @param  SendingServer  $server
     *
     * @return JsonResponse
     */
    public function sync(SendingServer $server): JsonResponse
    {

        $ch = curl_init();

        curl_setopt_array($ch, [
                CURLOPT_URL            => "https://api.whatsender.io/v1/devices/".$server['device_id']."/sync",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING       => "",
                CURLOPT_MAXREDIRS      => 10,
                CURLOPT_TIMEOUT        => 30,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST  => "GET",
                CURLOPT_HTTPHEADER     => [
                        "Token: ".$server['api_token'],
                ],
        ]);

        $response = curl_exec($ch);
        $err      = curl_error($ch);

        curl_close($ch);

        if ($err) {
            return response()->json([
                    'status'  => 'success',
                    'message' => $err,
            ]);
        }

        $sync = json_decode($response, true);

        if (is_array($sync) && array_key_exists('status', $sync)) {
            return response()->json([
                    'status'  => 'success',
                    'message' => 'Device synchronized. Current session status: <b>'.ucfirst($sync['status'])."</b>",
            ]);
        }

        return response()->json([
                'status'  => 'error',
                'message' => 'Device info not found',
        ]);
    }


}
