<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // เพิ่มบรรทัดนี้

class Category extends Model
{
    protected $guarded = [];

    // --- เติมตรงนี้ครับ ---
    public function products(): HasMany
    {
        // หมวดหมู่นี้ "มี" สินค้าหลายชิ้น
        return $this->hasMany(Product::class);
    }
}
