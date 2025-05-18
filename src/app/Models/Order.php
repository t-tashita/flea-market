<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function paymentMethods()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    protected $fillable = [
        'item_id',
        'payment_method_id',
        'order_postal_code',
        'order_address',
        'order_building',
        'user_id',
    ];

}
