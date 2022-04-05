<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Jobs\PostVariantToOmetria;
use Slince\Shopify\PrivateAppCredential;
use Slince\Shopify\Client;
use Illuminate\Http\Request;

class ProductsController extends Controller {

    protected $credential;
    protected $client;
    protected $country;

    public function __construct(Request $request) {
        $this->country = $request->route('country');
        $this->credential = new PrivateAppCredential(config('vivo.shopify.'.$this->country.'.key'), config('vivo.shopify.'.$this->country.'.password'), config('vivo.shopify.'.$this->country.'.secret'));
        
        $this->client = new Client(config('vivo.shopify.'.$this->country.'.url'), $this->credential, [
            'meta_cache_dir' => './tmp' // Metadata cache dir, required
        ]);
    }

    public function index() {
        $products = Product::simplePaginate(50);

        return view('products.index', [
            'products' => $products,
            'country' => $this->country
        ]);
    }

    public function ometriaUp() {
        $products = Product::all();

        foreach($products as $product) {
            foreach($product->variants as $variant) {
                PostVariantToOmetria::dispatch($variant);
            }
        }
        
        return redirect()->route('products.index')->with('flash.banner', 'Added to queue!');
    }

    public function sync() {
        $shopProducts = $this->_getShopProducts();

        foreach($shopProducts as $shopProduct) {

            $product_id = $shopProduct->getId();
            $title      = $shopProduct->getTitle();
            $handle     = $shopProduct->getHandle();
            $image      = $shopProduct->getImage();
            $imageSrc   = '';

            if (isset($image)) {
                $imageSrc   = $image->getSrc();
            }

            $images      = $shopProduct->getImages();
            $options     = $shopProduct->getOptions();

            $option_1 = isset($options[0]) ? $options[0]->getName() : '';
            $option_2 = isset($options[1]) ? $options[1]->getName() : '';
            $option_3 = isset($options[2]) ? $options[2]->getName() : '';

            $product = Product::updateOrCreate([
                'id' => $product_id
            ],
            [
                'title'         => $title,
                'handle'        => $handle,
                'image_src'     => $imageSrc,
                'option_1'      => $option_1,
                'option_2'      => $option_2,
                'option_3'      => $option_3
            ]);

            // Variants
            $shopVariants = $shopProduct->getVariants();

            foreach($shopVariants as $shopVariant) {
                $variant_id         = $shopVariant->getId();
                $title              = $shopVariant->getTitle();
                $price              = $shopVariant->getPrice();
                $sku                = $shopVariant->getSku();
                $inventoryQuantity  = $shopVariant->getInventoryQuantity();
                $imageId            = $shopVariant->getImageId();
                $imageSrc           = '';

                $option_1 = $shopVariant->getOption1();
                $option_2 = $shopVariant->getOption2();
                $option_3 = $shopVariant->getOption3();

                if ($imageId != null) {
                    $found = array_filter(
                        $images,
                        function ($e) use ($imageId) {
                            return $e->getId() == $imageId;
                        }
                    );
    
                    $image      = current($found);
                    $imageSrc   = $image->getSrc();
                }

                $product->variants()->updateOrCreate([
                    'id'            => $variant_id,
                    'product_id'    => $product_id // This can be removed - need to test when have time
                ],
                [
                    'title'                 => $title,
                    'price'                 => $price,
                    'sku'                   => $sku,
                    'handle'                => $handle,
                    'country'               => $this->country,
                    'inventory_quantity'    => $inventoryQuantity,
                    'image_src'             => $imageSrc,
                    'option_1'              => isset($option_1) ? $option_1 : '',
                    'option_2'              => isset($option_2) ? $option_2 : '',
                    'option_3'              => isset($option_3) ? $option_3 : ''
                ]);
            }
        }

        return redirect()->route('products.index', [
            'country' => $this->country
        ]);
    }

    public function show(Product $product) {

        return view('products.show', [
            'product' => $product
        ]);
    }

    protected function _getShopProducts() {

        $pagination = $this->client->getProductManager()->paginate([
            'status' => 'active',
            'vendor' => 'Vivo Life'
        ]);

        $products = [];
        
        $products = $pagination->current();

        while ($pagination->hasNext()) {
            $nextProducts = $pagination->next();
            $products = array_merge($products, $nextProducts);
        }

        return $products;
    }
}
