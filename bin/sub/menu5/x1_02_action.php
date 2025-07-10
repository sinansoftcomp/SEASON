<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

if($_POST['type']=='in'){
	$yymm = $_POST['yymm_s'];
	$inscode = $_POST['inscode_s'];
}else{
	$yymm = $_POST['yymm'];
	$inscode = $_POST['inscode'];
}

$yymm=rtrim(ltrim(str_replace('-','',$yymm)));

$insert = "";
$values = "";
$set = "";

for($i=1;$i<=10;$i++){
	if($_POST["dataset".(string)$i]){
		$insert .= ","."dataset".$i;
		$values .= " ,".str_replace(',','',$_POST['dataset'.(string)$i]);
		$set .= "dataset".(string)$i." = ".str_replace(',','',$_POST['dataset'.(string)$i])." ,";
	}
}

$set = substr($set,0,-1);	// 마지막 쉼표 자르기

//----------------------------------------------------------//
//                    입력시 처리요직							// 
//----------------------------------------------------------//
if($_POST['type']=='in'){
	// 필수정보 확인
	if($_SESSION['S_SCODE'] == null or $_POST['yymm_s'] == null or $_POST['inscode_s'] == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '세센연결 error 필수입력값 오류, 재 로그인해주세요.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}

	// ------------------예외처리 시작-----------------//

	// 데이터 중복 체크
	$sql	= "
			select count(*) cnt
			from INSCHARGE_SET
			where scode = '".$_SESSION['S_SCODE']."' and yymm = '".$yymm."' and inscode = '".$inscode."'
				";
	$result  = sqlsrv_query( $mscon, $sql );
	$row =  sqlsrv_fetch_array($result); 
	$cnt = $row["cnt"];

	if($cnt > 0){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = $_POST['yymm_s'].' 에 이미 등록된 데이터가 존재합니다.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;			
	}

	// ------------------예외처리 종료-----------------//

	$sql = "insert into INSCHARGE_SET(scode,yymm,inscode ".$insert.")
			values('".$_SESSION['S_SCODE']."','".$yymm."','".$inscode."' ".$values.")";
	$result =  sqlsrv_query( $mscon, $sql );

	if ($result == false){
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' 수수료 기초자료 등록 중 오류';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
	}

	// 기준율테이블에 전달데이터 인서트
	$sql = "insert into INSCHARGE_SET_sub(scode,yymm,inscode,selfbit,seq,dataset1,dataset2)
			select scode,'".$yymm."',inscode,selfbit,seq,dataset1,dataset2
			from INSCHARGE_SET_sub
			where scode = '".$_SESSION['S_SCODE']."' and yymm = convert(varchar(6),dateadd(MM,-1,'".$yymm."'+'01'),112) and inscode = '".$inscode."'";
	$result =  sqlsrv_query( $mscon, $sql );

	if ($result == false){
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' 수수료 기초자료 등록 중 오류_2';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
	}


    sqlsrv_query($mscon,"COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' 수수료 기초자료를 등록하였습니다.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "yymm"=>"$yymm" , "inscode"=>"$inscode" , "rtype" => "in");
	echo json_encode($returnJson);
	exit;

}

//----------------------------------------------------------//
//                    수정시 처리요직							// 
//----------------------------------------------------------//
if($_POST['type']=='up'){
	// 필수정보 확인
	if($_SESSION['S_SCODE'] == null or $_POST['yymm'] == null or $_POST['inscode'] == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '세센연결 error 필수입력값 오류, 재 로그인해주세요.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}

	// ------------------예외처리 시작-----------------//

	// 수당정산여부체크
	$sql	= "
			select count(*) cnt
			from sumst
			where scode = '".$_SESSION['S_SCODE']."' and yymm = '".$yymm."'
				";
	$result  = sqlsrv_query( $mscon, $sql );
	$row =  sqlsrv_fetch_array($result); 
	$cnt = $row["cnt"];

	if($cnt > 0){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = $_POST['yymm'].' 에 이미 수당이 정산되어 수정이 불가합니다.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;			
	}

	// ------------------예외처리 종료-----------------//


	$sql = "update INSCHARGE_SET
			set ".$set."
			where scode = '".$_SESSION['S_SCODE']."' and yymm = '".$yymm."' and inscode = '".$inscode."'
			";
	$result =  sqlsrv_query( $mscon, $sql );
	
	if ($result == false){
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' 수수료 기초자료 수정 중 오류';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
	}

    sqlsrv_query($mscon,"COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' 수수료 기초자료를 수정하였습니다.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "rtype" => "up");
	echo json_encode($returnJson);
	exit;

}


//----------------------------------------------------------//
//                    삭제시 처리요직							// 
//----------------------------------------------------------//
if($_POST['type'] == "del"){
	// 필수정보 확인
	if($_SESSION['S_SCODE'] == null or $_POST['yymm'] == null or $_POST['inscode'] == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '세센연결 error 필수입력값 오류, 재 로그인해주세요.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}

	// ------------------예외처리 시작-----------------//

	// 수당정산여부체크
	$sql	= "
			select count(*) cnt
			from sumst
			where scode = '".$_SESSION['S_SCODE']."' and yymm = '".$yymm."'
				";
	$result  = sqlsrv_query( $mscon, $sql );
	$row =  sqlsrv_fetch_array($result); 
	$cnt = $row["cnt"];

	if($cnt > 0){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = $_POST['yymm'].' 에 이미 수당이 정산되어 삭제가 불가합니다.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;			
	}

	// ------------------예외처리 종료-----------------//

	$sql = "delete from INSCHARGE_SET
			where scode = '".$_SESSION['S_SCODE']."' and yymm = '".$yymm."' and inscode = '".$inscode."' ";

	// 트렌젝션 시작
    sqlsrv_query($mscon,"BEGIN TRAN");
    $result =  sqlsrv_query( $mscon, $sql );

    if ($result == false){
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' 수수료 기초자료 삭제 중 오류';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
    }

	$sql = "delete from INSCHARGE_SET_sub
			where scode = '".$_SESSION['S_SCODE']."' and yymm = '".$yymm."' and inscode = '".$inscode."' ";

	// 트렌젝션 시작
    sqlsrv_query($mscon,"BEGIN TRAN");
    $result =  sqlsrv_query( $mscon, $sql );

    if ($result == false){
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' 수수료 기초자료 삭제 중 오류_2';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
    }

    sqlsrv_query($mscon,"COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' 수수료 기초자료를 삭제하였습니다.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "rtype" => "del");
	echo json_encode($returnJson);
	exit;
}

?>