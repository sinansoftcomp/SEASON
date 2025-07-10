<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

$seq	=	$_POST['seq'];
$gubun	=	$_POST['gubun'];
$skey	=	$_POST['skey'];
$status	=	$_POST['status'];
$sdate	=	str_replace("-","",iconv("UTF-8","EUCKR",$_POST['sdate']));
$sdate	=	str_replace(".","",$sdate);

$title	=	iconv("UTF-8","EUCKR",$_POST['title']);
$bigo	=	iconv("UTF-8","EUCKR",$_POST['bigo']);

// 전체일정일 경우 사원번호 빈값
if($gubun == '1'){
	$skey = '';
}
/*
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '필수입력값 오류, 재로그인해주세요.11111';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
*/

//----------------------------------------------------------//
//                    수정시 처리요직							// 
//----------------------------------------------------------//
if($_POST['type']=='up'){

	// 필수정보 확인
	if($_SESSION['S_SCODE'] == null || $seq == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '필수입력값 오류, 재로그인해주세요.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;		
	}

	$sql="UPDATE schd	
		  SET
			sdate	=	'$sdate',
			title	=	'$title',
			bigo	=	'$bigo',
			status	=	'$status'
		WHERE scode = '".$_SESSION['S_SCODE']."'
		  and seq	= '".$_POST['seq']."' ";

	// 트렌젝션 시작
    sqlsrv_query($mscon, "BEGIN TRAN");
    $result =  sqlsrv_query( $mscon, $sql );

    if ($result == false){
		sqlsrv_query($mscon, "ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = ' 스케줄 정보 수정 중 오류발생';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
    }

    sqlsrv_query($mscon, "COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' 스케줄 정보를 수정 하였습니다.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "");
	echo json_encode($returnJson);
	exit;

}

//----------------------------------------------------------//
//                    입력시 처리요직							// 
//----------------------------------------------------------//
if($_POST['type']=='in'){

	// 필수정보 확인
	if($_SESSION['S_SCODE'] == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '필수입력값 오류, 재로그인해주세요.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;		
	}

	//---> 순번 가져오기
	$sql  = "select isnull(Max(seq),0) maxseq
			 from schd
			 where scode = '".$_SESSION['S_SCODE']."'  " ;

	$result  = sqlsrv_query( $mscon, $sql );
	$row =  sqlsrv_fetch_array($result); 

	if($row['maxseq'] == null  or $row['maxseq'] < 1){
		$numcnt = 1;
	}else{
		$numcnt =	(int)$row['maxseq'] + 1;
	}

	$sql="insert into schd (scode, seq, sdate,  gubun, skey,
							title, bigo, status, idate, iswon)
		  values('".$_SESSION['S_SCODE']."', $numcnt, '$sdate', '$gubun', '".$skey."',
				 '$title', '$bigo', '$status', getdate(), '".$_SESSION['S_SKEY']."')";


	// 트렌젝션 시작
    sqlsrv_query($mscon, "BEGIN TRAN");
    $result =  sqlsrv_query( $mscon, $sql );


    if ($result == false){
		sqlsrv_query($mscon, "ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = ' 스케줄 등록 중 오류발생';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error",  "rtype" => "in");
		echo json_encode($returnJson);
		exit;
    }

    sqlsrv_query($mscon, "COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' 스케줄 정보를 등록 하였습니다.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "skey" => $skey, "seq" => $numcnt, "rtype" => "in");
	echo json_encode($returnJson);
	exit;

}

//----------------------------------------------------------//
//                    삭제시 처리요직							// 
//----------------------------------------------------------//
if($_POST['type']=='del'){

	// 필수정보 확인
	if($_SESSION['S_SCODE'] == null || $seq == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '필수입력값 오류, 재로그인해주세요.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;		
	}

	$sql="delete from schd where scode = '".$_SESSION['S_SCODE']."' and seq = '".$_POST['seq']."' ";

	// 트렌젝션 시작
    sqlsrv_query($mscon, "BEGIN TRAN");
    $result =  sqlsrv_query( $mscon, $sql );

    if ($result == false){
		sqlsrv_query($mscon, "ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = ' 스케줄 정보 삭제 중 오류발생';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
    }

    sqlsrv_query($mscon, "COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' 스케줄 정보를 삭제하였습니다.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "rtype" => "del");
	echo json_encode($returnJson);
	exit;

}