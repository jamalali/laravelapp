<?php

namespace App\Http\Controllers;

use App\Jobs\OmetriaEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TypeformController extends Controller
{
    public function signupEvent(Request $request)
    {

        $response = true;
        $oresponse = '';
        $comments = '';
        $person = array(
            "name" => '', 
            "email" => '', 
            "segment" => $request->input("form_response")['hidden']['segment'], 
            "subscription_store" => 'uk',
            "supplement_quiz_sum21" => ''
        );

        $answers = $request->input("form_response")['answers'];

        foreach($answers as $answer) {
            if($answer['type'] == 'text') {
                if(filter_var($answer['text'], FILTER_VALIDATE_EMAIL)) {
                    if(!$person['email']) {$person['email'] = $answer['text'];}
                } else {
                    if(!$person['name']) {$person['name'] = $answer['text'];}
                }
            }
        }

        $outcome = $request->input("form_response")['outcome']['id'];
        $outcomes = $request->input("form_response")['definition']['outcome']['choices'];
        
        $endings = $request->input("form_response")['hidden']['endings'];
        $endings = explode(",",trim($endings, '[]'));
        
        foreach($outcomes as $k => $possibleoutcome) {
            if ($possibleoutcome['id'] == $outcome) {
                $person['supplement_quiz_sum21'] = $endings[$k];
            }
        }

        //$comments .= 'name: '.$person['name'].' | email: '.$person['email'].' | segment: '.$person['segment'].' | subscription_store: '.$person['subscription_store'].' | supplement_quiz_sum21: '.$person['supplement_quiz_sum21'];

        if(!$person['email']) {
            $response = false;
        }

        $collection = 'AppAdded';

        if($response) {
            $event = array(
                'method' => 'post',
                'endpoint' => 'https://api.ometria.com/v2/contacts/' . $collection . '/' . trim($person['email']),
                'data' => array(
                    "@add_to_lists" => array($person['segment']),
                    "@collection" => $collection,
                    "@type" => "contact",
                    "email" => trim($person['email']),
                    "name" => trim($person['name']),
                    "marketing_optin" => (($request->input('marketing'))?"EXPLICITLY_OPTEDIN":null),
                    "subscription_store" => strtoupper($person['subscription_store']),
                    "supplement_quiz_sum21" => trim($person['supplement_quiz_sum21']),
                    "timestamp_subscribed" => date("Y-m-d\TH:i:s")
                ),
            );
            $comments .= "\n\n".' Dispatching...';
            OmetriaEvent::dispatch($event);
        } 
        
        return view('bis-signup-event', [
            'response' => $response ? 'true' : 'false',
            'comments' => $comments
        ]);
    }
}
