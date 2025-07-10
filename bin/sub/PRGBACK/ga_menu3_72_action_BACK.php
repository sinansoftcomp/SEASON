<?
// programmer : 김순관    개발일자 : 2024.02.04  GA플러스 최고중요 PROGRAMM
//특정이미지가 저장안되는 현상이 나타남..!! 2017-11-29  
@ini_set('gd.jpeg_ignore_warning', 1);

include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/class/excel_upload.php");

include($_SERVER['DOCUMENT_ROOT']."/bin/sub/menu3/ga_menu3_72_fun.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/sub/menu3/ga_menu3_70_common_fun.php");

require_once $_SERVER['DOCUMENT_ROOT']."/bin/include/lib/PHPExcel/Classes/PHPExcel.php"; 
require_once $_SERVER['DOCUMENT_ROOT']."/bin/include/lib/PHPExcel/Classes/PHPExcel/IOFactory.php";  // IOFactory.php을 불러와야 하며, 경로는 사용자의 설정에 맞게 수정해야 한다.

$upldate		=	str_replace('-','',$_POST['upldate']);
$gubun		=	$_POST['gubun'];
$filename	=	$_POST['filename'];
$code		=	$_POST['code'];
$bigo		=	$_POST['bigo'];
$uploadFile =	$_POST['filename'];
$file_cnt		=	$_POST['imgcnt'];		// 업로드 파일 수 

$del_upldate		=	str_replace('-','',$_POST['upldate']);
$del_uplnum		=	$_POST['uplnum'];

if($_SESSION['S_SCODE'] == null ){
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);
	$message = '필수입력값 오류, 재 로그인해주세요.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
	echo json_encode($returnJson);
	exit;			
}

$EXCEL_all_col =['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
								'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ',
								'BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ',
								'CA','CB','CC','CD','CE','CF','CG','CH','CI','CJ','CK','CL','CM','CN','CO','CP','CQ','CR','CS','CT','CU','CV','CW','CX','CY','CZ'];
