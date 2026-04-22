<?php

require __DIR__.'/../vendor/autoload.php';

use GuzzleHttp\Client;

$client = new Client(['base_uri' => 'http://localhost:8000', 'http_errors' => false]);

echo "--- TESTING LOGIN ---\n";
$response = $client->post('/api/login', [
    'json' => [
        'email' => 'admin@gmail.com',
        'password' => 'password123'
    ]
]);

$body = json_decode($response->getBody(), true);
print_r($body);

if (isset($body['data']['token'])) {
    $token = $body['data']['token'];
    echo "\n--- TESTING ME (WITH TOKEN) ---\n";
    $responseMe = $client->get('/api/me', [
        'headers' => [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ]
    ]);
    print_r(json_decode($responseMe->getBody(), true));

    echo "\n--- TESTING LOGOUT ---\n";
    $responseLogout = $client->post('/api/logout', [
        'headers' => [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ]
    ]);
    print_r(json_decode($responseLogout->getBody(), true));
}
