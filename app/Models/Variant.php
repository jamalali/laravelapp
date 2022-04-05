<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variant extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $fillable = [
        'id',
        'product_id',
        'title',
        'country',
        'handle',
        'price',
        'sku',
        'inventory_quantity',
        'image_src',
        'option_1',
        'option_2',
        'option_3',
        'attributes'
    ];

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function getShopAdminUrl() {
        return 'https://' . config('vivo.shopify.url') . '/admin/products/' . $this->product->id . '/variants/' . $this->id;
    }

    public function getOmetriaId($country) {
        return $country . ':' . $this->product->id . '_' . $this->id;
    }
}
