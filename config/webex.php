<?php

return [
    'url' => env('WEBEX_URL', 'https://webexapis.com/v1/messages'),
    'token' => env('WEBEX_TOKEN'),
    'room_id' => env('WEBEX_ROOM_ID'),
    'to_person_email' => env('WEBEX_TO_PERSON_EMAIL'),
];