//===============================================================================================================//
//   file upload rtn 
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
			$file_name	= $_SESSION['S_SCODE']."_P".$up_time.".".$ext;	 

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
					//-->입력패턴를 찾는다 
					$col_cnt = 108; // data수집 건수   108 / 3 = 36항목 수집  
					$pt  = pattern_ser($objWorksheet,$mscon,$col_cnt);
					//print_r($pt);

					$maxRow		= $objWorksheet->getHighestRow();
					$maxCol        = $objWorksheet->getHighestColumn();
					$SERIALCNT	= 0;
					$existChk	= 0;
					$updateCnt=0;
															
					
					$EXCEL_declare = array( // 1차원 배열을 1개 갖는 2차원 배열 선언
						array()
					);

					$sql = "select *  from UPLOAD_EXCEL where scode = '".$_SESSION['S_SCODE']."' and code = '".$pt[CODE]."' and gubun = '".$pt[GUBUN]."' and gubunsub = '".$pt[GUBUNSUB]."'";
					$result  = sqlsrv_query( $mscon, $sql );
					$row =  sqlsrv_fetch_array($result); 

					$start_line = $pt[tit_line]+1;

					//2차원 배열에 talble 특성을 담는다.
					$ex_i=0; 
					$ex_seq =1; 
					for ($i = 1 ; $i <= 108; $i++) {		 
						$ex_i       =   $ex_i + 1;

						//sql table layout 필드 지정. 	
						$A = "A".$ex_seq; 
						$B = "B".$ex_seq; 
						$C = "C".$ex_seq; 

						 if ($ex_i  == 1) {
							 $EXCEL_declare[$ex_seq ][1] =  preg_replace('/\s+/', '', $row[$A]);
						 }
						 if ($ex_i  == 2) {
							 $EXCEL_declare[$ex_seq ][2] = preg_replace('/\s+/', '', $row[$B]);
						 }
						 if ($ex_i  == 3) {
							 $EXCEL_declare[$ex_seq ][3] = preg_replace('/\s+/', '', $row[$C]);
						 }
						//3개필드 마다 필드가 반복된다.
						if ($ex_i  == 3) {
							$ex_i = 0;
							$ex_seq  =  $ex_seq + 1;
						}
					}

					/*
					for ($i = 1 ; $i <= 101 ; $i++) {
						for ($y = 1 ; $y <= 3 ; $y++) {
							 print_r( $EXCEL_declare[$i][$y]."<br>") ;
						}
					}
					*/ 

					 //insert 구룹번호구하기  
					$sql = "select max(UPLNUM) UPLNUM   from upload_history where scode = '".$_SESSION['S_SCODE']."' and upldate = '".$upldate."' and gubun = '".$pt[GUBUN]."' and gubunsub = '".$pt[GUBUNSUB]."' ";
					$result  = sqlsrv_query( $mscon, $sql );
					$row =  sqlsrv_fetch_array($result); 
					$cnt = $row["UPLNUM"];	
					 
					if (is_null($row["UPLNUM"]) ) {
						$LS_UPLNUM	 = '1';
					}else{
						$LS_UPLNUM =     $row["UPLNUM"]  + 1;
					}

					$ins_tot_cnt = 0 ; 
					$exc_bit = 'Y'; 
					for ($i = $start_line ; $i <= $maxRow ; $i++) {		//정의된 시작 행부터 부터 데이터시작
	
							unset( $EXCEL_data ); //배열초기화 
							for ($ed=1;  $ed <= 36  ; $ed++ ) {    //나중에 필드 숫자만큼  하기 
								   
								   //-->수집하지않는 필드. 
								   if ($EXCEL_declare[$ed][3] == '4'  || empty($EXCEL_declare[$ed][3])  ) {
										$EXCEL_data[$ed] = "";		
										continue;
								   }
									//지정된 열과 현재의 행에서 엑셀DATA를 가져와  DATA순서로 일차원배열에 담는다.  
									$EXCEL_data[$ed] =	 $objWorksheet->getCell($EXCEL_declare[$ed][2] . $i)->getValue();
									$EXCEL_data[$ed] =   iconv("UTF-8","EUC-KR",$EXCEL_data[$ed]);		
	
									//현재 DATA의 TYPE CHECKING( number 일때) 
									 if (!is_null($EXCEL_data[$ed])) {
										   if ($EXCEL_declare[$ed][3] == '1' ) {
												$EXCEL_data[$ed] = str_replace(",","",$EXCEL_data[$ed]);			
 										   }
									}
									//현재 DATA의 TYPE CHECKING( date일때 숫자만 취함) 
									 if (!is_null($EXCEL_data[$ed])) {
										   if ( $EXCEL_declare[$ed][3] == '2' ) {
												$EXCEL_data[$ed] = preg_replace('/[^0-9]*/s', '',$EXCEL_data[$ed]);								
										   }
									}
							}
							$EXCEL_data[18] = $pt[$EXCEL_declare[19][2]] ; //-->1번수당명 
							$EXCEL_data[20] = $pt[$EXCEL_declare[21][2]];  //-->2번수당명 
							$EXCEL_data[22] = $pt[$EXCEL_declare[23][2]];  //-->3번수당명 
							$EXCEL_data[24] = $pt[$EXCEL_declare[25][2]];  //-->4번수당명
							$EXCEL_data[26] = $pt[$EXCEL_declare[27][2]];  //-->5번수당명 
							$EXCEL_data[28] = $pt[$EXCEL_declare[29][2]];  //-->6번수당명 
							$EXCEL_data[30] = $pt[$EXCEL_declare[31][2]];  //-->7번수당명 
							$EXCEL_data[32] = $pt[$EXCEL_declare[33][2]];  //-->8번수당명 
							$EXCEL_data[34] = $pt[$EXCEL_declare[35][2]];  //-->9번수당명 
							
							//엑셀전체 필드저장.
							$e_text = "";
							$e_text_tit = "";
							$e_text_col = "";
							for ($ei=0; $ei <=200  ; $ei++ ) {  //--여기는 배열 첨자 때문에 0부터.
									$e_text_tit = $pt[$EXCEL_all_col[$ei]];
									$e_text_col =  $objWorksheet->getCell($EXCEL_all_col[$ei] . $i)->getValue();
									$e_text_col  =   iconv("UTF-8","EUC-KR",$e_text_col );		
									$e_text = $e_text.$e_text_tit.'&&'.$e_text_col.'###';		
									if ( $maxCol  == $EXCEL_all_col[$ei] ) {
										 break;
									}	
							}

					 




					} //-->하나의 shhet대한 







				print_r($EXCEL_data);
				print_r($e_text);
				
				/*
				$message ='$EXCEL_data';
				$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "");
				echo json_encode($returnJson);
				exit;	
				*/ 	
			}//-->전체sheet for end.
		} //file_no end 
		 unlink($del_file); //작업파일삭제 
} // upload if 에대한 end 


