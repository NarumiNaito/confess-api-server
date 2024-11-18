<?php

namespace App\Http\Requests\BookMark;

use Illuminate\Foundation\Http\FormRequest;

class ToggleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'post_id' => ['required', 'integer'],
            'is_bookmarks' => ['required', 'boolean'],
        ];
    }

    public function attributes()
    {
        return [
        'post_id' => 'ポストID',
        'user_id' => 'ユーザーID',
    ];
    }
}
