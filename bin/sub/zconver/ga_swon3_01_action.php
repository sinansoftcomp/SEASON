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

					for ($i = 2 ; $i <= $maxRow ; $i++) {		//���ǵ� ���� ����� ���� �����ͽ���



							$SKEY  =  $objWorksheet->getCell('G' . $i)->getValue();
							$SKEY =   iconv("UTF-8","EUC-KR",$SKEY);		
							$SKEY = str_replace("'","",$SKEY);
							//-->�Ｚ����      
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


 							//-->db�պ�      
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

 							//-->KB�պ�      
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


 							//-->�����ػ�      
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

 							//-->MG�պ�      
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


 							//-->�Ｚȭ��     
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


 							//-->��ȭ�պ�
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


 							//-->�޸���ȭ��      
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


 							//-->�ﱹȭ��     
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

 							//-->�Ե��պ�     
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

 							//-->�����պ�     
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

					} //-->�ϳ��� shhet���� 
					
 
			}//-->��üsheet for end.
			unlink($del_file); //�۾����ϻ��� 
		} //file_no end 
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);

		$message ='�������������  ��࿢������ ���ε� �Ϸ�ó�� !';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "");
		echo json_encode($returnJson);
		exit;	
} // upload if ������ end 

 
 
?> 

