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
							
							//-->사원번호
							$SKEY =	 $objWorksheet->getCell('A' . $i)->getValue();
							$SKEY =   iconv("UTF-8","EUC-KR",$SKEY);		
							$SKEY = str_replace("'","",$SKEY);

							//-->사원명 
							$SNAME =	 $objWorksheet->getCell('B' . $i)->getValue();
							$SNAME =   iconv("UTF-8","EUC-KR",$SNAME);		
							$SNAME = str_replace("'","",$SNAME);

							//-->입사일 
							$INDATE =	 $objWorksheet->getCell('C' . $i)->getValue();
							$INDATE =   iconv("UTF-8","EUC-KR",$INDATE);		
							$INDATE = str_replace("'","",$INDATE);

							//-->위촉일 
							$YDATE =	 $objWorksheet->getCell('E' . $i)->getValue();
							$YDATE =   iconv("UTF-8","EUC-KR",$YDATE);		
							$YDATE = str_replace("'","",$YDATE);

							//-->퇴사일 
							$TDATE =	 $objWorksheet->getCell('G' . $i)->getValue();
							$TDATE =   iconv("UTF-8","EUC-KR",$TDATE);		
							$TDATE = str_replace("'","",$TDATE);

							//-->직책
							$POS =	 $objWorksheet->getCell('H' . $i)->getValue();
							$POS =   iconv("UTF-8","EUC-KR",$POS);		
							$POS = str_replace("'","",$POS);

							if ($POS == 'FC' ) {
								$POS = '1100';
								$JIK  = '1001';
							}
							if ($POS == 'CA' ) {
								$POS = '1110';
								$JIK  = '1001';
							}				
							if ($POS == 'CA' ) {
								$POS = '1110';
								$JIK  = '1001';
							}
							if ($POS == '주임' ) {
								$POS = '1120';
								$JIK  = '1001';
							}
							if ($POS == '매니저' ) {
								$POS = '1130';
								$JIK  = '1001';
							}
							if ($POS == '손보매니저' ) {
								$POS = '1140';
								$JIK  = '1001';
							}
							if ($POS == '과장' ) {
								$POS = '1150';
								$JIK  = '1001';
							}
							if ($POS == '사내이사' ) {
								$POS = '1160';
								$JIK  = '1001';
							}
							if ($POS == '팀장' ) {
								$POS = '2001';
								$JIK  = '2001';
							}
							if ($POS == '지점장' ) {
								$POS = '3001';
								$JIK  = '3001';
							}
							if ($POS == '지사장' ) {
								$POS = '4001';
								$JIK  = '4001';
							}
							if ($POS == '본부장' ) {
								$POS = '5001';
								$JIK  = '5001';
							}

							//-->재직구분
							$TBIT =	 $objWorksheet->getCell('I' . $i)->getValue();
							$TBIT =   iconv("UTF-8","EUC-KR",$TBIT);		
							$TBIT = str_replace("'","",$TBIT);
 
							if ($TBIT == '재직' ) {
								$TBIT = '1';
 							}

							if ($TBIT == '퇴직' ) {
								$TBIT = '2';
 							}
							if ($TBIT == '위촉' ) {
								$TBIT = '3';
 							}
							if ($TBIT == '해촉' ) {
								$TBIT = '4';
 							}

							//-->주민번호 
							$SJUNO =	 $objWorksheet->getCell('J' . $i)->getValue();
							$SJUNO =   iconv("UTF-8","EUC-KR",$SJUNO);		
							$SJUNO = str_replace("'","",$SJUNO);	 


							//-->휴대전화번호  
							$HTEL =	 $objWorksheet->getCell('K' . $i)->getValue();
							$HTEL =   iconv("UTF-8","EUC-KR",$HTEL);		
							$HTEL = str_replace("'","",$HTEL);
	
							$HTEL1	= substr($HTEL,0,3); 
							$HTEL2	=substr($HTEL,3,4); 
							$HTEL3	=substr($HTEL,7,4); 

							//-->이메일 
							$EMAIL =	 $objWorksheet->getCell('L' . $i)->getValue();
							$EMAIL =   iconv("UTF-8","EUC-KR",$EMAIL);		
							$EMAIL = str_replace("'","",$EMAIL);
	
  							//-->우편번호 
							$POST =	 $objWorksheet->getCell('M' . $i)->getValue();
							$POST =   iconv("UTF-8","EUC-KR",$POST);		
							$POST = str_replace("'","",$POST);


  							//-->주소 
							$ADDR =	 $objWorksheet->getCell('N' . $i)->getValue();
							$ADDR =   iconv("UTF-8","EUC-KR",$ADDR);		
							$ADDR = str_replace("'","",$ADDR);

							//-->본부
							$BONBU = '000001';

							//-->지사 
 							$JISA =	 $objWorksheet->getCell('P' . $i)->getValue();
							$JISA =   iconv("UTF-8","EUC-KR",$JISA);		
							$JISA = str_replace("'","",$JISA);

							$sql = "select JSCODE   from JISA where scode = '".$_SESSION['S_SCODE']."' and JSNAME = '".$JISA."'";
							$result  = sqlsrv_query( $mscon, $sql );
							$row =  sqlsrv_fetch_array($result); 
							$JISA = $row["JSCODE"];	


							//-->지점 
 							$JIJUM =	 $objWorksheet->getCell('Q' . $i)->getValue();
							$JIJUM =   iconv("UTF-8","EUC-KR",$JIJUM);		
							$JIJUM = str_replace("'","",$JIJUM);

							$sql = "select JCODE   from JIJUM where scode = '".$_SESSION['S_SCODE']."' and JNAME = '".$JIJUM."'";
							$result  = sqlsrv_query( $mscon, $sql );
							$row =  sqlsrv_fetch_array($result); 
							$JIJUM = $row["JCODE"];	 
							$TEAM = '';

							$sql = "
									insert into SWON( SCODE  ,SKEY   ,SSPWD   ,SNAME   ,SJUNO    ,BONBU   ,JISA   ,JIJUM   ,TEAM  
																	    ,INDATE      ,YDATE      ,TDATE           ,TBIT	  ,POS      ,JIK      , HTEL1      ,HTEL2      ,HTEL3    
																		,EMAIL      ,POST      ,ADDR       ,IDATE      ,ISWON      ,UDATE      ,USWON )
									values('".$_SESSION['S_SCODE']."', '$SKEY',   dbo.ENCRYPTKEY('0000') , '".$SNAME."', dbo.ENCRYPTKEY('".$SJUNO."') ,'".$BONBU."' ,'".$JISA."' ,'".$JIJUM."' ,'".$TEAM."'
																			, ".$INDATE.",'".$YDATE."','".$TDATE."'  ,'".$TBIT."' ,'".$POS."' ,'".$JIK."' ,'".$HTEL1."' ,'".$HTEL2."' ,'".$HTEL3."'
																			,'".$EMAIL."','".$POST."','".$ADDR."' ,getdate(),'".$_SESSION['S_SKEY']."' ,getdate(),'".$_SESSION['S_SKEY']."')";

						
									
 					        //print_r($sql);
							//exit; 
							

							
							sqlsrv_query("BEGIN TRAN");
							$result =  sqlsrv_query( $mscon, $sql );
							if ($result == false){
								sqlsrv_query("ROLLBACK");
 								//print_r($sql);
								//exit; 
							}else{
								$ins_tot_cnt = $ins_tot_cnt + 1; 
								sqlsrv_query("COMMIT");
							}
 

		
		

					} //-->하나의 shhet대한 
					
 
			}//-->전체sheet for end.
			unlink($del_file); //작업파일삭제 
		} //file_no end 
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);

		$message ='사원파일  계약엑셀파일 업로드 완료처리 !';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "");
		echo json_encode($returnJson);
		exit;	
} // upload if 에대한 end 

 
 
?> 

