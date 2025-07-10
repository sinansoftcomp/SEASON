<?

require_once($_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php');


use Google\Auth\Credentials\ServiceAccountCredentials;

// 구글 AOuth 2.0인증 토큰 생성
function getAccessToken()
{
    $credentials = new ServiceAccountCredentials(
        'https://www.googleapis.com/auth/cloud-platform',
        __DIR__ . '/gaplus-9e260-firebase-adminsdk-t25w3-c6f8cc7218.json'
    );

    $credentials->fetchAuthToken();
    return $credentials->getLastReceivedToken()['access_token'];
}


// 메시지전송
function sendPushNotification($to, $title, $body, $data = [])
{
	$accessToken = getAccessToken();
	$projectId = 'gaplus-9e260';		// 파이어베이스 프로젝트ID

	$message = [
		'message' => [
			'token' => $to,
			'notification' => [
				'title' => $title,
				'body' => $body,
			],
			'data' => $data,
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

    curl_setopt($ch, CURLOPT_TIMEOUT_MS, 500); // Set timeout to 500 milliseconds
    curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);

	$result = curl_exec($ch);
	if ($result === FALSE) {
		die('FCM Send Error: ' . curl_error($ch));
	}

	curl_close($ch);

	return $result;
}

?>