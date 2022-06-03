<?php

namespace App\Utils;

use GuzzleHttp\Client;

/**
 * Documentation: https://smsaero.ru/description/api/
 */
class Sms
{
    public static function send(string $number, string $text)
    {
        if (app()->environment('local')) {
            return logger("SMS to {$number}: {$text}");
        }
        $email = config('services.sms-aero.email');
        $apiKey = config('services.sms-aero.api_key');
        $signature = config('services.sms-aero.signature');
        $channel = config('services.sms-aero.channel');

        $client = new Client([
            'base_uri' => sprintf('https://%s:%s@gate.smsaero.ru/v2/', $email, $apiKey),
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);

        $options = [
            'query' => [
                'channel' => $channel,
                'sign' => $signature,
                'number' => $number,
                'text' => $text,
            ]
        ];

        $response = $client->get('sms/send', $options);
        $body = json_decode((string) $response->getBody(), true);
        return $body['data'];
    }
}
