<?
// programmer : �����    �������� : 2024.02.04  GA�÷��� �ְ��߿� PROGRAMM
//Ư���̹����� ����ȵǴ� ������ ��Ÿ��..!! 2017-11-29  
@ini_set('gd.jpeg_ignore_warning', 1);

include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/class/excel_upload.php");

include($_SERVER['DOCUMENT_ROOT']."/bin/sub/menu3/ga_menu3_52_fun.php");
//-------> �ʵ庰 �� ó��  ���,���� �����Ḧ ���� ����Ͽ��� �ǳ� ���� �� ������ �����ϰ� ����� �� �ֵ���  ������ ������.
include($_SERVER['DOCUMENT_ROOT']."/bin/sub/menu3/ga_menu3_52_tit_fun.php");

require_once $_SERVER['DOCUMENT_ROOT']."/bin/include/lib/PHPExcel/Classes/PHPExcel.php"; 
require_once $_SERVER['DOCUMENT_ROOT']."/bin/include/lib/PHPExcel/Classes/PHPExcel/IOFactory.php";  
include($_SERVER['DOCUMENT_ROOT']."/bin/sub/menu3/ga_menu3_exc_date_fun.php");


 

//$upldate		=	str_replace('-','',$_POST['upldate']);
$upldate  = date("Ymd");  //�Է½� ��������

$gubun		=	$_POST['gubun'];
$filename	=	$_POST['filename'];
$uploadFile =	$_POST['filename'];
$file_cnt		=	$_POST['imgcnt'];		// ���ε� ���� �� 

 


 
//--->�Ϻη� ��ƾ �ø� �Ʒ��� �� �����ϰ� .....
if($_SESSION['S_SCODE'] == null ){
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);
	$message = '���ǿ����� ���������� �ʽ��ϴ� .';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
	echo json_encode($returnJson);
	exit;			
}
if ($_POST['type'] == 'in' ) {
	if (empty($file_cnt)) {
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message ='���ε��Ͻ� ����FILE�� ���õ��� �ʾҽ��ϴ�!';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "");
		echo json_encode($returnJson);
		exit;	
	}
}

 

