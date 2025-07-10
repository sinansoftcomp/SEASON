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
include($_SERVER['DOCUMENT_ROOT']."/bin/sub/menu3/ga_menu3_exc_date_fun.php");


 

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

					for ($i = 4 ; $i <= $maxRow ; $i++) {		//정의된 시작 행부터 부터 데이터시작
							
							//-->증권번호
							$KCODE =	 $objWorksheet->getCell('E' . $i)->getValue();
							$KCODE =   iconv("UTF-8","EUC-KR",$KCODE);		
							$KCODE = str_replace("'","",$KCODE);
							
							//-->계약자성명
							$KNAME =	 $objWorksheet->getCell('H' . $i)->getValue();
							$KNAME =   iconv("UTF-8","EUC-KR",$KNAME);		
							$KNAME = str_replace("'","",$KNAME);

							//-->계약상태 
							$KSTBIT =	 $objWorksheet->getCell('F' . $i)->getValue();
							$KSTBIT =   iconv("UTF-8","EUC-KR",$KSTBIT);		
							$KSTBIT = str_replace("'","",$KSTBIT);

							//-->계약일자 
							$KDATE =	 $objWorksheet->getCell('G' . $i)->getValue();
							$KDATE =   iconv("UTF-8","EUC-KR",$KDATE);		
							$KDATE = str_replace("'","",$KDATE);

							//-->개시일자 
							$FDATE =	 $objWorksheet->getCell('BI' . $i)->getValue();
							$FDATE =   iconv("UTF-8","EUC-KR",$FDATE);		
							$FDATE = str_replace("'","",$FDATE);

							//-->종료일자 
							$TDATE =	 $objWorksheet->getCell('O' . $i)->getValue();
							
							$TDATE = exc_date($TDATE);	//-->엑셀 date가 숫자로 	getValue되었을 때 date형식으로 변환함.											

							$TDATE =   iconv("UTF-8","EUC-KR",$TDATE);		
							$TDATE = str_replace("-","",$TDATE);
							$TDATE = substr($TDATE,0,8);

							//-->상품구분 
							$INSILJ =	 $objWorksheet->getCell('AJ' . $i)->getValue();
							$INSILJ =   iconv("UTF-8","EUC-KR",$INSILJ);		
							$INSILJ = str_replace("'","",$INSILJ);

							if ($INSILJ == '일반' ) {
								$INSILJ = '1';
							}
							if ($INSILJ == '장기' ) {
								$INSILJ = '2';
							}
							if ($INSILJ == '자동차' ) {
								$INSILJ = '3';
							}
			
							//-->원수사코드 
							$INSCODE =	 $objWorksheet->getCell('C' . $i)->getValue();
							$INSCODE =   iconv("UTF-8","EUC-KR",$INSCODE);		
							$INSCODE = str_replace("'","",$INSCODE);
							 
							if ($INSCODE == '삼성화재' ) {
								$INSCODE = '00021';
							}
							if ($INSCODE == '현대해상' ) {
								$INSCODE = '00018';
							}
							if ($INSCODE == 'KB손보' ) {
								$INSCODE = '00019';
							}
							if ($INSCODE == 'DB손보' ) {
								$INSCODE = '00022';
							}
							if ($INSCODE == '메리츠화재' ) {
								$INSCODE = '00017';
							}
							if ($INSCODE == 'MG손보' ) {
								$INSCODE = '00033';
							}
							if ($INSCODE == '롯데손보' ) {
								$INSCODE = '00025';
							}
							if ($INSCODE == '한화손보' ) {
								$INSCODE = '00020';
							}
							if ($INSCODE == '흥국화재' ) {
								$INSCODE = '00023';
							}

							
							//-->상품명
							$ITEMNM =	 $objWorksheet->getCell('J' . $i)->getValue();
							$ITEMNM =   iconv("UTF-8","EUC-KR",$ITEMNM);		
							$ITEMNM = str_replace("'","",$ITEMNM);

							//-->보험료
							$MAMT =	 $objWorksheet->getCell('K' . $i)->getValue();
							$MAMT =   iconv("UTF-8","EUC-KR",$MAMT);		
							$MAMT = str_replace(",","",$MAMT);

							//-->수정보험료
							$SAMT =	 $objWorksheet->getCell('M' . $i)->getValue();
							$SAMT =   iconv("UTF-8","EUC-KR",$SAMT);		
							$SAMT = str_replace(",","",$SAMT);

							//-->납입방법
							$NBIT =	 $objWorksheet->getCell('AD' . $i)->getValue();
							$NBIT =   iconv("UTF-8","EUC-KR",$NBIT);		
							$NBIT = str_replace("'","",$NBIT);

							//-->차번호
							$CARNUM =	 $objWorksheet->getCell('T' . $i)->getValue();
							$CARNUM =   iconv("UTF-8","EUC-KR",$CARNUM);		
							$CARNUM = str_replace("'","",$CARNUM);
							
							
							//-->모집사원
							$GSKEY =	 $objWorksheet->getCell('AO' . $i)->getValue();
							$GSKEY =   iconv("UTF-8","EUC-KR",$GSKEY);		
							$GSKEY = str_replace("'","",$GSKEY);

							//-->수금사원
							$KSKEY =	 $objWorksheet->getCell('AN' . $i)->getValue();
							$KSKEY =   iconv("UTF-8","EUC-KR",$KSKEY);		
							$KSKEY = str_replace("'","",$KSKEY);


							
							//원수사 모집코드   	
							$sql = " SELECT INSCODE , MAX(CASE WHEN SGUBUN = '1' THEN BSCODE ELSE '' END) NEW_BSCODE , MAX(CASE WHEN SGUBUN = '2' THEN BSCODE ELSE '' END) OLD_BSCODE 
							          FROM INSWON WHERE SCODE = '".$_SESSION['S_SCODE']."' AND SKEY = '".$GSKEY."' AND 
		                                                             INSCODE = '".$INSCODE."' 
									GROUP BY INSCODE " ; 
							$result  = sqlsrv_query( $mscon, $sql );
							$row =  sqlsrv_fetch_array($result); 
							
							if (empty($row["NEW_BSCODE"])) {
								$KSMAN = $row["OLD_BSCODE"];
							}else{
								$KSMAN = $row["NEW_BSCODE"];;
							}

							//원수사 사용인코드   	
							$sql = " SELECT INSCODE , MAX(CASE WHEN SGUBUN = '1' THEN BSCODE ELSE '' END) NEW_BSCODE , MAX(CASE WHEN SGUBUN = '2' THEN BSCODE ELSE '' END) OLD_BSCODE
		                               FROM INSWON WHERE SCODE = '".$_SESSION['S_SCODE']."' AND SKEY = '".$KSKEY."' AND INSCODE = '".$INSCODE."' 
									  GROUP BY INSCODE " ; 
							$result  = sqlsrv_query( $mscon, $sql );
							$row =  sqlsrv_fetch_array($result); 

							if (empty($row["NEW_BSCODE"])) {
								$KDMAN = $row["OLD_BSCODE"]; 
							}else{
								$KDMAN = $row["NEW_BSCODE"];
							}

							//-->피보험자
							$PNAME =	 $objWorksheet->getCell('I' . $i)->getValue();
							$PNAME =   iconv("UTF-8","EUC-KR",$PNAME);		
							$PNAME = str_replace("'","",$PNAME);

							//지사,지점,팀,지사장 	
							$sql = " SELECT A.BONBU AS BONBU , A.JISA AS JISA , A.JIJUM AS JIJUM , A.TEAM AS TEAM ,
                                                        						  (SELECT SKEY FROM SWON
																				    WHERE BONBU = A.BONBU AND
																					      JISA = A.JISA AND 
																						  JIK = '4001' ) JSJANG  
																					FROM SWON A 
																				   WHERE SCODE = '".$_SESSION['S_SCODE']."' AND SKEY = '".$KSKEY."'" ; 
							$result  = sqlsrv_query( $mscon, $sql );
							$row =  sqlsrv_fetch_array($result); 
							
							$JISA = $row["JISA"];

							
							//-->본부
							$BONBU = '000001';

							//지사,지점,팀,지사장 	
							$sql = " SELECT A.BONBU AS BONBU , A.JISA AS JISA , A.JIJUM AS JIJUM , A.TEAM AS TEAM ,
                                                        						  (SELECT SKEY FROM SWON
																				    WHERE BONBU = A.BONBU AND
																					      JISA = A.JISA AND 
																						  JIK = '4001' ) JSJANG  
																					FROM SWON A 
																				   WHERE SCODE = '".$_SESSION['S_SCODE']."' AND SKEY = '".$KSKEY."'" ; 
							$result  = sqlsrv_query( $mscon, $sql );
							$row =  sqlsrv_fetch_array($result); 
							
							$JISA = $row["JISA"];
                            $JIJUM = $row["JIJUM"];							
							$TEAM = $row["TEAM"];							
							$JSJANG = $row["JSJANG"];							
							
							$sql = "
									insert into KWN( SCODE ,KCODE ,KNAME ,KSTBIT ,KDATE , FDATE , TDATE , INSILJ , INSCODE , KSMAN , KDMAN , GSKEY , KSKEY , BONBU ,JISA ,JIJUM , TEAM , JISAID , 
													 ITEMNM, MAMT, SAMT, NBIT, CARNUM,
									                 PNAME , IDATE      ,ISWON      ,UDATE      ,USWON )
									values('".$_SESSION['S_SCODE']."', '".$KCODE."' , '".$KNAME."' , '".$KSTBIT."' , '".$KDATE."' , '".$FDATE."' , '".$TDATE."' , '".$INSILJ."' ,  '".$INSCODE."' ,'".$KSMAN."','".$KDMAN."','".$GSKEY."' ,'".$KSKEY."' , '".$BONBU."' , '".$JISA."' ,
                                           '".$JIJUM."' , '".$TEAM."' , '".$JSJANG."' , 
										   '".$ITEMNM."' , ".$MAMT." , ".$SAMT." , '".$NBIT."' , '".$CARNUM."' ,
										   '".$PNAME."' , getdate(),'".$_SESSION['S_SKEY']."' ,getdate(),'".$_SESSION['S_SKEY']."')";

							
							/*
							$sql = "
									insert into KWN( SCODE ,KCODE ,KNAME ,KSTBIT ,KDATE , FDATE , TDATE , INSILJ , INSCODE , KSMAN , KDMAN , GSKEY , KSKEY , BONBU ,JISA ,JIJUM , TEAM , JISAID , 
									                 PNAME , IDATE      ,ISWON      ,UDATE      ,USWON )
									values('".$_SESSION['S_SCODE']."', '".$KCODE."' , '".$KNAME."' , '".$KSTBIT."' , '".$KDATE."' , '".$FDATE."' , '".$TDATE."' , '".$INSILJ."' ,  '".$INSCODE."' ,'".$KSMAN."','".$KDMAN."','".$GSKEY."' ,'".$KSKEY."' , '".$BONBU."' , '".$JISA."' ,
                                           '".$JIJUM."' , '".$TEAM."' , '".$JSJANG."' , '".$PNAME."' , getdate(),'".$_SESSION['S_SKEY']."' ,getdate(),'".$_SESSION['S_SKEY']."')";
*/
							
							sqlsrv_query("BEGIN TRAN");
							$result =  sqlsrv_query( $mscon, $sql );
							if ($result == false){
								sqlsrv_query("ROLLBACK");
 								print_r($sql);
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

		$message ='계약 데이터 업로드 완료처리 !';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "");
		echo json_encode($returnJson);
		exit;	
} // upload if 에대한 end 

 
 
?> 

