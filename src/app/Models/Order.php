<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Item;
use App\Models\User;

class Order extends Model
{
    use HasFactory;

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

        protected $fillable = [
        'buyer_id',
        'seller_id',
        'item_id',
        'status',
        'item_price',
        'payment_method',
        'shipping_postal_code',
        'shipping_address',
        'shipping_building',
        'paid_at',
    ];
}
