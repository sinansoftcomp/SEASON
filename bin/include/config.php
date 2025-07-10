<?
/*
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 1);
ini_set('session.gc_maxlifetime', 10);

*/
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

header('Access-Control-Allow-Origin: *');
header('Content-Type: text/html; charset=euc-kr');

$conf['homeDir'] = "/bin";
$conf['mobileDir'] = "/bin";
$conf['loginPage']	=  "http://bokdola.co.kr/login.php";
$conf['logoutPage']	= $_SERVER['DOCUMENT_ROOT']."/logoutOk.php";

$conf['MainDir'] = $_SERVER['DOCUMENT_ROOT'];
// 설정파일 (글로벎)
$conf['rootDir'] = $_SERVER['DOCUMENT_ROOT'].$conf['homeDir'];
$conf['daumKey']	= 'a085465824f67e5c320c823cc09e36b5';
include_once($conf['rootDir']."/include/source/common.lib.php");
include_once($conf['rootDir']."/include/dbConn.php");

$mobile_agent = "/(iPod|iPhone|Android|BlackBerry|SymbianOS|SCH-M\d+|Opera Mini|Windows CE|Nokia|SonyEricsson|webOS|PalmOS)/";

if(isset($loginCheck)){
	$loginCheck = $loginCheck;
}else{
	$loginCheck = true;
}


if($loginCheck!==false && !isset($_SESSION['S_SCODE'])){
/*
		if(preg_match($mobile_agent, $_SERVER['HTTP_USER_AGENT'])){
			alert("로그인 후 이용해주세요 #2...!!!",$conf['loginPageMobile'],'parent');
		}else{
			alert("로그인 후 이용해주세요 #2...!!!",$conf['loginPage'],'parent');
		}
*/
	
	// 쿠키에서 사원번호, 패스워드 가져오기
	$scode	= $_COOKIE["sz_saveid"];
	$spass	= $_COOKIE["sz_savepass"];

	// 쿠기삭제시 초기화면으로
	if(!$scode){
		session_destroy();
		if(preg_match($mobile_agent, $_SERVER['HTTP_USER_AGENT'])){
			alert("로그인 후 이용해주세요 #1_1...!!!",$conf['loginPageMobile'],'parent');
		}else{
			alert("로그인 후 이용해주세요 #1...!!!",$conf['loginPage'],'parent');
		}
	}
	
	// 사원번호, 비밀번호, 데모여부, 안드기기id, 아이폰기기id
	$returnLogin	= LoginPC($scode , $spass, '', '', '');
	
	// 정상 로그인이 아니면, true값이 아니면 알럿메세지를 리턴한다
	if($returnLogin!==true || $returnLogin!==1){
		if(preg_match($mobile_agent, $_SERVER['HTTP_USER_AGENT'])){
			alert("로그인 후 이용해주세요 #2_2...!!!",$conf['loginPageMobile'],'parent');
		}else{
			alert("로그인 후 이용해주세요 #2...!!!",$conf['loginPage'],'parent');
		}
	}
	
	
}


$conf['pageRow']  = 25;
$conf['pageRow1']  = 25;


// 마스킹
function masking($str){
    $strlen = mb_strlen($str, 'euc-kr');
    $retrun = mb_substr($str, 0, 1, 'euc-kr');

    if ($strlen < 3) {
        for ($i=0; $i<$strlen-1; $i++) {
            $retrun .= '*';
        }
    } else {
        for ($i=0; $i<$strlen-2; $i++) {
            $retrun .= '*';
        }
        $retrun .= mb_substr($str, -1, 1, 'euc-kr');
    }

    return $retrun;
}

function mssql_escape($data) {
    if ( !isset($data) or empty($data) ) return '';
	if ( is_numeric($data) ) return $data;

	$non_displayables = array(
		'/%0[0-8bcef]/',            // url encoded 00-08, 11, 12, 14, 15
		'/%1[0-9a-f]/',             // url encoded 16-31
		'/[\x00-\x08]/',            // 00-08
		'/\x0b/',                   // 11
		'/\x0c/',                   // 12
		'/[\x0e-\x1f]/'             // 14-31
	);
	foreach ( $non_displayables as $regex )
		$data = preg_replace( $regex, '', $data );
	$data = str_replace("'", "''", $data );
	return $data;
}


