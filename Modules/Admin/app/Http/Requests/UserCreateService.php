<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserCreateService extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $siteGroup = epcache('site_group');
        $isReqd = $siteGroup == "lending" ? "nullable" : "required";
        return [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'mobile' => $isReqd.'|numeric|digits:10',
            'pan' => 'required|min:10|max:10|unique:kyc,pan|regex:/^([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}?$/',
            'roles' => 'required'
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
