<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $fillable = [
        'id',
        'title',
        'handle',
        'image_src',
        'option_1',
        'option_2',
        'option_3',
        'attributes'
    ];

    public function variants() {
        return $this->hasMany(Variant::class);
    }

    public function getProductPageUrl() {
        return config('app.store_url') . '/products/' . $this->handle;
    }

    public function getShopAdminUrl() {
        return 'https://' . config('vivo.shopify.url') . '/admin/products/' . $this->id;
    }
}
