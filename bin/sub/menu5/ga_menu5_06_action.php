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

require_once $_SERVER['DOCUMENT_ROOT']."/bin/include/lib/PHPExcel/Classes/PHPExcel.php"; 
require_once $_SERVER['DOCUMENT_ROOT']."/bin/include/lib/PHPExcel/Classes/PHPExcel/IOFactory.php";  


$file_name="";
$file_path="";
/*
if($_FILES){
	$file_ori = iconv("UTF-8","EUCKR",$_FILES['file1']['name']); 
	$tmp_name = iconv("UTF-8","EUCKR",$_FILES['file1']['tmp_name']);   
	
	$file_name	= $_SESSION['S_SCODE']."_".$file_ori;	 // 12�������� ����� ���ϸ�
	$file_path  = '/gaplus/temp/excelup/';

	//������ file�� �����ϸ� ����� 
	$del_file = 'D:\\www\gaplus\temp\excelup\\'. $file_name;
	unlink($del_file);

	//����ã�⿡�� ������ ������ 12�������� ����
	$rtn =  file_upload($file_path, $file_name, 700,$file_ori, $tmp_name, '2') ;
}
*/
if($_FILES){
	$file_ori = iconv("UTF-8","EUCKR",$_FILES['file1']['name']); 
	$tmp_name = iconv("UTF-8","EUCKR",$_FILES['file1']['tmp_name']);   
	
	$file_name	= "BIMAUP";	 // �ѱ۷� ���ڵ��ϸ� ���ε尡 �ȵǼ� ����� ������.
	$file_path  = '/gaplus/temp/excelup/';

	//������ file�� �����ϸ� ����� 
	$del_file = 'D:\\www\gaplus\temp\excelup\\'. $file_name;
	unlink($del_file);

	//����ã�⿡�� ������ ������ 12�������� ����
	$rtn =  file_upload($file_path, $file_name, 700,$file_ori, $tmp_name, '2') ;
}


$EXCEL_data= array();
//============================ 12�������� ����� excel �о kwn���̺� insert�ϴ� �κ�===================================
$objPHPExcel = new PHPExcel();
$filename = 'D:\\www\gaplus\temp\excelup\\'.$file_name; // �о���� ���� ������ ��ο� ���ϸ��� �����Ѵ�.


// ���ε� �� ���� ���Ŀ� �´� Reader��ü�� �����.
$objReader = PHPExcel_IOFactory::createReaderForFile($filename);

// �б��������� ����
$objReader->setReadDataOnly(true);
// ���������� �д´�
$objExcel = $objReader->load($filename);

$objExcel->setActiveSheetIndex(0);
$objWorksheet = $objExcel->getActiveSheet(); 

$maxRow		= $objWorksheet->getHighestRow();
$maxCol     = $objWorksheet->getHighestColumn();

$aa="";
$bb="";
$cc="";

// ���Ī�� �ʿ��� �����͸� ������������ ���� ������ �ʼ����� �� ������.
for($a='A'; $a<=$maxCol; $a++){
	$titlename = $objWorksheet->getCell($a.'1')->getValue();
	$titlename = iconv("UTF-8","EUC-KR",$titlename);
	
	if($titlename == "�����"){
		$aa = $a;
	}else if($titlename == "���ǹ�ȣ"){
		$bb = $a;
	}else if($titlename == "������ڵ�"){
		$cc = $a;
	}
}

// ���� 1�� �����߿� �ʼ����� �ϳ��� ������ ����
if(!$aa or !$bb or !$cc){
	unlink($del_file);
	$message = '�ʿ��� �׸��� ���������ʽ��ϴ�.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
	echo json_encode($returnJson);
	exit;	
}


sqlsrv_query($mscon,"BEGIN TRAN");

for ($i = 2 ; $i <= $maxRow ; $i++) {

	$yymm   =  $objWorksheet->getCell($aa.$i)->getValue();		// �����
	$yymm=  substr($yymm,0,4).substr($yymm,5,2); 
	$kcode  =  $objWorksheet->getCell($bb.$i)->getValue();		// ���ǹ�ȣ
	$ksman  =  $objWorksheet->getCell($cc.$i)->getValue();		// ������ڵ�

	$sql = "
			update ins_ipmst
			set ksman = '".$ksman."'
			where scode = '".$_SESSION['S_SCODE']."' and yymm = '".$yymm."' and kcode = '".$kcode."' and isnull(nmgubun,'') = 'Y'
			";

	$result =  sqlsrv_query( $mscon, $sql );
	if ($result == false){
		unlink($del_file);
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = '���� ���ε��� �����Դϴ�_1';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}
}

sqlsrv_query($mscon,"COMMIT");
sqlsrv_free_stmt($result);
sqlsrv_close($mscon);

unlink($del_file);
$message = '�������ε带 �Ϸ��߽��ϴ�.';
$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "rtype" => "up");
echo json_encode($returnJson);
exit;	
?>