<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsShopify {

    public function handle(Request $request, Closure $next) {

        $hmac_header = $request->server('HTTP_X_SHOPIFY_HMAC_SHA256');

        $data = $request->getContent();
        $country = $request->route('country');

        $verified = $this->_verifyWebhook($data, $hmac_header, $country);

        if (!$verified) {
           abort(403);
        }

        return $next($request);
    }

    private function _verifyWebhook($data, $hmac_header, $country) {
        $secret = config('vivo.shopify.'.$country.'.webhook_secret');

        $calculated_hmac = base64_encode(hash_hmac('sha256', $data, $secret, true));
        return hash_equals($hmac_header, $calculated_hmac);
    }
}