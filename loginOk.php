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
	if(!$_POST['SCODE']) alert('���� ID�� �Է����ּ���.');
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

	// �α���üũ
	$returnLogin	= LoginPC($scode , $spass, $demo, $ios_udid, $and_udid);


	echo "<!-- �α��� üũ : ".$returnLogin." -->\n";
	
	if($returnLogin===true){

		/*
			���� 
			1:������
			2:�ѱ�����
			3:��۱��
			4:�߱�
			5:��Ʈ��
			6:�ݷҺ��
		*/
		/* �ӽ��ּ�ó��
		if($_SESSION['S_SBIT'] == '1' || $_SESSION['S_SBIT'] == '2'){
			goto_url("/bin/main.html");			// ������ �� �ѱ�����
		}else{
			alert("���α׷� ���� ������ �����ϴ�");
		}
		*/
		// ���������� �۾����� �ӽüҽ�
		if($_SESSION['S_SBIT'] == '1'){
			goto_url("/bin/main.html");			// ������ �� �ѱ�����
		}else if($_SESSION['S_SBIT'] == '2'){
			goto_url("/bin/main2.html");			// ������ �� �ѱ�����
		}else{
			alert("���α׷� ���� ������ �����ϴ�");
		}
		

	}else{

		alert($returnLogin,"login.php");
		
	}
}



?>

