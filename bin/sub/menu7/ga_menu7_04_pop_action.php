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

// ��ü������ ��� �����ȣ ��
if($gubun == '1'){
	$skey = '';
}
/*
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '�ʼ��Է°� ����, ��α������ּ���.11111';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
*/

//----------------------------------------------------------//
//                    ������ ó������							// 
//----------------------------------------------------------//
if($_POST['type']=='up'){

	// �ʼ����� Ȯ��
	if($_SESSION['S_SCODE'] == null || $seq == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '�ʼ��Է°� ����, ��α������ּ���.';
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

	// Ʈ������ ����
    sqlsrv_query($mscon, "BEGIN TRAN");
    $result =  sqlsrv_query( $mscon, $sql );

    if ($result == false){
		sqlsrv_query($mscon, "ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = ' ������ ���� ���� �� �����߻�';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
    }

    sqlsrv_query($mscon, "COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' ������ ������ ���� �Ͽ����ϴ�.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "");
	echo json_encode($returnJson);
	exit;

}

//----------------------------------------------------------//
//                    �Է½� ó������							// 
//----------------------------------------------------------//
if($_POST['type']=='in'){

	// �ʼ����� Ȯ��
	if($_SESSION['S_SCODE'] == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '�ʼ��Է°� ����, ��α������ּ���.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;		
	}

	//---> ���� ��������
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


	// Ʈ������ ����
    sqlsrv_query($mscon, "BEGIN TRAN");
    $result =  sqlsrv_query( $mscon, $sql );


    if ($result == false){
		sqlsrv_query($mscon, "ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = ' ������ ��� �� �����߻�';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error",  "rtype" => "in");
		echo json_encode($returnJson);
		exit;
    }

    sqlsrv_query($mscon, "COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' ������ ������ ��� �Ͽ����ϴ�.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "skey" => $skey, "seq" => $numcnt, "rtype" => "in");
	echo json_encode($returnJson);
	exit;

}

//----------------------------------------------------------//
//                    ������ ó������							// 
//----------------------------------------------------------//
if($_POST['type']=='del'){

	// �ʼ����� Ȯ��
	if($_SESSION['S_SCODE'] == null || $seq == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '�ʼ��Է°� ����, ��α������ּ���.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;		
	}

	$sql="delete from schd where scode = '".$_SESSION['S_SCODE']."' and seq = '".$_POST['seq']."' ";

	// Ʈ������ ����
    sqlsrv_query($mscon, "BEGIN TRAN");
    $result =  sqlsrv_query( $mscon, $sql );

    if ($result == false){
		sqlsrv_query($mscon, "ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = ' ������ ���� ���� �� �����߻�';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
    }

    sqlsrv_query($mscon, "COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' ������ ������ �����Ͽ����ϴ�.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "rtype" => "del");
	echo json_encode($returnJson);
	exit;

}