<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

$inscode = $_POST['inscode'];

$ksuname = array();
$gsuname = array();


for($i = 1; $i <= 20; $i++){
	$ksuname[$i] = iconv("UTF-8","EUC-KR",$_POST['ksuname'.(string)$i]);
}
for($i = 1; $i <= 15; $i++){
	$gsuname[$i] = iconv("UTF-8","EUC-KR",$_POST['gsuname'.(string)$i]);
}


//----------------------------------------------------------//
//                    저장시 처리요직							// 
//----------------------------------------------------------//
if($_POST['type']=='save'){
	// 필수정보 확인
	if($_SESSION['S_SCODE'] == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '세센연결 error 필수입력값 오류, 재 로그인해주세요.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}


	for($i=1; $i<=20; $i++){
		$sql = "
				update suname_set
				set suname = '".$ksuname[$i]."' , useyn = '".$_POST['kuseyn'.$i]."'
				where scode = '".$_SESSION['S_SCODE']."' and sucode = '".$_POST['kamt'.$i]."'
				";
		$result =  sqlsrv_query( $mscon, $sql );
		
		if ($result == false){
			sqlsrv_query($mscon,"ROLLBACK");
			sqlsrv_free_stmt($result);
			sqlsrv_close($mscon);

			$message = ' 수수료 명칭 저장 중 오류_1';
			$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
			echo json_encode($returnJson);
			exit;
		}
	}

	for($i=1; $i<=15; $i++){
		$sql = "
				update suname_set
				set suname = '".$gsuname[$i]."' , useyn = '".$_POST['guseyn'.$i]."'
				where scode = '".$_SESSION['S_SCODE']."' and sucode = '".$_POST['gamt'.$i]."'
				";
		$result =  sqlsrv_query( $mscon, $sql );
		
		if ($result == false){
			sqlsrv_query($mscon,"ROLLBACK");
			sqlsrv_free_stmt($result);
			sqlsrv_close($mscon);

			$message = ' 수수료 명칭 저장 중 오류_2';
			$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
			echo json_encode($returnJson);
			exit;
		}
	}

    sqlsrv_query($mscon,"COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' 수수료 명칭을 저장하였습니다.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "" , "inscode" => "$inscode", "rtype" => "save");
	echo json_encode($returnJson);
	exit;

}

//----------------------------------------------------------//
//                    초기화시 처리요직							// 
//----------------------------------------------------------//
if($_POST['type']=='reset'){
	// 필수정보 확인
	if($_SESSION['S_SCODE'] == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '세센연결 error 필수입력값 오류, 재 로그인해주세요.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}


	for($i=1; $i<=20; $i++){
		if($i==1){
			$sql = "
					update suname_set
					set suname = '신계약수수료' , useyn = 'Y'
					where scode = '".$_SESSION['S_SCODE']."' and sucode = '".$_POST['kamt'.$i]."'
					";
		}else if($i==2) {
			$sql = "
					update suname_set
					set suname = '유지수수료' , useyn = 'Y'
					where scode = '".$_SESSION['S_SCODE']."' and sucode = '".$_POST['kamt'.$i]."'
					";
		}else{
			$sql = "
					update suname_set
					set suname = '' , useyn = 'N'
					where scode = '".$_SESSION['S_SCODE']."' and sucode = '".$_POST['kamt'.$i]."'
					";
		}
		$result =  sqlsrv_query( $mscon, $sql );
		
		if ($result == false){
			sqlsrv_query($mscon,"ROLLBACK");
			sqlsrv_free_stmt($result);
			sqlsrv_close($mscon);

			$message = ' 수수료 명칭 초기화 중 오류_1';
			$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
			echo json_encode($returnJson);
			exit;
		}
	}

	for($i=1; $i<=15; $i++){

		if($i==1){
			$sql = "
					update suname_set
					set suname = '소득세' , useyn = 'Y'
					where scode = '".$_SESSION['S_SCODE']."' and sucode = '".$_POST['gamt'.$i]."'
					";
		}else if($i==2) {
			$sql = "
					update suname_set
					set suname = '주민세' , useyn = 'Y'
					where scode = '".$_SESSION['S_SCODE']."' and sucode = '".$_POST['gamt'.$i]."'
					";
		}else{
			$sql = "
					update suname_set
					set suname = '' , useyn = 'N'
					where scode = '".$_SESSION['S_SCODE']."' and sucode = '".$_POST['gamt'.$i]."'
					";
		}

		$result =  sqlsrv_query( $mscon, $sql );
		
		if ($result == false){
			sqlsrv_query($mscon,"ROLLBACK");
			sqlsrv_free_stmt($result);
			sqlsrv_close($mscon);

			$message = ' 수수료 명칭 초기화 중 오류_2';
			$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
			echo json_encode($returnJson);
			exit;
		}
	}

    sqlsrv_query($mscon,"COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' 수수료 명칭을 초기화하였습니다.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "" , "inscode" => "$inscode", "rtype" => "save");
	echo json_encode($returnJson);
	exit;

}

?>