<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\PlanSendingCreditPrice;
use Illuminate\Database\Seeder;

class DefaultPlanSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        // First look for a plan named 'Default Unlimited Plan'
        $existingPlan = Plan::where('name', 'Default Unlimited Plan')->first();
        $currency = \App\Models\Currency::where('status', true)->first() ?? \App\Models\Currency::first();
        
        $options = [
            'sms_max'                   => '0', // Start with 0 credits, buy as needed
            'list_max'                  => '-1', // Unlimited
            'subscriber_max'            => '-1', // Unlimited
            'subscriber_per_list_max'   => '-1', // Unlimited
            'segment_per_list_max'      => '-1', // Unlimited
            'billing_cycle'             => 'non_expiry',
            'sending_limit'             => 'unlimited',
            'sending_quota'             => '-1', // Unlimited
            'sending_quota_time'        => '1',
            'sending_quota_time_unit'   => 'hour',
            'max_process'               => '50',
            'list_import'               => 'yes',
            'list_export'               => 'yes',
            'api_access'                => 'yes',
            'create_sub_account'        => 'yes',
            'delete_sms_history'        => 'yes',
            'add_previous_balance'      => 'yes',
            'sender_id_verification'    => 'yes',
            'send_spam_message'         => 'yes'
        ];

        if (!$existingPlan) {

            if (!$currency) {
                echo "No currency found. Please seed currencies first.\n";
                return;
            }

            // Create an "Unlimited" default free plan
            $plan = Plan::create([
                'user_id'              => 1,
                'currency_id'          => $currency->id,
                'name'                 => 'Default Unlimited Plan',
                'description'          => 'Enjoy unlimited access with no expiry.',
                'price'                => 0.00,
                'billing_cycle'        => 'non_expiry',
                'frequency_amount'     => 1,
                'frequency_unit'       => 'year',
                'options'              => json_encode($options),
                'status'               => true,
                'tax_billing_required' => false,
            ]);

            // Add default credit pricing rules (cost per 1 SMS unit when buying more)
            PlanSendingCreditPrice::create([
                'plan_id'         => $plan->id,
                'unit_from'       => 1,
                'unit_to'         => 10000000,
                'per_credit_cost' => 0.40, // 0.40 BDT/USD per unit
            ]);
            
            $existingPlan = $plan;
        } else {
            // Update existing plan options
            $existingPlan->update([
                'options' => json_encode($options),
                'billing_cycle' => 'non_expiry',
                'price' => 0.00, // Ensure it's free
            ]);
        }

        // Update app config to use this plan as default for registration
        \App\Models\AppConfig::updateOrCreate(
            ['setting' => 'registration_default_plan'],
            ['value' => $existingPlan->id]
        );

        // Ensure Coverage is enabled for Bangladesh and other active countries for this plan
        $this->seedCoverage($existingPlan->id);

        // Migrate all existing users to this plan if they don't have a valid active one
        $this->migrateUsersToDefaultPlan($existingPlan->id);
    }

    private function migrateUsersToDefaultPlan($planId)
    {
        $users = \App\Models\User::all();
        foreach ($users as $user) {
            if (!$user->customer && !$user->is_admin) continue;

            $activeSub = \App\Models\Subscription::where('user_id', $user->id)->where('status', 'active')->first();
            
            if (!$activeSub || $activeSub->plan_id != $planId) {
                if ($activeSub) {
                    $activeSub->update(['status' => 'ended', 'end_at' => now()]);
                }

                \App\Models\Subscription::create([
                    'user_id' => $user->id,
                    'plan_id' => $planId,
                    'status' => 'active',
                    'start_at' => now(),
                    'current_period_ends_at' => now()->addYears(100),
                    'end_period_last_days' => '10',
                ]);
                echo "Migrated user " . $user->email . " to Unlimited Plan\n";
            } else {
                // Ensure periods are far in future
                $activeSub->update(['current_period_ends_at' => now()->addYears(100)]);
            }
        }
    }

    private function seedCoverage($planId)
    {
        $countries = \App\Models\Country::where('status', true)->get();
        $server = \App\Models\SendingServer::where('status', true)->first(); // Use first active server (e.g., GreenWebBD)

        if (!$server) {
            echo "No active sending server found. Skipping coverage seeding.\n";
            return;
        }

        $options = [
            'plain' => 'true',
            'plain_sms' => 1,
            'receive_plain_sms' => 0,
            'voice_sms' => 0,
            'receive_voice_sms' => 0,
            'mms_sms' => 0,
            'receive_mms_sms' => 0,
            'whatsapp_sms' => 0,
            'receive_whatsapp_sms' => 0,
            'viber_sms' => 0,
            'receive_viber_sms' => 0,
            'otp_sms' => 0,
            'receive_otp_sms' => 0
        ];

        foreach ($countries as $country) {
            \App\Models\PlansCoverageCountries::updateOrCreate(
                [
                    'plan_id' => $planId,
                    'country_id' => $country->id,
                ],
                [
                    'options' => json_encode($options),
                    'sending_server' => $server->id,
                    'status' => true,
                ]
            );
        }
        echo "Enabled coverage for " . $countries->count() . " active countries for plan " . $planId . "\n";
    }
}
