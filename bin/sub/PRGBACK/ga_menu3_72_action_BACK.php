<?
// programmer : �����    �������� : 2024.02.04  GA�÷��� �ְ��߿� PROGRAMM
//Ư���̹����� ����ȵǴ� ������ ��Ÿ��..!! 2017-11-29  
@ini_set('gd.jpeg_ignore_warning', 1);

include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/class/excel_upload.php");

include($_SERVER['DOCUMENT_ROOT']."/bin/sub/menu3/ga_menu3_72_fun.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/sub/menu3/ga_menu3_70_common_fun.php");

require_once $_SERVER['DOCUMENT_ROOT']."/bin/include/lib/PHPExcel/Classes/PHPExcel.php"; 
require_once $_SERVER['DOCUMENT_ROOT']."/bin/include/lib/PHPExcel/Classes/PHPExcel/IOFactory.php";  // IOFactory.php�� �ҷ��;� �ϸ�, ��δ� ������� ������ �°� �����ؾ� �Ѵ�.

$upldate		=	str_replace('-','',$_POST['upldate']);
$gubun		=	$_POST['gubun'];
$filename	=	$_POST['filename'];
$code		=	$_POST['code'];
$bigo		=	$_POST['bigo'];
$uploadFile =	$_POST['filename'];
$file_cnt		=	$_POST['imgcnt'];		// ���ε� ���� �� 

$del_upldate		=	str_replace('-','',$_POST['upldate']);
$del_uplnum		=	$_POST['uplnum'];

