<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class ApiChapterRequest extends FormRequest
{
    private $error_messages = array();
    private $error_mobiles = [];

    public function rules()
    {
        return [
            'chapter_name'=> 'required'
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

    protected function prepareForValidation()
    {
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'errors' => $this->error_messages,
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message' => "fails",
                'error_mobiles' => $this->error_mobiles
            ], Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            if ($validator->failed()) {
                $this->error_messages = $validator->messages()->getMessages();
                foreach ($this->error_messages as $key => $value) {
                    $item = [
                        'field' => $key,
                        'message' => $value[0]
                    ];
                    array_push($this->error_mobiles, $item);
                }
                return;
            }
        });
    }

    public function messages()
    {
        return [
            'chapter_name.required' => 'Bắt buộc nhập',
        ];
    }
}
