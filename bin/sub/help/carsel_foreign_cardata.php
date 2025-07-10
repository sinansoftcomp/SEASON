<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");



//$code = $_POST['optVal'];
$code	=  iconv("UTF-8","EUCKR",$_POST['optVal']);
$carsub	=  iconv("UTF-8","EUCKR",$_POST['carsub']);


$sql="	select car_code, car_brand, car_sub, bae_gi, isnull(".$code.",0) amt,
			   car_part, hyoung_sik, car_grade, sport, 
			   people_num, fuel, hi_repair,
			   case when isnull(fracc,'') != '' then 'Y' else 'N' end fraccyn,
			   case when isnull(lineout,'') != '' then 'Y' else 'N' end lineoutyn,
			   case when isnull(connectcar,'') != '' then 'Y' else 'N' end connectcaryn 
		from cardtb(nolock) 
		where car_sub = '".$carsub."'  ";

$result  = sqlsrv_query($mscon, $sql);
$row =  sqlsrv_fetch_array($result); 


$car_code	= $row['car_code'];
$car_brand	= $row['car_brand'];
$car_sub	= $row['car_sub'];
$bae_gi		= $row['bae_gi'];
$amt		= $row['amt'];
$car_part	= $row['car_part'];

$hyoung_sik	= $row['hyoung_sik'];
$car_grade	= $row['car_grade'];
$sport		= $row['sport'];
$people_num	= $row['people_num'];
$fuel		= $row['fuel'];
$hi_repair	= $row['hi_repair'];

$fracc		= $row['fraccyn'];
$lineout	= $row['lineoutyn'];
$connectcar	= $row['connectcaryn'];

/* 배열담기 */
$rtnData	= array();

$rtnData['car_code']	= $car_code;
$rtnData['car_brand']	= iconv("EUCKR","UTF-8",$car_brand);

$rtnData['car_sub']		= iconv("EUCKR","UTF-8",$car_sub);
$rtnData['bae_gi']		= $bae_gi;
$rtnData['amt']			= $amt;
$rtnData['car_part']	= iconv("EUCKR","UTF-8",$car_part);

$rtnData['hyoung_sik']	= $hyoung_sik;
$rtnData['car_grade']	= $car_grade;
$rtnData['sport']		= $sport;
$rtnData['people_num']	= $people_num;
$rtnData['fuel']		= $fuel;
$rtnData['hi_repair']	= $hi_repair;


$rtnData['fracc']		= $fracc;
$rtnData['lineout']		= $lineout;
$rtnData['connectcar']	= $connectcar;


echo json_encode($rtnData);
?>