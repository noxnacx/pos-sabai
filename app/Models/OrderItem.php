<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $guarded = [];

    // รายการนี้อยู่ในออเดอร์ไหน?
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // รายการนี้คือสินค้าตัวไหน?
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
