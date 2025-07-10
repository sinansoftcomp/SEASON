<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

$pid	= $_POST['pid'];
$cid	= $_POST['cid'];
$ctext	= iconv("UTF-8","EUC-KR",$_POST['ctext']);



//----------------------------------------------------------//
//                    입력시 처리요직							// 
//----------------------------------------------------------//
if($_POST['type']=='in'){
	// 필수정보 확인
	if($_SESSION['S_SCODE'] == null || $pid == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '세센연결 error 필수입력값 오류, 재 로그인해주세요.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}

	// 순번따기
	$sql	= "
			select isnull(max(convert(int,replace(cid,'C',''))),0)+1 seq
			from comment
			where scode = '".$_SESSION['S_SCODE']."' and pid = '".$pid."'
			";
	$result  = sqlsrv_query( $mscon, $sql );
	$row =  sqlsrv_fetch_array($result); 
	$newcid = 'C'.(string)$row["seq"];


	$sql = "insert into comment(scode,cid,pid,ctext,idate,iswon)
			values('".$_SESSION['S_SCODE']."', '".$newcid."' , '".$pid."' , '".$ctext."' , getdate(),'".$_SESSION['S_SKEY']."') ";
	// 트렌젝션 시작
    sqlsrv_query($mscon,"BEGIN TRAN");
	$result =  sqlsrv_query( $mscon, $sql );


	if ($result == false){
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' 댓글 등록 중 오류';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
	}


    sqlsrv_query($mscon,"COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' 댓글을 등록하였습니다.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "pid"=>"$pid" , "rtype" => "in");
	echo json_encode($returnJson);
	exit;

}


//----------------------------------------------------------//
//                    수정시 처리요직							// 
//----------------------------------------------------------//
if($_POST['type']=='up'){
	// 필수정보 확인
	if($_SESSION['S_SCODE'] == null || $pid == null || $cid == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '세센연결 error 필수입력값 오류, 재 로그인해주세요.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}

	$sql = "update comment
				set ctext	= '".$ctext."' , 
					uswon	= '".$_SESSION['S_SKEY']."',
					udate	= getdate()
			where scode = '".$_SESSION['S_SCODE']."' and pid = '".$pid."' and cid= '".$_POST['cid']."'
			";
	// 트렌젝션 시작
    sqlsrv_query($mscon,"BEGIN TRAN");
	$result =  sqlsrv_query( $mscon, $sql );
	
	if ($result == false){
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		echo $sql;
		$message = ' 댓글 수정 중 오류';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
	}

    sqlsrv_query($mscon,"COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' 수정하였습니다.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "pid"=>"$pid" , "rtype" => "up");
	echo json_encode($returnJson);
	exit;
}

//----------------------------------------------------------//
//                    삭제시 처리요직							// 
//----------------------------------------------------------//
if($_POST['type'] == "del"){
	// 필수정보 확인
	if($_SESSION['S_SCODE'] == null || $pid == null || $cid == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '세센연결 error 필수입력값 오류, 재 로그인해주세요.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}

	// 트렌젝션 시작
    sqlsrv_query($mscon,"BEGIN TRAN");
	$sql = "delete from comment
			where scode = '".$_SESSION['S_SCODE']."' and pid= '".$_POST['pid']."' and cid= '".$_POST['cid']."'";

    $result =  sqlsrv_query( $mscon, $sql );

    if ($result == false){
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' 댓글 삭제 중 오류 #1';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
    }


    sqlsrv_query($mscon,"COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' 댓글을 삭제하였습니다.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "pid"=>"$pid" , "rtype" => "del");
	echo json_encode($returnJson);
	exit;
}

?>