//===============================================================================================================//
//   file delete에대한  rtn 
//===============================================================================================================//

if ($_POST['type'] == 'del' ) { 
		if($_SESSION['S_SCODE'] == null ||  $del_upldate == null || $del_uplnum == null ){
			sqlsrv_free_stmt($result);
			sqlsrv_close($mscon);
			$message = '삭제할 수 없는 data입니다 ';
			$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
			echo json_encode($returnJson);
			exit;	
		}

		//업로드 경과시간이 3시간 이상이면 삭제불가  
		$sql = "SELECT DATEDIFF(HOUR,   IDATE, getdate()) AS time_cha	  FROM UPLOAD_HISTORY  where scode = '".$_SESSION['S_SCODE']."' and upldate = '".$del_upldate."' and uplnum = '".$del_uplnum."' and  GUBUN = '". 'A'."' ";
		$result  = sqlsrv_query( $mscon, $sql );
		$row =  sqlsrv_fetch_array($result); 
		$time_cha = $row["time_cha"];	
		if ($time_cha == null || $time_cha >  '3'  ) {
			sqlsrv_free_stmt($result);
			sqlsrv_close($mscon);
			$message = '해당히스토리는 업로드 후 3시간이상 경과되어 삭제불가합니다. ';
			$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
			echo json_encode($returnJson);
			exit;	

		} 

		// 계약서  삭제 
		$sql = "delete from kwn where scode = '".$_SESSION['S_SCODE']."' and upldate = '".$del_upldate."' and  uplnum = '". $del_uplnum."' ";
		sqlsrv_query("BEGIN TRAN");
		$result =  sqlsrv_query( $mscon, $sql );

		if ($result == false){
			sqlsrv_query("ROLLBACK");
			sqlsrv_free_stmt($result);
			sqlsrv_close($mscon);
			$message = '계약파일 삭제중 ERROR가 발생하였습니다! ';
			$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
			echo json_encode($returnJson);
			exit;	

		}
		sqlsrv_query("COMMIT");
 

		// 업로드히스토리 삭제  
		$sql = "delete from UPLOAD_HISTORY where scode = '".$_SESSION['S_SCODE']."' and upldate = '".$del_upldate."' and uplnum = '".$del_uplnum."' and  GUBUN = '". 'A'."' ";
		sqlsrv_query("BEGIN TRAN");
		$result =  sqlsrv_query( $mscon, $sql );
		
		if ($result == false){
			sqlsrv_query("ROLLBACK");
			sqlsrv_free_stmt($result);
			sqlsrv_close($mscon);
			$message = '업로드 히스토리 삭제중 ERROR가 발생하였습니다! ';
			$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
			echo json_encode($returnJson);
			exit;	

		}
		sqlsrv_query("COMMIT");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);

		$message = '삭제완료';
			$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
			echo json_encode($returnJson);
			exit;	
}

?> 

