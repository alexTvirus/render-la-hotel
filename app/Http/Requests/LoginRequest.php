<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 4/16/2018
 * Time: 2:16 PM
 */

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'required|email|max:255',
            'password' => 'required|min:6',
        ];
    }

    public function messages()
    {
        return [
            'email.required' => trans('auth.failed'),
            'email.email' => trans('auth.failed'),
            'email.max' => trans('auth.failed'),
            'password.required' => trans('auth.failed'),
            'password.min' => trans('auth.failed'),
        ];
    }
}