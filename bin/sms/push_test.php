<?

		/* 안드로이드 푸쉬 테스트 */
		$url = 'https://fcm.googleapis.com/fcm/send';

		$headers = array (
		'Authorization: key=AAAAAInfrFk:APA91bEA19E7hG-RFycOwbAzLCJk9hKClWj1YqzadXD378UdXAeIcq1JudF2xiyTICOixnWHM1xXi1GoqJ-UK0H9GCBXHmx0UaWA3QUiHnzEW-dkuq_5WiDA9K2XS3JWPIaweAxqnqXm',
		'Content-Type: application/json'
		);



			$fields = array ( 
			'data' => array ("message" => iconv("euckr","utf-8","push test")),
			'data' => array ("body" => iconv("euckr","utf-8","22222222222222222222"))
			);

			$fields['to'] = 'f_lpAgmzQfOVlal37QJUCD:APA91bEZxhc1LqxKwJa2H2U1jWJyKRKP4wFM0Xb92PUy7We0muuo2B33PsTil3WfK5oJRtqZk4LfD-BnqZdggGOOGQNhJ-elAqjU1qNFcvWMN88IU43llJ4moua6tuU0x2ZWpwsDGA34';


			$fields['priority'] = "high";

			$fields = json_encode ($fields);

			$ch = curl_init ();
			curl_setopt ( $ch, CURLOPT_URL, $url );
			curl_setopt ( $ch, CURLOPT_POST, true );
			curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
			curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_setopt ( $ch, CURLOPT_POSTFIELDS, $fields );

			$result = curl_exec ( $ch );
			if ($result === FALSE) {
			//die('FCM Send Error: ' . curl_error($ch));
			} 

			$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		
			echo "HTTP Code: $httpcode\n";
			
		
		curl_close($ch);

?>