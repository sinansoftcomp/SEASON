<?
//Ư���̹����� ����ȵǴ� ������ ��Ÿ��..!! 2017-11-29  
@ini_set('gd.jpeg_ignore_warning', 1);

include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/class/excel_upload.php");

include($_SERVER['DOCUMENT_ROOT']."/bin/sub/menu3/ga_menu3_70_fun.php");

require_once $_SERVER['DOCUMENT_ROOT']."/bin/include/lib/PHPExcel/Classes/PHPExcel.php"; 

$upldate		=	str_replace('-','',$_POST['upldate']);
$gubun		=	$_POST['gubun'];
$filename	=	$_POST['filename'];
$code		=	$_POST['code'];
$bigo		=	$_POST['bigo'];
$uploadFile =	$_POST['filename'];
$imgcnt		=	$_POST['imgcnt'];		// ���ε� ���� �� 

$del_upldate		=	str_replace('-','',$_POST['upldate']);
$del_uplnum		=	$_POST['uplnum'];

if($_SESSION['S_SCODE'] == null ){
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);
		$message = '�ʼ��Է°� ����, �� �α������ּ���.';
		echo "<script>alert('$message');history.go('-1');</script>";
		exit;			
}

//echo "Count: ".count($_FILES['attach_file']['name']);
//echo '<br>';

// ���� ����ŭ for��
for( $i=0 ; $i < $imgcnt ; $i++ ) {
	//echo iconv("UTF-8","EUCKR",$_FILES['attach_file']['name']);
	//echo iconv("UTF-8","EUCKR",$_FILES['attach_file']['name'][$i]);
	//echo '<br>';
}



$message = 'Test!!';
$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "");
echo json_encode($returnJson);

exit;

