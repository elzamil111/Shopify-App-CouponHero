<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActivationRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'g-recaptcha-response' => 'required|captcha',
            'license' => 'required|email',
            'shop_subdomain' => ['required', 'alpha_dash']
        ];
    }

    public function messages()
    {
        return [
            'captcha' => 'Captcha verification failed. Please try again'
        ];
    }
}
