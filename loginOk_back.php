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
	if(!$_POST['SCODE']) alert('회사 ID를 입력해주세요.');
	if(!$_POST['SKEY']) alert('직원 ID를 입력해주세요.');
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
	$sql= "select SCODE, SKEY, convert(nvarchar(100),decryptbykey(SSPWD)) as pwd, SNAME, TBIT from SWON where SCODE='GADEMO' and SKEY='S24010001'";	

	$qry  = sqlsrv_query( $mscon, $sql );
	$fet1 = sqlsrv_fetch_array($qry);

	$scode	= $fet1['SCODE'];
	$skey	= $fet1['SKEY'];
	$spass	= $fet1['pwd'];

}else{
	$scode	= $_POST['SCODE'];
	$skey	= $_POST['SKEY'];
	$spass	= $_POST['SSPWD'];
}


$mobile_agent = "/(iPod|iPhone|Android|BlackBerry|SymbianOS|SCH-M\d+|Opera Mini|Windows CE|Nokia|SonyEricsson|webOS|PalmOS)/";

if($scode && $skey && $spass){

	
	if($mobile == 'Y'){	// 모바일의 경우(현재는 분리필요가 없어서 일단은 같은 함수 호출)
		$returnLogin	= LoginPC($scode , $skey , $spass, $demo, $ios_udid, $and_udid);
	}else{
		$returnLogin	= LoginPC($scode , $skey , $spass, $demo, $ios_udid, $and_udid);
	}

	
	if($returnLogin===true){

		if($mobile == 'Y'){		// 모바일 로그인의 경우

			//window.AppUI.get_reg_id();
			goto_url("/bin/mainmobile.php");	

		}else{					// Web 관리자 페이지 및 인트라넷

			/*
				AUTHOR 권한 
				1:본사
				2:인트라넷
				3:사용권한없음
			*/
			if($_SESSION['S_MASTER'] == 'A'){
				goto_url("/bin/main.html");			// 본사
			}else if($_SESSION['S_MASTER'] == 'B'){
				goto_url("/bin/mainjisa.html");		// 인트라넷
			}else{
				alert("프로그램 접속 권한이 없습니다");
			}
		}



	}else{
		if(preg_match($mobile_agent, $_SERVER['HTTP_USER_AGENT'])){
			alert($returnLogin,"m_login.php");
		}else{
			alert($returnLogin,"login.php");
		}
	}
}



?>

