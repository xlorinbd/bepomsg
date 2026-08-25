<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Requests\LicenseRequest;
use App\Http\Requests\Settings\AuthenticationRequest;
use App\Http\Requests\Settings\DefaultCustomerPermission;
use App\Http\Requests\Settings\DLTRequest;
use App\Http\Requests\Settings\NotificationsRequest;
use App\Http\Requests\Settings\PostGeneralRequest;
use App\Http\Requests\Settings\PusherRequest;
use App\Http\Requests\Settings\SystemEmailRequest;
use App\Library\Tool;
use App\Library\Unzipper;
use App\Models\AppConfig;
use App\Models\Customer;
use App\Models\Language;
use App\Models\Plan;
use App\Models\SendingServer;
use App\Models\User;
use App\Repositories\Contracts\SettingsRepository;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;
use Symfony\Component\Console\Output\BufferedOutput;

class SettingsController extends AdminBaseController
{
    protected SettingsRepository $settings;

    /**
     * SettingsController constructor.
     *
     * @param SettingsRepository $settings
     */
    public function __construct(SettingsRepository $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Update all system settings.
     *
     * @return Application|Factory|\Illuminate\Contracts\View\View|string
     * @throws AuthorizationException
     */
    public function general(): \Illuminate\Contracts\View\View|Factory|string|Application
    {

        $this->authorize('general settings');

        $breadcrumbs = [
            ['link' => url(config('app.admin_path') . "/dashboard"), 'name' => __('locale.menu.Dashboard')],
            ['link' => url(config('app.admin_path') . "/dashboard"), 'name' => __('locale.menu.Settings')],
            ['name' => __('locale.menu.All Settings')],
        ];

        $language = Language::where('status', true)->get();
        $sending_servers = SendingServer::where('status', true)->get();


        // Suggestion paths
        $paths = [
            '/usr/bin/php',
            '/usr/local/bin/php',
            '/bin/php',
            '/usr/bin/php81',
            '/usr/bin/php8.1',
            '/opt/plesk/php/8.1/bin/php',
            '/opt/alt/php/8.1/bin/php',
            '/opt/alt/php81/usr/bin/php',
            '/usr/bin/php82',
            '/usr/bin/php8.2',
            '/opt/plesk/php/8.2/bin/php',
            '/opt/alt/php/8.2/bin/php',
            '/opt/alt/php82/usr/bin/php',
            '/usr/bin/php83',
            '/usr/bin/php8.3',
            '/opt/plesk/php/8.3/bin/php',
            '/opt/alt/php/8.3/bin/php',
            '/opt/alt/php83/usr/bin/php',
            '/usr/local/lsws/lsphp/bin/lsphp',
            '/usr/local/lsws/lsphp81/bin/lsphp',
            '/usr/local/lsws/lsphp82/bin/lsphp',
            '/usr/local/lsws/lsphp83/bin/lsphp',
        ];

        // try to detect system's PHP CLI
        if (Helper::exec_enabled()) {
            try {
                $paths = array_unique(array_merge($paths, explode(" ", exec("whereis php"))));
                $server_php_path = exec('which php');
                if ($server_php_path == "") {
                    $server_php_path = Helper::app_config('php_bin_path');
                }
                $get_message = '';
            } catch (Exception $e) {
                $server_php_path = Helper::app_config('php_bin_path');
                $get_message = $e->getMessage();
            }
        } else {
            $server_php_path = Helper::app_config('php_bin_path');
            $get_message = 'WARNING: Please enable PHP `exec` function to validate the cron job setting';
        }

        $paths = array_values(array_filter($paths, function ($path) {
            try {
                return is_executable($path) && preg_match($path, "/php[0-9\.a-z]{0,3}$/i");
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }));

        $categories = collect(config('customer-permissions'))->map(function ($value, $key) {
            $value['name'] = $key;

            return $value;
        })->groupBy('category');

        $permissions = $categories->keys()->map(function ($key) use ($categories) {
            return [
                'title' => $key,
                'permissions' => $categories[$key],
            ];
        });

        $existing_permission = json_decode(Customer::customerPermissions(), true);
        $plans               = Plan::where('status', true)->get();

        return view('admin.settings.AllSettings.system_settings', compact('breadcrumbs', 'language', 'sending_servers', 'paths', 'get_message', 'server_php_path', 'permissions', 'existing_permission', 'plans'));

    }


    /**
     * update general settings
     *
     * @param PostGeneralRequest $request
     *
     * @return RedirectResponse
     */

    public function postGeneral(PostGeneralRequest $request): RedirectResponse
    {

        if (config('app.stage') == 'demo') {
            return redirect()->route('admin.settings.general')->with([
                'status' => 'error',
                'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }


        if (isset($request->app_logo) && $request->hasFile('app_logo') && $request->file('app_logo')->isValid()) {
            AppConfig::uploadFile($request->file('app_logo'), (string) 'app_logo');
        }

        if (isset($request->app_favicon) && $request->hasFile('app_favicon') && $request->file('app_favicon')->isValid()) {
            AppConfig::uploadFile($request->file('app_favicon'), (string) 'app_favicon');
        }

        if (isset($request->language)) {
            session(['locale' => $request->language]);
        }

        if (isset($request->timezone)) {
            User::where('id', 1)->update([
                'timezone' => $request->timezone,
            ]);
        }

        $this->settings->general($request->except('_token', 'app_logo', 'app_favicon'));

        return redirect()->route('admin.settings.general')->withInput(['tab' => 'general'])->with([
            'status' => 'success',
            'message' => __('locale.settings.settings_successfully_updated'),
        ]);
    }


    /**
     * update system email settings
     *
     * @param SystemEmailRequest $request
     *
     * @return RedirectResponse
     */
    public function email(SystemEmailRequest $request): RedirectResponse
    {
        if (config('app.stage') == 'demo') {
            return redirect()->route('admin.settings.general')->with([
                'status' => 'error',
                'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }


        $this->settings->systemEmail($request->except('_token'));

        return redirect()->route('admin.settings.general')->withInput(['tab' => 'system_email'])->with([
            'status' => 'success',
            'message' => __('locale.settings.settings_successfully_updated'),
        ]);
    }

    /**
     * update authentication settings
     *
     * @param AuthenticationRequest $request
     *
     * @return RedirectResponse
     */
    public function authentication(AuthenticationRequest $request): RedirectResponse
    {
        if (config('app.stage') == 'demo') {
            return redirect()->route('admin.settings.general')->with([
                'status' => 'error',
                'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }


        $this->settings->authentication($request->except('_token'));

        return redirect()->route('admin.settings.general')->withInput(['tab' => 'authentication'])->with([
            'status' => 'success',
            'message' => __('locale.settings.settings_successfully_updated'),
        ]);
    }


    /**
     * update notifications settings
     *
     * @param NotificationsRequest $request
     *
     * @return RedirectResponse
     */
    public function notifications(NotificationsRequest $request): RedirectResponse
    {
        if (config('app.stage') == 'demo') {
            return redirect()->route('admin.settings.general')->with([
                'status' => 'error',
                'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }


        $this->settings->notifications($request->except('_token'));

        return redirect()->route('admin.settings.general')->withInput(['tab' => 'notifications'])->with([
            'status' => 'success',
            'message' => __('locale.settings.settings_successfully_updated'),
        ]);
    }

    /**
     * update pusher settings
     *
     * @param PusherRequest $request
     *
     * @return RedirectResponse
     */
    public function pusher(PusherRequest $request): RedirectResponse
    {

        if (config('app.stage') == 'demo') {
            return redirect()->route('admin.settings.general')->with([
                'status' => 'error',
                'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->settings->pusherSettings($request->except('_token'));

        return redirect()->route('admin.settings.general')->withInput(['tab' => 'pusher'])->with([
            'status' => 'success',
            'message' => __('locale.settings.settings_successfully_updated'),
        ]);

    }

    /**
     * @param LicenseRequest $request
     *
     * @return RedirectResponse
     */
    public function license(LicenseRequest $request): RedirectResponse
    {
        if (config('app.stage') == 'demo') {
            return redirect()->route('admin.settings.general')->with([
                'status' => 'error',
                'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }


        $purchase_code = $request->input('license');
        $get_data = array();
        $get_data['status'] = 'success';
        $get_data['license_type'] = 'Extended license';

        if (is_array($get_data) && array_key_exists('status', $get_data)) {
            if ($get_data['status'] == 'success') {
                AppConfig::where('setting', 'license')->update(['value' => $purchase_code]);
                AppConfig::where('setting', 'license_type')->update(['value' => $get_data['license_type']]);
                AppConfig::where('setting', 'valid_domain')->update(['value' => 'yes']);

                return redirect()->route('admin.settings.general')->withInput(['tab' => 'license'])->with([
                    'status' => 'success',
                    'message' => 'License updated successfully',
                ]);

            }

            return redirect()->route('admin.settings.general')->withInput(['tab' => 'license'])->with([
                'status' => 'error',
                'message' => 'Invalid license key',
            ]);
        }

        return redirect()->route('admin.settings.general')->withInput(['tab' => 'license'])->with([
            'status' => 'error',
            'message' => __('locale.exceptions.something_went_wrong'),
        ]);

    }

    /**
     * manage maintenance mode
     *
     * @return Application|Factory|View
     * @throws AuthorizationException
     */
    //    public function maintenanceMode(): Factory|View|Application
//    {
//
//        $this->authorize('manage maintenance_mode');
//
//        $breadcrumbs = [
//                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Dashboard')],
//                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Settings')],
//                ['name' => __('locale.menu.All Settings')],
//        ];
//
//
//        return view('admin.settings.system_settings', compact('breadcrumbs'));
//    }

    /**
     * check update
     *
     * @return Application|Factory|\Illuminate\Contracts\View\View
     */
    public function updateApplication(): \Illuminate\Contracts\View\View|Factory|Application
    {
        $breadcrumbs = [
            ['link' => url(config('app.admin_path') . "/dashboard"), 'name' => __('locale.menu.Dashboard')],
            ['link' => url(config('app.admin_path') . "/dashboard"), 'name' => __('locale.menu.Settings')],
            ['name' => __('locale.menu.All Settings')],
        ];


        return view('admin.settings.UpdateApplication.index', compact('breadcrumbs'));

    }

    /**
     * @return RedirectResponse
     */
    public function checkAvailableUpdate(): RedirectResponse
    {

        if (config('app.stage') == 'demo') {
            return redirect()->route('admin.settings.update_application')->with([
                'status' => 'error',
                'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }


        $app_version = config('app.version');
        $get_verification = 'https://ultimatesms.codeglen.com/version/';


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $get_verification);
        curl_setopt($ch, CURLOPT_HTTPGET, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $data = curl_exec($ch);
        curl_close($ch);

        if ($app_version == $data) {
            return redirect()->route('admin.settings.update_application')->with([
                'status' => 'success',
                'message' => 'You are using latest version',
            ]);
        }

        return redirect()->route('admin.settings.update_application')->with([
            'update_required' => true,
            'version' => $data,
        ]);

    }


    /**
     * Post Update Request
     *
     * @param Request        $request
     * @param BufferedOutput $outputLog
     *
     * @return JsonResponse
     */
    public function postUpdateApplication(Request $request, BufferedOutput $outputLog): JsonResponse
    {
        if (config('app.stage') == 'demo') {
            return response()->json([
                'status' => 'error',
                'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }


        $get_version = 'https://ultimatesms.codeglen.com/version/php-version.php';


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $get_version);
        curl_setopt($ch, CURLOPT_HTTPGET, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $required_version = curl_exec($ch);
        curl_close($ch);

        if (phpversion() < $required_version) {
            return response()->json([
                'status' => 'error',
                'message' => "Sorry! You will need to upgrade your PHP to version $required_version to update to the latest version.",
            ]);
        }

        $purchase_code = $request->input('purchase_code');
        $domain_name = config('app.url');
        $input = trim($domain_name, '/');
        $urlParts = parse_url($input);
        $domain_name = preg_replace('/^www\./', '', $urlParts['host']);

        $post_data = [
            'purchase_code' => $purchase_code,
            'domain' => $domain_name,
        ];


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://ultimatesms.codeglen.com/verify/');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $data = curl_exec($ch);
        curl_close($ch);

        $get_data = json_decode($data, true);

        if (is_array($get_data) && array_key_exists('status', $get_data)) {
            if ($get_data['status'] == 'success') {
                $get_response = Unzipper::extractZipArchive($request->file('update_file'), base_path());

                if (isset($get_response->getData()->status)) {

                    if ($get_response->getData()->status == 'success') {
                        try {

                            $app_path = base_path() . '/bootstrap/cache/';
                            if (File::isDirectory($app_path)) {
                                File::cleanDirectory($app_path);
                            }

                            Artisan::call('optimize:clear');
                            Artisan::call('migrate', ['--force' => true], $outputLog);

                            /*Update Seeder for new version*/
                            Tool::versionSeeder(config('app.version'));

                            AppConfig::setEnv('APP_VERSION', $request->input('version'));

                            return response()->json([
                                'status' => 'success',
                                'message' => 'You have successfully updated your application.',
                            ]);
                        } catch (Exception $e) {

                            return response()->json([
                                'status' => 'error',
                                'message' => $e->getMessage(),
                            ]);

                        }
                    }

                    return response()->json([
                        'message' => $get_response->getData()->message,
                        'status' => 'error',
                    ]);

                }

                return response()->json([
                    'message' => __('locale.exceptions.something_went_wrong'),
                    'status' => 'error',
                ]);
            }

            return response()->json([
                'message' => $get_data['msg'],
                'status' => 'error',
            ]);
        }

        return response()->json([
            'message' => 'Invalid request',
            'status' => 'error',
        ]);
    }

    /*Version 3.4*/

    /**
     * Update Default Customer Permissions
     *
     * @param DefaultCustomerPermission $request
     *
     * @return RedirectResponse
     */
    public function permissions(DefaultCustomerPermission $request): RedirectResponse
    {
        $permissions = array_values($request->only('permissions')['permissions']);

        $app_config = AppConfig::where('setting', 'customer_permissions')->update([
            'value' => $permissions,
        ]);

        if ($app_config) {
            return redirect()->route('admin.settings.general')->withInput(['tab' => 'permissions'])->with([
                'status' => 'success',
                'message' => __('locale.settings.settings_successfully_updated'),
            ]);
        }

        return redirect()->route('admin.settings.general')->withInput(['tab' => 'permissions'])->with([
            'status' => 'error',
            'message' => __('locale.exceptions.something_went_wrong'),
        ]);
    }


    /*Version 3.5*/

    public function dlt(DLTRequest $request): RedirectResponse
    {

        if (config('app.stage') == 'demo') {
            return redirect()->route('admin.settings.general')->withInput(['tab' => 'dlt'])->with([
                'status' => 'error',
                'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }


        $this->settings->dlt($request->except('_token'));

        return redirect()->route('admin.settings.general')->withInput(['tab' => 'dlt'])->with([
            'status' => 'success',
            'message' => __('locale.settings.settings_successfully_updated'),
        ]);
    }

    public function termsOfUse()
    {
        $this->authorize('general settings');

        $breadcrumbs = [
            ['link' => url(config('app.admin_path') . "/dashboard"), 'name' => __('locale.menu.Dashboard')],
            ['link' => url(config('app.admin_path') . "/email-templates"), 'name' => __('locale.menu.Email Templates')],
            ['name' => __('locale.labels.terms_of_use')],
        ];

        $termsOfUse = AppConfig::where('setting', 'terms_of_use')->first();
        $termsOfUseData = empty($termsOfUse) ? null : $termsOfUse->value;

        $termsOfUseBN = AppConfig::where('setting', 'terms_of_use_bn')->first();
        $termsOfUseDataBN = empty($termsOfUseBN) ? null : $termsOfUseBN->value;

        return view('admin.settings.AllSettings.terms-of-use', compact('breadcrumbs', 'termsOfUseData', 'termsOfUseDataBN'));

    }


    public function privacyPolicy()
    {

        $this->authorize('general settings');

        $breadcrumbs = [
            ['link' => url(config('app.admin_path') . "/dashboard"), 'name' => __('locale.menu.Dashboard')],
            ['link' => url(config('app.admin_path') . "/email-templates"), 'name' => __('locale.menu.Email Templates')],
            ['name' => __('locale.labels.privacy_policy')],
        ];

        $privacyPolicy = AppConfig::where('setting', 'privacy_policy')->first();
        $privacyPolicyData = empty($privacyPolicy) ? null : $privacyPolicy->value;

        $privacyPolicyBN = AppConfig::where('setting', 'privacy_policy_bn')->first();
        $privacyPolicyDataBN = empty($privacyPolicyBN) ? null : $privacyPolicyBN->value;

        return view('admin.settings.AllSettings.privacy-policy', compact('breadcrumbs', 'privacyPolicyData', 'privacyPolicyDataBN'));

    }

    public function postTermsOfUse(Request $request)
    {

        if (config('app.stage') == 'demo') {
            return redirect()->route('admin.settings.terms-of-use')->with([
                'status' => 'error',
                'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }


        $this->authorize('general settings');

        AppConfig::updateOrCreate(
            ['setting' => 'terms_of_use'],
            ['value' => $request->input('terms_of_use')]
        );

        AppConfig::updateOrCreate(
            ['setting' => 'terms_of_use_bn'],
            ['value' => $request->input('terms_of_use_bn')]
        );

        Artisan::call('optimize:clear');

        return redirect()->route('admin.settings.terms-of-use')->with([
            'status' => 'success',
            'message' => __('locale.settings.settings_successfully_updated'),
        ]);

    }

    public function postPrivacyPolicy(Request $request)
    {

        if (config('app.stage') == 'demo') {
            return redirect()->route('admin.settings.privacy-policy')->with([
                'status' => 'error',
                'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }


        $this->authorize('general settings');

        AppConfig::updateOrCreate(
            ['setting' => 'privacy_policy'],
            ['value' => $request->input('privacy_policy')]
        );

        AppConfig::updateOrCreate(
            ['setting' => 'privacy_policy_bn'],
            ['value' => $request->input('privacy_policy_bn')]
        );

        Artisan::call('optimize:clear');

        return redirect()->route('admin.settings.privacy-policy')->with([
            'status' => 'success',
            'message' => __('locale.settings.settings_successfully_updated'),
        ]);

    }

    public function registrationAgreement()
    {
        $this->authorize('general settings');

        $breadcrumbs = [
            ['link' => url(config('app.admin_path') . "/dashboard"), 'name' => __('locale.menu.Dashboard')],
            ['link' => url(config('app.admin_path') . "/settings"), 'name' => __('locale.menu.Settings')],
            ['name' => __('locale.labels.registration_agreement')],
        ];

        $registrationAgreement = AppConfig::where('setting', 'registration_agreement')->first();
        $registrationAgreementData = empty($registrationAgreement) ? null : $registrationAgreement->value;

        $registrationAgreementBN = AppConfig::where('setting', 'registration_agreement_bn')->first();
        $registrationAgreementDataBN = empty($registrationAgreementBN) ? null : $registrationAgreementBN->value;

        return view('admin.settings.AllSettings.registration-agreement', compact('breadcrumbs', 'registrationAgreementData', 'registrationAgreementDataBN'));
    }

    public function postRegistrationAgreement(Request $request)
    {
        if (config('app.stage') == 'demo') {
            return redirect()->route('admin.settings.registration-agreement')->with([
                'status' => 'error',
                'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->authorize('general settings');

        $registration_agreement = $request->input('registration_agreement');
        $registration_agreement_bn = $request->input('registration_agreement_bn');

        AppConfig::updateOrCreate(
            ['setting' => 'registration_agreement'],
            ['value' => $registration_agreement]
        );

        AppConfig::updateOrCreate(
            ['setting' => 'registration_agreement_bn'],
            ['value' => $registration_agreement_bn]
        );

        return redirect()->route('admin.settings.registration-agreement')->with([
            'status' => 'success',
            'message' => __('locale.settings.settings_successfully_updated'),
        ]);
    }

    /**
     * Clear application cache
     *
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function clearCache(): RedirectResponse
    {
        if (config('app.stage') == 'demo') {
            return redirect()->back()->with([
                'status'  => 'error',
                'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->authorize('general settings');

        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');

        return redirect()->back()->with([
            'status'  => 'success',
            'message' => 'Cache cleared successfully',
        ]);
    }

}
