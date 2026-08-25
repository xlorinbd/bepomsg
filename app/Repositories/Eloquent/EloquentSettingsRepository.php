<?php

namespace App\Repositories\Eloquent;

use App\Models\AppConfig;
use App\Repositories\Contracts\SettingsRepository;
use Exception;

class EloquentSettingsRepository extends EloquentBaseRepository implements SettingsRepository
{
    /**
     * EloquentSettingsRepository constructor.
     */
    public function __construct(AppConfig $app_config)
    {
        parent::__construct($app_config);
    }

    /**
     * update general settings
     */
    public function general(array $input): bool
    {
        foreach ($input as $key => $value) {
            AppConfig::where('setting', $key)->update([
                'value' => $value,
            ]);
        }

        // Sync important keys to .env
        if (isset($input['app_name'])) AppConfig::setEnv('APP_NAME', $input['app_name']);
        if (isset($input['app_title'])) AppConfig::setEnv('APP_TITLE', $input['app_title']);
        if (isset($input['app_keyword'])) AppConfig::setEnv('APP_KEYWORD', $input['app_keyword']);
        if (isset($input['footer_text'])) AppConfig::setEnv('APP_FOOTER_TEXT', $input['footer_text']);
        if (isset($input['timezone'])) AppConfig::setEnv('APP_TIMEZONE', $input['timezone']);
        if (isset($input['language'])) AppConfig::setEnv('APP_LOCALE', $input['language']);
        if (isset($input['country'])) AppConfig::setEnv('APP_COUNTRY', $input['country']);
        if (isset($input['date_format'])) AppConfig::setEnv('APP_DATE_FORMAT', $input['date_format']);

        return true;
    }

    /**
     * update system email settings
     */
    public function systemEmail(array $input): bool
    {
        if ($input['driver'] == 'sendmail') {
            AppConfig::setEnv('MAIL_DRIVER', 'sendmail');
            AppConfig::setEnv('MAIL_MAILER', 'sendmail');
        } else {
            AppConfig::setEnv('MAIL_DRIVER', 'smtp');
            AppConfig::setEnv('MAIL_MAILER', 'smtp');
            AppConfig::setEnv('MAIL_HOST', $input['host']);
            AppConfig::setEnv('MAIL_PORT', $input['port']);
            AppConfig::setEnv('MAIL_USERNAME', $input['username']);
            AppConfig::setEnv('MAIL_ENCRYPTION', $input['encryption']);
            AppConfig::setEnv('MAIL_PASSWORD', $input['password'] ?? '');
        }

        AppConfig::setEnv('MAIL_FROM_ADDRESS', $input['from_email']);
        AppConfig::setEnv('MAIL_FROM_NAME', $input['from_name']);

        foreach ($input as $key => $value) {
            AppConfig::where('setting', $key)->update([
                'value' => $value,
            ]);
        }

        return true;
    }

