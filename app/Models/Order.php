<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $guarded = []; // ปลดล็อคให้ใส่ข้อมูลได้ทุกช่อง

    // ออเดอร์นี้เป็นของโต๊ะไหน?
    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    // ออเดอร์นี้มีรายการอาหารอะไรบ้าง?
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
