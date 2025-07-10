<?php
require 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;

$client = new Client();
$url = 'https://fcm.googleapis.com/fcm/send';
$headers = [
    'Authorization' => 'key=AAAAAInfrFk:APA91bEA19E7hG-RFycOwbAzLCJk9hKClWj1YqzadXD378UdXAeIcq1JudF2xiyTICOixnWHM1xXi1GoqJ-UK0H9GCBXHmx0UaWA3QUiHnzEW-dkuq_5WiDA9K2XS3JWPIaweAxqnqXm',
    'Content-Type' => 'application/json'
];

$fields = [
    'data' => [
        'message' => iconv("euckr", "utf-8", "push test"),
        'body' => iconv("euckr", "utf-8", "22222222222222222222")
    ],
    'to' => 'fVaR9WyS0BI:APA91bF1uihOM8C9hE90eSATCSwBV_DgWXhomdEQSQ4XTQUce38TCxwocN__JANG8UWATLg9aKOfr6EjObqqskfO-KH7qfGGfoRBkz-D0_yK749XnnjsC-P34tJff5ZshYLrXBkpRwOZ',
    'priority' => 'high'
];

$request = new Request('POST', $url, $headers, json_encode($fields));

$promise = $client->sendAsync($request)->then(
    function ($response) {
        echo 'HTTP Code: ' . $response->getStatusCode() . "\n";
        echo 'Response: ' . $response->getBody() . "\n";
    },
    function (RequestException $e) {
        echo 'Request failed: ' . $e->getMessage() . "\n";
    }
);

$promise->wait();