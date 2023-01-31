<?php

namespace Pterodactyl\Listeners;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Pterodactyl\Events\User\EmailChange;

class EmailChangeWebhook
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param \Pterodactyl\Events\EmailChange $event
     *
     * @return void
     */
    public function handle(EmailChange $event)
    {
        try {
            $url = env('APP_WEBHOOK_API');
            $token = env('APP_WEBHOOK_TOKEN');

            if (is_null($url) || is_null($token)) {
                throw new \Exception('No URL or TOKEN');
            }

            $data = json_encode([
                'email' => $event->email,
                'uuid' => $event->uuid,
            ]);

            Http::withBody($data, 'application/json')->withHeaders([
                'Content-Length: ' . strlen($data),
                'Authorization: Bearer ' . $token,
            ])->post($url);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
}
