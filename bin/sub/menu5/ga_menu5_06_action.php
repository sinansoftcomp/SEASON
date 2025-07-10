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
	
	$file_name	= $_SESSION['S_SCODE']."_".$file_ori;	 // 12번서버에 저장될 파일명
	$file_path  = '/gaplus/temp/excelup/';

	//기존에 file이 존재하면 지운다 
	$del_file = 'D:\\www\gaplus\temp\excelup\\'. $file_name;
	unlink($del_file);

	//파일찾기에서 선택한 엑셀을 12번서버에 저장
	$rtn =  file_upload($file_path, $file_name, 700,$file_ori, $tmp_name, '2') ;
}
*/
if($_FILES){
	$file_ori = iconv("UTF-8","EUCKR",$_FILES['file1']['name']); 
	$tmp_name = iconv("UTF-8","EUCKR",$_FILES['file1']['tmp_name']);   
	
	$file_name	= "BIMAUP";	 // 한글로 인코딩하면 업로드가 안되서 영어로 저장함.
	$file_path  = '/gaplus/temp/excelup/';

	//기존에 file이 존재하면 지운다 
	$del_file = 'D:\\www\gaplus\temp\excelup\\'. $file_name;
	unlink($del_file);

	//파일찾기에서 선택한 엑셀을 12번서버에 저장
	$rtn =  file_upload($file_path, $file_name, 700,$file_ori, $tmp_name, '2') ;
}


$EXCEL_data= array();
//============================ 12번서버에 저장된 excel 읽어서 kwn테이블에 insert하는 부분===================================
$objPHPExcel = new PHPExcel();
$filename = 'D:\\www\gaplus\temp\excelup\\'.$file_name; // 읽어들일 엑셀 파일의 경로와 파일명을 지정한다.


// 업로드 된 엑셀 형식에 맞는 Reader객체를 만든다.
$objReader = PHPExcel_IOFactory::createReaderForFile($filename);

// 읽기전용으로 설정
$objReader->setReadDataOnly(true);
// 엑셀파일을 읽는다
$objExcel = $objReader->load($filename);

$objExcel->setActiveSheetIndex(0);
$objWorksheet = $objExcel->getActiveSheet(); 

$maxRow		= $objWorksheet->getHighestRow();
$maxCol     = $objWorksheet->getHighestColumn();

$aa="";
$bb="";
$cc="";

// 비매칭에 필요한 데이터를 가져오기위해 엑셀 제목이 필수값인 것 가져옴.
for($a='A'; $a<=$maxCol; $a++){
	$titlename = $objWorksheet->getCell($a.'1')->getValue();
	$titlename = iconv("UTF-8","EUC-KR",$titlename);
	
	if($titlename == "정산월"){
		$aa = $a;
	}else if($titlename == "증권번호"){
		$bb = $a;
	}else if($titlename == "사용인코드"){
		$cc = $a;
	}
}

// 엑셀 1행 제목중에 필수값이 하나라도 없으면 리턴
if(!$aa or !$bb or !$cc){
	unlink($del_file);
	$message = '필요한 항목이 존재하지않습니다.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
	echo json_encode($returnJson);
	exit;	
}


sqlsrv_query($mscon,"BEGIN TRAN");

for ($i = 2 ; $i <= $maxRow ; $i++) {

	$yymm   =  $objWorksheet->getCell($aa.$i)->getValue();		// 정산월
	$yymm=  substr($yymm,0,4).substr($yymm,5,2); 
	$kcode  =  $objWorksheet->getCell($bb.$i)->getValue();		// 증권번호
	$ksman  =  $objWorksheet->getCell($cc.$i)->getValue();		// 사용인코드

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
		$message = '엑셀 업로드중 오류입니다_1';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}
}

sqlsrv_query($mscon,"COMMIT");
sqlsrv_free_stmt($result);
sqlsrv_close($mscon);

unlink($del_file);
$message = '엑셀업로드를 완료했습니다.';
$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "rtype" => "up");
echo json_encode($returnJson);
exit;	
?>