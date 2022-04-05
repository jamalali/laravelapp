<?php

namespace App\Jobs;

use App\Jobs\Middleware\OmetriaPushApiRateLimit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OmetriaEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    protected $response;
    protected $methodEndpoint;
    protected $eventMethod;

    public $tries = 5;
    public $backoff = 3;

    public function __construct($event)
    {
        $this->data = $event['data'];
        $this->eventMethod = $event['method'];
        $this->methodEndpoint = $event['endpoint'];
        $this->onQueue('ometria-push');
    }

    public function middleware()
    {
        return [new OmetriaPushApiRateLimit];
    }

    public function handle()
    {
        $apikey = config('vivo.ometria.api_key');

        if ($apikey) {
            switch ($this->eventMethod) {
                case 'post':
                    $response = Http::withHeaders([
                        'X-Ometria-Auth' => config('vivo.ometria.api_key'),
                    ])->post($this->methodEndpoint, $this->data);
                    $status = $response->status();
                    break;
                case 'get':
                    $response = Http::withHeaders([
                        'X-Ometria-Auth' => config('vivo.ometria.api_key'),
                    ])->get($this->methodEndpoint, $this->$data);
                    $status = $response->status();
                    break;
            }
            Log::info('Ometria Call', [
                'status' => $status,
                'json' => $response->json(),
            ]);
            return $status;

        } else {
            return false;
        }
    }
}
