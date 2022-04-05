<?php

namespace App\Console\Commands;

use App\Jobs\OmetriaEvent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class NotifyBisContacts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifycustomers:bis';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify customers of products back in stock';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $debug = false;
        $collection = env('OMETRIA_BIS_COLLECTION_NAME', false);
        $segment = config('vivo.ometria.bis_segment_id');
        $oresponse = '';
        $count = array(
            'products-in-stock' => 0,
            'customers-waiting' => 0,
            'removed-from-ometria' => 0,
            'emails-sent' => 0,
        );
        $q = "select * from variants where inventory_quantity > 50";
        if ($debug) {echo $q . "\n";}
        $products = DB::select($q);
        foreach ($products as $variant) {
            $count['products-in-stock']++;
            $q = "select * from bis_customers where variant = '" . $variant->id . "' order by created_at asc limit 0,15";
            if ($debug) {echo $q . "\n";}
            $customers = DB::select($q);
            foreach ($customers as $customer) {
                $count['customers-waiting']++;
                if ($debug) {echo 'PID: ' . $customer->store . ':' . $customer->product . '_' . $variant->id . "\n";}
                switch($variant->country) {
                    case '':
                    case 'uk':
                        $currencypre = '£';
                        $currencypost = '';
                    break;
                    case 'us':
                        $currencypre = '$';
                        $currencypost = '';
                    break;
                    default:
                        $currencypre = '€';
                        $currencypost = '';
                    break;
                }
                $event = array(
                    'method' => 'post',
                    'endpoint' => 'https://api.ometria.com/v2/custom-events/',
                    'data' => array(
                        "timestamp" => date("Y-m-d\TH:i:s"),
                        "@type" => "custom_event",
                        "event_type" => "back_in_stock",
                        "identity_email" => $customer->email,
                        "id" => "BackInStock-" . $customer->id,
                        "properties" => array(
                            "pid" => array($variant->country . ':' . $customer->product . '_' . $variant->id),
                            "price" => $currencypre.$variant->price.$currencypost
                        ),
                    ),
                );
                OmetriaEvent::dispatch($event);

                $count['emails-sent']++;

                DB::table('bis_customers')->delete($customer->id);
                if (DB::table('bis_customers')->where('email', '=', $customer->email)->count() == 0) {
                    $count['removed-from-ometria']++;
                    $event = array(
                        'method' => 'post',
                        'endpoint' => 'https://api.ometria.com/v2/contacts/' . $collection . '/' . $customer->id,
                        'data' => array(
                            "@remove_from_lists" => array($segment),
                            "@collection" => $collection,
                            "@type" => "contact",
                            "email" => $customer->email,
                        ),
                    );
                    OmetriaEvent::dispatch($event);
                }
            }
        }

        foreach ($count as $k => $v) {
            echo $k . ': ' . $v . "\r\n";
        }
    }
}
