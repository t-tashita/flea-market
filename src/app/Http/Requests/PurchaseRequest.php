<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
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
            'payment_method' => 'required',

            // withValidatorにてバリデーションを対応
            'order_postal_code' => 'nullable',
            'order_address' => 'nullable',
            'order_building' => 'nullable',
        ];
    }

    public function messages()
    {
        return [
            'payment_method.required' => '支払い方法を選択してください',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (
                empty($this->order_postal_code) ||
                empty($this->order_address) ||
                empty($this->order_building)
            ) {
                $validator->errors()->add('delivery_info', '配送先を入力してください');
            }
        });
    }
}
