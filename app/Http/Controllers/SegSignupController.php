<?php

namespace App\Http\Controllers;

use App\Jobs\OmetriaEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SegSignupController extends Controller
{
    public function signupEvent(Request $request)
    {

        $response = true;
        $oresponse = '';
        $comments = '';
        $segment = $request->input('segment');

        if (
            !filter_var($request->input('email'), FILTER_VALIDATE_EMAIL)
            or !$segment
        ) {
            $response = false;
            if(!$segment) {$comments .= ' Couldn\'t see which segment to use ';}
        } else {
            $collection = 'AppAdded';
            
            $event = array(
                'method' => 'post',
                'endpoint' => 'https://api.ometria.com/v2/contacts/' . $collection . '/' . $request->input('email'),
                'data' => array(
                    "@add_to_lists" => array($segment),
                    "@collection" => $collection,
                    "@type" => "contact",
                    "email" => $request->input('email'),
                    "marketing_optin" => (($request->input('marketing'))?"EXPLICITLY_OPTEDIN":null),
                    "subscription_store" => strtoupper($request->input('store')),
                    "timestamp_subscribed" => date("Y-m-d\TH:i:s")
                ),
            );
            $comments .= ' Dispatching... ';
            OmetriaEvent::dispatch($event);
        }
        
        return view('bis-signup-event', [
            'response' => $response ? 'true' : 'false',
            'comments' => $comments
        ]);
    }
}
