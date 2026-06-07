<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public static function customerId(): ?int
    {
        return static::query()->where('slug', 'customer')->value('id');
    }

    public static function adminId(): ?int
    {
        return static::query()->where('slug', 'admin')->value('id');
    }
}
