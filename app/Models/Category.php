<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    public const CATEGORY_NAMES = [
        '友達',
        '家族',
        '恋愛',
        '職場',
        '学校',
        'その他',
    ];

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
    }