<?php

namespace App\Jobs\Middleware;

use Illuminate\Support\Facades\Redis;

class OmetriaPushApiRateLimit {

    public function handle($job, $next) {

        Redis::throttle('allow4every1second')
                ->block(5)->allow(4)->every(1)
                ->then(function () use ($job, $next) {
                    // Lock obtained...

                    $next($job);
                }, function () use ($job) {
                    // Could not obtain lock...

                    $job->release(60);
                });
    }
}