<?php

namespace App\Jobs;

use App\Models\User;
use GuzzleHttp\Pool;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Support\Facades\Log;

class BillUsersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $client = new Client();
        $users = User::cursor();

        $requests = function ($users) use($client) {
            $uri = 'http://newsletter-project.com/api/bill';
            foreach ($users as $user) {

                yield function() use ($client, $uri, $user) {
                    return $client->postAsync($uri, [
                        'json' => [
                            'username' => $user->username,
                            'amount' => $user->amount_to_bill,
                        ]
                    ]);
                };
                
            }

        };

        $pool = new Pool($client, $requests($users), [
            'concurrency' => 5,
            'fulfilled' => function (Response $response, $index) {
                // this is delivered each successful response
                Log::info($response->getBody());
                // may update billing history to successful
            },
            'rejected' => function (RequestException $reason, $index) {
                // this is delivered each failed request
                Log::error($reason->getMessage());
                // may update billing history to failed
            },
        ]);

        // Initiate the transfers and create a promise
        $promise = $pool->promise();

        // Force the pool of requests to complete.
        $promise->wait();
    }
}
