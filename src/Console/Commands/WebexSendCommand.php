<?php

namespace Fabamb\LaravelWebex\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;

class WebexSendCommand extends Command
{
    protected $signature = 'webex:send
        {message : Message text to send}
        {--room-id= : Webex room or space ID}
        {--to-person-email= : Webex recipient email address}
        {--token= : Webex access token override}
        {--file= : File path to attach to the message}
        {--markdown : Send the message as Markdown}';

    protected $description = 'Send a test message to a Webex room or person';

    public function handle(): int
    {
        $roomId = $this->option('room-id') ?? config('webex.room_id');
        $email = $this->option('to-person-email') ?? config('webex.to_person_email');

        if (($roomId === null) === ($email === null)) {
            $this->error('Specify exactly one of --room-id or --to-person-email.');

            return self::FAILURE;
        }

        $token = $this->option('token') ?? config('webex.token');

        if (! is_string($token) || $token === '') {
            $this->error('Webex notification token is not configured.');

            return self::FAILURE;
        }

        $file = $this->option('file');

        if ($file !== null && (! is_string($file) || ! is_readable($file))) {
            $this->error("Webex attachment '{$file}' is missing or unreadable.");

            return self::FAILURE;
        }

        $fields = [
            $roomId !== null ? 'roomId' : 'toPersonEmail' => $roomId ?? $email,
            $this->option('markdown') ? 'markdown' : 'text' => $this->argument('message'),
        ];

        $request = $file === null
            ? ['json' => $fields]
            : ['multipart' => [
                ...array_map(static fn (string $name, string $value): array => [
                    'name' => $name,
                    'contents' => $value,
                ], array_keys($fields), array_values($fields)),
                [
                    'name' => 'files',
                    'contents' => fopen($file, 'r'),
                    'filename' => basename($file),
                ],
            ]];

        try {
            $response = (new Client([
                'base_uri' => rtrim((string) config('webex.url'), '/'),
                'connect_timeout' => 5,
                'timeout' => 15,
                'headers' => [
                    'Authorization' => 'Bearer '.$token,
                    'Accept' => 'application/json',
                ],
            ]))->post('', $request);
        } catch (\Throwable $exception) {
            $this->error('Webex request failed: '.$exception->getMessage());

            return self::FAILURE;
        }

        if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
            $this->error('Webex returned HTTP '.$response->getStatusCode().'.');

            return self::FAILURE;
        }

        $this->info('Webex message sent successfully.');

        return self::SUCCESS;
    }
}
