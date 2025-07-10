<?
// programmer : 김순관    개발일자 : 2024.02.04  GA플러스 최고중요 PROGRAMM
//특정이미지가 저장안되는 현상이 나타남..!! 2017-11-29  
@ini_set('gd.jpeg_ignore_warning', 1);

include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/class/excel_upload.php");

include($_SERVER['DOCUMENT_ROOT']."/bin/sub/menu3/ga_menu3_52_fun.php");
//-------> 필드별 상세 처리  계약,수납 수수료를 같이 사용하여도 되나 향후 각 업무에 유연하게 대비할 수 있도록  별도로 정의함.
include($_SERVER['DOCUMENT_ROOT']."/bin/sub/menu3/ga_menu3_52_tit_fun.php");

require_once $_SERVER['DOCUMENT_ROOT']."/bin/include/lib/PHPExcel/Classes/PHPExcel.php"; 
require_once $_SERVER['DOCUMENT_ROOT']."/bin/include/lib/PHPExcel/Classes/PHPExcel/IOFactory.php";  


 

//$upldate		=	str_replace('-','',$_POST['upldate']);
$upldate  = date("Ymd");  //입력시 서버일자

$gubun		=	$_POST['gubun'];
$filename	=	$_POST['filename'];
$uploadFile =	$_POST['filename'];
$file_cnt		=	$_POST['imgcnt'];		// 업로드 파일 수 

 


 
//--->일부러 루틴 올림 아래가 덜 복잡하게 .....
if($_SESSION['S_SCODE'] == null ){
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);
	$message = '세션연결이 정상적이지 않습니다 .';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
	echo json_encode($returnJson);
	exit;			
}
if ($_POST['type'] == 'in' ) {
	if (empty($file_cnt)) {
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message ='업로드하실 엑셀FILE이 선택되지 않았습니다!';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "");
		echo json_encode($returnJson);
		exit;	
	}
}

 

