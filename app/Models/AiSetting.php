<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'is_enabled',
        'model',
        'system_prompt',
        'faq',
    ];

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
            'faq' => 'array',
        ];
    }
}
