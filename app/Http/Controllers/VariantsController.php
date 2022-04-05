<?php

namespace App\Http\Controllers;

use App\Models\Variant;
use App\Jobs\PostVariantToOmetria;

class VariantsController extends Controller {

    public function show($product_id, Variant $variant) {

        $variant_slince_object = unserialize($variant->attributes);

        // dd(unserialize($variant->product->attributes));
        // dd($variant_slince_object);

        return view('variants.show', [
            'product' => $variant->product,
            'variant' => $variant
        ]);
    }

    public function ometriaUp($product_id, Variant $variant) {

        PostVariantToOmetria::dispatch($variant);

        return redirect()->route('products.variants.show', [
            $product_id, $variant->id
        ])->with('flash.banner', 'Added to queue!');
    }
}