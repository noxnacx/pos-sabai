<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // เพิ่มบรรทัดนี้

class Product extends Model
{
    protected $guarded = [];

    // --- เติมตรงนี้ครับ ---
    public function category(): BelongsTo
    {
        // สินค้านี้ "เป็นของ" หมวดหมู่ (เชื่อมด้วย category_id)
        return $this->belongsTo(Category::class);
    }
}
