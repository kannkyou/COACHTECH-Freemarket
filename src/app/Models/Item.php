<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class Item extends Model
{
    protected $fillable = [
        'seller_id',
        'status',
        'title',
        'brand_name',
        'price',
        'description',
        'category_id',
        'condition',
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class,'category_item')->withTimestamps();
    }

    public function images(): HasMany
    {
        return $this->hasMany(ItemImage::class);
    }

    public function favoritedUsers()
    {
        return $this->belongsToMany(User::class, 'mylist', 'item_id', 'user_id')
            ->withTimestamps();
        }
}
