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
		echo "<xmp style='font-family:tahoma, ����; font-size:12px;'>$str</xmp>";
		if($flag == true) exit;
}

// POST�� ������ �迭 ����
$postData = array(
   'ret_url' => 'www.gaplus.net:452/bin/sub/menu6/ga_menu6_01_ret_url_yoyul.php?scode='.$scode.'&carseq='.$carseq,
   'agent' => 'samsungkw2',
   'company' => 'hd',
   'user_code' => '4LU910',
   'jumin' => '8510041659511',
   'carnumber' => '11��8141',
   'caruse' => '1'
);


$headers = array(
  'Content-Type: application/x-www-form-urlencoded'
);

// CURL �ڵ� ����
$ch = curl_init();

// CURL �ɼ� ����
curl_setopt($ch, CURLOPT_URL, 'http://www.ibss-b.co.kr/car/gas_rate.php'); // POST ��û�� ���� URL ����
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // ������ ���ڿ��� ��ȯ
curl_setopt($ch, CURLOPT_POST, true); // POST ��û ����
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData)); // POST ������ ����
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // SSL ������ ���� ����
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // ȣ��Ʈ ���� ����
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

// ��û ������ ���� �ޱ�
$response = curl_exec($ch);

         codeView("========================");
         codeView(curl_errno($ch));
         codeView(curl_error($ch));
         codeView(curl_getinfo($ch));
         codeView($response);
         codeView("========================");

// CURL ���� ����
curl_close($ch);

// ���� ���
echo $response;

exit;
?>