function LoginPC($scode , $spass, $demo, $ios_udid, $and_udid){
	global $mscon;


	if($demo=='1'){
		$sql= "select SCODE, convert(nvarchar(100),decryptbykey(SSPWD)) as pwd, SNAME, SBIT
			   from SWON 
			   where SCODE='A101' ";
	}else{
		$sql= "Select SCODE, convert(nvarchar(100),decryptbykey(SSPWD)) as pwd, SNAME, SBIT
				From SWON 
				Where SCODE ='".mssql_escape($scode)."' ";
	}

	$qry  = sqlsrv_query( $mscon, $sql );
	$fet2 = sqlsrv_fetch_array($qry);


	// 비밀번호 기존 오류 누적카운트 조회 및 락 여부 조회
	$sql  = "SELECT LOCKYN, FAILCNT, LASTLOGIN, LOCKCNT, DATEDIFF(MINUTE,LASTLOGIN,GETDATE()) DIFFMM 
			 FROM LOGIN_CHK 
			 WHERE SCODE = '".mssql_escape($scode)."' ";
	$result  = sqlsrv_query( $mscon, $sql );
	$row =  sqlsrv_fetch_array($result); 

	$LOCKYN		= $row['LOCKYN'];     // 락 여부
	$FAILCNT	= $row['FAILCNT'];      // 로그인 실패 횟수
	$LASTLOGIN	= $row['LASTLOGIN'];  // 마지막 로그인 시도 일시
	$LOCKCNT	= $row['LOCKCNT'];      // 락 횟수
	$DIFFMM		= $row['DIFFMM'];       // 마지막 로그인 시도 일시로부터 경과된 분(MINUTE)

	if($demo=='2'){

		if($LOCKYN == 'Y'){
			if($DIFFMM < 10)	alert('로그인 5회 연속실패로 10분간 로그인이 불가합니다.');			
		}
		
		if($spass != $fet2['pwd']){
			$ERRCNT = $FAILCNT + 1;

			// 트렌젝션 시작
			sqlsrv_query($mscon,"BEGIN TRAN");		
			if($ERRCNT < 5){
				// 5회 미만일 경우 실패 실패회수와 마지막 로그인일시만 업데이트
				$sql="UPDATE LOGIN_CHK
					  SET FAILCNT	= $ERRCNT,
						  LASTLOGIN	= GETDATE()
					  WHERE SCODE = '".mssql_escape($scode)."'  ";

				$result =  sqlsrv_query( $mscon, $sql );
			}else{
				// 5회 이상일 경우 실패 로그인 락 처리
				$sql="UPDATE LOGIN_CHK
					  SET FAILCNT	= $ERRCNT,
						  LASTLOGIN	= GETDATE(),
						  LOCKYN	= 'Y',
						  LOCKCNT	= LOCKCNT + 1
					  WHERE SCODE = '".mssql_escape($scode)."' ";		

				$result =  sqlsrv_query( $mscon, $sql );				
			}

			sqlsrv_query($mscon,"COMMIT");

			if($ERRCNT < 4){
				$message = '비밀번호가 맞지 않습니다. '.$ERRCNT.'회 실패입니다.';
			}else if($ERRCNT == 4){
				$message = '비밀번호 '.$ERRCNT.'회 실패입니다. 5회 오류시 10분간 접속이 불가합니다.';
			}else{
				$message = '5회 연속 실패로 10분간 접속이 불가합니다.';
			}
			alert($message);

		}

	}


	/* 모바일 경우 토큰값 업데이트 */
	// 기기 udid 업데이트
	// ios 기기값이 있는경우
	/*
		추후 토큰 업데이트시 정리, 현재 컬럼미정리

	if($demo=='2' && $ios_udid){

		$sql="update swon set PUSHGUBUN='2',PUSHTOKEN='".$ios_udid."' where scode='".$fet['SCODE']."' and skey='".$fet2['SKEY']."'" ;	
		
		sqlsrv_query($mscon,"BEGIN TRAN");
		$result =  sqlsrv_query( $mscon, $sql );		
		if($result==false){
			sqlsrv_query($mscon,"ROLLBACK");
		}

		sqlsrv_query($mscon,"COMMIT");

	}elseif($demo=='2' && $and_udid){

		$sql="update swon set PUSHGUBUN='1',PUSHTOKEN='".$and_udid."' where scode='".$fet['SCODE']."' and skey='".$fet2['SKEY']."'" ;	

		sqlsrv_query($mscon,"BEGIN TRAN");
		$result =  sqlsrv_query( $mscon, $sql );		
		if($result==false){
			sqlsrv_query($mscon,"ROLLBACK");
		}

		sqlsrv_query($mscon,"COMMIT");
	}

	*/
	

	/*  접속로그  isnert*/

	$REMOTE_ADDR  = $_SERVER["REMOTE_ADDR"];

	$sql="INSERT INTO  USER_LOG (SCODE,IDATE,BIGO,IP) 
		VALUES ('".$fet2['SCODE']."',getdate(),'로그인','".$REMOTE_ADDR."')"; 


	// 트렌젝션 시작
	sqlsrv_query($mscon,"BEGIN TRAN");		
	$result =  sqlsrv_query( $mscon, $sql );

	if ($result == false){
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		//echo "<script>alert('접속로그저장중 오류!!! ');opener.location.reload(true);self.close();</script>";
		exit;
	}

	// 정상적으로 로그인 되었을 경우 락 풀기
	$sql="UPDATE LOGIN_CHK
		  SET FAILCNT	= 0,
			  LASTLOGIN	= GETDATE(),
			  LOCKYN	= 'N'
		  WHERE SCODE = '".mssql_escape($scode)."' ";


	$result =  sqlsrv_query( $mscon, $sql );

	if ($result == false){
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		alert("접속로그인 정보 저장 중 오류!!!!");
		exit;
	}


	sqlsrv_query($mscon,"COMMIT");


	$_SESSION['S_SCODE']   = $fet2['SCODE'];
	$_SESSION['S_SNAME']   = $fet2['SNAME'];

	// 사용구분
	$_SESSION['S_SBIT']    = $fet2['SBIT'];

	$_SESSION['homeDir']   = "/bin" ;


	return true;
	
}



function Encrypt_where($str, $secret_key='secret key', $secret_iv='secret iv')
{
    $key = hash('sha256', $secret_key);
    $iv = substr(hash('sha256', $secret_iv), 0, 16)    ;
    return str_replace("=", "", base64_encode(
    	openssl_encrypt($str, "AES-256-CBC", $key, 0, $iv))
    );
}

function Decrypt_where($str, $secret_key='secret key', $secret_iv='secret iv')
{
    $key = hash('sha256', $secret_key);
    $iv = substr(hash('sha256', $secret_iv), 0, 16);
    return openssl_decrypt(
    	base64_decode($str), "AES-256-CBC", $key, 0, $iv
    );
}

$secret_key = "sinit_secretkey";
$secret_iv = "sinit_secretiv";

?>
