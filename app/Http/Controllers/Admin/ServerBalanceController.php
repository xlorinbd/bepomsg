<?php

namespace App\Http\Controllers\Admin;

use App\Models\SendingServer;
use App\Services\ServerBalanceChecker;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServerBalanceController extends AdminBaseController
{
    protected ServerBalanceChecker $balanceChecker;

    public function __construct(ServerBalanceChecker $balanceChecker)
    {
        $this->balanceChecker = $balanceChecker;
    }

    /**
     * Server Balance dedicated page
     *
     * @throws AuthorizationException
     */
    public function index(): Factory|View|Application
    {
        $this->authorize('view sending_servers');

        $breadcrumbs = [
            ['link' => url(config('app.admin_path') . '/dashboard'), 'name' => __('locale.menu.Dashboard')],
            ['link' => url(config('app.admin_path') . '/sending-servers'), 'name' => __('locale.menu.Sending Servers')],
            ['name' => 'Server Balance'],
        ];

        return view('admin.ServerBalance.index', compact('breadcrumbs'));
    }

    /**
     * Check balance of all active servers (AJAX)
     *
     * @throws AuthorizationException
     */
    public function checkAll(): JsonResponse
    {
        $this->authorize('view sending_servers');

        $servers = SendingServer::where('status', true)->get();

        $results = [];
        foreach ($servers as $server) {
            $result = $this->balanceChecker->checkBalance($server);

            $results[] = [
                'uid'          => $server->uid,
                'name'         => $server->name,
                'type'         => $server->settings ?? $server->type,
                'balance'      => $result['balance'],
                'currency'     => $result['currency'],
                'status'       => $result['status'],
                'raw_response' => $result['raw_response'],
                'checked_at'   => now()->toDateTimeString(),
            ];
        }

        return response()->json([
            'status'  => 'success',
            'data'    => $results,
            'total'   => count($results),
            'checked' => collect($results)->where('status', 'success')->count(),
            'skipped' => collect($results)->where('status', 'skipped')->count(),
            'errors'  => collect($results)->whereIn('status', ['error', 'unsupported'])->count(),
        ]);
    }

    /**
     * Check balance of a single server (AJAX)
     *
     * @throws AuthorizationException
     */
    public function checkSingle(SendingServer $server): JsonResponse
    {
        $this->authorize('view sending_servers');

        $result = $this->balanceChecker->checkBalance($server);

        return response()->json([
            'status'  => 'success',
            'data'    => [
                'uid'          => $server->uid,
                'name'         => $server->name,
                'type'         => $server->settings ?? $server->type,
                'balance'      => $result['balance'],
                'currency'     => $result['currency'],
                'check_status' => $result['status'],
                'raw_response' => $result['raw_response'],
                'checked_at'   => now()->toDateTimeString(),
            ],
        ]);
    }

    /**
     * DataTable search (server-side)
     *
     * @throws AuthorizationException
     */
    public function search(Request $request): void
    {
        $this->authorize('view sending_servers');

        $columns = [
            0 => 'responsive_id',
            1 => 'uid',
            2 => 'uid',
            3 => 'name',
            4 => 'settings',
            5 => 'status',
            6 => 'action',
        ];

        $totalData = SendingServer::where('status', true)->count();
        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')] ?? 'name';
        $dir   = $request->input('order.0.dir') ?? 'asc';

        if (empty($request->input('search.value'))) {
            $servers = SendingServer::where('status', true)
                ->limit($limit)
                ->offset($start)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');

            $servers = SendingServer::where('status', true)
                ->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                      ->orWhere('settings', 'like', "%$search%")
                      ->orWhere('type', 'like', "%$search%");
                })
                ->limit($limit)
                ->offset($start)
                ->orderBy($order, $dir)
                ->get();

            $totalFiltered = SendingServer::where('status', true)
                ->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                      ->orWhere('settings', 'like', "%$search%")
                      ->orWhere('type', 'like', "%$search%");
                })
                ->count();
        }

        $data = [];
        foreach ($servers as $server) {
            $capabilities = $server->getCapabilities();
            $hasCredentials = !empty($server->api_key) || !empty($server->api_token) ||
                              !empty($server->api_secret) || !empty($server->username) ||
                              !empty($server->password) || !empty($server->account_sid) ||
                              !empty($server->auth_token) || !empty($server->auth_id) ||
                              !empty($server->access_key) || !empty($server->access_token) ||
                              !empty($server->c1) || !empty($server->c2);

            $data[] = [
                'responsive_id' => '',
                'uid'           => $server->uid,
                'name'          => $server->name,
                'type'          => '<span class="badge bg-primary text-uppercase">' . ($server->settings ?? $server->type) . '</span>',
                'capabilities'  => $capabilities,
                'status'        => $hasCredentials
                    ? '<span class="badge bg-success">Ready</span>'
                    : '<span class="badge bg-warning">No Credentials</span>',
                'action'        => $server->uid,
            ];
        }

        echo json_encode([
            'draw'            => intval($request->input('draw')),
            'recordsTotal'    => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data'            => $data,
        ]);
        exit();
    }
}
