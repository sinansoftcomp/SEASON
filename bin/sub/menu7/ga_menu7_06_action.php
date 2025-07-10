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
	$recvnm = '��ü';
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
//                    �Է½� ó������							// 
//----------------------------------------------------------//
if($_POST['type']=='in'){
	
	// �ʼ����� Ȯ��
	if($_SESSION['S_SCODE'] == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '�������� error �ʼ��Է°� ����, �� �α������ּ���.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}

	// Ʈ������ ����
    sqlsrv_query($mscon,"BEGIN TRAN");

	// ��������
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
		$message = ' �˸��� ��� �� ����';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
	}


	//-------------------------------------------------------------------
	//����/��� ���� ����鿡 ���� push ���� ���� �ۼ�
	
	$where = "";
	if($gubun=='1'){	// ��ü
		$where = "";
	}else if($gubun=='2') {		// ����
		$where = " and bonbu = '".$recv."' ";
	}else if($gubun=='3') {		// ����
		$where = " and jisa = '".$recv."' ";
	}else if($gubun=='4') {		// ����
		$where = " and jijum = '".$recv."' ";
	}else if($gubun=='5') {		// ��
		$where = " and team = '".$recv."' ";
	}else if($gubun=='6') {		// ����

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
            'body' => iconv('EUC-KR','UTF-8','Ŭ���Ͽ� Ȯ���ϼ���.'),
            'data' => $data
        ];
    }

    sendPushNotifications($notifications);

	//------------------------------------------------------------------- 
	

    sqlsrv_query($mscon,"COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' �˸����� ����Ͽ����ϴ�.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "pid"=>"$newpid" , "rtype" => "in");
	echo json_encode($returnJson);
	exit;

}


//----------------------------------------------------------//
//                    ������ ó������							// 
//----------------------------------------------------------//
if($_POST['type']=='up'){
	// �ʼ����� Ȯ��
	if($_SESSION['S_SCODE'] == null || $pid == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '�������� error �ʼ��Է°� ����, �� �α������ּ���.';
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
	// Ʈ������ ����
    sqlsrv_query($mscon,"BEGIN TRAN");
	$result =  sqlsrv_query( $mscon, $sql );
	
	if ($result == false){
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		echo $sql;
		$message = ' �˸��� ���� �� ����';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
	}

	//-------------------------------------------------------------------
	//����/��� ���� ����鿡 ���� push ���� ���� �ۼ�
	if($pushsend=='Y'){		// �����϶� üũ�ϰ�쿡�� Ǫ������
		$where = "";
		if($gubun=='1'){	// ��ü
			$where = "";
		}else if($gubun=='2') {		// ����
			$where = " and bonbu = '".$recv."' ";
		}else if($gubun=='3') {		// ����
			$where = " and jisa = '".$recv."' ";
		}else if($gubun=='4') {		// ����
			$where = " and jijum = '".$recv."' ";
		}else if($gubun=='5') {		// ��
			$where = " and team = '".$recv."' ";
		}else if($gubun=='6') {		// ����

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
				'body' => iconv('EUC-KR','UTF-8','Ŭ���Ͽ� Ȯ���ϼ���.'),
				'data' => $data
			];
		}

		sendPushNotifications($notifications);
	}
	//------------------------------------------------------------------- 


    sqlsrv_query($mscon,"COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' �˸��� �����Ͽ����ϴ�.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "pid"=>"$pid" , "rtype" => "up");
	echo json_encode($returnJson);
	exit;
}

//----------------------------------------------------------//
//                    ������ ó������							// 
//----------------------------------------------------------//
if($_POST['type'] == "del"){
	// �ʼ����� Ȯ��
	if($_SESSION['S_SCODE'] == null || $pid == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '�������� error �ʼ��Է°� ����, �� �α������ּ���.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}

	// Ʈ������ ����
    sqlsrv_query($mscon,"BEGIN TRAN");
	$sql = "delete from postlist
			where scode = '".$_SESSION['S_SCODE']."' and pid= '".$_POST['pid']."' ";

    $result =  sqlsrv_query( $mscon, $sql );

    if ($result == false){
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' �˸��� ���� �� ���� #1';
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
		$message = ' �˸��� ���� �� ���� #2';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
    }

    sqlsrv_query($mscon,"COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' �˸����� �����Ͽ����ϴ�.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "rtype" => "del");
	echo json_encode($returnJson);
	exit;
}

?>