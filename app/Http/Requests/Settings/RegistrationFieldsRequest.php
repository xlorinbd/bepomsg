<?php

namespace App\Http\Requests\Settings;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegistrationFieldsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('general settings');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'req_username' => 'required|in:-1,0,1',
            'req_first_name' => 'required|in:-1,0,1',
            'req_gender' => 'required|in:-1,0,1',
            'req_email' => 'required|in:-1,0,1',
            'req_password' => 'required|in:-1,0,1',
            'req_phone' => 'required|in:-1,0,1',
            'req_nid_number' => 'required|in:-1,0,1',
            'req_date_of_birth' => 'required|in:-1,0,1',
            'req_address' => 'required|in:-1,0,1',
            'req_company_name' => 'required|in:-1,0,1',
            'req_company_address' => 'required|in:-1,0,1',
            'req_purpose_of_use' => 'required|in:-1,0,1',
            'req_nid_upload' => 'required|in:-1,0,1',
            'req_trade_license' => 'required|in:-1,0,1',
            'req_profile_photo' => 'required|in:-1,0,1',
        ];
    }
}
