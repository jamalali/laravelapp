<?php

namespace App\Http\Controllers;

use App\Jobs\OmetriaEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class BisSignupController extends Controller
{
    public function signupEvent(Request $request)
    {

        $response = true;
        $oresponse = '';
        $comments = '';
        $segment = config('vivo.ometria.bis_segment_id');
        
        
        //env('OMETRIA_BIS_SEGMENT_ID', false);
        if (
            !filter_var($request->input('email'), FILTER_VALIDATE_EMAIL)
            or !ctype_alnum($request->input('store'))
            or !filter_var($request->input('product'), FILTER_VALIDATE_INT)
            or !filter_var($request->input('variant'), FILTER_VALIDATE_INT)
            or !$segment
        ) {
            $response = false;
            if(!$segment) {$comments .= ' Couldn\'t see which segment to use ';}
        } else {
            if (!DB::select("select * from bis_customers where email = '" . $request->input('email') . "' and variant = '" . $request->input('variant') . "'")) {
                if (
                    !$customer_id = DB::table('bis_customers')->insertGetId(
                        array(
                            'email' => $request->input('email'),
                            'store' => $request->input('store'),
                            'product' => $request->input('product'),
                            'variant' => $request->input('variant'),
                        )
                    )
                ) {
                    $response = false;
                    $comments .= ' Couldn\'t add to database ';
                } else {
                    $comments .= ' Added to database ';
                    $collection = config('vivo.ometria.bis_collection');
                    if(!$collection) {$comments .= ' Couldn\'t add to database ';} else {
                    $event = array(
                        'method' => 'post',
                        'endpoint' => 'https://api.ometria.com/v2/contacts/' . $collection . '/' . $request->input('email'),
                        'data' => array(
                            "@add_to_lists" => array($segment),
                            "@collection" => $collection,
                            "@type" => "contact",
                            "email" => $request->input('email'),
                            "marketing_optin" => null,
                            "subscription_store" => strtoupper($request->input('store'))
                        ),
                    );
                    $comments .= ' Dispatching... ';
                    OmetriaEvent::dispatch($event);
                    

                }
                }
            } else {
                DB::update("update bis_customers set updated_at='" . date('Y-m-d H:i:s') . "' where email = '" . $request->input('email') . "' and variant = '" . $request->input('variant') . "'");
                $comments .= ' Already in database ';
            }
        }
        
        return view('bis-signup-event', [
            'response' => $response ? 'true' : 'false',
            'comments' => $comments
        ]);
    }
}
