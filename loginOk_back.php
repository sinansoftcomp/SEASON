<?

session_start();

$loginCheck	= false;
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

if (isset($_GET['demo'])) {
	$demo = $_GET['demo'];	// ���� �α���o
}else{
	$demo = '2';			// ���� �α���x
}


if($demo == '2'){
	if(!$_POST['SCODE']) alert('ȸ�� ID�� �Է����ּ���.');
	if(!$_POST['SKEY']) alert('���� ID�� �Է����ּ���.');
	if(!$_POST['SSPWD']) alert('��й�ȣ�� �Է����ּ���.');
}

// ������� ��� �ش簪 Y�� ����
if (isset($_POST['mobile'])) {
	$mobile	= $_POST['mobile'];
}else{
	$mobile	= 'N';
}

/* ������� ��� ��ū�� */
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

	
	if($mobile == 'Y'){	// ������� ���(����� �и��ʿ䰡 ��� �ϴ��� ���� �Լ� ȣ��)
		$returnLogin	= LoginPC($scode , $skey , $spass, $demo, $ios_udid, $and_udid);
	}else{
		$returnLogin	= LoginPC($scode , $skey , $spass, $demo, $ios_udid, $and_udid);
	}

	
	if($returnLogin===true){

		if($mobile == 'Y'){		// ����� �α����� ���

			//window.AppUI.get_reg_id();
			goto_url("/bin/mainmobile.php");	

		}else{					// Web ������ ������ �� ��Ʈ���

			/*
				AUTHOR ���� 
				1:����
				2:��Ʈ���
				3:�����Ѿ���
			*/
			if($_SESSION['S_MASTER'] == 'A'){
				goto_url("/bin/main.html");			// ����
			}else if($_SESSION['S_MASTER'] == 'B'){
				goto_url("/bin/mainjisa.html");		// ��Ʈ���
			}else{
				alert("���α׷� ���� ������ �����ϴ�");
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

