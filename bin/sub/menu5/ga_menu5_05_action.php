<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

$sdate1 = substr(str_replace('-','',$_POST['SDATE1']) ,0,6);

// ���ó��
if($_POST['pname']){

	if($_SESSION['S_SCODE'] == null || $_POST['pname'] == null ){
		$message = '�ʼ��Է°� ����, �ٽ� ���ó���� �������ּ���.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}


	//-------------------����ó�� ����------------------//
	// Ȯ��ó�� ����üũ
	$sql = "select count(*) cnt from sumst where scode = '".$_SESSION['S_SCODE']."' and yymm = '".$sdate1."' and gbit = '1' ";
	$result  = sqlsrv_query( $mscon, $sql );
	$row =  sqlsrv_fetch_array($result); 
	$cnt = $row["cnt"];

	if($cnt>0){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '�̹� Ȯ��ó���� ���� �ٽ� ���ó���� ������ �� �����ϴ�.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;			
	}
	//-------------------����ó�� ����------------------//


	if($_POST['pname'] == "process_1"){
		$sql= "exec sp_x1_basic_sale_sudang '".$_SESSION['S_SCODE']."' , '".$sdate1."' ";
		$result  = sqlsrv_query($mscon, $sql);	

		if($result === false){
			$message = '������(�Ϲ�) ���ó�� �� �����߻�';
			$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
			echo json_encode($returnJson);
			exit;
		}else{
			$returnJson	= array("result"	=> "suc" , "pname" => "process_1");
			echo json_encode($returnJson);
			exit;
		}
	}else if($_POST['pname'] == "process_2") {
		$sql= "exec sp_x1_car_sale_sudang '".$_SESSION['S_SCODE']."' , '".$sdate1."' ";
		$result  = sqlsrv_query($mscon, $sql);	

		if($result === false){
			$message = '������(�ڵ���) ���ó�� �� �����߻�';
			$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
			echo json_encode($returnJson);
			exit;
		}else{
			$returnJson	= array("result"	=> "suc" , "pname" => "process_2");
			echo json_encode($returnJson);
			exit;
		}

	}else if($_POST['pname'] == "process_3") {
		$sql= "exec sp_x1_manager_sale_sudang '".$_SESSION['S_SCODE']."' , '".$sdate1."' ";
		$result  = sqlsrv_query($mscon, $sql);	

		if($result === false){
			$message = '������(���) ���ó�� �� �����߻�';
			$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
			echo json_encode($returnJson);
			exit;
		}else{
			$returnJson	= array("result"	=> "suc" , "pname" => "process_3");
			echo json_encode($returnJson);
			exit;
		}
	}else if($_POST['pname'] == "process_4") {
		$sql= "exec sp_SudangMasterProcess '".$_SESSION['S_SCODE']."' , '".$sdate1."' ";
		$result  = sqlsrv_query($mscon, $sql);	

		if($result === false){
			$message = '���޼����� ��ü ���� �� �����߻�';
			$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
			echo json_encode($returnJson);
			exit;
		}else{
			$message = '���޼����� ���ó���� �Ϸ�Ǿ����ϴ�.';
			$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "suc" , "pname" => "process_4");
			echo json_encode($returnJson);
			exit;
		}
	}
}


// Ȯ��ó��
if($_POST['hbit'] == "hwak"){

	if($_SESSION['S_SCODE'] == null){
		$message = '�ʼ��Է°� ����, �ٽ� Ȯ��ó���� �������ּ���.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}


	//-------------------����ó�� ����------------------//
	// Ȯ��ó�� ����üũ
	$sql = "select count(*) cnt from sumst where scode = '".$_SESSION['S_SCODE']."' and yymm = '".$sdate1."' and gbit = '1' ";
	$result  = sqlsrv_query( $mscon, $sql );
	$row =  sqlsrv_fetch_array($result); 
	$cnt = $row["cnt"];

	if($cnt>0){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '�ش���� �̹� Ȯ��ó���Ǿ����ϴ�.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;			
	}
	//-------------------����ó�� ����------------------//

	$sql = "update sumst
			set gbit = '1' , jdate = getdate()
			where scode = '".$_SESSION['S_SCODE']."' and yymm = '".$sdate1."'
			";

	$result =  sqlsrv_query( $mscon, $sql );

	if ($result == false){
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' Ȯ��ó�� ������ �����߻�';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
	}

    sqlsrv_query($mscon,"COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' Ȯ��ó���� �Ϸ��Ͽ����ϴ�.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "suc", "rtype" => "up");
	echo json_encode($returnJson);
	exit;

}

?>