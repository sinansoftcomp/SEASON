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
							
							//-->�����ȣ
							$SKEY =	 $objWorksheet->getCell('A' . $i)->getValue();
							$SKEY =   iconv("UTF-8","EUC-KR",$SKEY);		
							$SKEY = str_replace("'","",$SKEY);

							//-->����� 
							$SNAME =	 $objWorksheet->getCell('B' . $i)->getValue();
							$SNAME =   iconv("UTF-8","EUC-KR",$SNAME);		
							$SNAME = str_replace("'","",$SNAME);

							//-->�Ի��� 
							$INDATE =	 $objWorksheet->getCell('C' . $i)->getValue();
							$INDATE =   iconv("UTF-8","EUC-KR",$INDATE);		
							$INDATE = str_replace("'","",$INDATE);

							//-->������ 
							$YDATE =	 $objWorksheet->getCell('E' . $i)->getValue();
							$YDATE =   iconv("UTF-8","EUC-KR",$YDATE);		
							$YDATE = str_replace("'","",$YDATE);

							//-->����� 
							$TDATE =	 $objWorksheet->getCell('G' . $i)->getValue();
							$TDATE =   iconv("UTF-8","EUC-KR",$TDATE);		
							$TDATE = str_replace("'","",$TDATE);

							//-->��å
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
							if ($POS == '����' ) {
								$POS = '1120';
								$JIK  = '1001';
							}
							if ($POS == '�Ŵ���' ) {
								$POS = '1130';
								$JIK  = '1001';
							}
							if ($POS == '�պ��Ŵ���' ) {
								$POS = '1140';
								$JIK  = '1001';
							}
							if ($POS == '����' ) {
								$POS = '1150';
								$JIK  = '1001';
							}
							if ($POS == '�系�̻�' ) {
								$POS = '1160';
								$JIK  = '1001';
							}
							if ($POS == '����' ) {
								$POS = '2001';
								$JIK  = '2001';
							}
							if ($POS == '������' ) {
								$POS = '3001';
								$JIK  = '3001';
							}
							if ($POS == '������' ) {
								$POS = '4001';
								$JIK  = '4001';
							}
							if ($POS == '������' ) {
								$POS = '5001';
								$JIK  = '5001';
							}

							//-->��������
							$TBIT =	 $objWorksheet->getCell('I' . $i)->getValue();
							$TBIT =   iconv("UTF-8","EUC-KR",$TBIT);		
							$TBIT = str_replace("'","",$TBIT);
 
							if ($TBIT == '����' ) {
								$TBIT = '1';
 							}

							if ($TBIT == '����' ) {
								$TBIT = '2';
 							}
							if ($TBIT == '����' ) {
								$TBIT = '3';
 							}
							if ($TBIT == '����' ) {
								$TBIT = '4';
 							}

							//-->�ֹι�ȣ 
							$SJUNO =	 $objWorksheet->getCell('J' . $i)->getValue();
							$SJUNO =   iconv("UTF-8","EUC-KR",$SJUNO);		
							$SJUNO = str_replace("'","",$SJUNO);	 


							//-->�޴���ȭ��ȣ  
							$HTEL =	 $objWorksheet->getCell('K' . $i)->getValue();
							$HTEL =   iconv("UTF-8","EUC-KR",$HTEL);		
							$HTEL = str_replace("'","",$HTEL);
	
							$HTEL1	= substr($HTEL,0,3); 
							$HTEL2	=substr($HTEL,3,4); 
							$HTEL3	=substr($HTEL,7,4); 

							//-->�̸��� 
							$EMAIL =	 $objWorksheet->getCell('L' . $i)->getValue();
							$EMAIL =   iconv("UTF-8","EUC-KR",$EMAIL);		
							$EMAIL = str_replace("'","",$EMAIL);
	
  							//-->�����ȣ 
							$POST =	 $objWorksheet->getCell('M' . $i)->getValue();
							$POST =   iconv("UTF-8","EUC-KR",$POST);		
							$POST = str_replace("'","",$POST);


  							//-->�ּ� 
							$ADDR =	 $objWorksheet->getCell('N' . $i)->getValue();
							$ADDR =   iconv("UTF-8","EUC-KR",$ADDR);		
							$ADDR = str_replace("'","",$ADDR);

							//-->����
							$BONBU = '000001';

							//-->���� 
 							$JISA =	 $objWorksheet->getCell('P' . $i)->getValue();
							$JISA =   iconv("UTF-8","EUC-KR",$JISA);		
							$JISA = str_replace("'","",$JISA);

							$sql = "select JSCODE   from JISA where scode = '".$_SESSION['S_SCODE']."' and JSNAME = '".$JISA."'";
							$result  = sqlsrv_query( $mscon, $sql );
							$row =  sqlsrv_fetch_array($result); 
							$JISA = $row["JSCODE"];	


							//-->���� 
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
 

		
		

					} //-->�ϳ��� shhet���� 
					
 
			}//-->��üsheet for end.
			unlink($del_file); //�۾����ϻ��� 
		} //file_no end 
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);

		$message ='�������  ��࿢������ ���ε� �Ϸ�ó�� !';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "");
		echo json_encode($returnJson);
		exit;	
} // upload if ������ end 

 
 
?> 