//===============================================================================================================//
//   file upload rtn  *ȯȭ-ȯ������ �������κ� Ÿ��Ʋ�� ���� Ʋ��.
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
			$file_name	= $_SESSION['S_SCODE']."_A".$up_time.".".$ext;	 

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
					$sheetname =   iconv("UTF-8","EUC-KR", $objWorksheet->getTitle() );		

					//print_r($EXCEL_declare);
					//exit; 

					//--->���⼭���� ���� 

					$maxRow		= $objWorksheet->getHighestRow();
					$maxCol        = $objWorksheet->getHighestColumn();
					$SERIALCNT	= 0;
					$existChk	= 0;
					$updateCnt=0;

					$ins_tot_cnt = 0 ;
					$up_tot_cnt = 0 ;
					$ins_tot_err_cnt = 0 ;

					for ($i = 4 ; $i <= $maxRow ; $i++) {		//���ǵ� ���� ����� ���� �����ͽ���
							
							//-->���ǹ�ȣ
							$KCODE =	 $objWorksheet->getCell('E' . $i)->getValue();
							$KCODE =   iconv("UTF-8","EUC-KR",$KCODE);		
							$KCODE = str_replace("'","",$KCODE);
							
							//-->����ڼ���
							$KNAME =	 $objWorksheet->getCell('H' . $i)->getValue();
							$KNAME =   iconv("UTF-8","EUC-KR",$KNAME);		
							$KNAME = str_replace("'","",$KNAME);

							//-->������ 
							$KSTBIT =	 $objWorksheet->getCell('F' . $i)->getValue();
							$KSTBIT =   iconv("UTF-8","EUC-KR",$KSTBIT);		
							$KSTBIT = str_replace("'","",$KSTBIT);

							//-->������� 
							$KDATE =	 $objWorksheet->getCell('G' . $i)->getValue();
							$KDATE =   iconv("UTF-8","EUC-KR",$KDATE);		
							$KDATE = str_replace("'","",$KDATE);

							//-->�������� 
							$FDATE =	 $objWorksheet->getCell('BI' . $i)->getValue();
							$FDATE =   iconv("UTF-8","EUC-KR",$FDATE);		
							$FDATE = str_replace("'","",$FDATE);

							//-->�������� 
							$TDATE =	 $objWorksheet->getCell('O' . $i)->getValue();
							
							$TDATE = exc_date($TDATE);	//-->���� date�� ���ڷ� 	getValue�Ǿ��� �� date�������� ��ȯ��.											

							$TDATE =   iconv("UTF-8","EUC-KR",$TDATE);		
							$TDATE = str_replace("-","",$TDATE);
							$TDATE = substr($TDATE,0,8);

							//-->��ǰ���� 
							$INSILJ =	 $objWorksheet->getCell('AJ' . $i)->getValue();
							$INSILJ =   iconv("UTF-8","EUC-KR",$INSILJ);		
							$INSILJ = str_replace("'","",$INSILJ);

							if ($INSILJ == '�Ϲ�' ) {
								$INSILJ = '1';
							}
							if ($INSILJ == '���' ) {
								$INSILJ = '2';
							}
							if ($INSILJ == '�ڵ���' ) {
								$INSILJ = '3';
							}
			
							//-->�������ڵ� 
							$INSCODE =	 $objWorksheet->getCell('C' . $i)->getValue();
							$INSCODE =   iconv("UTF-8","EUC-KR",$INSCODE);		
							$INSCODE = str_replace("'","",$INSCODE);
							 
							if ($INSCODE == '�Ｚȭ��' ) {
								$INSCODE = '00021';
							}
							if ($INSCODE == '�����ػ�' ) {
								$INSCODE = '00018';
							}
							if ($INSCODE == 'KB�պ�' ) {
								$INSCODE = '00019';
							}
							if ($INSCODE == 'DB�պ�' ) {
								$INSCODE = '00022';
							}
							if ($INSCODE == '�޸���ȭ��' ) {
								$INSCODE = '00017';
							}
							if ($INSCODE == 'MG�պ�' ) {
								$INSCODE = '00033';
							}
							if ($INSCODE == '�Ե��պ�' ) {
								$INSCODE = '00025';
							}
							if ($INSCODE == '��ȭ�պ�' ) {
								$INSCODE = '00020';
							}
							if ($INSCODE == '�ﱹȭ��' ) {
								$INSCODE = '00023';
							}

							
							//-->��ǰ��
							$ITEMNM =	 $objWorksheet->getCell('J' . $i)->getValue();
							$ITEMNM =   iconv("UTF-8","EUC-KR",$ITEMNM);		
							$ITEMNM = str_replace("'","",$ITEMNM);

							//-->�����
							$MAMT =	 $objWorksheet->getCell('K' . $i)->getValue();
							$MAMT =   iconv("UTF-8","EUC-KR",$MAMT);		
							$MAMT = str_replace(",","",$MAMT);

							//-->���������
							$SAMT =	 $objWorksheet->getCell('M' . $i)->getValue();
							$SAMT =   iconv("UTF-8","EUC-KR",$SAMT);		
							$SAMT = str_replace(",","",$SAMT);

							//-->���Թ��
							$NBIT =	 $objWorksheet->getCell('AD' . $i)->getValue();
							$NBIT =   iconv("UTF-8","EUC-KR",$NBIT);		
							$NBIT = str_replace("'","",$NBIT);

							//-->����ȣ
							$CARNUM =	 $objWorksheet->getCell('T' . $i)->getValue();
							$CARNUM =   iconv("UTF-8","EUC-KR",$CARNUM);		
							$CARNUM = str_replace("'","",$CARNUM);
							
							
							//-->�������
							$GSKEY =	 $objWorksheet->getCell('AO' . $i)->getValue();
							$GSKEY =   iconv("UTF-8","EUC-KR",$GSKEY);		
							$GSKEY = str_replace("'","",$GSKEY);

							//-->���ݻ��
							$KSKEY =	 $objWorksheet->getCell('AN' . $i)->getValue();
							$KSKEY =   iconv("UTF-8","EUC-KR",$KSKEY);		
							$KSKEY = str_replace("'","",$KSKEY);


							
							//������ �����ڵ�   	
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

							//������ ������ڵ�   	
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

							//-->�Ǻ�����
							$PNAME =	 $objWorksheet->getCell('I' . $i)->getValue();
							$PNAME =   iconv("UTF-8","EUC-KR",$PNAME);		
							$PNAME = str_replace("'","",$PNAME);

							//����,����,��,������ 	
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

							
							//-->����
							$BONBU = '000001';

							//����,����,��,������ 	
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

					} //-->�ϳ��� shhet���� 
					
 
			}//-->��üsheet for end.
			unlink($del_file); //�۾����ϻ��� 
		} //file_no end 
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);

		$message ='��� ������ ���ε� �Ϸ�ó�� !';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "");
		echo json_encode($returnJson);
		exit;	
} // upload if ������ end 

 
 
?> 

