<?php

namespace App\Jobs;

use App\Jobs\Middleware\OmetriaPushApiRateLimit;
use App\Models\Variant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class PostVariantToOmetria implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $variant;
    public $country;

    public $backoff = 60;
    public $tries = 10;

    public function __construct(Variant $variant, Request $request)
    {
        $this->variant = $variant;
        $this->country = $request->route('country');
        
        $this->onQueue('ometria-push');
    }

    public function middleware()
    {
        return [new OmetriaPushApiRateLimit];
    }

    public function handle()
    {
        
        $om_id = $this->variant->getOmetriaId($this->country);
        $product = $this->variant->product;

        $options = [];

        if ($product['option_1']) {$options[$product['option_1']] = $this->variant['option_1'];}
        if ($product['option_2']) {$options[$product['option_2']] = $this->variant['option_2'];}
        if ($product['option_3']) {$options[$product['option_3']] = $this->variant['option_3'];}

        $price = $this->variant->price;
        $sku = $this->variant->sku;
        $title = $product->title . ' | ' . $this->variant->title;
        $image_url = $this->variant->image_src ? $this->variant->image_src : '';
        $handle = $this->variant->handle;

        $response = Http::withHeaders([
            'X-Ometria-Auth' => config('vivo.ometria.api_key'),
        ])->post('https://api.ometria.com/v2/products/' . $om_id, [
            '@type' => 'product',
            'id' => $om_id,
            'is_active' => true,
            'is_variant' => true,
            'price' => $price,
            'currency' => config('vivo.shopify.'.$this->country.'.currency'),
            'url' => 'https://'.config('vivo.shopify.'.$this->country.'.front') . (($handle) ? '/products/' . $handle : ''),
            'properties' => $options,
            'sku' => $sku,
            'title' => $title,
            'image_url' => $image_url,
            'listings' => [[
                'title' => $title,
                "is_active" => true,
                'currency' => config('vivo.shopify.'.$this->country.'.currency'),
                'price' => $price,
                'url' => 'https://'.config('vivo.shopify.'.$this->country.'.front') . (($handle) ? '/products/' . $handle : ''),
                'image_url' => $image_url,
                "store" => config('vivo.shopify.'.$this->country.'.front')
            ]]
        ]);

        $status = $response->status();

        switch ($status) {
            case 201:
                Log::info('Variant pushed to Ometria sucessfully', [
                    '@type' => 'product',
                    'id' => $om_id,
                    'is_active' => true,
                    'is_variant' => true,
                    'price' => $price,
                    'currency' => config('vivo.shopify.'.$this->country.'.currency'),
                    'url' => 'https://'.config('vivo.shopify.'.$this->country.'.front') . (($handle) ? '/products/' . $handle : ''),
                    'properties' => $options,
                    'sku' => $sku,
                    'title' => $title,
                    'image_url' => $image_url,
                ]);
                break;

            default:
                Log::error('Variant NOT pushed to Ometria', [
                    '@type' => 'product',
                    'id' => $om_id,
                    'is_active' => true,
                    'is_variant' => true,
                    'price' => $price,
                    'currency' => config('vivo.shopify.'.$this->country.'.currency'),
                    'url' => 'https://'.config('vivo.shopify.'.$this->country.'.front') . (($handle) ? '/products/' . $handle : ''),
                    'properties' => $options,
                    'sku' => $sku,
                    'title' => $title,
                    'image_url' => $image_url,
                ]);
        }
    }

    public function failed(Throwable $exception) {

        $msg    = $exception->getMessage();
        $om_id  = $this->variant->getOmetriaId($this->country);

        Log::error($msg, [
            'variant' => $om_id
        ]);

        $this->release(120);
    }
}
