<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

$inscode = $_POST['inscode'];
$datacode = array();
$dataname = array();
$gubun = array();

$cnt = 0;
for($i = 1; $i <= 10; $i++){
	if(!empty($_POST['dataname'.(string)$i])){
		$cnt = $cnt+1;

		$datacode[$cnt] = 'dataset'.(string)$i;
		$dataname[$cnt] = iconv("UTF-8","EUC-KR",$_POST['dataname'.(string)$i]);
		$gubun[$cnt] = $_POST['gubun'.(string)$i];
	}
}

//----------------------------------------------------------//
//                    ����� ó������							// 
//----------------------------------------------------------//
if($_POST['type']=='save'){
	// �ʼ����� Ȯ��
	if($_SESSION['S_SCODE'] == null or $_POST['inscode'] == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '�������� error �ʼ��Է°� ����, �� �α������ּ���.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}


	$sql = "delete from INSCHARGE_NAMESET
			where scode = '".$_SESSION['S_SCODE']."' and inscode = '".$inscode."'
			";
	$result =  sqlsrv_query( $mscon, $sql );
	
	if ($result == false){
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' ������ ���ʼ��� ���� �� ����_1';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
	}

	for($i=1; $i<=$cnt; $i++){
		$sql = "insert into INSCHARGE_NAMESET(scode,inscode,datacode,dataname,gubun)
				values('".$_SESSION['S_SCODE']."','".$inscode."','".$datacode[$i]."','".$dataname[$i]."','".$gubun[$i]."')
				";
		$result =  sqlsrv_query( $mscon, $sql );
		
		if ($result == false){
			sqlsrv_query($mscon,"ROLLBACK");
			sqlsrv_free_stmt($result);
			sqlsrv_close($mscon);

			$message = ' ������ ���ʼ��� ���� �� ����_2';
			$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
			echo json_encode($returnJson);
			exit;
		}
	}

    sqlsrv_query($mscon,"COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' ������ ���ʼ����� �����Ͽ����ϴ�.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "" , "inscode" => "$inscode", "rtype" => "save");
	echo json_encode($returnJson);
	exit;

}

//----------------------------------------------------------//
//                    �ʱ�ȭ�� ó������							// 
//----------------------------------------------------------//
if($_POST['type']=='reset'){
	// �ʼ����� Ȯ��
	if($_SESSION['S_SCODE'] == null or $_POST['inscode'] == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '�������� error �ʼ��Է°� ����, �� �α������ּ���.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}


	$sql = "delete from INSCHARGE_NAMESET
			where scode = '".$_SESSION['S_SCODE']."' and inscode = '".$inscode."'
			";
	$result =  sqlsrv_query( $mscon, $sql );
	
	if ($result == false){
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' ������ ���ʼ��� �ʱ�ȭ �� ����_1';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
	}

    sqlsrv_query($mscon,"COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' ������ ���ʼ����� �ʱ�ȭ�Ͽ����ϴ�.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "" , "inscode" => "$inscode" , "rtype" => "reset");
	echo json_encode($returnJson);
	exit;

}

?>