//===============================================================================================================//
//   file upload rtn 
//===============================================================================================================//
if ($_POST['type'] == 'in' ) {
		//--------------------���� �ٿ�ε�
		$file_ori = $_FILES['uploadFile']['name']; 
		$tmp_name = $_FILES['uploadFile']['tmp_name'];   
		$filename = $_FILES['uploadFile']['tmp_name'];
		$ext_temp	= explode(".",$file_ori);
		$ext		= $ext_temp[count($ext_temp)-1];
		$file_name	= $_SESSION['S_SCODE']."_A.".$ext;

		$file_path  = '/gaplus/temp/';
		$ori_filename	= $_SERVER['DOCUMENT_ROOT']."/temp/".$file_name;

		$kwn_item =		"SCODE  
								   , KCODE          , KNAME      , SBIT 	         , SJUNO 	      , SNUM          , COMNM       , CUPNM         , EMAILSEL 	     , EMAIL       , TELBIT 	    
								   , TEL             , HTELBIT 	   ,HTEL             , ADDBIT        , POST 	     , ADDR            , ADDR_DT 	  , BIGO                , KSTBIT         , KDATE 
								   , FDATE      , TDATE           , INSCODE    , INSILJ   		   , ITEM   			,ITEMNM     , KSMAN         , KDMAN           , MAMT		  , HAMT 
								   , SAMT  	      , SRATE           , INSTERM    , INSTBIT    	  , FBIT               , NBIT  		   , NTERM         , KDAY 	     	, KSBIT             , KSGUBUN 
								   , BANK           , SYJUNO       , SYJ      		  , CARD 	         	 , CARDNUM   , CYJ   	     , VCBANK 	     , VCNO  			   , PBDATE      , AGENCY 
								   , RCODE    	  , PAYBIT        , BIGO2 		   , REL          	  , PNAME 		  , PSBIT            , PSJUNO 	  , PSNUM 	   , PCOMNM 	  , PCUPNM 	
								   , PEMAILSEL   , PEMAIL      , PTELBIT       , PTEL 			  , PHTELBIT    , PHTEL 	       , PADDBIT 	   , PPOST 		  , PADDR 	  , PADDR_DT 
								   , PBIGO 		  , CARNUM 	   , CARVIN 	  , CARJONG 	  , CARYY  	  , CARCODE      , CARKIND     , CARGAMT      ,   CARTAMT   , CARSUB1 
								   , CARSAMT1   , CARSUB2    , CARSAMT2   , CARSUB3 , CARSAMT3  ,CARSUB4  , CARSAMT4  , CARSUB5 	  , CARSAMT5  , CAROBJ 
								   , CARTY 	          , CARPAY1   , CARPAY2    , CARBAE  , CARBODY1 	  , CARBODY2   , CARBODY3   , CARLOSS   , CARACAMT   , CARINS  
								   , CAREMG  
								  , GSKEY 		  , KSKEY	    , BONBU 	  , JISA 		  , TEAM 	 ,BONBUID   ,JISAID   ,TEAMID    ,SJIK	  , SUGI 	 , IDATE     	  , ISWON   , UDATE 		  , USWON 		  , UPLDATE 		  , UPLNUM   , UPLSEQ ";			 //--->��ü�Է� �׸�  

		//������ file�� �����ϸ� ����� 
		$del_file = 'D:\\www\gaplus\temp\\'. $file_name;
		 unlink($del_file);

		//����ã�⿡�� ������ ������ 9�������� ����
		$rtn =  file_upload($file_path, $file_name, 700,$file_ori, $tmp_name, '2') ;
		//----------------------------

		//�����ε��϶�
		if($gubun == 'A'){
			// �ʼ����� Ȯ��
			if($_SESSION['S_SCODE'] == null || $code == null ){
				sqlsrv_free_stmt($result);
				sqlsrv_close($mscon);
				$message = '�ʼ��Է°� ����, �� �α������ּ���.';
				echo "<script>alert('$message');history.go('-1');</script>";
				exit;	
			}
		}

		$EXCEL_declare = array( // 1���� �迭�� 1�� ���� 2���� �迭 ����
			array()
		);

		$sql = "select *  from UPLOAD_EXCEL where scode = '".$_SESSION['S_SCODE']."' and code = '".$code."' and gubun = 'A' ";
		//echo $sql;
		$result  = sqlsrv_query( $mscon, $sql );
		$row =  sqlsrv_fetch_array($result); 
		$start_line = $row['STLINE'];


		//2���� �迭�� talble Ư���� ��´�.
		$ex_i=0; 
		$ex_seq =1; 
		for ($i = 1 ; $i <= 303 ; $i++) {		//2������ �����ͽ���
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


		 
		//2���� �迭�� ���������� ��Ҵ��� ���÷��� �غ���.  
		 
		 /*
		for ($i = 1 ; $i <= 101 ; $i++) {
			for ($y = 1 ; $y <= 3 ; $y++) {
				 echo $EXCEL_declare[$i][$y]."<br>";
			}
		}
		 */ 

		 //insert �����ȣ���ϱ�  
		$sql = "select max(UPLNUM) UPLNUM   from upload_history where scode = '".$_SESSION['S_SCODE']."' and upldate = '".$upldate."' and gubun = 'A' ";
		$result  = sqlsrv_query( $mscon, $sql );
		$row =  sqlsrv_fetch_array($result); 
		$cnt = $row["UPLNUM"];	
		 
		if (is_null($row["UPLNUM"]) ) {
			$LS_UPLNUM	 = '1';
		}else{
			$LS_UPLNUM =     $row["UPLNUM"]  + 1;
		}

		$EXCEL_data= array();
		//============================ 9�������� ����� excel �о kwn���̺� insert�ϴ� �κ�===================================
			$objPHPExcel = new PHPExcel();

			require_once $_SERVER['DOCUMENT_ROOT']."/bin/include/lib/PHPExcel/Classes/PHPExcel/IOFactory.php";  // IOFactory.php�� �ҷ��;� �ϸ�, ��δ� ������� ������ �°� �����ؾ� �Ѵ�.

			$filename = 'D:\\www\gaplus\temp\\'.$file_name; // �о���� ���� ������ ��ο� ���ϸ��� �����Ѵ�.

			try {

				  // ���ε� �� ���� ���Ŀ� �´� Reader��ü�� �����.
					$objReader = PHPExcel_IOFactory::createReaderForFile($filename);

					// �б��������� ����
					$objReader->setReadDataOnly(true);
					// ���������� �д´�
					$objExcel = $objReader->load($filename);

					// ù��° ��Ʈ�� ����
					$objExcel->setActiveSheetIndex(0);
					$objWorksheet = $objExcel->getActiveSheet();
					$rowIterator = $objWorksheet->getRowIterator();

					foreach ($rowIterator as $row) { // ��� �࿡ ���ؼ�
					   $cellIterator = $row->getCellIterator();
					   $cellIterator->setIterateOnlyExistingCells(false); 
					}
					$maxRow		= $objWorksheet->getHighestRow();
					$SERIALCNT	= 0;
					$existChk	= 0;
					$updateCnt=0;

					$ins_tot_cnt = 0 ; 
					$exc_bit = 'Y'; 
					for ($i = $start_line ; $i <= $maxRow ; $i++) {		//���ǵ� ���� ����� ���� �����ͽ���

							//����Ÿ��Ʋ�� TABLE Ÿ��Ʋ ���Ͽ� ����ġ �Ҷ� ó���Ұ� 
							if ($i == $start_line) {
									unset( $EXCEL_data ); //�迭�ʱ�ȭ 
									for ($ed=1;  $ed <= 101  ; $ed++ ) {    //���߿� �ʵ� ���ڸ�ŭ  �ϱ� 
											//������ ���� ������ �࿡�� ����DATA�� ������  DATA������ �������迭�� ��´�.  
											$ti = $i - 1;
											
											//---�̼���data�̸� 
											if ($EXCEL_declare[$ed][3] == '4' || is_null($EXCEL_declare[$ed][3])  ) {
												continue;
											}
											/*
											echo $EXCEL_data[$ed]."<br>";
											echo $EXCEL_declare[$ed][1]."<br>";
											echo $EXCEL_declare[$ed][2]."<br>";
											*/ 
															
											$EXCEL_data[$ed] =	 $objWorksheet->getCell($EXCEL_declare[$ed][2] . $ti)->getValue();
											$EXCEL_data[$ed] =   iconv("UTF-8","EUC-KR",$EXCEL_data[$ed]);
											if (preg_replace('/\s+/', '', $EXCEL_declare[$ed][1]) != preg_replace('/\s+/', '',$EXCEL_data[$ed])) {
												 sqlsrv_free_stmt($result);
												 sqlsrv_close($mscon);
												 echo $EXCEL_declare[$ed][1]."<br>";
												 echo $EXCEL_data[$ed]."<br>";
												 exit;
												$message = ' ��࿢�����ε� Ÿ��Ʋ��  table���� ���ǵ� Ÿ��Ʋ���� ��ġ���� �ʽ��ϴ� '. $ed.' ���Դϴ� ';
												echo "<script>alert('$message'); location.href='./ga_menu3_70.php' ;  </script>";
												exit; 
											}
									}					
							}
			
							unset( $EXCEL_data ); //�迭�ʱ�ȭ 
							for ($ed=1;  $ed <= 101  ; $ed++ ) {    //���߿� �ʵ� ���ڸ�ŭ  �ϱ� 
								   
								   //-->���������ʴ� �ʵ�. 
								   if ($EXCEL_declare[$ed][3] == '4'  ) {
										$EXCEL_data[$ed] = "";		
										continue;
								   }

									//������ ���� ������ �࿡�� ����DATA�� ������  DATA������ �������迭�� ��´�.  
									$EXCEL_data[$ed] =	 $objWorksheet->getCell($EXCEL_declare[$ed][2] . $i)->getValue();
									$EXCEL_data[$ed] =   iconv("UTF-8","EUC-KR",$EXCEL_data[$ed]);
									
									echo $EXCEL_declare[$ed][2]."<br>";


									//�����纰 ������ Ư�̻��� �ʵ尡 ������ �Լ����� �����Ѵ� 	
									$EXCEL_data[$ed] = code_special($code,$EXCEL_declare[$ed][2],$EXCEL_data[$ed]);						 					
									//���� DATA�� TYPE CHECKING( number , date�϶� ���ڸ� ����) 
								   if (!is_null($EXCEL_data[$ed])) {
										   if ($EXCEL_declare[$ed][3] == '1' || $EXCEL_declare[$ed][3] == '2' ) {
												$EXCEL_data[$ed] = preg_replace('/[^0-9]*/s', '',$EXCEL_data[$ed]);								
										   }
									}
							}

							// -->�ʼ� ���̳� �������� �ʴ� ���� ���� seting�ϱ� 
							$EXCEL_data[23] =$code; //����� ���� seting			 

							//---> ��ǰ������ �ڵ� �������� �ʿ� �ʵ� ü���(1���� �迭��ü�� �ְ�ޱ�)			 
							if ($EXCEL_data[25] == "" ||  is_null($EXCEL_data[25] )) {
								//--�����翡�� ��ǰ�ڵ尡 �ȿ���.
								$EXCEL_data = item_seting_nocode($mscon,$code,$EXCEL_data);	
							}else{
								//--�����翡�� ��ǰ�ڵ尡  ����.
								$EXCEL_data = item_seting_yescode($mscon,$code,$EXCEL_data);	
							}
							
							if ($EXCEL_data[29] == "" || is_null($EXCEL_data[29])) {
								$EXCEL_data[29] = 0;
							}
							if ($EXCEL_data[30] == "" || is_null($EXCEL_data[30])) {
								$EXCEL_data[30] = 0;
							}
							if ($EXCEL_data[31] == "" || is_null($EXCEL_data[31])) {
								$EXCEL_data[31] = 0;
							}
							if ($EXCEL_data[32] == "" || is_null($EXCEL_data[32])) {
								$EXCEL_data[32] = 0;
							}
							if ($EXCEL_data[33] == "" || is_null($EXCEL_data[33])) {
								$EXCEL_data[33] = 0;
							}
							if ($EXCEL_data[37] == "" || is_null($EXCEL_data[37])) {
								$EXCEL_data[37] = 0;
							}
							if ($EXCEL_data[78] == "" || is_null($EXCEL_data[78])) {
								$EXCEL_data[78] = 0;
							}
							if ($EXCEL_data[79] == "" || is_null($EXCEL_data[79])) {
								$EXCEL_data[79] = 0;
							}
							if ($EXCEL_data[81] == "" || is_null($EXCEL_data[81])) {
								$EXCEL_data[81] = 0;
							}
							if ($EXCEL_data[83] == "" || is_null($EXCEL_data[83])) {
								$EXCEL_data[83] = 0;
							}
							if ($EXCEL_data[85] == "" || is_null($EXCEL_data[85])) {
								$EXCEL_data[85] = 0;
							}
							if ($EXCEL_data[87] == "" || is_null($EXCEL_data[87])) {
								$EXCEL_data[87] = 0;
							}
							if ($EXCEL_data[89] == "" || is_null($EXCEL_data[89])) {
								$EXCEL_data[89] = 0;
							}					

							$kwn_excel = "						   
													   '$EXCEL_data[1]','$EXCEL_data[2]','$EXCEL_data[3]',dbo.ENCRYPTKEY('$EXCEL_data[4]'),'$EXCEL_data[5]','$EXCEL_data[6]','$EXCEL_data[7]','$EXCEL_data[8]','$EXCEL_data[9]','$EXCEL_data[10]',
													   '$EXCEL_data[11]','$EXCEL_data[12]','$EXCEL_data[13]','$EXCEL_data[14]','$EXCEL_data[15]','$EXCEL_data[16]','$EXCEL_data[17]','$EXCEL_data[18]','$EXCEL_data[19]','$EXCEL_data[20]',
													   '$EXCEL_data[21]','$EXCEL_data[22]','$EXCEL_data[23]','$EXCEL_data[24]', '$EXCEL_data[25]','$EXCEL_data[26]','$EXCEL_data[27]','$EXCEL_data[28]', $EXCEL_data[29],$EXCEL_data[30] , 
														$EXCEL_data[31] , $EXCEL_data[32] ,$EXCEL_data[33] ,'$EXCEL_data[34]','$EXCEL_data[35]','$EXCEL_data[36]',$EXCEL_data[37],'$EXCEL_data[38]','$EXCEL_data[39]','$EXCEL_data[40]', 
														'$EXCEL_data[41]',dbo.ENCRYPTKEY('$EXCEL_data[42]'),'$EXCEL_data[43]','$EXCEL_data[44]',dbo.ENCRYPTKEY('$EXCEL_data[45]'),'$EXCEL_data[46]','$EXCEL_data[47]','$EXCEL_data[48]' , '$EXCEL_data[49]','$EXCEL_data[50]',
													   '$EXCEL_data[51]','$EXCEL_data[52]','$EXCEL_data[53]','$EXCEL_data[54]','$EXCEL_data[55]','$EXCEL_data[56]',dbo.ENCRYPTKEY('$EXCEL_data[57]'),'$EXCEL_data[58]','$EXCEL_data[59]','$EXCEL_data[60]',  
													   '$EXCEL_data[61]','$EXCEL_data[62]','$EXCEL_data[63]','$EXCEL_data[64]','$EXCEL_data[65]','$EXCEL_data[66]','$EXCEL_data[67]','$EXCEL_data[68]','$EXCEL_data[69]','$EXCEL_data[70]',
													   '$EXCEL_data[71]','$EXCEL_data[72]' , '$EXCEL_data[73]','$EXCEL_data[74]','$EXCEL_data[75]','$EXCEL_data[76]','$EXCEL_data[77]',$EXCEL_data[78] ,$EXCEL_data[79] , '$EXCEL_data[80]',
														$EXCEL_data[81],'$EXCEL_data[82]',  $EXCEL_data[83] ,'$EXCEL_data[84]'  ,$EXCEL_data[85] ,'$EXCEL_data[86]', $EXCEL_data[87] ,'$EXCEL_data[88]',$EXCEL_data[89] ,'$EXCEL_data[90]',
													   '$EXCEL_data[91]','$EXCEL_data[92]','$EXCEL_data[93]','$EXCEL_data[94]','$EXCEL_data[95]','$EXCEL_data[96]' , '$EXCEL_data[97]','$EXCEL_data[98]','$EXCEL_data[99]','$EXCEL_data[100]',
													   '$EXCEL_data[101]' 
													  ";

							//--->Ű(����ȣ)�� �ִٸ� insert start 
							if (!empty($EXCEL_data[1])  ) {
									//---> ������ ����ڵ忡�� ��ü����ڵ� �� ������ ��������. 		
									$sql  = "
											select  a.bscode,
													a.skey,
													b.bonbu,
													b.jisa,
													b.team,
													c.skey bonbuid,
													d.skey jisaid,
													e.skey teamid,
													b.jik
											from inswon a
												left outer join swon b on a.scode = b.scode and a.skey = b.skey
												left outer join swon c on a.scode = c.scode and b.bonbu = c.bonbu and c.jik = '5001'
												left outer join swon d on a.scode = d.scode and b.jisa = d.jisa and c.jik = '4001'
												left outer join swon e on a.scode = e.scode and b.team = e.team and c.jik = '2001'
											where a.scode = '".$_SESSION['S_SCODE']."'
											  and a.inscode = '".$code."'
											  and a.bscode = '".$EXCEL_data[27]."'	";
									$result  = sqlsrv_query( $mscon, $sql );
									$row =  sqlsrv_fetch_array($result); 

									$LS_GSKEY	=	$row['skey'];
									$LS_BONBU	=	$row['bonbu'];
									$LS_JISA	=	$row['jisa'];
									$LS_TEAM	=	$row['team'];

									$bonbuid=	$row['bonbuid'];
									$jisaid	=	$row['jisaid'];
									$teamid	=	$row['teamid'];
									$sjik	=	$row['jik'];

									//echo $sql;
									//exit;

									$LS_SUGI  = '2'; //������� 

									$sql = "
											insert into kwn( ".$kwn_item." )
											values('".$_SESSION['S_SCODE']."', ".$kwn_excel.", '".$LS_GSKEY."', '".$LS_GSKEY."','".$LS_BONBU."'  ,'".$LS_JISA."' ,'".$LS_TEAM."' 
											,'".$bonbuid."' ,'".$jisaid."' ,'".$teamid."' ,'".$sjik."' ,'".$LS_SUGI."' ,getdate(),'".$_SESSION['S_SKEY']."' ,getdate(),'".$_SESSION['S_SKEY']."','".$upldate."',".$LS_UPLNUM." ,'".$i ."' )
											";
									sqlsrv_query("BEGIN TRAN");
									$result =  sqlsrv_query( $mscon, $sql );
									if ($result == false){
										echo $sql."<br>";
										exit;
										sqlsrv_query("ROLLBACK");
										$exc_bit = 'N' ;
										 break;  // for���� Ż���ϰ� ����data�� �Է���. 
									}else{
											$ins_tot_cnt = $ins_tot_cnt + 1; 
											sqlsrv_query("COMMIT");
									}
						}  //--->record ���ٿ�����( for end) 
					} //-->������ü�� ����
					
						//--���ε� �����丮�Է� 
						if ( $ins_tot_cnt == 0 ) {
							$bigo ='�Է� �Ǽ��� �����ϴ�.';
						}
						$sql = "
						insert into UPLOAD_HISTORY( 	SCODE, UPLDATE , UPLNUM , GUBUN       , FILENAME  , CODE     , CNT               , AMT       , BIGO       , IDATE       , ISWON  )
							values('".$_SESSION['S_SCODE']."','$upldate',$LS_UPLNUM,'A','$file_ori'   ,'$code' ,'$ins_tot_cnt' ,0,'$bigo' ,getdate(),'".$_SESSION['S_SKEY']."')";
						
							sqlsrv_query("BEGIN TRAN");
							$result =  sqlsrv_query( $mscon, $sql );
							if ($result == false){
								sqlsrv_query("ROLLBACK");
							}else{
								sqlsrv_query("COMMIT");
							}

						 sqlsrv_free_stmt($result);
						 sqlsrv_close($mscon);

							if ($exc_bit == 'Y') {
									$message = ' ������� �������ε� ' . $ins_tot_cnt . ' �� �Է��� �Ϸ��Ͽ����ϴ�.';
									echo "<script>alert('$message'); location.href='./ga_menu3_70.php' ;  </script>";
							}else{
									$message = ' ��࿢�����ε� �� �����߻�  ������������  ���� '. $i.' ���Դϴ� ';
									echo "<script>alert('$message'); location.href='./ga_menu3_70.php' ;  </script>";
							}
				}  //---> try end 
			 catch (exception $e) {
				sqlsrv_free_stmt($result);
				sqlsrv_close($mscon);
				echo $e;
				exit;
				$message = '��࿢������ ���ε��� SYSTEM ERROR�� �߻��Ͽ����ϴ�!';
				echo "<script>alert('$message'); location.href='./ga_menu3_70.php' ;  </script>";
			}
} // upload if ������ end 


//===============================================================================================================//
//   file delete������  rtn 
//===============================================================================================================//

if ($_POST['type'] == 'del' ) {
 
		if($_SESSION['S_SCODE'] == null ||  $del_upldate == null || $del_uplnum == null ){
			sqlsrv_free_stmt($result);
			sqlsrv_close($mscon);
			$message = '������ �� ���� data�Դϴ� ';
			echo "<script>alert('$message'); location.href='./ga_menu3_70.php' ;  </script>";
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
			echo "<script>alert('$message'); location.href='./ga_menu3_70.php' ;  </script>";
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
			echo "<script>alert('$message'); location.href='./ga_menu3_70.php' ;  </script>";
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
			echo "<script>alert('$message'); location.href='./ga_menu3_70.php' ;  </script>";
			exit;
		}
		sqlsrv_query("COMMIT");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);

		$message = '�����Ϸ�';
		echo "<script>alert('$message'); location.href='./ga_menu3_70.php' ;  </script>";
		exit;
}

?> 