if($_SESSION['S_SCODE'] == null ){
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);
	$message = '�ʼ��Է°� ����, �� �α������ּ���.';
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
 	//���ε� ���� ����ŭ ȸ�� 
	for ($file_no=0 ; $file_no < $file_cnt ; $file_no++) { 
			//--------------------���� �ٿ�ε�
			$file_ori =  iconv("UTF-8","EUCKR",$_FILES['attach_file']['name'][ $file_no]); 
			$tmp_name = iconv("UTF-8","EUCKR",$_FILES['attach_file']['tmp_name'][ $file_no]);   
			$filename = iconv("UTF-8","EUCKR",$_FILES['attach_file']['tmp_name'][ $file_no]);
			$ext_temp	= explode(".",$file_ori);
			$ext		= $ext_temp[count($ext_temp)-1];
			$up_time = preg_replace('/[^0-9]*/s', '', date("Y-m-d H:i:s"));
			$file_name	= $_SESSION['S_SCODE']."_P".$up_time.".".$ext;	 

			$file_path  = '/gaplus/temp/';
			$ori_filename	= $_SERVER['DOCUMENT_ROOT']."/temp/".$file_name;
			//������ file�� �����ϸ� ����� 
			$del_file = 'D:\\www\gaplus\temp\\'. $file_name;
			 unlink($del_file);

			//����ã�⿡�� ������ ������ 9�������� ����
			$rtn =  file_upload($file_path, $file_name, 700,$file_ori, $tmp_name, '2') ;
		

			$EXCEL_data= array();
			//============================ 9�������� ����� excel �о kwn���̺� insert�ϴ� �κ�===================================
			$objPHPExcel = new PHPExcel();

			$filename = 'D:\\www\gaplus\temp\\'.$file_name; // �о���� ���� ������ ��ο� ���ϸ��� �����Ѵ�.

			// ���ε� �� ���� ���Ŀ� �´� Reader��ü�� �����.
			$objReader = PHPExcel_IOFactory::createReaderForFile($filename);

			// �б��������� ����
			$objReader->setReadDataOnly(true);
			// ���������� �д´�
			$objExcel = $objReader->load($filename);

			//sheet ������ŭ ó��
			$sheet_tot_cnt           = $objExcel->getSheetCount();
			for ($sheet_no=0 ; $sheet_no < $sheet_tot_cnt ; $sheet_no++) { 
					// ù��° ��Ʈ�� ����
					$objExcel->setActiveSheetIndex($sheet_no);
					$objWorksheet = $objExcel->getActiveSheet(); 
					$rowIterator = $objWorksheet->getRowIterator();
					foreach ($rowIterator as $row) { // ��� �࿡ ���ؼ�
					$cellIterator = $row->getCellIterator();
					$cellIterator->setIterateOnlyExistingCells(false); 
					}
					//-->�Է����ϸ� ã�´� 
					$col_cnt = 108; // data���� �Ǽ�   108 / 3 = 36�׸� ����  
					$pt  = pattern_ser($objWorksheet,$mscon,$col_cnt);
					//print_r($pt);

					$maxRow		= $objWorksheet->getHighestRow();
					$maxCol        = $objWorksheet->getHighestColumn();
					$SERIALCNT	= 0;
					$existChk	= 0;
					$updateCnt=0;
															
					
					$EXCEL_declare = array( // 1���� �迭�� 1�� ���� 2���� �迭 ����
						array()
					);

					$sql = "select *  from UPLOAD_EXCEL where scode = '".$_SESSION['S_SCODE']."' and code = '".$pt[CODE]."' and gubun = '".$pt[GUBUN]."' and gubunsub = '".$pt[GUBUNSUB]."'";
					$result  = sqlsrv_query( $mscon, $sql );
					$row =  sqlsrv_fetch_array($result); 

					$start_line = $pt[tit_line]+1;

					//2���� �迭�� talble Ư���� ��´�.
					$ex_i=0; 
					$ex_seq =1; 
					for ($i = 1 ; $i <= 108; $i++) {		 
						$ex_i       =   $ex_i + 1;

						//sql table layout �ʵ� ����. 	
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
						//3���ʵ� ���� �ʵ尡 �ݺ��ȴ�.
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

					 //insert �����ȣ���ϱ�  
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
					for ($i = $start_line ; $i <= $maxRow ; $i++) {		//���ǵ� ���� ����� ���� �����ͽ���
	
							unset( $EXCEL_data ); //�迭�ʱ�ȭ 
							for ($ed=1;  $ed <= 36  ; $ed++ ) {    //���߿� �ʵ� ���ڸ�ŭ  �ϱ� 
								   
								   //-->���������ʴ� �ʵ�. 
								   if ($EXCEL_declare[$ed][3] == '4'  || empty($EXCEL_declare[$ed][3])  ) {
										$EXCEL_data[$ed] = "";		
										continue;
								   }
									//������ ���� ������ �࿡�� ����DATA�� ������  DATA������ �������迭�� ��´�.  
									$EXCEL_data[$ed] =	 $objWorksheet->getCell($EXCEL_declare[$ed][2] . $i)->getValue();
									$EXCEL_data[$ed] =   iconv("UTF-8","EUC-KR",$EXCEL_data[$ed]);		
	
									//���� DATA�� TYPE CHECKING( number �϶�) 
									 if (!is_null($EXCEL_data[$ed])) {
										   if ($EXCEL_declare[$ed][3] == '1' ) {
												$EXCEL_data[$ed] = str_replace(",","",$EXCEL_data[$ed]);			
 										   }
									}
									//���� DATA�� TYPE CHECKING( date�϶� ���ڸ� ����) 
									 if (!is_null($EXCEL_data[$ed])) {
										   if ( $EXCEL_declare[$ed][3] == '2' ) {
												$EXCEL_data[$ed] = preg_replace('/[^0-9]*/s', '',$EXCEL_data[$ed]);								
										   }
									}
							}
							$EXCEL_data[18] = $pt[$EXCEL_declare[19][2]] ; //-->1������� 
							$EXCEL_data[20] = $pt[$EXCEL_declare[21][2]];  //-->2������� 
							$EXCEL_data[22] = $pt[$EXCEL_declare[23][2]];  //-->3������� 
							$EXCEL_data[24] = $pt[$EXCEL_declare[25][2]];  //-->4�������
							$EXCEL_data[26] = $pt[$EXCEL_declare[27][2]];  //-->5������� 
							$EXCEL_data[28] = $pt[$EXCEL_declare[29][2]];  //-->6������� 
							$EXCEL_data[30] = $pt[$EXCEL_declare[31][2]];  //-->7������� 
							$EXCEL_data[32] = $pt[$EXCEL_declare[33][2]];  //-->8������� 
							$EXCEL_data[34] = $pt[$EXCEL_declare[35][2]];  //-->9������� 
							
							//������ü �ʵ�����.
							$e_text = "";
							$e_text_tit = "";
							$e_text_col = "";
							for ($ei=0; $ei <=200  ; $ei++ ) {  //--����� �迭 ÷�� ������ 0����.
									$e_text_tit = $pt[$EXCEL_all_col[$ei]];
									$e_text_col =  $objWorksheet->getCell($EXCEL_all_col[$ei] . $i)->getValue();
									$e_text_col  =   iconv("UTF-8","EUC-KR",$e_text_col );		
									$e_text = $e_text.$e_text_tit.'&&'.$e_text_col.'###';		
									if ( $maxCol  == $EXCEL_all_col[$ei] ) {
										 break;
									}	
							}

					 




					} //-->�ϳ��� shhet���� 







				print_r($EXCEL_data);
				print_r($e_text);
				
				/*
				$message ='$EXCEL_data';
				$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "");
				echo json_encode($returnJson);
				exit;	
				*/ 	
			}//-->��üsheet for end.
		} //file_no end 
		 unlink($del_file); //�۾����ϻ��� 
} // upload if ������ end 


