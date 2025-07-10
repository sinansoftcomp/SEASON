<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

$sdate1 = substr(str_replace('-','',$_POST['SDATE1']) ,0,6);

// 계산처리
if($_POST['pname']){

	if($_SESSION['S_SCODE'] == null || $_POST['pname'] == null ){
		$message = '필수입력값 오류, 다시 계산처리를 진행해주세요.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}


	//-------------------예외처리 시작------------------//
	// 확정처리 여부체크
	$sql = "select count(*) cnt from sumst where scode = '".$_SESSION['S_SCODE']."' and yymm = '".$sdate1."' and gbit = '1' ";
	$result  = sqlsrv_query( $mscon, $sql );
	$row =  sqlsrv_fetch_array($result); 
	$cnt = $row["cnt"];

	if($cnt>0){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '이미 확정처리된 월은 다시 계산처리를 진행할 수 없습니다.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;			
	}
	//-------------------예외처리 종료------------------//


	if($_POST['pname'] == "process_1"){
		$sql= "exec sp_x1_basic_sale_sudang '".$_SESSION['S_SCODE']."' , '".$sdate1."' ";
		$result  = sqlsrv_query($mscon, $sql);	

		if($result === false){
			$message = '보종군(일반) 계산처리 중 오류발생';
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
			$message = '보종군(자동차) 계산처리 중 오류발생';
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
			$message = '보종군(장기) 계산처리 중 오류발생';
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
			$message = '지급수수료 전체 집계 중 오류발생';
			$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
			echo json_encode($returnJson);
			exit;
		}else{
			$message = '지급수수료 계산처리가 완료되었습니다.';
			$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "suc" , "pname" => "process_4");
			echo json_encode($returnJson);
			exit;
		}
	}
}


// 확정처리
if($_POST['hbit'] == "hwak"){

	if($_SESSION['S_SCODE'] == null){
		$message = '필수입력값 오류, 다시 확정처리를 진행해주세요.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}


	//-------------------예외처리 시작------------------//
	// 확정처리 여부체크
	$sql = "select count(*) cnt from sumst where scode = '".$_SESSION['S_SCODE']."' and yymm = '".$sdate1."' and gbit = '1' ";
	$result  = sqlsrv_query( $mscon, $sql );
	$row =  sqlsrv_fetch_array($result); 
	$cnt = $row["cnt"];

	if($cnt>0){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '해당월은 이미 확정처리되었습니다.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;			
	}
	//-------------------예외처리 종료------------------//

	$sql = "update sumst
			set gbit = '1' , jdate = getdate()
			where scode = '".$_SESSION['S_SCODE']."' and yymm = '".$sdate1."'
			";

	$result =  sqlsrv_query( $mscon, $sql );

	if ($result == false){
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' 확정처리 진행중 오류발생';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
	}

    sqlsrv_query($mscon,"COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' 확정처리를 완료하였습니다.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "suc", "rtype" => "up");
	echo json_encode($returnJson);
	exit;

}

?>