<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code',
        'customer_id',
        'total_amount',
        'order_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'order_date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function treatments()
    {
        return $this->hasManyThrough(Treatment::class, OrderItem::class, 'order_id', 'id', 'id', 'treatment_id');
    }
} 