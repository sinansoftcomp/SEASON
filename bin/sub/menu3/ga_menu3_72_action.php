<?
//error_reporting(E_ALL); ini_set('display_errors', 1);
// programmer : �����    �������� : 2024.02.04  GA�÷��� �ְ��߿� PROGRAMM
//Ư���̹����� ����ȵǴ� ������ ��Ÿ��..!! 2017-11-29  
@ini_set('gd.jpeg_ignore_warning', 1);

include($_SERVER['DOCUMENT_ROOT']."/bin/include/class/common_class.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/class/excel_upload.php");

include($_SERVER['DOCUMENT_ROOT']."/bin/sub/menu3/ga_menu3_72_fun.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/sub/menu3/ga_menu3_72_tit_fun.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/sub/menu3/ga_menu3_exc_date_fun.php");


require_once $_SERVER['DOCUMENT_ROOT']."/bin/include/lib/PHPExcel/Classes/PHPExcel.php"; 
require_once $_SERVER['DOCUMENT_ROOT']."/bin/include/lib/PHPExcel/Classes/PHPExcel/IOFactory.php";  // IOFactory.php�� �ҷ��;� �ϸ�, ��δ� ������� ������ �°� �����ؾ� �Ѵ�.

//$upldate		=	str_replace('-','',$_POST['upldate']);
$upldate  = date("Ymd");  //�Է½� ��������

$gubun		=	$_POST['gubun'];
$filename	=	$_POST['filename'];
$yymm		=	str_replace('-','',$_POST['yymm']);  //���������� 
$uploadFile =	$_POST['filename'];
//$file_cnt		=	$_POST['imgcnt'];		

//---> delete�� ����ʵ� 
$del_upldate	   	  =	str_replace('-','',$_POST['upldate']);
$del_gubun		  =	$_POST['gubun'];
$del_gubunsub	  =	$_POST['gubunsub'];
$del_uplnum		  =	$_POST['uplnum'];



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
	$file_cnt = count($_FILES['attach_file']['name']); // ���ε� ���� ��, �ö�� file ��  
	if ($yymm == "" || empty($yymm )) {
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message ='���������� ���� �Է��Ͻñ� �ٶ��ϴ�!';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "");
		echo json_encode($returnJson);
		exit;	
	}
	if (empty($file_cnt)) {
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message ='���ε��Ͻ� ����FILE�� ���õ��� �ʾҽ��ϴ�!';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "");
		echo json_encode($returnJson);
		exit;	
	}
}


$ins_ipmst = "  SCODE    , IPDATE    , GUBUN    , GUBUNSUB    , INO    , ISEQ     , YYMM 
						  , ADATE	   , BDATE    , CDATE     , DDATE      , EDATE       , INSCODE       , KSMAN      , MKSMAN       , KCODE       , ITEM       , ITEMNM       , NBIT       , NJUKI       , NCNT       , ISTBIT 
					      , MAMT       , HWANAMT       , ST1       , SAMT1       , ST2       , SAMT2       , ST3       , SAMT3       , ST4       , SAMT4       , ST5       , SAMT5       , ST6       , SAMT6       , ST7       , SAMT7 
					      , ST8       , SAMT8       , ST9       , SAMT9        , ST10       , SAMT10   , ST11      , SAMT11  , ST12       , SAMT12   , ST13       , SAMT13     , HSSU       , KNAME, PNAME, CARNUM ,KSMAN_NAME, MKSMAN_NAME	,BSU	,BCON	,DCODE	,DNAME	,GKCODE,	BKIKAN	,NKIKAN	, SAGUBUN , MULGUBUN , CAKGUBUN , JCAREBIT , CARTYPE , INSILJ , CHMONTH ,
						  CHCNT , CHBIT , SELFBIT , SUSUNGBIT , CHGCNT  
						  , IDATE       , ISWON       , UDATE       , USWON       , ORIDATA  "; 

$EXCEL_all_col =['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
								'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ',
								'BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ',
								'CA','CB','CC','CD','CE','CF','CG','CH','CI','CJ','CK','CL','CM','CN','CO','CP','CQ','CR','CS','CT','CU','CV','CW','CX','CY','CZ'];

