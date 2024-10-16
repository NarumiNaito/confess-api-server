<?php

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function rules(): array
    {

        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'content' => ['required', 'string']
        ];
    }

    public function attributes()
    {
        return [
            'category_id' => 'カテゴリ',
            'content' => '内容',
        ];
    }
}