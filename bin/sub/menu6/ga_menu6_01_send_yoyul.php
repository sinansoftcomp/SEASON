<?
$loginCheck	= false;

include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

$scode	= $_GET['scode'];
$carseq	= $_GET['carseq'];

function codeView($var,$flag = false) {
		ob_start();
		print_r($var);
		$str = ob_get_contents();
		ob_end_clean();
		echo "<xmp style='font-family:tahoma, 굴림; font-size:12px;'>$str</xmp>";
		if($flag == true) exit;
}

// POST할 데이터 배열 생성
$postData = array(
   'ret_url' => 'www.gaplus.net:452/bin/sub/menu6/ga_menu6_01_ret_url_yoyul.php?scode='.$scode.'&carseq='.$carseq,
   'agent' => 'samsungkw2',
   'company' => 'hd',
   'user_code' => '4LU910',
   'jumin' => '8510041659511',
   'carnumber' => '11라8141',
   'caruse' => '1'
);


$headers = array(
  'Content-Type: application/x-www-form-urlencoded'
);

// CURL 핸들 생성
$ch = curl_init();

// CURL 옵션 설정
curl_setopt($ch, CURLOPT_URL, 'http://www.ibss-b.co.kr/car/gas_rate.php'); // POST 요청을 보낼 URL 설정
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 응답을 문자열로 반환
curl_setopt($ch, CURLOPT_POST, true); // POST 요청 설정
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData)); // POST 데이터 설정
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // SSL 인증서 검증 무시
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 호스트 검증 무시
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

// 요청 보내고 응답 받기
$response = curl_exec($ch);

         codeView("========================");
         codeView(curl_errno($ch));
         codeView(curl_error($ch));
         codeView(curl_getinfo($ch));
         codeView($response);
         codeView("========================");

// CURL 세션 종료
curl_close($ch);

// 응답 출력
echo $response;

exit;
?>