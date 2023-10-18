<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class ListRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
			'name' => [ 'nullable', 'string', 'unique',],
			'description' => [ 'nullable', 'string',],
			'category_id' => [ 'nullable', 'integer',],
		];
    }


}