//===============================================================================================================//
//   file upload rtn  *ȯȭ-ȯ������ �������κ� Ÿ��Ʋ�� ���� Ʋ��.
//===============================================================================================================//

if ($_POST['type'] == 'in' ) {

try {
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
					$sheetname =   iconv("UTF-8","EUC-KR", $objWorksheet->getTitle() );		


					//==============================================================//
					//-->�Է����ϸ� ã�´� 
					//==============================================================//


					$col_cnt = 204; // data���� �Ǽ�   68�׸� ����  
					$pt  = pattern_ser($objWorksheet,$mscon,$col_cnt);
				 

					//print_r($pt);
					//print_r($pt[b_Status]);      //���Ϻ���ġ error������.
					//exit;	

					//-->���������� ã�����Ͽ��ٸ� 	
					if ($pt['YN'] == 'N') {
						$pt['GUBUN'] = 'P';
						$pt['GUBUNSUB'] = 'E1';
					}

					 //insert �����ȣ���ϱ�  (������ ���б����� ���� ���� DISPLAY ������) 
					$sql = "select max(UPLNUM) UPLNUM   from upload_history where scode = '".$_SESSION['S_SCODE']."' and upldate = '".$upldate."' and gubun = '".$pt['GUBUN']."' ";
					$result  = sqlsrv_query( $mscon, $sql );
					$row =  sqlsrv_fetch_array($result); 
					$LS_UPLNUM = $row["UPLNUM"];	

					if (is_null($LS_UPLNUM) ||$LS_UPLNUM == "" || $LS_UPLNUM < 1  ) {
						$LS_UPLNUM	 = 1;
					}else{
						$LS_UPLNUM =     $LS_UPLNUM  + 1;
					}



					//--->������ ã�� ���Ͽ��ٸ� �����丮�� ���� �����Ų��.
					if ($pt['YN'] == 'N') {
							
							//print_r($pt);
							//print_r("=====================");
							//print_r( $objWorksheet->getHighestColumn());
							//print_r("======================");

								
							$bigo = "�����Է� ������ ã�� ���Ͽ����ϴ�.";
							$bigo  =	"St:[".$sheetname ."] ".$bigo.$pt['b_Status'] ;
							$bigo = substr($bigo, 0, 6450); 
							$sql = "
							insert into UPLOAD_HISTORY( 	SCODE, UPLDATE  , GUBUN  , GUBUNSUB, UPLNUM      , FILENAME  , CODE     , CNT               , FCNT       , BIGO  ,YYMM,     IDATE       , ISWON  )
							values('".$_SESSION['S_SCODE']."','$upldate', '$pt[GUBUN]','$pt[GUBUNSUB]', $LS_UPLNUM,'$file_ori'   ,'' ,'0' ,'0','$bigo', '$yymm' ,getdate(),'".$_SESSION['S_SKEY']."')";
							
							//print_r($sql);

							sqlsrv_query($mscon,"BEGIN TRAN");
							$result =  sqlsrv_query( $mscon, $sql );
							if ($result == false){
								sqlsrv_query($mscon,"ROLLBACK");
							}else{
								sqlsrv_query($mscon,"COMMIT");
							}
							 continue; // ���� Sheet read...
					}


					$maxRow		= $objWorksheet->getHighestRow();
					$maxCol        = $objWorksheet->getHighestColumn();
					$SERIALCNT	= 0;
					$existChk	= 0;
					$updateCnt=0;
															
					
					$EXCEL_declare = array( // 1���� �迭�� 1�� ���� 2���� �迭 ����
						array()
					);

					$sql = "select *  from UPLOAD_EXCEL where scode = '".$_SESSION['S_SCODE']."' and code = '".$pt['CODE']."' and gubun = '".$pt['GUBUN']."' and gubunsub = '".$pt['GUBUNSUB']."'";
					$result  = sqlsrv_query( $mscon, $sql );
					$row =  sqlsrv_fetch_array($result); 

					$start_line = (int)$pt['tit_line']+1;

					//2���� �迭�� talble Ư���� ��´�.
					$ex_i=0; 
					$ex_seq =1; 
					for ($i = 1 ; $i <= 204; $i++) {		 
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

					//print_r($EXCEL_declare);
					//exit; 

					//--->���⼭���� ���� 
					$ins_tot_cnt = 0 ;
					$ins_tot_err_cnt = 0 ;

 					

					for ($i = $start_line ; $i <= $maxRow ; $i++) {		//���ǵ� ���� ����� ���� �����ͽ���
	
							unset( $EXCEL_data ); //�迭�ʱ�ȭ 
							for ($ed=1;  $ed <= 68  ; $ed++ ) {    //���߿� �ʵ� ���ڸ�ŭ  �ϱ� 
								   
								   //-->���������ʴ� �ʵ�. 
								   if ($EXCEL_declare[$ed][3] == '4'  || empty($EXCEL_declare[$ed][3])  ) {
										$EXCEL_data[$ed] = "";		
										continue;
								   }
									 
									//������ ���� ������ �࿡�� ����DATA�� ������  DATA������ �������迭�� ��´�.  
									$EXCEL_data[$ed] =	 $objWorksheet->getCell($EXCEL_declare[$ed][2] . $i)->getValue();
									$EXCEL_data[$ed] =   iconv("UTF-8","EUC-KR",$EXCEL_data[$ed]);		

									//print_r($EXCEL_data[$ed].'==' );
									//print_r($EXCEL_declare[$ed][2].'-' );
									
									//sql ������ ���� ��� ���ݹ��� ���� 
									$EXCEL_data[$ed] = SQL_Injection(str_replace("'","",$EXCEL_data[$ed]));
		
									//���� DATA�� TYPE CHECKING( number �϶�) 
									 if (!is_null($EXCEL_data[$ed])) {
										   if ($EXCEL_declare[$ed][3] == '1' ) {
												$EXCEL_data[$ed] = str_replace(",","",$EXCEL_data[$ed]);			
 										   }
									}


									//���� DATA�� TYPE CHECKING( date�϶� ���ڸ� ����) 
									 if (!is_null($EXCEL_data[$ed])) {
											   if ( $EXCEL_declare[$ed][3] == '2' ) {													
													if(isValidDate($EXCEL_data[$ed])==1){
														$EXCEL_data[$ed] = exc_date($EXCEL_data[$ed]);	//--->���� DATE��ȯ 
													}													
											   }
									}
							}
							//---->���ǹ�ȣ��  ���ų� �հ��� �Ǿ�������  �������� �ʴ´�. 
							if (str_replace(" ","",$EXCEL_data[9]) == ""  ||  is_null($EXCEL_data[9]) || str_replace(" ", "", $EXCEL_data[9] ) == '�հ�' ){
								 continue;
							}
 
							$EXCEL_data[6] = $pt['CODE'] ; //-->�����
							$EXCEL_data[18] = $pt[$EXCEL_declare[19][2]] ; //-->1������� 
							$EXCEL_data[20] = $pt[$EXCEL_declare[21][2]];  //-->2������� 
							$EXCEL_data[22] = $pt[$EXCEL_declare[23][2]];  //-->3������� 
							$EXCEL_data[24] = $pt[$EXCEL_declare[25][2]];  //-->4�������
							$EXCEL_data[26] = $pt[$EXCEL_declare[27][2]];  //-->5������� 
							$EXCEL_data[28] = $pt[$EXCEL_declare[29][2]];  //-->6������� 
							$EXCEL_data[30] = $pt[$EXCEL_declare[31][2]];  //-->7������� 
							$EXCEL_data[32] = $pt[$EXCEL_declare[33][2]];  //-->8������� 
							$EXCEL_data[34] = $pt[$EXCEL_declare[35][2]];  //-->9������� 
							$EXCEL_data[36] = $pt[$EXCEL_declare[37][2]];  //-->10������� 
							$EXCEL_data[38] = $pt[$EXCEL_declare[39][2]];  //-->11������� 
							$EXCEL_data[40] = $pt[$EXCEL_declare[41][2]];  //-->12������� 
							$EXCEL_data[42] = $pt[$EXCEL_declare[43][2]];  //-->13������� 

							$EXCEL_data[57] = str_replace(" ", "", $EXCEL_data[57] ) ;
							$EXCEL_data[58] = str_replace(" ", "", $EXCEL_data[58] ) ;
							
                            //--> rpa��Ͻ� ������ �����´�.����(1:�Ϲ� 2:��� 3:�ڵ���) ��Ÿ�� �ڵ尡 ���°����� ���� 
							if($pt['INSILJ'] == '1' || $pt['INSILJ'] == '2' || $pt['INSILJ'] == '3' ) 	{  
								$EXCEL_data[62] = $pt['INSILJ'] ; 
							}else{ //�� �����纰 Ư���� ã�Ƽ� ������ ���� (��ü ������ ã������ �Լ���....) --rpa�� ������ �ȵǴ°�� 
								
								if ($pt['CODE'] == '00018' ) {      //�����ػ� 
								    $item_kbn = substr($EXCEL_data[9],0,1);  //���ǹ�ȣ ��1�ڸ��� �����´�.  
									
									//L:��� M:�ڵ��� F:�Ϲ�  
									if ($item_kbn == 'F') {       
										$EXCEL_data[62] = '1' ;
									}elseif ($item_kbn == 'L') {  
										$EXCEL_data[62] = '2' ;
									}else{     
										$EXCEL_data[62] = '3' ;
									}

									//���� ga���� ������ ���ǹ�ȣ 00000 �� �ٿ���� ��Ī�� �ȴ�
									if (strlen($EXCEL_data[9]) < 17 ) {
										$EXCEL_data[9] = $EXCEL_data[9].'00000' ; 		
									}
								} 

								if ($pt['CODE'] == '00020' ) {      //��ȭ�պ� 
								    $item_kbn = substr($EXCEL_data[10],0,2);  //��ǰ�ڵ� ��2�ڸ��� �����´�.  
									
									//LA:��� CA:�ڵ��� FA :�Ϲ�  
									if ($item_kbn == 'LA') {       
										$EXCEL_data[62] = '2' ;
									}elseif ($item_kbn == 'CA') {  
										$EXCEL_data[62] = '3' ;
									}else{
										$EXCEL_data[62] = '1' ;
									}
								} 
								

								if ($pt['CODE'] == '00021' ) {      //�Ｚȭ�� 
									if (!empty($EXCEL_data[57])) {       //��������� �����ϸ� ������ �ڵ��� 
										$EXCEL_data[62] = '3' ;
									}elseif (!empty($EXCEL_data[58])) {  //���Ǳ����� �����ϸ� ������ ��� (������а� ���� ���� �����Ҽ� ����)
										$EXCEL_data[62] = '2' ;
									}else{
										$EXCEL_data[62] = '1' ;
									}
								} 
								
							}	
							
							//������ü �ʵ�����.
							$e_text = "";
							$e_text_tit = "";
							$e_text_col = "";
							$e_save_text_col  = "";
							for ($ei=0; $ei <=200  ; $ei++ ) {  //--����� �迭 ÷�� ������ 0����.
									$e_text_tit = $pt[$EXCEL_all_col[$ei]];
									$e_text_col =  $objWorksheet->getCell($EXCEL_all_col[$ei] . $i)->getValue();
									$e_text_col  =   iconv("UTF-8","EUC-KR",$e_text_col );	
									$e_text_col = SQL_Injection(str_replace("'","",$e_text_col));		
									

									//--->exc date��ȯ e_save_text_col�� �� Ÿ��Ʋ�̴�.						
									$exc_bit = 'N';
								
									$result = strpos($e_text_tit ,'����');
									if($result === false) {
									}else{
										$exc_bit = 'Y';    
									} 
									$result = strpos($e_text_tit ,'�ñ�');
									if($result === false) {
									}else{
										$exc_bit = 'Y';    
									} 
									
									$result = strpos($e_text_tit ,'��');
									if($result === false) {
									}else{
										$exc_bit = 'Y';    
									} 

									$result = strpos($e_text_tit ,'������');
									if($result === false) {
									}else{
										$exc_bit = 'Y';    
									} 
									$result = strpos($e_text_tit ,'������');
									if($result === false) {
									}else{
										$exc_bit = 'Y';    
									} 
									$result = strpos($e_text_tit ,'������');
									if($result === false) {
									}else{
										$exc_bit = 'Y';    
									} 
									$result = strpos($e_text_tit ,'�Ա���');
									if($result === false) {
									}else{
										$exc_bit = 'Y';    
									} 
									$result = strpos($e_text_tit ,'������');
									if($result === false) {
									}else{
										$exc_bit = 'Y';    
									} 

									if ($exc_bit  == 'Y') {
										if(isValidDate($e_text_col)==1){
											$e_text_col  = exc_date($e_text_col );	//--->���� DATE��ȯ 
										}
									}

									$e_text = $e_text.$e_text_tit.'.***.'.$e_text_col.'.****.';		
									if ( $maxCol  == $EXCEL_all_col[$ei] ) {
										 break;
									}	
								 
							}
							//�����ʵ� null�� zero �迭��ȸ
							$num = [13, 16,17,19, 21, 23, 25, 27, 29, 31, 33, 35,37,39,41,43,44,50];
							foreach ($num as $num) {
								if (empty($EXCEL_data[$num])) {
									$EXCEL_data[$num] =0;
								}										 
							}

							//--->ȯ���ʵ� data�� ������ (-) ���Է��Ѵ�
							if ($EXCEL_data[44] > 0 ) {
								$EXCEL_data[44] =  $EXCEL_data[44]  * -1; 
							}

							//print_r($EXCEL_data);
							//---->�Ѱ���data�Է� ���� start. 
							$ins_ipmst_excel = "						   
													   '$EXCEL_data[1]','$EXCEL_data[2]','$EXCEL_data[3]','$EXCEL_data[4]','$EXCEL_data[5]','$EXCEL_data[6]','$EXCEL_data[7]','$EXCEL_data[8]','$EXCEL_data[9]','$EXCEL_data[10]',
													   '$EXCEL_data[11]','$EXCEL_data[12]','$EXCEL_data[13]','$EXCEL_data[14]','$EXCEL_data[15]','$EXCEL_data[16]','$EXCEL_data[17]','$EXCEL_data[18]','$EXCEL_data[19]','$EXCEL_data[20]',
													   '$EXCEL_data[21]','$EXCEL_data[22]','$EXCEL_data[23]','$EXCEL_data[24]', '$EXCEL_data[25]','$EXCEL_data[26]','$EXCEL_data[27]','$EXCEL_data[28]', '$EXCEL_data[29]','$EXCEL_data[30]' , 
														'$EXCEL_data[31]' , '$EXCEL_data[32]' ,'$EXCEL_data[33]' ,'$EXCEL_data[34]','$EXCEL_data[35]','$EXCEL_data[36]','$EXCEL_data[37]','$EXCEL_data[38]','$EXCEL_data[39]','$EXCEL_data[40]',
														'$EXCEL_data[41]' , '$EXCEL_data[42]' ,'$EXCEL_data[43]' ,'$EXCEL_data[44]','$EXCEL_data[45]','$EXCEL_data[46]','$EXCEL_data[47]','$EXCEL_data[48]','$EXCEL_data[49]','$EXCEL_data[50]'
														,'$EXCEL_data[51]','$EXCEL_data[52]','$EXCEL_data[53]','$EXCEL_data[54]','$EXCEL_data[55]','$EXCEL_data[56]','$EXCEL_data[57]','$EXCEL_data[58]','$EXCEL_data[59]','$EXCEL_data[60]','$EXCEL_data[61]','$EXCEL_data[62]','$EXCEL_data[63]','$EXCEL_data[64]','$EXCEL_data[65]','$EXCEL_data[66]','$EXCEL_data[67]','$EXCEL_data[68]'
													  ";		

							//print_r($EXCEL_data[63]); 
												  
							$sql = "
									insert into ins_ipmst( ".$ins_ipmst." )
									values('".$_SESSION['S_SCODE']."','".$upldate."','".$pt['GUBUN']."','".$pt['GUBUNSUB']."','".$LS_UPLNUM. "','".$i."','".$yymm."',".$ins_ipmst_excel ."
									,".'getdate()'." ,'".$_SESSION['S_SKEY']."' , getdate(), '".$_SESSION['S_SKEY']."' ,'".$e_text ."' )
									";

								
							sqlsrv_query($mscon,"BEGIN TRAN");
							$result =  sqlsrv_query( $mscon, $sql );
							if ($result == false){
								sqlsrv_query($mscon,"ROLLBACK");
								$err = $err.' err Line->'. $i;
								$ins_tot_err_cnt =  $ins_tot_err_cnt + 1 ;
								print_r($sql);
								exit; 
							}else{
								$ins_tot_cnt = $ins_tot_cnt + 1; 
								sqlsrv_query($mscon,"COMMIT");
							}
					} //-->�ϳ��� shhet���� 
					
					//--->�ϳ��� sheet�� ����Ǹ� ���ε������丮 �Է��ϱ�.		
			
					if ( $ins_tot_err_cnt == 0 ) {
						$bigo ='����';
					}else{
						$bigo =$err;
					}

					$bigo  =	"St:[".$sheetname ."] ".$bigo ;
					$bigo = substr($bigo, 0, 6450); 

					$exe_file_name = $file_ori.'[Rpa : '.$pt['CODE'].'-'.$pt['GUBUN'].'-'.$pt['GUBUNSUB'].']';  
			
					$sql = "
					insert into UPLOAD_HISTORY( 	SCODE, UPLDATE  , GUBUN  , GUBUNSUB, UPLNUM      , FILENAME  , CODE     , CNT               , FCNT       , BIGO  ,YYMM,     IDATE       , ISWON  )
					values('".$_SESSION['S_SCODE']."','$upldate', '$pt[GUBUN]','$pt[GUBUNSUB]', $LS_UPLNUM,'$exe_file_name'   ,'$pt[CODE]' ,'$ins_tot_cnt' ,'$ins_tot_err_cnt','$bigo', '$yymm' ,getdate(),'".$_SESSION['S_SKEY']."')";
					
					//print_r($sql);
					
					sqlsrv_query($mscon,"BEGIN TRAN");
					$result =  sqlsrv_query( $mscon, $sql );
					if ($result == false){
						sqlsrv_query($mscon,"ROLLBACK");
					}else{
						sqlsrv_query($mscon,"COMMIT");
					}				


					// ��������ε带 ������ �Ŀ� (�������Ϻ���) ���ǹ�ȣ�� ���� KWN���̺� �������� ������ �μ�Ʈ
					$sql = "
							select count(*) cnt
							from ins_ipmst a 
							where a.scode = '".$_SESSION['S_SCODE']."' and a.ipdate = '".$upldate."' and a.gubun = '".$pt['GUBUN']."' and a.gubunsub = '".$pt['GUBUNSUB']."'
									and not exists(select * from kwn where scode = '".$_SESSION['S_SCODE']."' and a.scode = kwn.scode and a.kcode = kwn.kcode)					
							";
					$result  = sqlsrv_query( $mscon, $sql );
					$row =  sqlsrv_fetch_array($result); 
					$kwn_cnt = $row["cnt"];	

					if($kwn_cnt > 0){
						$sql = "
								insert into kwn (scode,kcode,kname,kstbit,kdate,fdate,tdate,inscode,insilj,item,itemnm,ksman,kdman,mamt,
												gskey,kskey,bonbu,jisa,jijum,team,idate,iswon)
								select a.scode,a.kcode,a.kname,a.istbit,a.ddate,a.adate,a.bdate,a.inscode,a.insilj,a.item,a.itemnm,a.mksman,a.ksman,a.mamt,
										c.skey,b.skey,d.bonbu,d.jisa,d.jijum,d.team,getdate(),'TESTTTT'
								from ins_ipmst a left outer join inswon b on a.scode = b.scode and a.ksman = b.BSCODE
												left outer join inswon c on a.scode = c.scode and a.mksman = c.bscode
												left outer join swon d on a.scode = d.scode and c.skey = d.skey
								where a.scode = '".$_SESSION['S_SCODE']."' and a.ipdate = '".$upldate."' and a.gubun = '".$pt['GUBUN']."' and a.gubunsub = '".$pt['GUBUNSUB']."'
										and not exists(select * from kwn where scode = '".$_SESSION['S_SCODE']."' and a.scode = kwn.scode and a.kcode = kwn.kcode)						
								";			
								
						sqlsrv_query($mscon,"BEGIN TRAN");
						$result =  sqlsrv_query( $mscon, $sql );
						if ($result == false){
							sqlsrv_query($mscon,"ROLLBACK");
						}else{
							sqlsrv_query($mscon,"COMMIT");
						}		
					}


			}//-->��üsheet for end.
			unlink($del_file); //�۾����ϻ��� 
		} //file_no end 
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);

		$message ='������  �����ῢ������ ���ε� �Ϸ�ó�� !';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "");
		echo json_encode($returnJson);
		exit;	

} catch (Exception $e) {
		$message ='������  ������ �������� ���ε� ó���� error�� �߻��Ͽ����ϴ�'. $e;
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "");
		echo json_encode($returnJson);
		exit;		
} //--try end 

} // upload if ������ end 



