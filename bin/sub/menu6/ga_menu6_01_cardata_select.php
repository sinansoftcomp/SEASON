<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

$carcode	=  iconv("UTF-8","EUCKR",$_POST['carcode']);
$ipyear		=  iconv("UTF-8","EUCKR",$_POST['caryear']);
$cardate	=  str_replace("-" , "" , iconv("UTF-8","EUCKR",$_POST['cardate']));


function codeView($var,$flag = false) {
		ob_start();
		print_r($var);
		$str = ob_get_contents();
		ob_end_clean();
		echo "<xmp style='font-family:tahoma, 굴림; font-size:12px;'>$str</xmp>";
		if($flag == true) exit;
}

$data = "";
$sql="	select 
			max(year) year, 
			max(quater) quater,
			max(y00) y00,
			max(y01) y01,
			max(y02) y02,
			max(y03) y03,
			max(y04) y04,
			max(y05) y05,
			max(y06) y06,
			max(y07) y07,
			max(y08) y08,
			max(y09) y09,
			max(y10) y10,
			max(y11) y11,
			max(y12) y12,
			max(y13) y13,
			max(y14) y14,
			max(y15) y15,
			max(y16) y16,
			max(y17) y17,
			max(y18) y18,
			max(y19) y19,
			max(y20) y20,
			max(y21) y21,
			max(y22) y22,
			max(y23) y23,
			max(y24) y24,
			max(y25) y25,
			max(y26) y26,
			max(y27) y27,
			max(y28) y28,
			max(y29) y29,
			max(y30) y30,
			max(y31) y31,
			max(y32) y32,
			max(y33) y33
		from cardta where  car_code = '".$carcode."'  ";

$result= sqlsrv_query( $mscon, $sql );
while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC)){

	/*
		1/2분기는 전년도가 A/B로 구분
		3/4분기는 해당년도 A/B로 구분
	*/

	$i = 0;
	$j = 0;			// 2022년부터 1을 더 빼기위한
	$gubun = "A";	// 초기값
	$yy =  date("Y");

	if($row['quater'] == '1' || $row['quater'] == '2'){
		$byy = $yy - 1;
		$b2yy = $yy - 2;
	}else{
		$byy = $yy;
		$b2yy = $yy - 1;	
	}
	
	while($i<=32){

		if(mb_strlen($i)==1){
			$ii = '0'.$i;
		}else{
			$ii = $i;
		}

		$ydata	= 'y'.$ii;
		$caryear= $yy-$i+$j;

		// 2023년이 A/B로 구분되어 있기에 처리 (2024-2023A-2023B-2022-2021..) 순서
		if($caryear == $byy && $gubun == 'A'){
			$caryear = $byy.$gubun;
			$gubun = "B";
		}else if($caryear == $b2yy && $gubun == 'B'){
			$caryear = $byy.$gubun;
			$gubun = "A";	// 초기화
			$j = 1;
		}

		if($caryear == $ipyear){
			$selyear	=	$ydata;
		}

		$i++;
	}

}

/*
	gubun, car_code, car_grade, bae_gi, caryeartxt, cardate, hyoung_sik, car_sub, amt, fuel, hi_repair, car_part, people_num, sport
	bit:A-승용차, B-외제차, C-승합/화물차, D-임시코드
*/
$sql="

select bit,			car_code,		car_brand,	car_sub,	car_grade,	people_num,
	  bae_gi,		car_part,	amt,		hyoung_sik,	fuel,
	  hi_repair,	sport,		fraccyn,	lineoutyn,	connectcaryn
from(
	select 'A' bit, car_code, '' car_brand, car_sub, car_grade, people_num, bae_gi, car_part, isnull(".$selyear.",0) amt,
		   hyoung_sik, fuel, hi_repair, '' sport,
		   case when fracc <= ".$cardate." then 'Y' else 'N' end fraccyn,
		   case when lineout <= ".$cardate." then 'Y' else 'N' end lineoutyn,
		   case when connectcar <= ".$cardate." then 'Y' else 'N' end connectcaryn
	from cardta(nolock)
	where car_code = '".$carcode."'
	
	union all

	select 'B' bit, car_code, car_brand, car_sub, car_grade, people_num, bae_gi, car_part, isnull(".$selyear.",0) amt,
		   hyoung_sik, fuel, hi_repair, sport, 		   
			   case when isnull(fracc,'') != '' then 'Y' else 'N' end fraccyn,
			   case when isnull(lineout,'') != '' then 'Y' else 'N' end lineoutyn,
			   case when isnull(connectcar,'') != '' then 'Y' else 'N' end connectcaryn 
	from cardtb(nolock)
	where car_sub = '".$carsub."' 

	union all

	select 'C' bit, car_code, '' car_brand, '' car_sub, '' car_grade, people_num, '' bae_gi, car_part, isnull(".$selyear.",0) amt,
		   hyoung_sik, fuel, '' hi_repair, '' sport,
		   case when fracc <= ".$cardate." then 'Y' else 'N' end fraccyn,
		   case when lineout <= ".$cardate." then 'Y' else 'N' end lineoutyn,
		   case when connectcar <= ".$cardate." then 'Y' else 'N' end connectcaryn 
	from cardtc(nolock)
	where car_code = '".$carcode."'

	union all

	select 
			'D' bit, car_code, '' car_brand, car_nm car_sub, car_grade, '' people_num, '' bae_gi, '' car_part, 0 amt,
			'' hyoung_sik, '' fuel, hi_repair, '' sport,
			'N' fraccyn, 'N' lineoutyn, 'N' connectcaryn	
	from cardtd(nolock)
	Where car_code = '".$carcode."'
	  and gubun = '2'

) tbl

	";

/*
echo '<pre>';
echo $sql;
echo '</pre>';
*/

$result= sqlsrv_query( $mscon, $sql );
$row =  sqlsrv_fetch_array($result); 

/* 배열담기 */
$rtnData	= array();

$bit		= $row['bit'];
$car_code	= $row['car_code'];
$car_brand	= $row['car_brand'];
$car_sub	= $row['car_sub'];
$car_grade	= $row['car_grade'];
$people_num	= $row['people_num'];

$bae_gi		= $row['bae_gi'];
$car_part	= $row['car_part'];
$amt		= $row['amt'];
$hyoung_sik	= $row['hyoung_sik'];
$fuel		= $row['fuel'];

$hi_repair	= $row['hi_repair'];
$sport		= $row['sport'];
$fracc		= $row['fraccyn'];
$lineout	= $row['lineoutyn'];
$connectcar	= $row['connectcaryn'];


$rtnData['gubun']		= $bit;
$rtnData['car_code']	= $car_code;
$rtnData['car_brand']	= iconv("EUCKR","UTF-8",$car_brand);
$rtnData['car_sub']		= iconv("EUCKR","UTF-8",$car_sub);
$rtnData['car_grade']	= $car_grade;
$rtnData['people_num']	= $people_num;

$rtnData['bae_gi']		= $bae_gi;
$rtnData['car_part']	= iconv("EUCKR","UTF-8",$car_part);
$rtnData['amt']			= $amt;
$rtnData['hyoung_sik']	= $hyoung_sik;
$rtnData['fuel']		= $fuel;

$rtnData['sport']		= $sport;
$rtnData['hi_repair']	= $hi_repair;
$rtnData['fracc']		= $fracc;
$rtnData['lineout']		= $lineout;
$rtnData['connectcar']	= $connectcar;

echo json_encode($rtnData);
?>