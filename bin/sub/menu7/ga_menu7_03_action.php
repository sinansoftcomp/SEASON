<?
//error_reporting(E_ALL); ini_set('display_errors', 1);
@ini_set('gd.jpeg_ignore_warning', 1);

include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/class/excel_upload.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/sub/menu3/ga_menu3_52_fun.php");

include($_SERVER['DOCUMENT_ROOT']."/bin/sub/menu3/ga_menu3_52_tit_fun.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/sub/menu3/ga_menu3_exc_date_fun.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/class/common_class.php");

include($_SERVER['DOCUMENT_ROOT']."/bin/sms/send_push_async.php");

require_once $_SERVER['DOCUMENT_ROOT']."/bin/include/lib/PHPExcel/Classes/PHPExcel.php"; 
require_once $_SERVER['DOCUMENT_ROOT']."/bin/include/lib/PHPExcel/Classes/PHPExcel/IOFactory.php";  

$gubun = $_POST['gubun'];
$title = iconv("UTF-8","EUC-KR",$_POST['title']);
$bigo = iconv("UTF-8","EUC-KR",$_POST['bigo']);
$topsort="";
if($_POST['topsort']){
	$topsort = 'Y';
}

$file_name="";
$file_path="";

if($_FILES){
	$file_ori = iconv("UTF-8","EUCKR",$_FILES['file1']['name']); 
	$tmp_name = iconv("UTF-8","EUCKR",$_FILES['file1']['tmp_name']);   
	
	$file_name	= $_SESSION['S_SCODE']."_".$file_ori;	 // 9�������� ����� ���ϸ�
	$file_path  = '/gaplus/temp/gongji/';

	//������ file�� �����ϸ� ����� 
	$del_file = 'D:\\www\gaplus\temp\gongji\\'. $file_name;
	unlink($del_file);

	//����ã�⿡�� ������ ������ 9�������� ����
	$rtn =  file_upload($file_path, $file_name, 700,$file_ori, $tmp_name, '2') ;

}

if($_FILES['file1']['size'] > 1000000){
	$message = '÷�����Ͽ뷮�� 1MB�� �ѱ� �� �����ϴ�.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
	echo json_encode($returnJson);
	exit;	
}


//----------------------------------------------------------//
//                    �Է½� ó������							// 
//----------------------------------------------------------//
if($_POST['type']=='in'){
	// �ʼ����� Ȯ��
	if($_SESSION['S_SCODE'] == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '�������� error �ʼ��Է°� ����, �� �α������ּ���.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}

	// ��������
	$sql	= "
			select isnull(max(seq),0)+1 seq
			from gongji
			where scode = '".$_SESSION['S_SCODE']."'
			";
	sqlsrv_query($mscon,"BEGIN TRAN");
	$result  = sqlsrv_query( $mscon, $sql );
	$row =  sqlsrv_fetch_array($result); 
	$seq = $row["seq"];


	$sql = "insert into gongji(scode,seq,gubun,title,bigo,jocnt,topsort,filename,filepath,idate,iswon)
			values('".$_SESSION['S_SCODE']."', ".$seq." , '".$gubun."' , '".$title."' , '".$bigo."' , 0 ,'".$topsort."','".$file_name."','".$file_path."' ,getdate(),'".$_SESSION['S_SKEY']."')";
	$result =  sqlsrv_query( $mscon, $sql );

	if ($result == false){
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' �������� ��� �� ����';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
	}



	//-------------------------------------------------------------------
	//����/��� ���� ����鿡 ���� push ���� ���� �ۼ�

	$sql = "
			select pushtoken
			from swon
			where scode = '".$_SESSION['S_SCODE']."' and tbit in ('1','3') and pushyn = 'Y' and isnull(pushtoken,'') <> ''
			" ;		

	$qry	= sqlsrv_query( $mscon, $sql );
	while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
		$pushlist[]	= $fet;
	}	

	$data = ['url' => 'https://gaplus.net:452/bin/submobile/m_main_gongji_dt.php?seq='.$seq, 'key2' => 'value2'];

	$notifications = [];
	foreach ($pushlist as $key => $val) {
		extract($val);
		$notifications[] = [
			'to' => $pushtoken,
			'title' => iconv('EUC-KR','UTF-8',$title),
			'body' => iconv('EUC-KR','UTF-8','Ŭ���Ͽ� Ȯ���ϼ���.'),
			'data' => $data
		];
	}

	sendPushNotifications($notifications);
	

	//------------------------------------------------------------------- 



    sqlsrv_query($mscon,"COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' ���������� ����Ͽ����ϴ�.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "seq"=>"$seq" , "rtype" => "in");
	echo json_encode($returnJson);
	exit;

}


//----------------------------------------------------------//
//                    ������ ó������							// 
//----------------------------------------------------------//
if($_POST['type']=='up'){
	// �ʼ����� Ȯ��
	if($_SESSION['S_SCODE'] == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '�������� error �ʼ��Է°� ����, �� �α������ּ���.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}
	$seq = $_POST['seq'];
	$sql = "update gongji
			set gubun = '".$gubun."' , title = '".$title."' , bigo = '".$bigo."' , topsort='".$topsort."',filename='".$file_name."',filepath='".$file_path."',uswon='".$_SESSION['S_SKEY']."',udate=getdate()
			where scode = '".$_SESSION['S_SCODE']."' and seq = '".$seq."'
			";
	sqlsrv_query($mscon,"BEGIN TRAN");
	$result =  sqlsrv_query( $mscon, $sql );
	
	if ($result == false){
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		echo $sql;
		$message = ' �������� ���� �� ����';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
	}

    sqlsrv_query($mscon,"COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' ���������� �����Ͽ����ϴ�.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "seq"=>"$seq" , "rtype" => "up");
	echo json_encode($returnJson);
	exit;
}

//----------------------------------------------------------//
//                    ������ ó������							// 
//----------------------------------------------------------//
if($_POST['type'] == "del"){
	// �ʼ����� Ȯ��
	if($_SESSION['S_SCODE'] == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '�������� error �ʼ��Է°� ����, �� �α������ּ���.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}

	$sql = "delete from gongji
			where scode = '".$_SESSION['S_SCODE']."' and seq = '".$_POST['seq']."' ";

	// Ʈ������ ����
    sqlsrv_query($mscon,"BEGIN TRAN");
    $result =  sqlsrv_query( $mscon, $sql );

    if ($result == false){
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' �������� ���� �� ����';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
    }

    sqlsrv_query($mscon,"COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' ���������� �����Ͽ����ϴ�.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "rtype" => "del");
	echo json_encode($returnJson);
	exit;
}

?>