//===============================================================================================================//
//   file delete������  rtn 
//===============================================================================================================//


if ($_POST['type'] == 'del' ) { 
try {
		if($_SESSION['S_SCODE'] == null ||  $del_upldate == null || $del_uplnum == null ){
			sqlsrv_free_stmt($result);
			sqlsrv_close($mscon);
			$message = '������ �� ���� data�Դϴ� ';
			$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
			echo json_encode($returnJson);
			exit;	
		}
		if (empty($del_upldate) ||empty($del_gubun) ||empty($del_gubunsub) ||empty($del_uplnum)  ) {
			sqlsrv_free_stmt($result);
			sqlsrv_close($mscon);
			$message = '������ �� ���� data�Դϴ� ';
			$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
			echo json_encode($returnJson);
			exit;	
		}

		//���ε� ����ð��� 3�ð� �̻��̸� �����Ұ�  
		$sql = "SELECT DATEDIFF(HOUR,   IDATE, getdate()) AS time_cha	  FROM UPLOAD_HISTORY  where scode = '".$_SESSION['S_SCODE']."' and upldate = '".$del_upldate."' and gubun = '".$del_gubun."' and gubunsub = '".$del_gubunsub."' and uplnum = '".$del_uplnum."'";

		$result  = sqlsrv_query( $mscon, $sql );
		$row =  sqlsrv_fetch_array($result); 
		$time_cha = $row["time_cha"];	
		if ( $time_cha >  '23'  ) {
			sqlsrv_free_stmt($result);
			sqlsrv_close($mscon);
			$message = '�ش������丮�� ���ε� �� 3�ð��̻� ����Ǿ� �����Ұ��մϴ�. ';
			$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
			echo json_encode($returnJson);
			exit;	
		} 


		// ������ ���� 
		$sql = "delete from INS_IPMST where scode = '".$_SESSION['S_SCODE']."' and  IPDATE = '".$del_upldate."' and gubun = '".$del_gubun."' and gubunsub = '".$del_gubunsub."' and INO = '".$del_uplnum." '";
		sqlsrv_query($mscon,"BEGIN TRAN");
		$result =  sqlsrv_query( $mscon, $sql );

		if ($result == false){
			sqlsrv_query($mscon,"ROLLBACK");
			sqlsrv_free_stmt($result);
			sqlsrv_close($mscon);
			$message = '������ data ������ ERROR�� �߻��Ͽ����ϴ�! ';
			$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
			echo json_encode($returnJson);
			exit;	
		}
		sqlsrv_query($mscon,"COMMIT");

		// ���ε������丮 ����  
		$sql = "delete from UPLOAD_HISTORY where scode = '".$_SESSION['S_SCODE']."' and upldate = '".$del_upldate."' and gubun = '".$del_gubun."' and gubunsub = '".$del_gubunsub."' and uplnum = '".$del_uplnum." '";
		sqlsrv_query($mscon,"BEGIN TRAN");
		$result =  sqlsrv_query( $mscon, $sql );
		
		if ($result == false){
			sqlsrv_query($mscon,"ROLLBACK");
			sqlsrv_free_stmt($result);
			sqlsrv_close($mscon);
			$message = '���ε� �����丮 ������ ERROR�� �߻��Ͽ����ϴ�! ';
			$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
			echo json_encode($returnJson);
			exit;	
		}
		sqlsrv_query($mscon,"COMMIT");
		
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = '�����Ͻ� ������data�� �����Ϸ��Ͽ����ϴ�.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
} catch (Exception $e) {
		$message ='������  ������ �������� ���� ó���� error�� �߻��Ͽ����ϴ�'. $e;
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "");
		echo json_encode($returnJson);
		exit;		
} //try end...

} //if ($_POST['type'] == 'del' )  end  



?> 

