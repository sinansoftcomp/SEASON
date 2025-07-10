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
//                    ������ ó������							// 
//----------------------------------------------------------//
if($_POST['type']=='up'){

	// �ʼ����� Ȯ��
	if($_SESSION['S_SCODE'] == null || $inscode == null || $skey == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '�ʼ��Է°� ����, .';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}

	// Ʈ������ ����
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
		$message = ' �����纰 ��� ���� �� �����߻� #1';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}
	
    sqlsrv_query($mscon,"COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' �����纰 ��������� ���� �Ͽ����ϴ�.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "rtype" => "up" , "skey" => "$skey" , "inscode" => "$inscode" , "bscode" => "$bscode");
	echo json_encode($returnJson);
	exit;

}


//----------------------------------------------------------//
//                    �Է½� ó������							// 
//----------------------------------------------------------//
if($_POST['type']=='in'){

	// �ʼ����� Ȯ��
	if($_SESSION['S_SCODE'] == null || $inscode == null || $skey == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '�ʼ��Է°� ����, ��α������ּ���......!';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}

	// Ʈ������ ����
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
		$message = ' �̹� ������� ������ ��������� �����մϴ�.';
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
		$message = ' �����纰 ��� ��� �� �����߻� #1';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}


    sqlsrv_query($mscon,"COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' �����纰 ��������� ��� �Ͽ����ϴ�.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "rtype" => "in" , "skey" => "$skey" , "inscode" => "$inscode" , "bscode" => "$bscode");
	echo json_encode($returnJson);
	exit;

}



//----------------------------------------------------------//
//                    ������ ó������							// 
//----------------------------------------------------------//
if($_POST['type']=='del'){

	// �ʼ����� Ȯ��
	if($_SESSION['S_SCODE'] == null || $inscode == null || $skey == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '�ʼ��Է°� ����, ��α������ּ���.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}

	// Ʈ������ ����
    sqlsrv_query($mscon,"BEGIN TRAN");

	$sql = "
			delete from inswon where scode = '".$_SESSION['S_SCODE']."' and skey = '".$skey."' and inscode = '".$inscode."' and bscode = '".$bscode."'
			";

	$result =  sqlsrv_query( $mscon, $sql );

	if ($result == false){
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' �����纰 ��� ���� �� �����߻� #1';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}

    sqlsrv_query($mscon,"COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' �����纰 ��������� ���� �Ͽ����ϴ�.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "rtype" => "del");
	echo json_encode($returnJson);
	exit;

}

?>