<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

$pid	= $_POST['pid'];
$title	= iconv("UTF-8","EUC-KR",$_POST['title']);
$msg	= iconv("UTF-8","EUC-KR",$_POST['msg']);
$color	= $_POST['color'];


//----------------------------------------------------------//
//                    입력시 처리요직							// 
//----------------------------------------------------------//
if($_POST['type']=='in'){
	// 필수정보 확인
	if($_SESSION['S_SCODE'] == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '세센연결 error 필수입력값 오류, 재 로그인해주세요.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}

	// 순번따기
	$sql	= "
			select isnull(max(convert(int,replace(pid,'M',''))),0)+1 seq
			from community
			where scode = '".$_SESSION['S_SCODE']."'
			";
	$result  = sqlsrv_query( $mscon, $sql );
	$row =  sqlsrv_fetch_array($result); 
	$newpid = 'M'.(string)$row["seq"];


	$sql = "insert into community(scode,pid,
								 title,msg,jocnt,color,
								 idate,iswon)
			values('".$_SESSION['S_SCODE']."', '".$newpid."' , 
				   '".$title."' , '".$msg."' , 0 , '".$color."',
				   getdate(),'".$_SESSION['S_SKEY']."') ";
   sqlsrv_query($mscon,"BEGIN TRAN");
	$result =  sqlsrv_query( $mscon, $sql );


	if ($result == false){
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' 커뮤니티 등록 중 오류';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
	}


    sqlsrv_query($mscon,"COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' 커뮤니티 게시글을 등록하였습니다.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "pid"=>"$newpid" , "rtype" => "in");
	echo json_encode($returnJson);
	exit;

}


//----------------------------------------------------------//
//                    수정시 처리요직							// 
//----------------------------------------------------------//
if($_POST['type']=='up'){
	// 필수정보 확인
	if($_SESSION['S_SCODE'] == null || $pid == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '세센연결 error 필수입력값 오류, 재 로그인해주세요.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}

	$sql = "update community
				set title	= '".$title."' , 
					msg		= '".$msg."' , 
					color	= '".$color."',
					uswon	= '".$_SESSION['S_SKEY']."',
					udate	= getdate()
			where scode = '".$_SESSION['S_SCODE']."' and pid = '".$pid."'
			";
	sqlsrv_query($mscon,"BEGIN TRAN");
	$result =  sqlsrv_query( $mscon, $sql );
	
	if ($result == false){
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		echo $sql;
		$message = ' 커뮤니티 수정 중 오류';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
	}

    sqlsrv_query($mscon,"COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' 커뮤니티 게시글이 수정되었습니다.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "pid"=>"$pid" , "rtype" => "up");
	echo json_encode($returnJson);
	exit;
}

//----------------------------------------------------------//
//                    삭제시 처리요직							// 
//----------------------------------------------------------//
if($_POST['type'] == "del"){
	// 필수정보 확인
	if($_SESSION['S_SCODE'] == null || $pid == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '세센연결 error 필수입력값 오류, 재 로그인해주세요.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}

	// 트렌젝션 시작
    sqlsrv_query($mscon,"BEGIN TRAN");
	$sql = "delete from community
			where scode = '".$_SESSION['S_SCODE']."' and pid= '".$_POST['pid']."' ";

    $result =  sqlsrv_query( $mscon, $sql );

    if ($result == false){
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' 커뮤니티 게시글 삭제 중 오류 #1';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
    }


	$sql = "delete from comment
			where scode = '".$_SESSION['S_SCODE']."' and pid= '".$_POST['pid']."' ";

    $result =  sqlsrv_query( $mscon, $sql );

    if ($result == false){
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' 커뮤니티 게시글 삭제 중 오류 #2';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
    }

    sqlsrv_query($mscon,"COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' 커뮤니티 게시글을 삭제하였습니다.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "rtype" => "del");
	echo json_encode($returnJson);
	exit;
}

?>