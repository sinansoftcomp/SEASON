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
// �������� (�۷ι�)
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
			alert("�α��� �� �̿����ּ��� #2...!!!",$conf['loginPageMobile'],'parent');
		}else{
			alert("�α��� �� �̿����ּ��� #2...!!!",$conf['loginPage'],'parent');
		}
*/
	
	// ��Ű���� �����ȣ, �н����� ��������
	$scode	= $_COOKIE["sz_saveid"];
	$spass	= $_COOKIE["sz_savepass"];

	// �������� �ʱ�ȭ������
	if(!$scode){
		session_destroy();
		if(preg_match($mobile_agent, $_SERVER['HTTP_USER_AGENT'])){
			alert("�α��� �� �̿����ּ��� #1_1...!!!",$conf['loginPageMobile'],'parent');
		}else{
			alert("�α��� �� �̿����ּ��� #1...!!!",$conf['loginPage'],'parent');
		}
	}
	
	// �����ȣ, ��й�ȣ, ���𿩺�, �ȵ���id, ���������id
	$returnLogin	= LoginPC($scode , $spass, '', '', '');
	
	// ���� �α����� �ƴϸ�, true���� �ƴϸ� �˷��޼����� �����Ѵ�
	if($returnLogin!==true || $returnLogin!==1){
		if(preg_match($mobile_agent, $_SERVER['HTTP_USER_AGENT'])){
			alert("�α��� �� �̿����ּ��� #2_2...!!!",$conf['loginPageMobile'],'parent');
		}else{
			alert("�α��� �� �̿����ּ��� #2...!!!",$conf['loginPage'],'parent');
		}
	}
	
	
}


$conf['pageRow']  = 25;
$conf['pageRow1']  = 25;


// ����ŷ
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


	// ��й�ȣ ���� ���� ����ī��Ʈ ��ȸ �� �� ���� ��ȸ
	$sql  = "SELECT LOCKYN, FAILCNT, LASTLOGIN, LOCKCNT, DATEDIFF(MINUTE,LASTLOGIN,GETDATE()) DIFFMM 
			 FROM LOGIN_CHK 
			 WHERE SCODE = '".mssql_escape($scode)."' ";
	$result  = sqlsrv_query( $mscon, $sql );
	$row =  sqlsrv_fetch_array($result); 

	$LOCKYN		= $row['LOCKYN'];     // �� ����
	$FAILCNT	= $row['FAILCNT'];      // �α��� ���� Ƚ��
	$LASTLOGIN	= $row['LASTLOGIN'];  // ������ �α��� �õ� �Ͻ�
	$LOCKCNT	= $row['LOCKCNT'];      // �� Ƚ��
	$DIFFMM		= $row['DIFFMM'];       // ������ �α��� �õ� �Ͻ÷κ��� ����� ��(MINUTE)

	if($demo=='2'){

		if($LOCKYN == 'Y'){
			if($DIFFMM < 10)	alert('�α��� 5ȸ ���ӽ��з� 10�а� �α����� �Ұ��մϴ�.');			
		}
		
		if($spass != $fet2['pwd']){
			$ERRCNT = $FAILCNT + 1;

			// Ʈ������ ����
			sqlsrv_query($mscon,"BEGIN TRAN");		
			if($ERRCNT < 5){
				// 5ȸ �̸��� ��� ���� ����ȸ���� ������ �α����Ͻø� ������Ʈ
				$sql="UPDATE LOGIN_CHK
					  SET FAILCNT	= $ERRCNT,
						  LASTLOGIN	= GETDATE()
					  WHERE SCODE = '".mssql_escape($scode)."'  ";

				$result =  sqlsrv_query( $mscon, $sql );
			}else{
				// 5ȸ �̻��� ��� ���� �α��� �� ó��
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
				$message = '��й�ȣ�� ���� �ʽ��ϴ�. '.$ERRCNT.'ȸ �����Դϴ�.';
			}else if($ERRCNT == 4){
				$message = '��й�ȣ '.$ERRCNT.'ȸ �����Դϴ�. 5ȸ ������ 10�а� ������ �Ұ��մϴ�.';
			}else{
				$message = '5ȸ ���� ���з� 10�а� ������ �Ұ��մϴ�.';
			}
			alert($message);

		}

	}


	/* ����� ��� ��ū�� ������Ʈ */
	// ��� udid ������Ʈ
	// ios ��Ⱚ�� �ִ°��
	/*
		���� ��ū ������Ʈ�� ����, ���� �÷�������

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
	

	/*  ���ӷα�  isnert*/

	$REMOTE_ADDR  = $_SERVER["REMOTE_ADDR"];

	$sql="INSERT INTO  USER_LOG (SCODE,IDATE,BIGO,IP) 
		VALUES ('".$fet2['SCODE']."',getdate(),'�α���','".$REMOTE_ADDR."')"; 


	// Ʈ������ ����
	sqlsrv_query($mscon,"BEGIN TRAN");		
	$result =  sqlsrv_query( $mscon, $sql );

	if ($result == false){
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		//echo "<script>alert('���ӷα������� ����!!! ');opener.location.reload(true);self.close();</script>";
		exit;
	}

	// ���������� �α��� �Ǿ��� ��� �� Ǯ��
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
		alert("���ӷα��� ���� ���� �� ����!!!!");
		exit;
	}


	sqlsrv_query($mscon,"COMMIT");


	$_SESSION['S_SCODE']   = $fet2['SCODE'];
	$_SESSION['S_SNAME']   = $fet2['SNAME'];

	// ��뱸��
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