    /**
     * update authentication settings
     */
    public function authentication(array $input): bool
    {
        $captcha_login = false;
        $captcha_registration = false;
        $login_with_facebook = false;
        $login_with_twitter = false;
        $login_with_google = false;
        $login_with_github = false;
        $client_registration = true;
        $client_can_delete_account = true;
        $registration_verification = true;
        $two_factor = false;

        $two_factor = (isset($input['two_factor']) && $input['two_factor'] == 1);
        AppConfig::setEnv('TWO_FACTOR', $two_factor);

        if (isset($input['captcha_site_key'])) {
            AppConfig::setEnv('NOCAPTCHA_SITEKEY', $input['captcha_site_key']);
        }

        if (isset($input['captcha_secret_key'])) {
            AppConfig::setEnv('NOCAPTCHA_SECRET', $input['captcha_secret_key']);
        }

        if (isset($input['two_factor_send_by'])) {
            AppConfig::setEnv('AUTH_CODE_SEND_BY', $input['two_factor_send_by']);
        }

        $captcha_login = (isset($input['captcha_in_login']) && $input['captcha_in_login'] == 1);
        $captcha_registration = (isset($input['captcha_in_client_registration']) && $input['captcha_in_client_registration'] == 1);
        $client_registration = (isset($input['client_registration']) && $input['client_registration'] == 1);
        $client_can_delete_account = (isset($input['client_can_delete_account']) && $input['client_can_delete_account'] == 1);
        $registration_verification = (isset($input['registration_verification']) && $input['registration_verification'] == 1);

        if (isset($input['login_with_facebook']) && $input['login_with_facebook'] == 1) {
            $login_with_facebook = true;
            $facebook_redirect = config('app.url') . '/login/facebook/callback';
            AppConfig::setEnv('FACEBOOK_CLIENT_ID', $input['facebook_client_id']);
            AppConfig::setEnv('FACEBOOK_CLIENT_SECRET', $input['facebook_client_secret']);
            AppConfig::setEnv('FACEBOOK_REDIRECT', $facebook_redirect);
        }

        if (isset($input['login_with_twitter']) && $input['login_with_twitter'] == 1) {
            $login_with_twitter = true;
            $twitter_redirect = config('app.url') . '/login/twitter/callback';
            AppConfig::setEnv('TWITTER_CLIENT_ID', $input['twitter_client_id']);
            AppConfig::setEnv('TWITTER_CLIENT_SECRET', $input['twitter_client_secret']);
            AppConfig::setEnv('TWITTER_REDIRECT', $twitter_redirect);
        }

        if (isset($input['login_with_google']) && $input['login_with_google'] == 1) {
            $login_with_google = true;
            $google_redirect = config('app.url') . '/login/google/callback';
            AppConfig::setEnv('GOOGLE_CLIENT_ID', $input['google_client_id']);
            AppConfig::setEnv('GOOGLE_CLIENT_SECRET', $input['google_client_secret']);
            AppConfig::setEnv('GOOGLE_REDIRECT', $google_redirect);
        }

        if (isset($input['login_with_github']) && $input['login_with_github'] == 1) {
            $login_with_github = true;
            $github_redirect = config('app.url') . '/login/github/callback';
            AppConfig::setEnv('GITHUB_CLIENT_ID', $input['github_client_id']);
            AppConfig::setEnv('GITHUB_CLIENT_SECRET', $input['github_client_secret']);
            AppConfig::setEnv('GITHUB_REDIRECT', $github_redirect);
        }

        AppConfig::setEnv('NOCAPTCHA_IN_LOGIN', $captcha_login);
        AppConfig::setEnv('NOCAPTCHA_IN_REGISTRATION', $captcha_registration);
        AppConfig::setEnv('SOCIALITE_FACEBOOK', $login_with_facebook);
        AppConfig::setEnv('SOCIALITE_TWITTER', $login_with_twitter);
        AppConfig::setEnv('SOCIALITE_GOOGLE', $login_with_google);
        AppConfig::setEnv('SOCIALITE_GITHUB', $login_with_github);
        AppConfig::setEnv('ACCOUNT_CAN_REGISTER', $client_registration);
        AppConfig::setEnv('ACCOUNT_CAN_DELETE', $client_can_delete_account);
        AppConfig::setEnv('ACCOUNT_VERIFICATION', $registration_verification);

        foreach ($input as $key => $value) {
            AppConfig::updateOrCreate(
                ['setting' => $key],
                ['value' => $value]
            );
        }

        return true;
    }

    /**
     * update notification settings
     */
    public function notifications(array $input): bool
    {
        foreach (AppConfig::notificationsValues() as $value) {
            AppConfig::where('setting', $value)->update(['value' => false]);
        }

        foreach ($input as $key => $value) {
            AppConfig::where('setting', $key)->update([
                'value' => $value,
            ]);
        }

        return true;
    }

    /**
     * update pusher settings
     */
    public function pusherSettings(array $input): bool
    {
        AppConfig::setEnv('PUSHER_APP_ID', $input['app_id'] ?? '');
        AppConfig::setEnv('PUSHER_APP_KEY', $input['app_key'] ?? '');
        AppConfig::setEnv('PUSHER_APP_SECRET', $input['app_secret'] ?? '');
        AppConfig::setEnv('PUSHER_APP_CLUSTER', $input['app_cluster'] ?? '');
        AppConfig::setEnv('BROADCAST_DRIVER', $input['broadcast_driver'] ?? 'log');

        foreach ($input as $key => $value) {
            AppConfig::where('setting', $key)->update([
                'value' => $value,
            ]);
        }

        return true;
    }

    public function localization(array $input)
    {
        // TODO: Implement localization() method.
    }

    public function backgroundJob(array $input)
    {
        // TODO: Implement backgroundJob() method.
    }

    public function license(array $input)
    {
        // TODO: Implement license() method.
    }

    public function upgradeApplication(array $input)
    {
        // TODO: Implement upgradeApplication() method.
    }

    /**
     * TRAI DLT
     */
    public function dlt(array $input): bool
    {
        $dlt = (isset($input['trai_dlt']) && $input['trai_dlt'] == 1);
        AppConfig::setEnv('TRAI_DLT', $dlt);

        foreach ($input as $key => $value) {
            AppConfig::where('setting', $key)->update([
                'value' => $value,
            ]);
        }

        return true;
    }
}
