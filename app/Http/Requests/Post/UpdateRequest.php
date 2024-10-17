<?php

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function rules(): array
    {
        return [
            // 'id' => ['required', 'integer'],
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