//===============================================================================================================//
//   file upload rtn  *환화-환수엑셀 마지막부분 타이틀과 내용 틀림.
//===============================================================================================================//
if ($_POST['type'] == 'in' ) {
 	//업로드 파일 수만큼 회전 
	for ($file_no=0 ; $file_no < $file_cnt ; $file_no++) { 
			//--------------------엑셀 다운로드
			$file_ori =  iconv("UTF-8","EUCKR",$_FILES['attach_file']['name'][ $file_no]); 
			$tmp_name = iconv("UTF-8","EUCKR",$_FILES['attach_file']['tmp_name'][ $file_no]);   
			$filename = iconv("UTF-8","EUCKR",$_FILES['attach_file']['tmp_name'][ $file_no]);
			$ext_temp	= explode(".",$file_ori);
			$ext		= $ext_temp[count($ext_temp)-1];
			$up_time = preg_replace('/[^0-9]*/s', '', date("Y-m-d H:i:s"));
			$file_name	= $_SESSION['S_SCODE']."_A".$up_time.".".$ext;	 

			$file_path  = '/gaplus/temp/';
			$ori_filename	= $_SERVER['DOCUMENT_ROOT']."/temp/".$file_name;
			//기존에 file이 존재하면 지운다 
			$del_file = 'D:\\www\gaplus\temp\\'. $file_name;
			 unlink($del_file);

			//파일찾기에서 선택한 엑셀을 9번서버에 저장
			$rtn =  file_upload($file_path, $file_name, 700,$file_ori, $tmp_name, '2') ;
		

			$EXCEL_data= array();
			//============================ 9번서버에 저장된 excel 읽어서 kwn테이블에 insert하는 부분===================================
			$objPHPExcel = new PHPExcel();

			$filename = 'D:\\www\gaplus\temp\\'.$file_name; // 읽어들일 엑셀 파일의 경로와 파일명을 지정한다.

			// 업로드 된 엑셀 형식에 맞는 Reader객체를 만든다.
			$objReader = PHPExcel_IOFactory::createReaderForFile($filename);

			// 읽기전용으로 설정
			$objReader->setReadDataOnly(true);
			// 엑셀파일을 읽는다
			$objExcel = $objReader->load($filename);


			//sheet 갯수만큼 처리
			$sheet_tot_cnt           = $objExcel->getSheetCount();
			for ($sheet_no=0 ; $sheet_no < $sheet_tot_cnt ; $sheet_no++) { 
					// 첫번째 시트를 선택
					$objExcel->setActiveSheetIndex($sheet_no);
					$objWorksheet = $objExcel->getActiveSheet(); 
					$rowIterator = $objWorksheet->getRowIterator();
					
					foreach ($rowIterator as $row) { // 모든 행에 대해서
					$cellIterator = $row->getCellIterator();
					$cellIterator->setIterateOnlyExistingCells(false); 
					}
					$sheetname =   iconv("UTF-8","EUC-KR", $objWorksheet->getTitle() );		

					//print_r($EXCEL_declare);
					//exit; 

					//--->여기서부터 실전 

					$maxRow		= $objWorksheet->getHighestRow();
					$maxCol        = $objWorksheet->getHighestColumn();
					$SERIALCNT	= 0;
					$existChk	= 0;
					$updateCnt=0;

					$ins_tot_cnt = 0 ;
					$up_tot_cnt = 0 ;
					$ins_tot_err_cnt = 0 ;

					for ($i = 2 ; $i <= $maxRow ; $i++) {		//정의된 시작 행부터 부터 데이터시작



							$SKEY  =  $objWorksheet->getCell('G' . $i)->getValue();
							$SKEY =   iconv("UTF-8","EUC-KR",$SKEY);		
							$SKEY = str_replace("'","",$SKEY);
							//-->삼성생명      
							$INSCODE =	'00006';
							$BSCODE =	 $objWorksheet->getCell('M' . $i)->getValue();
							$BSCODE =   iconv("UTF-8","EUC-KR",$BSCODE);		
							$BSCODE = str_replace("'","",$BSCODE);
							$sql = "
									insert into INSWON( SCODE  ,SKEY   ,INSCODE   ,BSCODE   ,SGUBUN  ,IDATE  ,ISWON  )
									values('".$_SESSION['S_SCODE']."', '".$SKEY."' ,'".$INSCODE."' ,'".$BSCODE."','2',getdate(),'".$_SESSION['S_SKEY']."')";
							if (!empty($BSCODE )) {
										sqlsrv_query("BEGIN TRAN");
										$result =  sqlsrv_query( $mscon, $sql );
										if ($result == false){
											sqlsrv_query("ROLLBACK");
											//print_r($sql);
											//exit; 
										}else{
											sqlsrv_query("COMMIT");
										}
							}


 							//-->db손보      
							$INSCODE =	'00022';
							$BSCODE =	 $objWorksheet->getCell('N' . $i)->getValue();
							$BSCODE =   iconv("UTF-8","EUC-KR",$BSCODE);		
							$BSCODE = str_replace("'","",$BSCODE);
							$sql = "
									insert into INSWON( SCODE  ,SKEY   ,INSCODE   ,BSCODE   ,SGUBUN  ,IDATE  ,ISWON  )
									values('".$_SESSION['S_SCODE']."', '".$SKEY."' ,'".$INSCODE."' ,'".$BSCODE."','2',getdate(),'".$_SESSION['S_SKEY']."')";
							
 

							if (!empty($BSCODE )) {
										sqlsrv_query("BEGIN TRAN");
										$result =  sqlsrv_query( $mscon, $sql );
										if ($result == false){
											sqlsrv_query("ROLLBACK");
											//print_r($sql);
											//exit; 
										}else{
											sqlsrv_query("COMMIT");
										}
							}

 							//-->KB손보      
							$INSCODE =	'00019';
							$BSCODE =	 $objWorksheet->getCell('O' . $i)->getValue();
							$BSCODE =   iconv("UTF-8","EUC-KR",$BSCODE);		
							$BSCODE = str_replace("'","",$BSCODE);
							$sql = "
									insert into INSWON( SCODE  ,SKEY   ,INSCODE   ,BSCODE   ,SGUBUN  ,IDATE  ,ISWON  )
									values('".$_SESSION['S_SCODE']."', '".$SKEY."' ,'".$INSCODE."' ,'".$BSCODE."','2',getdate(),'".$_SESSION['S_SKEY']."')";
							
							if (!empty($BSCODE )) {
										sqlsrv_query("BEGIN TRAN");
										$result =  sqlsrv_query( $mscon, $sql );
										if ($result == false){
											sqlsrv_query("ROLLBACK");
											//print_r($sql);
											//exit; 
										}else{
											sqlsrv_query("COMMIT");
										}
							}


 							//-->현대해상      
							$INSCODE =	'00018';
							$BSCODE =	 $objWorksheet->getCell('P' . $i)->getValue();
							$BSCODE =   iconv("UTF-8","EUC-KR",$BSCODE);		
							$BSCODE = str_replace("'","",$BSCODE);
							$sql = "
									insert into INSWON( SCODE  ,SKEY   ,INSCODE   ,BSCODE   ,SGUBUN  ,IDATE  ,ISWON  )
									values('".$_SESSION['S_SCODE']."', '".$SKEY."' ,'".$INSCODE."' ,'".$BSCODE."','2',getdate(),'".$_SESSION['S_SKEY']."')";
							
							if (!empty($BSCODE )) {
										sqlsrv_query("BEGIN TRAN");
										$result =  sqlsrv_query( $mscon, $sql );
										if ($result == false){
											sqlsrv_query("ROLLBACK");
											//print_r($sql);
											//exit; 
										}else{
											sqlsrv_query("COMMIT");
										}
							}

 							//-->MG손보      
							$INSCODE =	'00033';
							$BSCODE =	 $objWorksheet->getCell('Q' . $i)->getValue();
							$BSCODE =   iconv("UTF-8","EUC-KR",$BSCODE);		
							$BSCODE = str_replace("'","",$BSCODE);
							$sql = "
									insert into INSWON( SCODE  ,SKEY   ,INSCODE   ,BSCODE   ,SGUBUN  ,IDATE  ,ISWON  )
									values('".$_SESSION['S_SCODE']."', '".$SKEY."' ,'".$INSCODE."' ,'".$BSCODE."','2',getdate(),'".$_SESSION['S_SKEY']."')";
							
							if (!empty($BSCODE )) {
										sqlsrv_query("BEGIN TRAN");
										$result =  sqlsrv_query( $mscon, $sql );
										if ($result == false){
											sqlsrv_query("ROLLBACK");
											//print_r($sql);
											//exit; 
										}else{
											sqlsrv_query("COMMIT");
										}
							}


 							//-->삼성화재     
							$INSCODE =	'00021';
							$BSCODE =	 $objWorksheet->getCell('R' . $i)->getValue();
							$BSCODE =   iconv("UTF-8","EUC-KR",$BSCODE);		
							$BSCODE = str_replace("'","",$BSCODE);
							$sql = "
									insert into INSWON( SCODE  ,SKEY   ,INSCODE   ,BSCODE   ,SGUBUN  ,IDATE  ,ISWON  )
									values('".$_SESSION['S_SCODE']."', '".$SKEY."' ,'".$INSCODE."' ,'".$BSCODE."','2',getdate(),'".$_SESSION['S_SKEY']."')";
							if (!empty($BSCODE )) {
										sqlsrv_query("BEGIN TRAN");
										$result =  sqlsrv_query( $mscon, $sql );
										if ($result == false){
											sqlsrv_query("ROLLBACK");
											//print_r($sql);
											//exit; 
										}else{
											sqlsrv_query("COMMIT");
										}
							}


 							//-->한화손보
							$INSCODE =	'00020';
							$BSCODE =	 $objWorksheet->getCell('S' . $i)->getValue();
							$BSCODE =   iconv("UTF-8","EUC-KR",$BSCODE);		
							$BSCODE = str_replace("'","",$BSCODE);
							$sql = "
									insert into INSWON( SCODE  ,SKEY   ,INSCODE   ,BSCODE   ,SGUBUN  ,IDATE  ,ISWON  )
									values('".$_SESSION['S_SCODE']."', '".$SKEY."' ,'".$INSCODE."' ,'".$BSCODE."','2',getdate(),'".$_SESSION['S_SKEY']."')";
							
							if (!empty($BSCODE )) {
										sqlsrv_query("BEGIN TRAN");
										$result =  sqlsrv_query( $mscon, $sql );
										if ($result == false){
											sqlsrv_query("ROLLBACK");
											//print_r($sql);
											//exit; 
										}else{
											sqlsrv_query("COMMIT");
										}
							}


 							//-->메리츠화재      
							$INSCODE =	'00017';
							$BSCODE =	 $objWorksheet->getCell('T' . $i)->getValue();
							$BSCODE =   iconv("UTF-8","EUC-KR",$BSCODE);		
							$BSCODE = str_replace("'","",$BSCODE);
							$sql = "
									insert into INSWON( SCODE  ,SKEY   ,INSCODE   ,BSCODE   ,SGUBUN  ,IDATE  ,ISWON  )
									values('".$_SESSION['S_SCODE']."', '".$SKEY."' ,'".$INSCODE."' ,'".$BSCODE."','2',getdate(),'".$_SESSION['S_SKEY']."')";
							
							if (!empty($BSCODE )) {
										sqlsrv_query("BEGIN TRAN");
										$result =  sqlsrv_query( $mscon, $sql );
										if ($result == false){
											sqlsrv_query("ROLLBACK");
											//print_r($sql);
											//exit; 
										}else{
											sqlsrv_query("COMMIT");
										}
							}


 							//-->흥국화재     
							$INSCODE =	'00023';
							$BSCODE =	 $objWorksheet->getCell('U' . $i)->getValue();
							$BSCODE =   iconv("UTF-8","EUC-KR",$BSCODE);		
							$BSCODE = str_replace("'","",$BSCODE);
							$sql = "
									insert into INSWON( SCODE  ,SKEY   ,INSCODE   ,BSCODE   ,SGUBUN  ,IDATE  ,ISWON  )
									values('".$_SESSION['S_SCODE']."', '".$SKEY."' ,'".$INSCODE."' ,'".$BSCODE."','2',getdate(),'".$_SESSION['S_SKEY']."')";
							
							if (!empty($BSCODE )) {
										sqlsrv_query("BEGIN TRAN");
										$result =  sqlsrv_query( $mscon, $sql );
										if ($result == false){
											sqlsrv_query("ROLLBACK");
											//print_r($sql);
											//exit; 
										}else{
											sqlsrv_query("COMMIT");
										}
							}

 							//-->롯데손보     
							$INSCODE =	'00025';
							$BSCODE =	 $objWorksheet->getCell('V' . $i)->getValue();
							$BSCODE =   iconv("UTF-8","EUC-KR",$BSCODE);		
							$BSCODE = str_replace("'","",$BSCODE);
							$sql = "
									insert into INSWON( SCODE  ,SKEY   ,INSCODE   ,BSCODE   ,SGUBUN  ,IDATE  ,ISWON  )
									values('".$_SESSION['S_SCODE']."', '".$SKEY."' ,'".$INSCODE."' ,'".$BSCODE."','2',getdate(),'".$_SESSION['S_SKEY']."')";
							
							if (!empty($BSCODE )) {
										sqlsrv_query("BEGIN TRAN");
										$result =  sqlsrv_query( $mscon, $sql );
										if ($result == false){
											sqlsrv_query("ROLLBACK");
											//print_r($sql);
											//exit; 
										}else{
											sqlsrv_query("COMMIT");
										}
							}

 							//-->농협손보     
							$INSCODE =	'00024';
							$BSCODE =	 $objWorksheet->getCell('W' . $i)->getValue();
							$BSCODE =   iconv("UTF-8","EUC-KR",$BSCODE);		
							$BSCODE = str_replace("'","",$BSCODE);
							$sql = "
									insert into INSWON( SCODE  ,SKEY   ,INSCODE   ,BSCODE   ,SGUBUN  ,IDATE  ,ISWON  )
									values('".$_SESSION['S_SCODE']."', '".$SKEY."' ,'".$INSCODE."' ,'".$BSCODE."','2',getdate(),'".$_SESSION['S_SKEY']."')";
							
							if (!empty($BSCODE )) {
										sqlsrv_query("BEGIN TRAN");
										$result =  sqlsrv_query( $mscon, $sql );
										if ($result == false){
											sqlsrv_query("ROLLBACK");
											//print_r($sql);
											//exit; 
										}else{
											sqlsrv_query("COMMIT");
										}
							}

					} //-->하나의 shhet대한 
					
 
			}//-->전체sheet for end.
			unlink($del_file); //작업파일삭제 
		} //file_no end 
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);

		$message ='사원원수사파일  계약엑셀파일 업로드 완료처리 !';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "");
		echo json_encode($returnJson);
		exit;	
} // upload if 에대한 end 

 
 
?> 

