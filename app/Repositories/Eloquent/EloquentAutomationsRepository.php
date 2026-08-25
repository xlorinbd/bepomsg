<?php


namespace App\Repositories\Eloquent;

use App\Library\SMSCounter;
use App\Library\Tool;
use App\Models\Automation;
use App\Models\ContactGroups;
use App\Models\Contacts;
use App\Models\Country;
use App\Models\CustomerBasedPricingPlan;
use App\Models\PhoneNumbers;
use App\Models\PlansCoverageCountries;
use App\Models\Senderid;
use App\Models\SendingServer;
use App\Models\SpamWord;
use App\Repositories\Contracts\AutomationsRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;
use Throwable;

class EloquentAutomationsRepository extends EloquentBaseRepository implements AutomationsRepository
{

    public static array $serverPools = [];

    /**
     * EloquentCampaignRepository constructor.
     *
     * @param Automation $automations
     */
    public function __construct(Automation $automations)
    {
        parent::__construct($automations);
    }


    /**
     * @param Automation $automation
     * @param array      $input
     *
     * @return JsonResponse
     */
    public function automationBuilder(Automation $automation, array $input): JsonResponse
    {

        $user = Auth::user();
        $customer = $user->customer;

        if ($user->sms_balance <= 0) {
            return response()->json([
                'status' => 'error',
                'message' => __('Insufficient SMS credits. Please purchase more SMS.'),
            ]);
        }

        $sms_type = $input['sms_type'];

        if (isset($input['sending_server'])) {
            $sending_server = SendingServer::where('status', true)->find($input['sending_server']);

            if (!$sending_server) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('locale.campaigns.sending_server_not_available'),
                ]);
            }

            if (!$sending_server->{$sms_type}) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('locale.sending_servers.sending_server_sms_capabilities', ['type' => strtoupper($sms_type)]),
                ]);
            }

        }

        if ($customer->getOption('send_spam_message') == 'no') {
            $spamWordCount = SpamWord::whereIn('word', array_map('strtolower', explode(' ', $input['message'])))->count();

            if ($spamWordCount > 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Your message contains spam words.',
                ]);
            }
        }

        $db_sms_type = ($sms_type === 'unicode') ? 'plain' : $sms_type;
        $capabilities_type = in_array($sms_type, ['plain', 'unicode']) ? 'sms' : $sms_type;

        $sender_id = null;

        if ($customer->getOption('sender_id_verification') == 'yes') {
            if (isset($input['originator'])) {
                if ($input['originator'] == 'sender_id') {
                    if (!isset($input['sender_id'])) {
                        return response()->json([
                            'status' => 'error',
                            'message' => __('locale.sender_id.sender_id_required'),
                        ]);
                    }

                    $sender_id = $input['sender_id'];

                    if (is_array($sender_id) && count($sender_id) > 0) {
                        $senderids = Senderid::where('user_id', $user->id)
                            ->where('status', 'active')
                            ->pluck('sender_id')
                            ->all();

                        $invalid = array_diff($sender_id, $senderids);

                        if (count($invalid)) {
                            return response()->json([
                                'status' => 'error',
                                'message' => __('locale.sender_id.sender_id_invalid', ['sender_id' => $invalid[0]]),
                            ]);
                        }
                    } else {
                        return response()->json([
                            'status' => 'error',
                            'message' => __('locale.sender_id.sender_id_required'),
                        ]);
                    }
                } else {
                    if (!isset($input['phone_number'])) {
                        return response()->json([
                            'status' => 'error',
                            'message' => __('locale.sender_id.phone_numbers_required'),
                        ]);
                    }

                    $sender_id = $input['phone_number'];

                    if (is_array($sender_id) && count($sender_id) > 0) {
                        $type_supported = [];
                        $numbers = PhoneNumbers::where('user_id', $user->id)
                            ->where('status', 'assigned')
                            ->cursor();

                        foreach ($numbers as $number) {
                            if (in_array($number->number, $sender_id) && !str_contains($number->capabilities, $capabilities_type)) {
                                $type_supported[] = $number->number;
                            }
                        }

                        if (count($type_supported)) {
                            return response()->json([
                                'status' => 'error',
                                'message' => __('locale.sender_id.sender_id_sms_capabilities', ['sender_id' => $type_supported[0], 'type' => $db_sms_type]),
                            ]);
                        }
                    } else {
                        return response()->json([
                            'status' => 'error',
                            'message' => __('locale.sender_id.sender_id_required'),
                        ]);
                    }
                }
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => __('locale.sender_id.sender_id_required'),
                ]);
            }
        } else if ($user->can('view_numbers') && isset($input['originator']) && $input['originator'] == 'phone_number' && isset($input['phone_number'])) {
            $sender_id = $input['phone_number'];

            if (is_array($sender_id) && count($sender_id) > 0) {
                $type_supported = [];
                $numbers = PhoneNumbers::where('user_id', $user->id)
                    ->where('status', 'assigned')
                    ->cursor();

                foreach ($numbers as $number) {
                    if (in_array($number->number, $sender_id) && !str_contains($number->capabilities, $capabilities_type)) {
                        $type_supported[] = $number->number;
                    }
                }

                if (count($type_supported)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => __('locale.sender_id.sender_id_sms_capabilities', ['sender_id' => $type_supported[0], 'type' => $db_sms_type]),
                    ]);
                }
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => __('locale.sender_id.sender_id_required'),
                ]);
            }
        } else {
            if (isset($input['originator'])) {
                if ($input['originator'] == 'sender_id') {
                    if (!isset($input['sender_id'])) {
                        return response()->json([
                            'status' => 'error',
                            'message' => __('locale.sender_id.sender_id_required'),
                        ]);
                    }

                    $sender_id = $input['sender_id'];
                } else {
                    if (!isset($input['phone_number'])) {
                        return response()->json([
                            'status' => 'error',
                            'message' => __('locale.sender_id.phone_numbers_required'),
                        ]);
                    }

                    $sender_id = $input['phone_number'];
                }

                if (!is_array($sender_id) || count($sender_id) <= 0) {
                    return response()->json([
                        'status' => 'error',
                        'message' => __('locale.sender_id.sender_id_required'),
                    ]);
                }
            }

            if (isset($input['sender_id'])) {
                $sender_id = $input['sender_id'];
            }
        }

        $contactGroup = ContactGroups::where('customer_id', $user->id)->where('status', true)->find($input['contact_groups']);

        if (!$contactGroup) {
            return response()->json([
                'status' => 'error',
                'message' => __('locale.contacts.contact_group_not_found'),
            ]);
        }

        $checkExistingAutomation = Automation::where('contact_list_id', $contactGroup->id)->where('user_id', $user->id)->first();

        if ($checkExistingAutomation) {
            return response()->json([
                'status' => 'error',
                'message' => __('locale.automations.automation_already_available_for_contact_group'),
            ]);
        }

        $total = $contactGroup->readCache('SubscribersCount');

        // SMS Credit System: estimate credits needed
        $sms_count_estimate = 1;
        if (isset($input['message'])) {
            $sms_counter = new SMSCounter();
            $message_data = $sms_counter->count($input['message'], $sms_type == 'whatsapp' ? 'WHATSAPP' : null);
            $sms_count_estimate = max($message_data->messages, 1);
        }
        $credits_needed = $sms_count_estimate * $total;

        if (!$user->hasEnoughCredits($credits_needed)) {
            return response()->json([
                'status' => 'error',
                'message' => __('Insufficient SMS credits. You need :needed credits but have :balance.', [
                    'needed' => $credits_needed,
                    'balance' => $user->sms_balance,
                ]),
            ]);
        }

        if ($total == 0) {
            return response()->json([
                'status' => 'error',
                'message' => __('locale.campaigns.contact_not_found'),
            ]);
        }

        $new_automation = Automation::create([
            'user_id' => $user->id,
            'name' => $input['name'],
            'message' => $input['message'],
            'sms_type' => $sms_type,
            'contact_list_id' => $contactGroup->id,
            'timezone' => $input['timezone'],
            'data' => json_encode([
                'options' => [
                    'before' => $input['before'],
                    'at' => $input['at'],
                    'birthday_field' => $input['birthday_field'],
                ],
            ]),
            'status' => $automation::STATUS_ACTIVE,
        ]);

        if (!$new_automation) {
            return response()->json([
                'status' => 'error',
                'message' => __('locale.exceptions.something_went_wrong'),
            ]);
        }


        if (isset($input['dlt_template_id'])) {
            $new_automation->dlt_template_id = $input['dlt_template_id'];
        }

        if (isset($input['sending_server'])) {
            $new_automation->sending_server_id = $input['sending_server'];
        }

        $sender_id = array_filter($sender_id);
        if (count($sender_id)) {
            $new_automation->sender_id = json_encode($sender_id);
        }

        $new_automation->cache = json_encode([
            'ContactCount' => $total,
            'DeliveredCount' => 0,
            'FailedDeliveredCount' => 0,
            'NotDeliveredCount' => 0,
            'PendingContactCount' => $total,
        ]);


        if ($sms_type == 'voice') {
            $new_automation->language = $input['language'];
            $new_automation->gender = $input['gender'];
        }

        if ($sms_type == 'mms') {
            $new_automation->media_url = Tool::uploadImage($input['mms_file']);
        }

        if ($sms_type == 'whatsapp') {

            if (isset($input['whatsapp_language'])) {
                $new_automation->language = $input['whatsapp_language'];
            }

            if (isset($input['whatsapp_mms_file'])) {
                $new_automation->media_url = Tool::uploadImage($input['whatsapp_mms_file']);
            }
        }


        //finally, store data and return response
        $camp = $new_automation->save();


        if ($camp) {

            try {

                return response()->json([
                    'status' => 'success',
                    'message' => __('locale.automations.automation_campaign_send_successfully'),
                ]);
            } catch (Throwable $exception) {
                $new_automation->delete();

                return response()->json([
                    'status' => 'error',
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        $new_automation->delete();

        return response()->json([
            'status' => 'success',
            'message' => __('locale.exceptions.something_went_wrong'),
        ]);
    }


    /**
     * Enable the Automation
     *
     * @param Automation $automation
     *
     * @return JsonResponse
     */
    public function enable(Automation $automation): JsonResponse
    {
        $automation->status = Automation::STATUS_ACTIVE;
        if (!$automation->save()) {
            return response()->json([
                'status' => 'error',
                'message' => __('locale.exceptions.something_went_wrong'),
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => __('locale.campaigns.campaign_was_successfully_paused'),
        ]);
    }

    /**
     * Disable the Automation
     *
     * @param Automation $automation
     *
     * @return JsonResponse
     */
    public function disable(Automation $automation): JsonResponse
    {
        $automation->status = Automation::STATUS_INACTIVE;
        if (!$automation->save()) {
            return response()->json([
                'status' => 'error',
                'message' => __('locale.exceptions.something_went_wrong'),
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => __('locale.campaigns.campaign_was_successfully_paused'),
        ]);
    }

    /**
     * Delete the Automation
     *
     * @param Automation $automation
     *
     * @return JsonResponse
     */
    public function delete(Automation $automation): JsonResponse
    {
        $automation->where('uid', $automation->uid)->with(['trackingLogs', 'reports'])->delete();

        return response()->json([
            'status' => 'success',
            'message' => __('locale.automations.automation_has_been_successfully_deleted'),
        ]);
    }

    /**
     * Batch Enable
     *
     * @param array $ids
     *
     * @return JsonResponse
     */
    public function batchEnable(array $ids): JsonResponse
    {
        DB::transaction(function () use ($ids) {
            if ($this->query()->whereIn('uid', $ids)->update(['status' => 'active'])) {
                return response()->json([
                    'status' => 'success',
                    'message' => __('locale.automations.selected_automation_enabled'),
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => __('locale.exceptions.something_went_wrong'),
            ]);
        });

        return response()->json([
            'status' => 'success',
            'message' => __('locale.automations.selected_automation_enabled'),
        ]);
    }

    /**
     * Batch Disable
     *
     * @param array $ids
     *
     * @return JsonResponse
     */
    public function batchDisable(array $ids): JsonResponse
    {

        DB::transaction(function () use ($ids) {
            if ($this->query()->whereIn('uid', $ids)->update(['status' => 'inactive'])) {
                return response()->json([
                    'status' => 'success',
                    'message' => __('locale.automations.selected_automation_disabled'),
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => __('locale.exceptions.something_went_wrong'),
            ]);
        });

        return response()->json([
            'status' => 'success',
            'message' => __('locale.automations.selected_automation_disabled'),
        ]);
    }

    public function batchDelete(array $ids)
    {

        $status = Automation::whereIn('uid', $ids)
            ->with(['trackingLogs', 'reports'])
            ->delete();

        if ($status) {
            return response()->json([
                'status' => 'success',
                'message' => __('locale.automations.selected_automation_deleted'),
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => __('locale.exceptions.something_went_wrong'),
        ]);
    }

}
