<?php

    namespace App\Http\Controllers;


    use App\Models\AppConfig;

    class PublicController extends Controller
    {

        public function termsOfUse()
        {
            $termsOfUse     = AppConfig::where('setting', 'terms_of_use')->first();
            $termsOfUseData = empty($termsOfUse) ? null : $termsOfUse->value;

            $termsOfUseBN     = AppConfig::where('setting', 'terms_of_use_bn')->first();
            $termsOfUseDataBN = empty($termsOfUseBN) ? null : $termsOfUseBN->value;

            return view('auth.termsOfUses', compact('termsOfUseData', 'termsOfUseDataBN'));

        }

        public function privacyPolicy()
        {


            $privacyPolicy     = AppConfig::where('setting', 'privacy_policy')->first();
            $privacyPolicyData = empty($privacyPolicy) ? null : $privacyPolicy->value;

            $privacyPolicyBN     = AppConfig::where('setting', 'privacy_policy_bn')->first();
            $privacyPolicyDataBN = empty($privacyPolicyBN) ? null : $privacyPolicyBN->value;


            return view('auth.privacyPolicy', compact('privacyPolicyData', 'privacyPolicyDataBN'));

        }

    }
