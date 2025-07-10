<?

session_start();
$loginCheck	= false;

include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

if (isset($_GET['demo'])) {
	$demo = $_GET['demo'];	// 데모 로그인o
}else{
	$demo = '2';			// 데모 로그인x
}


if($demo == '2'){
	if(!$_POST['SCODE']) alert('직원 ID를 입력해주세요.');
	if(!$_POST['SSPWD']) alert('비밀번호를 입력해주세요.');
}

// 모바일의 경우 해당값 Y로 전달
if (isset($_POST['mobile'])) {
	$mobile	= $_POST['mobile'];
}else{
	$mobile	= 'N';
}

/* 모바일의 경우 토큰값 */
if (isset($_POST['ios_udid'])) {
	$ios_udid	= $_POST['ios_udid'];
}else{
	$ios_udid	= '';
}

if (isset($_POST['and_udid'])) {
	$and_udid	= $_POST['and_udid'];
}else{
	$and_udid	= '';
}

if($demo == '1'){
	$sql= "select SCODE, convert(nvarchar(100),decryptbykey(SSPWD)) as pwd, SNAME from SWON where SCODE='A101'";	

	$qry  = sqlsrv_query( $mscon, $sql );
	$fet1 = sqlsrv_fetch_array($qry);

	$scode	= $fet1['SCODE'];
	$spass	= $fet1['pwd'];

}else{
	$scode	= $_POST['SCODE'];
	$spass	= $_POST['SSPWD'];
}


$mobile_agent = "/(iPod|iPhone|Android|BlackBerry|SymbianOS|SCH-M\d+|Opera Mini|Windows CE|Nokia|SonyEricsson|webOS|PalmOS)/";

if($scode && $spass){

	// 로그인체크
	$returnLogin	= LoginPC($scode , $spass, $demo, $ios_udid, $and_udid);


	echo "<!-- 로그인 체크 : ".$returnLogin." -->\n";
	
	if($returnLogin===true){

		/*
			구분 
			1:관리자
			2:한국농장
			3:배송기사
			4:중국
			5:베트남
			6:콜롬비아
		*/
		/* 임시주석처리
		if($_SESSION['S_SBIT'] == '1' || $_SESSION['S_SBIT'] == '2'){
			goto_url("/bin/main.html");			// 관리자 및 한국농장
		}else{
			alert("프로그램 접속 권한이 없습니다");
		}
		*/
		// 메인페이지 작업위한 임시소스
		if($_SESSION['S_SBIT'] == '1'){
			goto_url("/bin/main.html");			// 관리자 및 한국농장
		}else if($_SESSION['S_SBIT'] == '2'){
			goto_url("/bin/main2.html");			// 관리자 및 한국농장
		}else{
			alert("프로그램 접속 권한이 없습니다");
		}
		

	}else{

		alert($returnLogin,"login.php");
		
	}
}



?>

