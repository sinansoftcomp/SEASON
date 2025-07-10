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

$set = substr($set,0,-1);	// ������ ��ǥ �ڸ���

//----------------------------------------------------------//
//                    �Է½� ó������							// 
//----------------------------------------------------------//
if($_POST['type']=='in'){
	// �ʼ����� Ȯ��
	if($_SESSION['S_SCODE'] == null or $_POST['yymm_s'] == null or $_POST['inscode_s'] == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '�������� error �ʼ��Է°� ����, �� �α������ּ���.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}

	// ------------------����ó�� ����-----------------//

	// ������ �ߺ� üũ
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
		$message = $_POST['yymm_s'].' �� �̹� ��ϵ� �����Ͱ� �����մϴ�.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;			
	}

	// ------------------����ó�� ����-----------------//

	$sql = "insert into INSCHARGE_SET(scode,yymm,inscode ".$insert.")
			values('".$_SESSION['S_SCODE']."','".$yymm."','".$inscode."' ".$values.")";
	$result =  sqlsrv_query( $mscon, $sql );

	if ($result == false){
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' ������ �����ڷ� ��� �� ����';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
	}

	// ���������̺� ���޵����� �μ�Ʈ
	$sql = "insert into INSCHARGE_SET_sub(scode,yymm,inscode,selfbit,seq,dataset1,dataset2)
			select scode,'".$yymm."',inscode,selfbit,seq,dataset1,dataset2
			from INSCHARGE_SET_sub
			where scode = '".$_SESSION['S_SCODE']."' and yymm = convert(varchar(6),dateadd(MM,-1,'".$yymm."'+'01'),112) and inscode = '".$inscode."'";
	$result =  sqlsrv_query( $mscon, $sql );

	if ($result == false){
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' ������ �����ڷ� ��� �� ����_2';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
	}


    sqlsrv_query($mscon,"COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' ������ �����ڷḦ ����Ͽ����ϴ�.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "yymm"=>"$yymm" , "inscode"=>"$inscode" , "rtype" => "in");
	echo json_encode($returnJson);
	exit;

}

//----------------------------------------------------------//
//                    ������ ó������							// 
//----------------------------------------------------------//
if($_POST['type']=='up'){
	// �ʼ����� Ȯ��
	if($_SESSION['S_SCODE'] == null or $_POST['yymm'] == null or $_POST['inscode'] == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '�������� error �ʼ��Է°� ����, �� �α������ּ���.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}

	// ------------------����ó�� ����-----------------//

	// �������꿩��üũ
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
		$message = $_POST['yymm'].' �� �̹� ������ ����Ǿ� ������ �Ұ��մϴ�.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;			
	}

	// ------------------����ó�� ����-----------------//


	$sql = "update INSCHARGE_SET
			set ".$set."
			where scode = '".$_SESSION['S_SCODE']."' and yymm = '".$yymm."' and inscode = '".$inscode."'
			";
	$result =  sqlsrv_query( $mscon, $sql );
	
	if ($result == false){
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' ������ �����ڷ� ���� �� ����';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
	}

    sqlsrv_query($mscon,"COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' ������ �����ڷḦ �����Ͽ����ϴ�.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "rtype" => "up");
	echo json_encode($returnJson);
	exit;

}


//----------------------------------------------------------//
//                    ������ ó������							// 
//----------------------------------------------------------//
if($_POST['type'] == "del"){
	// �ʼ����� Ȯ��
	if($_SESSION['S_SCODE'] == null or $_POST['yymm'] == null or $_POST['inscode'] == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '�������� error �ʼ��Է°� ����, �� �α������ּ���.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}

	// ------------------����ó�� ����-----------------//

	// �������꿩��üũ
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
		$message = $_POST['yymm'].' �� �̹� ������ ����Ǿ� ������ �Ұ��մϴ�.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;			
	}

	// ------------------����ó�� ����-----------------//

	$sql = "delete from INSCHARGE_SET
			where scode = '".$_SESSION['S_SCODE']."' and yymm = '".$yymm."' and inscode = '".$inscode."' ";

	// Ʈ������ ����
    sqlsrv_query($mscon,"BEGIN TRAN");
    $result =  sqlsrv_query( $mscon, $sql );

    if ($result == false){
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' ������ �����ڷ� ���� �� ����';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
    }

	$sql = "delete from INSCHARGE_SET_sub
			where scode = '".$_SESSION['S_SCODE']."' and yymm = '".$yymm."' and inscode = '".$inscode."' ";

	// Ʈ������ ����
    sqlsrv_query($mscon,"BEGIN TRAN");
    $result =  sqlsrv_query( $mscon, $sql );

    if ($result == false){
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' ������ �����ڷ� ���� �� ����_2';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
    }

    sqlsrv_query($mscon,"COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' ������ �����ڷḦ �����Ͽ����ϴ�.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "rtype" => "del");
	echo json_encode($returnJson);
	exit;
}

?>