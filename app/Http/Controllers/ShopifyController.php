<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Jobs\PostVariantToOmetria;
use Illuminate\Http\Request;

class ShopifyController extends Controller {

    public function product(Request $request) {

        $inProduct = $request->all(); // Incoming product

        $vendor = $inProduct['vendor'];
        $status = $inProduct['status'];
        $country  = $request->route('country');

        // We only want active Vivo Life vendored products
        // Respond with 202 so Shopify does not retry
        if ($vendor != 'Vivo Life' || $status != 'active') {
            return response('Accepted', 202);
        }

        $product = Product::updateOrCreate([
            'id' => $inProduct['id']
        ],
        [
            'title'         => $inProduct['title'],
            'handle'        => $inProduct['handle'],
            'image_src'     => $inProduct['image'] ? $inProduct['image']['src'] : '',
            'option_1'      => isset($inProduct['options'][0]) ? $inProduct['options'][0]['name'] : '',
            'option_2'      => isset($inProduct['options'][1]) ? $inProduct['options'][1]['name'] : '',
            'option_3'      => isset($inProduct['options'][2]) ? $inProduct['options'][2]['name'] : ''
        ]);

        $inVariants  = $inProduct['variants'];
        $images      = $inProduct['images'];
        
        foreach($inVariants as $inVariant) {

            $image_id    = $inVariant['image_id'];
            $image_src   = '';

            $option_1 = $inVariant['option1'];
            $option_2 = $inVariant['option2'];
            $option_3 = $inVariant['option3'];

            if ($image_id != null) {
                $found = array_filter(
                    $images,
                    function ($e) use ($image_id) {
                        return $e['id'] == $image_id;
                    }
                );

                $image       = current($found);
                $image_src   = $image['src'];
            }

            $variant = $product->variants()->updateOrCreate([
                'id' => $inVariant['id'],
                'product_id' => $inProduct['id']
            ],
            [
                'title'                 => $inVariant['title'],
                'price'                 => $inVariant['price'],
                'handle'                => $inProduct['handle'],
                'country'               => $country,
                'sku'                   => isset($inVariant['sku']) ? $inVariant['sku'] : '',
                'inventory_quantity'    => isset($inVariant['inventory_quantity']) ? $inVariant['inventory_quantity'] : 0,
                'image_src'             => $image_src,
                'option_1'              => isset($option_1) ? $option_1 : '',
                'option_2'              => isset($option_2) ? $option_2 : '',
                'option_3'              => isset($option_3) ? $option_3 : ''
            ]);

            PostVariantToOmetria::dispatch($variant, $request)->afterCommit();
        }

        return response('Ok', 201);
    }
}