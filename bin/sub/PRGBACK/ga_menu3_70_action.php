<?
//특정이미지가 저장안되는 현상이 나타남..!! 2017-11-29  
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
$imgcnt		=	$_POST['imgcnt'];		// 업로드 파일 수 

$del_upldate		=	str_replace('-','',$_POST['upldate']);
$del_uplnum		=	$_POST['uplnum'];

if($_SESSION['S_SCODE'] == null ){
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);
		$message = '필수입력값 오류, 재 로그인해주세요.';
		echo "<script>alert('$message');history.go('-1');</script>";
		exit;			
}

//echo "Count: ".count($_FILES['attach_file']['name']);
//echo '<br>';

// 파일 수만큼 for문
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
		//--------------------엑셀 다운로드
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
								  , GSKEY 		  , KSKEY	    , BONBU 	  , JISA 		  , TEAM 	 ,BONBUID   ,JISAID   ,TEAMID    ,SJIK	  , SUGI 	 , IDATE     	  , ISWON   , UDATE 		  , USWON 		  , UPLDATE 		  , UPLNUM   , UPLSEQ ";			 //--->자체입력 항목  

		//기존에 file이 존재하면 지운다 
		$del_file = 'D:\\www\gaplus\temp\\'. $file_name;
		 unlink($del_file);

		//파일찾기에서 선택한 엑셀을 9번서버에 저장
		$rtn =  file_upload($file_path, $file_name, 700,$file_ori, $tmp_name, '2') ;
		//----------------------------

		//계약업로드일때
		if($gubun == 'A'){
			// 필수정보 확인
			if($_SESSION['S_SCODE'] == null || $code == null ){
				sqlsrv_free_stmt($result);
				sqlsrv_close($mscon);
				$message = '필수입력값 오류, 재 로그인해주세요.';
				echo "<script>alert('$message');history.go('-1');</script>";
				exit;	
			}
		}

		$EXCEL_declare = array( // 1차원 배열을 1개 갖는 2차원 배열 선언
			array()
		);

		$sql = "select *  from UPLOAD_EXCEL where scode = '".$_SESSION['S_SCODE']."' and code = '".$code."' and gubun = 'A' ";
		//echo $sql;
		$result  = sqlsrv_query( $mscon, $sql );
		$row =  sqlsrv_fetch_array($result); 
		$start_line = $row['STLINE'];


		//2차원 배열에 talble 특성을 담는다.
		$ex_i=0; 
		$ex_seq =1; 
		for ($i = 1 ; $i <= 303 ; $i++) {		//2열부터 데이터시작
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


		 
		//2차원 배열에 정상적으로 담았는지 디스플레이 해본다.  
		 
		 /*
		for ($i = 1 ; $i <= 101 ; $i++) {
			for ($y = 1 ; $y <= 3 ; $y++) {
				 echo $EXCEL_declare[$i][$y]."<br>";
			}
		}
		 */ 

		 //insert 구룹번호구하기  
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
		//============================ 9번서버에 저장된 excel 읽어서 kwn테이블에 insert하는 부분===================================
			$objPHPExcel = new PHPExcel();

			require_once $_SERVER['DOCUMENT_ROOT']."/bin/include/lib/PHPExcel/Classes/PHPExcel/IOFactory.php";  // IOFactory.php을 불러와야 하며, 경로는 사용자의 설정에 맞게 수정해야 한다.

			$filename = 'D:\\www\gaplus\temp\\'.$file_name; // 읽어들일 엑셀 파일의 경로와 파일명을 지정한다.

			try {

				  // 업로드 된 엑셀 형식에 맞는 Reader객체를 만든다.
					$objReader = PHPExcel_IOFactory::createReaderForFile($filename);

					// 읽기전용으로 설정
					$objReader->setReadDataOnly(true);
					// 엑셀파일을 읽는다
					$objExcel = $objReader->load($filename);

					// 첫번째 시트를 선택
					$objExcel->setActiveSheetIndex(0);
					$objWorksheet = $objExcel->getActiveSheet();
					$rowIterator = $objWorksheet->getRowIterator();

					foreach ($rowIterator as $row) { // 모든 행에 대해서
					   $cellIterator = $row->getCellIterator();
					   $cellIterator->setIterateOnlyExistingCells(false); 
					}
					$maxRow		= $objWorksheet->getHighestRow();
					$SERIALCNT	= 0;
					$existChk	= 0;
					$updateCnt=0;

					$ins_tot_cnt = 0 ; 
					$exc_bit = 'Y'; 
					for ($i = $start_line ; $i <= $maxRow ; $i++) {		//정의된 시작 행부터 부터 데이터시작

							//엑셀타이틀과 TABLE 타이틀 비교하여 불일치 할때 처리불가 
							if ($i == $start_line) {
									unset( $EXCEL_data ); //배열초기화 
									for ($ed=1;  $ed <= 101  ; $ed++ ) {    //나중에 필드 숫자만큼  하기 
											//지정된 열과 현재의 행에서 엑셀DATA를 가져와  DATA순서로 일차원배열에 담는다.  
											$ti = $i - 1;
											
											//---미수집data이면 
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
												$message = ' 계약엑셀업로드 타이틀과  table에사 정의된 타이틀명이 일치하지 않습니다 '. $ed.' 열입니다 ';
												echo "<script>alert('$message'); location.href='./ga_menu3_70.php' ;  </script>";
												exit; 
											}
									}					
							}
			
							unset( $EXCEL_data ); //배열초기화 
							for ($ed=1;  $ed <= 101  ; $ed++ ) {    //나중에 필드 숫자만큼  하기 
								   
								   //-->수집하지않는 필드. 
								   if ($EXCEL_declare[$ed][3] == '4'  ) {
										$EXCEL_data[$ed] = "";		
										continue;
								   }

									//지정된 열과 현재의 행에서 엑셀DATA를 가져와  DATA순서로 일차원배열에 담는다.  
									$EXCEL_data[$ed] =	 $objWorksheet->getCell($EXCEL_declare[$ed][2] . $i)->getValue();
									$EXCEL_data[$ed] =   iconv("UTF-8","EUC-KR",$EXCEL_data[$ed]);
									
									echo $EXCEL_declare[$ed][2]."<br>";


									//원수사별 복잡한 특이사항 필드가 있을때 함수에서 구현한다 	
									$EXCEL_data[$ed] = code_special($code,$EXCEL_declare[$ed][2],$EXCEL_data[$ed]);						 					
									//현재 DATA의 TYPE CHECKING( number , date일때 숫자만 취함) 
								   if (!is_null($EXCEL_data[$ed])) {
										   if ($EXCEL_declare[$ed][3] == '1' || $EXCEL_declare[$ed][3] == '2' ) {
												$EXCEL_data[$ed] = preg_replace('/[^0-9]*/s', '',$EXCEL_data[$ed]);								
										   }
									}
							}

							// -->필수 값이나 수집되지 않는 값은 강제 seting하기 
							$EXCEL_data[23] =$code; //보험사 강제 seting			 

							//---> 상품명으로 코드 가져오고 필요 필드 체우기(1차원 배열전체를 주고받기)			 
							if ($EXCEL_data[25] == "" ||  is_null($EXCEL_data[25] )) {
								//--원수사에서 상품코드가 안오면.
								$EXCEL_data = item_seting_nocode($mscon,$code,$EXCEL_data);	
							}else{
								//--원수사에서 상품코드가  오면.
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

							//--->키(계약번호)가 있다면 insert start 
							if (!empty($EXCEL_data[1])  ) {
									//---> 원수사 사원코드에서 자체사원코드 및 조직도 가져오기. 		
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

									$LS_SUGI  = '2'; //엑셀등록 

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
										 break;  // for문만 탈출하고 집계data는 입력함. 
									}else{
											$ins_tot_cnt = $ins_tot_cnt + 1; 
											sqlsrv_query("COMMIT");
									}
						}  //--->record 한줄에대한( for end) 
					} //-->엑셀전체에 대한
					
						//--업로드 히스토리입력 
						if ( $ins_tot_cnt == 0 ) {
							$bigo ='입력 건수가 없습니다.';
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
									$message = ' 계약파일 엑셀업로드 ' . $ins_tot_cnt . ' 건 입력을 완료하였습니다.';
									echo "<script>alert('$message'); location.href='./ga_menu3_70.php' ;  </script>";
							}else{
									$message = ' 계약엑셀업로드 중 오류발생  엑셀오류행은  엑셀 '. $i.' 행입니다 ';
									echo "<script>alert('$message'); location.href='./ga_menu3_70.php' ;  </script>";
							}
				}  //---> try end 
			 catch (exception $e) {
				sqlsrv_free_stmt($result);
				sqlsrv_close($mscon);
				echo $e;
				exit;
				$message = '계약엑셀파일 업로드중 SYSTEM ERROR가 발생하였습니다!';
				echo "<script>alert('$message'); location.href='./ga_menu3_70.php' ;  </script>";
			}
} // upload if 에대한 end 


//===============================================================================================================//
//   file delete에대한  rtn 
//===============================================================================================================//

if ($_POST['type'] == 'del' ) {
 
		if($_SESSION['S_SCODE'] == null ||  $del_upldate == null || $del_uplnum == null ){
			sqlsrv_free_stmt($result);
			sqlsrv_close($mscon);
			$message = '삭제할 수 없는 data입니다 ';
			echo "<script>alert('$message'); location.href='./ga_menu3_70.php' ;  </script>";
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
			echo "<script>alert('$message'); location.href='./ga_menu3_70.php' ;  </script>";
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
			echo "<script>alert('$message'); location.href='./ga_menu3_70.php' ;  </script>";
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
			echo "<script>alert('$message'); location.href='./ga_menu3_70.php' ;  </script>";
			exit;
		}
		sqlsrv_query("COMMIT");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);

		$message = '삭제완료';
		echo "<script>alert('$message'); location.href='./ga_menu3_70.php' ;  </script>";
		exit;
}

?> 

