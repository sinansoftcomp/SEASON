<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/sms/send_push_async.php");

$pid	= $_POST['pid'];
$gubun	= $_POST['gubun'];
$title	= iconv("UTF-8","EUC-KR",$_POST['title']);
$msg	= iconv("UTF-8","EUC-KR",$_POST['msg']);
$color	= $_POST['color'];
$recv	= $_POST['recv'];
$recvnm	= iconv("UTF-8","EUC-KR",$_POST['recvnm']);

if($recv == '1'){
	$recvnm = '전체';
}

$topsort="";
if($_POST['topsort']){
	$topsort = 'Y';
}

$pushsend ="";
if($_POST['pushsend']){
	$pushsend = 'Y';
}

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

	// 트렌젝션 시작
    sqlsrv_query($mscon,"BEGIN TRAN");

	// 순번따기
	$sql	= "
			select isnull(max(convert(int,replace(pid,'P',''))),0)+1 seq
			from postlist
			where scode = '".$_SESSION['S_SCODE']."'
			";
	$result  = sqlsrv_query( $mscon, $sql );
	$row =  sqlsrv_fetch_array($result); 
	$newpid = 'P'.(string)$row["seq"];


	$sql = "insert into postlist(scode,pid,gubun,recv,recvnm,
								 title,msg,jocnt,topsort,color,
								 idate,iswon)
			values('".$_SESSION['S_SCODE']."', '".$newpid."' , '".$gubun."' , '".$recv."' , '".$recvnm."' ,
				   '".$title."' , '".$msg."' , 0 ,'".$topsort."','".$color."',
				   getdate(),'".$_SESSION['S_SKEY']."') ";
	$result =  sqlsrv_query( $mscon, $sql );


	if ($result == false){
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' 알림장 등록 중 오류';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
	}


	//-------------------------------------------------------------------
	//구분/대상에 따른 사원들에 대해 push 전송 로직 작성
	
	$where = "";
	if($gubun=='1'){	// 전체
		$where = "";
	}else if($gubun=='2') {		// 본부
		$where = " and bonbu = '".$recv."' ";
	}else if($gubun=='3') {		// 지사
		$where = " and jisa = '".$recv."' ";
	}else if($gubun=='4') {		// 지점
		$where = " and jijum = '".$recv."' ";
	}else if($gubun=='5') {		// 팀
		$where = " and team = '".$recv."' ";
	}else if($gubun=='6') {		// 개인

		$recv_arr = explode(',',$recv);
		foreach ($recv_arr as &$value) {
			$value = "'" . $value . "'";
		}
		$recv_arr = implode(',', $recv_arr);

		$where = " and skey in ($recv_arr)";
	}

	$sql = "
			select pushtoken
			from swon
			where scode = '".$_SESSION['S_SCODE']."' $where and tbit in ('1','3') and pushyn = 'Y' and isnull(pushtoken,'') <> ''
			" ;		

	$qry	= sqlsrv_query( $mscon, $sql );
	while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
		$pushlist[]	= $fet;
	}	

	$data = ['url' => 'https://gaplus.net:452/bin/submobile/m_main_post_dt.php?pid='.$newpid, 'key2' => 'value2'];

    $notifications = [];

    foreach ($pushlist as $key => $val) {
        extract($val);
        $notifications[] = [
            'to' => $pushtoken,
            'title' => iconv('EUC-KR','UTF-8',$title),
            'body' => iconv('EUC-KR','UTF-8','클릭하여 확인하세요.'),
            'data' => $data
        ];
    }

    sendPushNotifications($notifications);

	//------------------------------------------------------------------- 
	

    sqlsrv_query($mscon,"COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' 알림장을 등록하였습니다.';
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

	$sql = "update postlist
				set gubun	= '".$gubun."' , 
					recv	= '".$recv."' , 
					recvnm	= '".$recvnm."' , 
					title	= '".$title."' , 
					msg		= '".$msg."' , 
					topsort	= '".$topsort."',
					color	= '".$color."',
					uswon	= '".$_SESSION['S_SKEY']."',
					udate	= getdate()
			where scode = '".$_SESSION['S_SCODE']."' and pid = '".$pid."'
			";
	// 트렌젝션 시작
    sqlsrv_query($mscon,"BEGIN TRAN");
	$result =  sqlsrv_query( $mscon, $sql );
	
	if ($result == false){
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		echo $sql;
		$message = ' 알림장 수정 중 오류';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
	}

	//-------------------------------------------------------------------
	//구분/대상에 따른 사원들에 대해 push 전송 로직 작성
	if($pushsend=='Y'){		// 수정일땐 체크일경우에만 푸시전송
		$where = "";
		if($gubun=='1'){	// 전체
			$where = "";
		}else if($gubun=='2') {		// 본부
			$where = " and bonbu = '".$recv."' ";
		}else if($gubun=='3') {		// 지사
			$where = " and jisa = '".$recv."' ";
		}else if($gubun=='4') {		// 지점
			$where = " and jijum = '".$recv."' ";
		}else if($gubun=='5') {		// 팀
			$where = " and team = '".$recv."' ";
		}else if($gubun=='6') {		// 개인

			$recv_arr = explode(',',$recv);
			foreach ($recv_arr as &$value) {
				$value = "'" . $value . "'";
			}
			$recv_arr = implode(',', $recv_arr);

			$where = " and skey in ($recv_arr)";
		}

		$sql = "
				select pushtoken
				from swon
				where scode = '".$_SESSION['S_SCODE']."' $where and tbit in ('1','3') and pushyn = 'Y' and isnull(pushtoken,'') <> ''
				" ;		

		$qry	= sqlsrv_query( $mscon, $sql );
		while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
			$pushlist[]	= $fet;
		}	

		$data = ['url' => 'https://gaplus.net:452/bin/submobile/m_main_post_dt.php?pid='.$pid, 'key2' => 'value2'];
	//	$data = ['url' => 'https://gaplus.net:452/bin/submobile/m_main_post_dt.php?pid='.$pid, 'key2' => 'value2'];

		$notifications = [];

		foreach ($pushlist as $key => $val) {
			extract($val);
			$notifications[] = [
				'to' => $pushtoken,
				'title' => iconv('EUC-KR','UTF-8',$title),
				'body' => iconv('EUC-KR','UTF-8','클릭하여 확인하세요.'),
				'data' => $data
			];
		}

		sendPushNotifications($notifications);
	}
	//------------------------------------------------------------------- 


    sqlsrv_query($mscon,"COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' 알림장 수정하였습니다.';
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
	$sql = "delete from postlist
			where scode = '".$_SESSION['S_SCODE']."' and pid= '".$_POST['pid']."' ";

    $result =  sqlsrv_query( $mscon, $sql );

    if ($result == false){
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' 알림장 삭제 중 오류 #1';
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
		$message = ' 알림장 삭제 중 오류 #2';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
    }

    sqlsrv_query($mscon,"COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' 알림장을 삭제하였습니다.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "rtype" => "del");
	echo json_encode($returnJson);
	exit;
}

?>