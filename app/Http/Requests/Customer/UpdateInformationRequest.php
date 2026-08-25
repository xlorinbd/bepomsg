<?php

    namespace App\Http\Requests\Customer;

    use App\Rules\Phone;
    use Illuminate\Foundation\Http\FormRequest;

    class UpdateInformationRequest extends FormRequest
    {
        /**
         * Determine if the user is authorized to make this request.
         *
         * @return bool
         */
        public function authorize(): bool
        {
            return $this->user()->can('edit customer');
        }

        public function rules(): array
        {
            return [
                'phone'   => ['required', 'numeric', new Phone($this->phone)],
                'website' => ['nullable', 'url'],
                'address' => ['required', 'string', 'regex:/^[\pL\pN\s\-,\.]+$/u'],
                'city'    => ['required', 'string', 'regex:/^[\pL\s\-]+$/u'],
                'country' => ['required', 'string', 'regex:/^[\pL\s\-]+$/u'],
                'gender'          => ['nullable', 'string', 'in:male,female,other'],
                'company'         => ['nullable', 'string', 'max:255'],
                'nid_number'      => ['nullable', 'string', 'max:50'],
                'date_of_birth'   => ['nullable', 'date'],
                'company_address' => ['nullable', 'string', 'max:255'],
                'purpose_of_use'  => ['nullable', 'string'],
            ];
        }

        public function messages(): array
        {
            return [
                'address.regex' => 'The address may only contain letters, numbers, spaces, hyphens, commas, and periods.',
                'city.regex'    => 'The city may only contain letters, spaces, and hyphens.',
                'country.regex' => 'The country may only contain letters, spaces, and hyphens.',
            ];
        }


    }
