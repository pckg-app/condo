<?php namespace Condo\Middleware;

use Condo\Record\Webhook;

class LogWebhookRequest
{

    public function execute(callable $next)
    {
        Webhook::create([
                            'created_at' => date('Y-m-d H:i:s'),
                            'data'       => json_encode(post()->all()),
                            'ip'         => server('REMOTE_ADDR'),
                        ]);

        return $next;
    }

}