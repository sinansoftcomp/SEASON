<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

//----------------------------------------------------------//
//                    ������ ó������							// 
//----------------------------------------------------------//
if($_POST['type_nm']=='nmgubun'){
	// �ʼ����� Ȯ��
	if($_SESSION['S_SCODE'] == null or $_POST['yymm_nm'] == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '�������� error �ʼ��Է°� ����, �� �α������ּ���.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}
	sqlsrv_query($mscon,"BEGIN TRAN");
	// ���Ī�� ��� nmgubun �� ������Ʈ
	$sql = "update ins_ipmst
			set nmgubun = case when isnull(ksman,'') = '' then 'Y' else '' end
			where scode = '".$_SESSION['S_SCODE']."' and yymm = '".$_POST['yymm_nm']."' and nmgubun is null
			";
	$result =  sqlsrv_query( $mscon, $sql );
	
	if ($result == false){
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' ���Ī��� ������Ʈ�� ����_11';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
	}

	// ������ ��Ī�� ������ִٸ� ������Ʈ
	$sql = "
			update ins_ipmst
			set ins_ipmst.ksman = isnull(b.ksman,'')
			from
				(
					select scode, yymm,kcode,ksman
					from ins_ipmst
					where scode = '".$_SESSION['S_SCODE']."' and yymm = '".$_POST['yymm_nm']."' and isnull(ksman,'')=''
					group by scode, yymm,kcode,ksman
					) a left outer join (
										select scode, yymm,kcode,ksman 
										from ins_ipmst
										where scode = '".$_SESSION['S_SCODE']."' and yymm = convert(varchar(6),dateadd(MONTH,-1,'".$_POST['yymm_nm']."'+'01'),112)
										group by scode, yymm,kcode,ksman
										) b on a.scode = b.scode and a.kcode = b.kcode 
			where ins_ipmst.scode = '".$_SESSION['S_SCODE']."' and ins_ipmst.yymm='".$_POST['yymm_nm']."' and ins_ipmst.kcode = a.kcode and isnull(ins_ipmst.ksman,'') = ''	
			";
	$result =  sqlsrv_query( $mscon, $sql );
	
	if ($result == false){
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' ���Ī��� ������Ʈ�� ����_22';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
	}


    sqlsrv_query($mscon,"COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' ���Ī���������Ʈ�� �Ϸ��Ͽ����ϴ�.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "");
	echo json_encode($returnJson);
	exit;

}



?>