<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php');

use Google\Auth\Credentials\ServiceAccountCredentials;

function getAccessToken()
{
    $credentials = new ServiceAccountCredentials(
        'https://www.googleapis.com/auth/cloud-platform',
        __DIR__ . '/gaplus-9e260-firebase-adminsdk-t25w3-c6f8cc7218.json'
    );

    $credentials->fetchAuthToken();
    return $credentials->getLastReceivedToken()['access_token'];
}

function sendPushNotifications($notifications)
{
    $accessToken = getAccessToken();
    $projectId = 'gaplus-9e260';

    $mh = curl_multi_init();
    $chArray = [];

    foreach ($notifications as $notification) {
        $message = [
            'message' => [
                'token' => $notification['to'],
                'notification' => [
                    'title' => $notification['title'],
                    'body' => $notification['body'],
                ],
                'data' => $notification['data'],
            ],
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/v1/projects/' . $projectId . '/messages:send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 500);
        curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        curl_multi_add_handle($mh, $ch);
        $chArray[] = $ch;
    }

    $running = null;
    do {
        curl_multi_exec($mh, $running);
    } while ($running);

    foreach ($chArray as $ch) {
        curl_multi_remove_handle($mh, $ch);
    }
    curl_multi_close($mh);
	//return 0;
}
?>