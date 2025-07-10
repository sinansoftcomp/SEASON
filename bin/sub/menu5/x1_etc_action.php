<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");


//----------------------------------------------------------//
//                    ������ ó������							// 
//----------------------------------------------------------//
if($_POST['type']=='up'){
	// �ʼ����� Ȯ��
	if($_SESSION['S_SCODE'] == null or $_POST['ori_yymm'] == null or $_POST['ori_skey'] == null or $_POST['ori_seq'] == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '�������� error �ʼ��Է°� ����, �� �α������ּ���.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}
	if($_POST['gubun'] == "1"){
		$gubuncode = $_POST['gubuncode1'];
	}else if($_POST['gubun'] == "2") {
		$gubuncode = $_POST['gubuncode2'];
	}else if($_POST['gubun'] == "3") {
		$gubuncode = $_POST['gubuncode3'];
	}else if($_POST['gubun'] == "4") {
		$gubuncode = $_POST['gubuncode4'];
	}


	$etcamt = str_replace(',', '', $_POST['etcamt']);
	$bigo = iconv("UTF-8","EUC-KR",$_POST['bigo']);


	// ------------------����ó�� ����-----------------//

	// Ȯ��ó������ üũ
	$sql	= "
			select count(*) cnt from sumst where scode = '".$_SESSION['S_SCODE']."' and yymm = '".$_POST['ori_yymm']."' and gbit = '1'
				";
	$result  = sqlsrv_query( $mscon, $sql );
	$row =  sqlsrv_fetch_array($result); 
	$cnt = $row["cnt"];

	if($cnt > 0){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = "�����ᰡ Ȯ��ó���� ���� ����_���������� ������ �� �����ϴ�.";
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;			
	}

	// ------------------����ó�� ����-----------------//


	$sql = "update sumst_etc
			set gubuncode = '".$gubuncode."' , etcamt = ".$etcamt." , bigo = '".$bigo."'
			where scode = '".$_SESSION['S_SCODE']."' and yymm = '".$_POST['ori_yymm']."' and skey = '".$_POST['ori_skey']."' and seq = ".$_POST['ori_seq']."
			";
	$result =  sqlsrv_query( $mscon, $sql );

	if ($result == false){
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		echo $sql;
		$message = ' ����_�������� ���� �� ����';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
	}

    sqlsrv_query($mscon,"COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' ����_���������� �����Ͽ����ϴ�.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "rtype" => "up");
	echo json_encode($returnJson);
	exit;
}

//----------------------------------------------------------//
//                    �Է½� ó������							// 
//----------------------------------------------------------//
if($_POST['type']=='in'){
	// �ʼ����� Ȯ��
	if($_SESSION['S_SCODE'] == null or $_POST['yymm'] == null or $_POST['skey'] == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '�������� error �ʼ��Է°� ����, �� �α������ּ���.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}
	$yymm=rtrim(ltrim(str_replace('-','',$_POST['yymm'])));
	$skey = $_POST['skey'];

	if($_POST['gubun'] == "1"){
		$gubuncode = $_POST['gubuncode1'];
	}else if($_POST['gubun'] == "2") {
		$gubuncode = $_POST['gubuncode2'];
	}else if($_POST['gubun'] == "3") {
		$gubuncode = $_POST['gubuncode3'];
	}else if($_POST['gubun'] == "4") {
		$gubuncode = $_POST['gubuncode4'];
	}

	$etcamt = str_replace(',', '', $_POST['etcamt']);
	$bigo = iconv("UTF-8","EUC-KR",$_POST['bigo']);

	// ------------------����ó�� ����-----------------//

	// Ȯ��ó������ üũ
	$sql	= "
			select count(*) cnt from sumst where scode = '".$_SESSION['S_SCODE']."' and yymm = '".$yymm."' and gbit = '1'
				";
	$result  = sqlsrv_query( $mscon, $sql );
	$row =  sqlsrv_fetch_array($result); 
	$cnt = $row["cnt"];

	if($cnt > 0){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = "�����ᰡ Ȯ��ó���� ���� ����_���������� ����� �� �����ϴ�.";
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;			
	}

	// ------------------����ó�� ����-----------------//


	// ����(seq)����
	$sql	= "
			select isnull(max(seq),0)+1 seq from sumst_etc where scode = '".$_SESSION['S_SCODE']."' and yymm = '".$yymm."' and skey = '".$skey."'
				";
	$result  = sqlsrv_query( $mscon, $sql );
	$row =  sqlsrv_fetch_array($result); 
	$seq = $row["seq"];


	$sql = "insert into sumst_etc(scode,yymm,skey,seq,gubuncode,etcamt,bigo)
			values('".$_SESSION['S_SCODE']."','".$yymm."','".$skey."',".$seq.",'".$gubuncode."',".$etcamt.",'".$bigo."')";
	$result =  sqlsrv_query( $mscon, $sql );

	if ($result == false){
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' ����_�������� ��� �� ����';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
	}


    sqlsrv_query($mscon,"COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' ����_���������� ����Ͽ����ϴ�.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "yymm"=>"$yymm" , "skey"=>"$skey" , "seq" => "$seq", "rtype" => "in");
	echo json_encode($returnJson);
	exit;

}


//----------------------------------------------------------//
//                    ������ ó������							// 
//----------------------------------------------------------//
if($_POST['type'] == "del"){
	// �ʼ����� Ȯ��
	if($_SESSION['S_SCODE'] == null or $_POST['ori_yymm'] == null or $_POST['ori_skey'] == null or $_POST['ori_seq'] == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '�������� error �ʼ��Է°� ����, �� �α������ּ���.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}

	// ------------------����ó�� ����-----------------//

	// Ȯ��ó������ üũ
	$sql	= "
			select count(*) cnt from sumst where scode = '".$_SESSION['S_SCODE']."' and yymm = '".$_POST['ori_yymm']."' and gbit = '1'
				";
	$result  = sqlsrv_query( $mscon, $sql );
	$row =  sqlsrv_fetch_array($result); 
	$cnt = $row["cnt"];

	if($cnt > 0){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = "�����ᰡ Ȯ��ó���� ���� ����_���������� ������ �� �����ϴ�.";
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;			
	}

	// ------------------����ó�� ����-----------------//

	$sql = "delete from sumst_etc
			where scode = '".$_SESSION['S_SCODE']."' and yymm = '".$_POST['ori_yymm']."' and skey = '".$_POST['ori_skey']."' and seq = ".$_POST['ori_seq']." ";

	// Ʈ������ ����
    sqlsrv_query($mscon,"BEGIN TRAN");
    $result =  sqlsrv_query( $mscon, $sql );

    if ($result == false){
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' ����_�������� ���� �� ����';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
    }

    sqlsrv_query($mscon,"COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' ����_���������� �����Ͽ����ϴ�.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "rtype" => "del");
	echo json_encode($returnJson);
	exit;
}

?>