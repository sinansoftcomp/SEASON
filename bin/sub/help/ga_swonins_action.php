<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

extract($_POST);

$skey		=	$_POST['skey'];
$inscode		=	$_POST['inscode'];
$bscode		=	$_POST['bscode'];
$bscode_f	=	$_POST['bscode_f'];
$ydate		=	str_replace('-','',$_POST['ydate']);
$hdate		=	str_replace('-','',$_POST['hdate']);
$sgubun		=	$_POST['sgubun'];


//----------------------------------------------------------//
//                    수정시 처리요직							// 
//----------------------------------------------------------//
if($_POST['type']=='up'){

	// 필수정보 확인
	if($_SESSION['S_SCODE'] == null || $inscode == null || $skey == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '필수입력값 오류, .';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}

	// 트렌젝션 시작
    sqlsrv_query($mscon,"BEGIN TRAN");

	$sql = "
			update inswon
			set bscode = '".$bscode."' , ydate = '".$ydate."' , hdate = '".$hdate."' , sgubun = '".$sgubun."'
			where scode = '".$_SESSION['S_SCODE']."' and skey = '".$skey."' and inscode = '".$inscode."' and bscode = '".$bscode_f."'
			";

	$result =  sqlsrv_query( $mscon, $sql );

	if ($result == false){
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' 원수사별 사원 수정 중 오류발생 #1';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}
	
    sqlsrv_query($mscon,"COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' 원수사별 사원정보를 수정 하였습니다.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "rtype" => "up" , "skey" => "$skey" , "inscode" => "$inscode" , "bscode" => "$bscode");
	echo json_encode($returnJson);
	exit;

}


//----------------------------------------------------------//
//                    입력시 처리요직							// 
//----------------------------------------------------------//
if($_POST['type']=='in'){

	// 필수정보 확인
	if($_SESSION['S_SCODE'] == null || $inscode == null || $skey == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '필수입력값 오류, 재로그인해주세요......!';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}

	// 트렌젝션 시작
    sqlsrv_query($mscon,"BEGIN TRAN");

	$sql= "
			select count(*) cnt
			from inswon
			where scode = '".$_SESSION['S_SCODE']."' and skey = '".$skey."' and inscode = '".$inscode."' and bscode = '".$bscode."'
		  " ;
	$result =  sqlsrv_query($mscon, $sql);
	$row =  sqlsrv_fetch_array($result); 
	$cnt = $row['cnt'];
	
	if($cnt > 0){
		$message = ' 이미 보험사의 원수사 사원정보가 존재합니다.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}

	$sql = "
			insert into inswon(scode,skey,inscode,bscode,ydate,hdate,idate,iswon,sgubun)
			values('".$_SESSION['S_SCODE']."','".$skey."','".$inscode."','".$bscode."','".$ydate."','".$hdate."',getdate(),'".$_SESSION['S_SKEY']."','".$sgubun."')
			";

	$result =  sqlsrv_query( $mscon, $sql );

	if ($result == false){
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' 원수사별 사원 등록 중 오류발생 #1';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}


    sqlsrv_query($mscon,"COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' 원수사별 사원정보를 등록 하였습니다.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "rtype" => "in" , "skey" => "$skey" , "inscode" => "$inscode" , "bscode" => "$bscode");
	echo json_encode($returnJson);
	exit;

}



//----------------------------------------------------------//
//                    삭제시 처리요직							// 
//----------------------------------------------------------//
if($_POST['type']=='del'){

	// 필수정보 확인
	if($_SESSION['S_SCODE'] == null || $inscode == null || $skey == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '필수입력값 오류, 재로그인해주세요.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}

	// 트렌젝션 시작
    sqlsrv_query($mscon,"BEGIN TRAN");

	$sql = "
			delete from inswon where scode = '".$_SESSION['S_SCODE']."' and skey = '".$skey."' and inscode = '".$inscode."' and bscode = '".$bscode."'
			";

	$result =  sqlsrv_query( $mscon, $sql );

	if ($result == false){
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' 원수사별 사원 삭제 중 오류발생 #1';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}

    sqlsrv_query($mscon,"COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' 원수사별 사원정보를 삭제 하였습니다.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "rtype" => "del");
	echo json_encode($returnJson);
	exit;

}

?>