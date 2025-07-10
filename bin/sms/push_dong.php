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

	$result = curl_exec($ch);
	if ($result === FALSE) {
		die('FCM Send Error: ' . curl_error($ch));
	}

	curl_close($ch);

	return $result;
}


// 예시 사용법
$to = 'euN7WEfOR_WzZfcwIy-tiZ:APA91bFcHLhJkt1SbVgQbgi4vCD-j0pCO9c3-u1J0-F13IjUw4OSgWFnv90UMF-x0NQuajcQlVQGWIoGumZP36vc1J5yKhfMkus_-Zyz8T6iKwpmLPwn9cEwlJFIlUCYQ4KshdNViZfX';
//$to = 'f_lpAgmzQfOVlal37QJUCD:APA91bEZxhc1LqxKwJa2H2U1jWJyKRKP4wFM0Xb92PUy7We0muuo2B33PsTil3WfK5oJRtqZk4LfD-BnqZdggGOOGQNhJ-elAqjU1qNFcvWMN88IU43llJ4moua6tuU0x2ZWpwsDGA34';
$title = iconv("EUC-KR","UTF-8","안녕");
$body = 'This is a test push';
$data = ['url' => 'https://gaplus.net:452/bin/submobile/m_main_post_dt.php?pid=P18&device=app', 'key2' => 'value2'];
//$data = ['url' => 'http://naver.com', 'key2' => 'value2'];

$response = sendPushNotification($to, $title, $body, $data);

echo $response;


?>