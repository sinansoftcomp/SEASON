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


function sendPushNotification($to, $title, $body) {
	
    $accessToken = getAccessToken();

    // 메시지 구성
    $message = [
        'message' => [
            'token' => $to,
            'notification' => [
                'title' => $title,
                'body' => $body
            ]
        ]
    ];

    // FCM API 엔드포인트
    $url = 'https://fcm.googleapis.com/v1/projects/YOUR_PROJECT_ID/messages:send';

    $headers = [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json'
    ];

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));

    $result = curl_exec($ch);
    if ($result === FALSE) {
        die('Curl failed: ' . curl_error($ch));
    }

    curl_close($ch);

    return $result;
}

// 테스트용 기기 토큰
$deviceToken = "f_lpAgmzQfOVlal37QJUCD:APA91bEZxhc1LqxKwJa2H2U1jWJyKRKP4wFM0Xb92PUy7We0muuo2B33PsTil3WfK5oJRtqZk4LfD-BnqZdggGOOGQNhJ-elAqjU1qNFcvWMN88IU43llJ4moua6tuU0x2ZWpwsDGA34";
$title = "Test Notification";
$body = "This is a test message.";


$response = sendPushNotification($deviceToken, $title, $body);
echo $response;
?>
