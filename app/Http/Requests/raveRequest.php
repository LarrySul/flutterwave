<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class raveRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'bvn' => 'required| numeric|max:11|min:1',
            'email' => 'required|email',
            'fullname' => 'required|string',
            'amount' => 'required| numeric|max:10|min:1',
            'currency' => 'required|string',
            'txref' => 'required|string'
        ];
    }
}
