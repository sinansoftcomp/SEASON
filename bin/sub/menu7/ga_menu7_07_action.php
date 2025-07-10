<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

$pid	= $_POST['pid'];
$title	= iconv("UTF-8","EUC-KR",$_POST['title']);
$msg	= iconv("UTF-8","EUC-KR",$_POST['msg']);
$color	= $_POST['color'];


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

	// ��������
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
		$message = ' Ŀ�´�Ƽ ��� �� ����';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
	}


    sqlsrv_query($mscon,"COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' Ŀ�´�Ƽ �Խñ��� ����Ͽ����ϴ�.';
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
		$message = ' Ŀ�´�Ƽ ���� �� ����';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
	}

    sqlsrv_query($mscon,"COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' Ŀ�´�Ƽ �Խñ��� �����Ǿ����ϴ�.';
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
	$sql = "delete from community
			where scode = '".$_SESSION['S_SCODE']."' and pid= '".$_POST['pid']."' ";

    $result =  sqlsrv_query( $mscon, $sql );

    if ($result == false){
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' Ŀ�´�Ƽ �Խñ� ���� �� ���� #1';
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
		$message = ' Ŀ�´�Ƽ �Խñ� ���� �� ���� #2';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
    }

    sqlsrv_query($mscon,"COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' Ŀ�´�Ƽ �Խñ��� �����Ͽ����ϴ�.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "rtype" => "del");
	echo json_encode($returnJson);
	exit;
}

?>