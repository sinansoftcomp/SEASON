<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

//$code = $_POST['optVal'];
$code	=  iconv("UTF-8","EUCKR",$_POST['optVal']);

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
		from cardtc where  car_nm = '".$code."'  ";

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

		// 해당년도에 값이 있을때만 
		if((int)$row[$ydata] > 0){
			
			$data=$data.'<option value="'.$ydata.'">'.$caryear.'</option>';
		}

		$i++;
	}

}


echo $data;
?>