//===============================================================================================================//
//   file delete������  rtn 
//===============================================================================================================//

if ($_POST['type'] == 'del' ) { 
		if($_SESSION['S_SCODE'] == null ||  $del_upldate == null || $del_uplnum == null ){
			sqlsrv_free_stmt($result);
			sqlsrv_close($mscon);
			$message = '������ �� ���� data�Դϴ� ';
			$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
			echo json_encode($returnJson);
			exit;	
		}

		//���ε� ����ð��� 3�ð� �̻��̸� �����Ұ�  
		$sql = "SELECT DATEDIFF(HOUR,   IDATE, getdate()) AS time_cha	  FROM UPLOAD_HISTORY  where scode = '".$_SESSION['S_SCODE']."' and upldate = '".$del_upldate."' and uplnum = '".$del_uplnum."' and  GUBUN = '". 'A'."' ";
		$result  = sqlsrv_query( $mscon, $sql );
		$row =  sqlsrv_fetch_array($result); 
		$time_cha = $row["time_cha"];	
		if ($time_cha == null || $time_cha >  '3'  ) {
			sqlsrv_free_stmt($result);
			sqlsrv_close($mscon);
			$message = '�ش������丮�� ���ε� �� 3�ð��̻� ����Ǿ� �����Ұ��մϴ�. ';
			$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
			echo json_encode($returnJson);
			exit;	

		} 

		// ��༭  ���� 
		$sql = "delete from kwn where scode = '".$_SESSION['S_SCODE']."' and upldate = '".$del_upldate."' and  uplnum = '". $del_uplnum."' ";
		sqlsrv_query("BEGIN TRAN");
		$result =  sqlsrv_query( $mscon, $sql );

		if ($result == false){
			sqlsrv_query("ROLLBACK");
			sqlsrv_free_stmt($result);
			sqlsrv_close($mscon);
			$message = '������� ������ ERROR�� �߻��Ͽ����ϴ�! ';
			$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
			echo json_encode($returnJson);
			exit;	

		}
		sqlsrv_query("COMMIT");
 

		// ���ε������丮 ����  
		$sql = "delete from UPLOAD_HISTORY where scode = '".$_SESSION['S_SCODE']."' and upldate = '".$del_upldate."' and uplnum = '".$del_uplnum."' and  GUBUN = '". 'A'."' ";
		sqlsrv_query("BEGIN TRAN");
		$result =  sqlsrv_query( $mscon, $sql );
		
		if ($result == false){
			sqlsrv_query("ROLLBACK");
			sqlsrv_free_stmt($result);
			sqlsrv_close($mscon);
			$message = '���ε� �����丮 ������ ERROR�� �߻��Ͽ����ϴ�! ';
			$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
			echo json_encode($returnJson);
			exit;	

		}
		sqlsrv_query("COMMIT");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);

		$message = '�����Ϸ�';
			$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
			echo json_encode($returnJson);
			exit;	
}

?> 

