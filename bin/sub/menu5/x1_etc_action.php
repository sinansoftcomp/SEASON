<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");


//----------------------------------------------------------//
//                    수정시 처리요직							// 
//----------------------------------------------------------//
if($_POST['type']=='up'){
	// 필수정보 확인
	if($_SESSION['S_SCODE'] == null or $_POST['ori_yymm'] == null or $_POST['ori_skey'] == null or $_POST['ori_seq'] == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '세센연결 error 필수입력값 오류, 재 로그인해주세요.';
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


	// ------------------예외처리 시작-----------------//

	// 확정처리여부 체크
	$sql	= "
			select count(*) cnt from sumst where scode = '".$_SESSION['S_SCODE']."' and yymm = '".$_POST['ori_yymm']."' and gbit = '1'
				";
	$result  = sqlsrv_query( $mscon, $sql );
	$row =  sqlsrv_fetch_array($result); 
	$cnt = $row["cnt"];

	if($cnt > 0){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = "수수료가 확정처리된 월은 지급_공제내역을 수정할 수 없습니다.";
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;			
	}

	// ------------------예외처리 종료-----------------//


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
		$message = ' 지급_공제내역 수정 중 오류';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
	}

    sqlsrv_query($mscon,"COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' 지급_공제내역을 수정하였습니다.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "rtype" => "up");
	echo json_encode($returnJson);
	exit;
}

//----------------------------------------------------------//
//                    입력시 처리요직							// 
//----------------------------------------------------------//
if($_POST['type']=='in'){
	// 필수정보 확인
	if($_SESSION['S_SCODE'] == null or $_POST['yymm'] == null or $_POST['skey'] == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '세센연결 error 필수입력값 오류, 재 로그인해주세요.';
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

	// ------------------예외처리 시작-----------------//

	// 확정처리여부 체크
	$sql	= "
			select count(*) cnt from sumst where scode = '".$_SESSION['S_SCODE']."' and yymm = '".$yymm."' and gbit = '1'
				";
	$result  = sqlsrv_query( $mscon, $sql );
	$row =  sqlsrv_fetch_array($result); 
	$cnt = $row["cnt"];

	if($cnt > 0){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = "수수료가 확정처리된 월은 지급_공제내역을 등록할 수 없습니다.";
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;			
	}

	// ------------------예외처리 종료-----------------//


	// 순번(seq)따기
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
		$message = ' 지급_공제내역 등록 중 오류';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
	}


    sqlsrv_query($mscon,"COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' 지급_공제내역을 등록하였습니다.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "yymm"=>"$yymm" , "skey"=>"$skey" , "seq" => "$seq", "rtype" => "in");
	echo json_encode($returnJson);
	exit;

}


//----------------------------------------------------------//
//                    삭제시 처리요직							// 
//----------------------------------------------------------//
if($_POST['type'] == "del"){
	// 필수정보 확인
	if($_SESSION['S_SCODE'] == null or $_POST['ori_yymm'] == null or $_POST['ori_skey'] == null or $_POST['ori_seq'] == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '세센연결 error 필수입력값 오류, 재 로그인해주세요.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}

	// ------------------예외처리 시작-----------------//

	// 확정처리여부 체크
	$sql	= "
			select count(*) cnt from sumst where scode = '".$_SESSION['S_SCODE']."' and yymm = '".$_POST['ori_yymm']."' and gbit = '1'
				";
	$result  = sqlsrv_query( $mscon, $sql );
	$row =  sqlsrv_fetch_array($result); 
	$cnt = $row["cnt"];

	if($cnt > 0){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = "수수료가 확정처리된 월은 지급_공제내역을 삭제할 수 없습니다.";
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;			
	}

	// ------------------예외처리 종료-----------------//

	$sql = "delete from sumst_etc
			where scode = '".$_SESSION['S_SCODE']."' and yymm = '".$_POST['ori_yymm']."' and skey = '".$_POST['ori_skey']."' and seq = ".$_POST['ori_seq']." ";

	// 트렌젝션 시작
    sqlsrv_query($mscon,"BEGIN TRAN");
    $result =  sqlsrv_query( $mscon, $sql );

    if ($result == false){
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' 지급_공제내역 삭제 중 오류';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
    }

    sqlsrv_query($mscon,"COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' 지급_공제내역을 삭제하였습니다.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "rtype" => "del");
	echo json_encode($returnJson);
	exit;
}

?>