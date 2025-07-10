<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

$inscode = $_POST['inscode'];

$ksuname = array();
$gsuname = array();


for($i = 1; $i <= 20; $i++){
	$ksuname[$i] = iconv("UTF-8","EUC-KR",$_POST['ksuname'.(string)$i]);
}
for($i = 1; $i <= 15; $i++){
	$gsuname[$i] = iconv("UTF-8","EUC-KR",$_POST['gsuname'.(string)$i]);
}


//----------------------------------------------------------//
//                    ����� ó������							// 
//----------------------------------------------------------//
if($_POST['type']=='save'){
	// �ʼ����� Ȯ��
	if($_SESSION['S_SCODE'] == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '�������� error �ʼ��Է°� ����, �� �α������ּ���.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}


	for($i=1; $i<=20; $i++){
		$sql = "
				update suname_set
				set suname = '".$ksuname[$i]."' , useyn = '".$_POST['kuseyn'.$i]."'
				where scode = '".$_SESSION['S_SCODE']."' and sucode = '".$_POST['kamt'.$i]."'
				";
		$result =  sqlsrv_query( $mscon, $sql );
		
		if ($result == false){
			sqlsrv_query($mscon,"ROLLBACK");
			sqlsrv_free_stmt($result);
			sqlsrv_close($mscon);

			$message = ' ������ ��Ī ���� �� ����_1';
			$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
			echo json_encode($returnJson);
			exit;
		}
	}

	for($i=1; $i<=15; $i++){
		$sql = "
				update suname_set
				set suname = '".$gsuname[$i]."' , useyn = '".$_POST['guseyn'.$i]."'
				where scode = '".$_SESSION['S_SCODE']."' and sucode = '".$_POST['gamt'.$i]."'
				";
		$result =  sqlsrv_query( $mscon, $sql );
		
		if ($result == false){
			sqlsrv_query($mscon,"ROLLBACK");
			sqlsrv_free_stmt($result);
			sqlsrv_close($mscon);

			$message = ' ������ ��Ī ���� �� ����_2';
			$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
			echo json_encode($returnJson);
			exit;
		}
	}

    sqlsrv_query($mscon,"COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' ������ ��Ī�� �����Ͽ����ϴ�.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "" , "inscode" => "$inscode", "rtype" => "save");
	echo json_encode($returnJson);
	exit;

}

//----------------------------------------------------------//
//                    �ʱ�ȭ�� ó������							// 
//----------------------------------------------------------//
if($_POST['type']=='reset'){
	// �ʼ����� Ȯ��
	if($_SESSION['S_SCODE'] == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '�������� error �ʼ��Է°� ����, �� �α������ּ���.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}


	for($i=1; $i<=20; $i++){
		if($i==1){
			$sql = "
					update suname_set
					set suname = '�Ű�������' , useyn = 'Y'
					where scode = '".$_SESSION['S_SCODE']."' and sucode = '".$_POST['kamt'.$i]."'
					";
		}else if($i==2) {
			$sql = "
					update suname_set
					set suname = '����������' , useyn = 'Y'
					where scode = '".$_SESSION['S_SCODE']."' and sucode = '".$_POST['kamt'.$i]."'
					";
		}else{
			$sql = "
					update suname_set
					set suname = '' , useyn = 'N'
					where scode = '".$_SESSION['S_SCODE']."' and sucode = '".$_POST['kamt'.$i]."'
					";
		}
		$result =  sqlsrv_query( $mscon, $sql );
		
		if ($result == false){
			sqlsrv_query($mscon,"ROLLBACK");
			sqlsrv_free_stmt($result);
			sqlsrv_close($mscon);

			$message = ' ������ ��Ī �ʱ�ȭ �� ����_1';
			$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
			echo json_encode($returnJson);
			exit;
		}
	}

	for($i=1; $i<=15; $i++){

		if($i==1){
			$sql = "
					update suname_set
					set suname = '�ҵ漼' , useyn = 'Y'
					where scode = '".$_SESSION['S_SCODE']."' and sucode = '".$_POST['gamt'.$i]."'
					";
		}else if($i==2) {
			$sql = "
					update suname_set
					set suname = '�ֹμ�' , useyn = 'Y'
					where scode = '".$_SESSION['S_SCODE']."' and sucode = '".$_POST['gamt'.$i]."'
					";
		}else{
			$sql = "
					update suname_set
					set suname = '' , useyn = 'N'
					where scode = '".$_SESSION['S_SCODE']."' and sucode = '".$_POST['gamt'.$i]."'
					";
		}

		$result =  sqlsrv_query( $mscon, $sql );
		
		if ($result == false){
			sqlsrv_query($mscon,"ROLLBACK");
			sqlsrv_free_stmt($result);
			sqlsrv_close($mscon);

			$message = ' ������ ��Ī �ʱ�ȭ �� ����_2';
			$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
			echo json_encode($returnJson);
			exit;
		}
	}

    sqlsrv_query($mscon,"COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' ������ ��Ī�� �ʱ�ȭ�Ͽ����ϴ�.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "" , "inscode" => "$inscode", "rtype" => "save");
	echo json_encode($returnJson);
	exit;

}

?>