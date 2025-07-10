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

//print_r($_POST);

$datacode = array();
$dataname = array();
$gubun = array();

for($i = 1; $i <= 10; $i++){
	if(!empty($_POST['dataname'.(string)$i])){
		$datacode[$i] = 'dataset'.(string)$i;
		$dataname[$i] = iconv("UTF-8","EUC-KR",$_POST['dataname'.(string)$i]);
		$gubun[$i] = $_POST['gubun'.(string)$i];
	}
}

//----------------------------------------------------------//
//                    수정시 처리요직							// 
//----------------------------------------------------------//
if($_POST['type']=='up'){
	// 필수정보 확인
	if($_SESSION['S_SCODE'] == null or $_POST['inscode'] == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '세센연결 error 필수입력값 오류, 재 로그인해주세요.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}


	$sql = "update INSCHARGE_NAMESET
			set dataset1 = '".$_POST['dataset1']."' , dataset2 = '".$_POST['dataset2']."' , dataset3 = '".$_POST['dataset3']."' , dataset4 = '".$_POST['dataset4']."' ,
				dataset5 = '".$_POST['dataset5']."' , dataset6 = '".$_POST['dataset6']."' , dataset7 = '".$_POST['dataset7']."' , dataset8 = '".$_POST['dataset8']."' ,
				dataset9 = '".$_POST['dataset9']."' , dataset10 = '".$_POST['dataset10']."'
			where scode = '".$_SESSION['S_SCODE']."' and yymm = '".$yymm."' and inscode = '".$inscode."'
			";
	$result =  sqlsrv_query( $mscon, $sql );
	
	if ($result == false){
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' 수수료 기초자료 수정 중 오류';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
	}

    sqlsrv_query($mscon,"COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' 수수료 기초자료를 수정하였습니다.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "rtype" => "up");
	echo json_encode($returnJson);
	exit;

}



?>