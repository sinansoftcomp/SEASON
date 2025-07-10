<?
//error_reporting(E_ALL); ini_set('display_errors', 1);
// programmer : 김순관    개발일자 : 2024.02.04  GA플러스 최고중요 PROGRAMM
//특정이미지가 저장안되는 현상이 나타남..!! 2017-11-29  
@ini_set('gd.jpeg_ignore_warning', 1);

include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/class/excel_upload.php");

include($_SERVER['DOCUMENT_ROOT']."/bin/sub/menu3/ga_menu3_52_fun.php");
//-------> 필드별 상세 처리  계약,수납 수수료를 같이 사용하여도 되나 향후 각 업무에 유연하게 대비할 수 있도록  별도로 정의함.
include($_SERVER['DOCUMENT_ROOT']."/bin/sub/menu3/ga_menu3_52_tit_fun.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/sub/menu3/ga_menu3_exc_date_fun.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/class/common_class.php");

require_once $_SERVER['DOCUMENT_ROOT']."/bin/include/lib/PHPExcel/Classes/PHPExcel.php"; 
require_once $_SERVER['DOCUMENT_ROOT']."/bin/include/lib/PHPExcel/Classes/PHPExcel/IOFactory.php";  


 

//$upldate		=	str_replace('-','',$_POST['upldate']);
$upldate  = date("Ymd");  //입력시 서버일자

$gubun		=	$_POST['gubun'];
$filename	=	$_POST['filename'];
$uploadFile =	$_POST['filename'];
//$file_cnt		=	$_POST['imgcnt'];		// 업로드 파일 수 

//---> delete시 사용필드 
$del_upldate	   	  =	str_replace('-','',$_POST['upldate']);
$del_gubun		  =	$_POST['gubun'];
$del_gubunsub	  =	$_POST['gubunsub'];
$del_uplnum		  =	$_POST['uplnum'];

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
	$file_cnt = count($_FILES['attach_file']['name']); // 업로드 파일 수, 올라온 file 수  	
	if (empty($file_cnt)) {
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message ='업로드하실 엑셀FILE이 선택되지 않았습니다!';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "");
		echo json_encode($returnJson);
		exit;	
	}
}

 
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
						  , GSKEY 		  , KSKEY	    , BONBU 	  , JISA,	JIJUM, TEAM 	 ,BONBUID   ,JISAID   ,JIJUMID,	TEAMID    ,SJIK	  , SUGI 	 , IDATE     	  , ISWON   , UDATE  , USWON  , UPLDATE ,GUBUN,GUBUNSUB, UPLNUM   , UPLSEQ  , ORIDATA";			 //--->자체입력 항목  


$EXCEL_all_col =['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
								'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ',
								'BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ',
								'CA','CB','CC','CD','CE','CF','CG','CH','CI','CJ','CK','CL','CM','CN','CO','CP','CQ','CR','CS','CT','CU','CV','CW','CX','CY','CZ'];

//===============================================================================================================//
//   file upload rtn  *환화-환수엑셀 마지막부분 타이틀과 내용 틀림.
//===============================================================================================================//
if ($_POST['type'] == 'in' ) {
try {
	
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

					//==============================================================//
					//-->입력패턴를 찾는다 
					//==============================================================//


					$col_cnt = 303; // data수집 건수  303 / 3 = 101항목 수집  
					$pt  = pattern_ser($objWorksheet,$mscon,$col_cnt);
				 

					//print_r($pt);
					//print_r($pt[b_Status]);      //패턴불일치 error추적시.
					//exit;	

					//-->엑셀패턴을 찾지못하였다면 	
					if ($pt['YN'] == 'N') {
						$pt['GUBUN'] = 'A';
						$pt['GUBUNSUB'] = 'E1';
					}

					 //insert 구룹번호구하기  (순번은 구분까지만 보고 딴다 DISPLAY 정열상) 
					$sql = "select max(UPLNUM) UPLNUM   from upload_history where scode = '".$_SESSION['S_SCODE']."' and upldate = '".$upldate."' and gubun = '".$pt['GUBUN']."' ";
					$result  = sqlsrv_query( $mscon, $sql );
					$row =  sqlsrv_fetch_array($result); 
					$LS_UPLNUM = $row["UPLNUM"];	

					if (is_null($LS_UPLNUM) ||$LS_UPLNUM == "" || $LS_UPLNUM < 1  ) {
						$LS_UPLNUM	 = 1;
					}else{
						$LS_UPLNUM =     $LS_UPLNUM  + 1;
					}


					//--->패턴을 찾지 못하였다면 히스토리만 쓰고 통과시킨다.
					if ($pt['YN'] == 'N') {
							
							//print_r($pt);
							//exit;
							//print_r("=====================");
							//print_r( $objWorksheet->getHighestColumn());
							//print_r("======================");
								
							$bigo = "엑셀입력 패턴을 찾지 못하였습니다.";
							$bigo  =	"St:[".$sheetname ."] ".$bigo.$pt[b_Status] ;
							$bigo = substr($bigo, 0, 3450); 
							$sql = "
							insert into UPLOAD_HISTORY( 	SCODE, UPLDATE  , GUBUN  , GUBUNSUB, UPLNUM      , FILENAME  , CODE     , CNT               , FCNT       , BIGO  ,YYMM,     IDATE       , ISWON  )
							values('".$_SESSION['S_SCODE']."','$upldate', '$pt[GUBUN]','$pt[GUBUNSUB]', $LS_UPLNUM,'$file_ori'   ,'' ,'0' ,'0','$bigo', '$yymm' ,getdate(),'".$_SESSION['S_SKEY']."')";
					
							sqlsrv_query($mscon,"BEGIN TRAN");
							$result =  sqlsrv_query( $mscon, $sql );
							if ($result == false){
								sqlsrv_query($mscon,"ROLLBACK");
							}else{
								sqlsrv_query($mscon,"COMMIT");
							}
							 continue; // 다음 Sheet read...
					}


					$maxRow		= $objWorksheet->getHighestRow();
					$maxCol        = $objWorksheet->getHighestColumn();
					$SERIALCNT	= 0;
					$existChk	= 0;
					$updateCnt=0;
															
					
					$EXCEL_declare = array( // 1차원 배열을 1개 갖는 2차원 배열 선언
						array()
					);

					$sql = "select *  from UPLOAD_EXCEL where scode = '".$_SESSION['S_SCODE']."' and code = '".$pt['CODE']."' and gubun = '".$pt['GUBUN']."' and gubunsub = '".$pt['GUBUNSUB']."'";
					$result  = sqlsrv_query( $mscon, $sql );
					$row =  sqlsrv_fetch_array($result); 

					$start_line = (int)$pt['tit_line']+1;

					//2차원 배열에 talble 특성을 담는다.
					$ex_i=0; 
					$ex_seq =1; 
					for ($i = 1 ; $i <= 303; $i++) {		 
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

					//print_r($EXCEL_declare);
					//exit; 

					//--->여기서부터 실전 
					$ins_tot_cnt = 0 ;
					$up_tot_cnt = 0 ;
					$ins_tot_err_cnt = 0 ;

					for ($i = $start_line ; $i <= $maxRow ; $i++) {		//정의된 시작 행부터 부터 데이터시작
						
							unset( $EXCEL_data ); //배열초기화 
							for ($ed=1;  $ed <= 101  ; $ed++ ) {    //나중에 필드 숫자만큼  하기 
								   
								   //-->수집하지않는 필드. 
								   if ($EXCEL_declare[$ed][3] == '4'  || empty($EXCEL_declare[$ed][3])  ) {
										$EXCEL_data[$ed] = "";		
										continue;
								   }
								 
									//지정된 열과 현재의 행에서 엑셀DATA를 가져와  DATA순서로 일차원배열에 담는다.  
									$EXCEL_data[$ed] =	 $objWorksheet->getCell($EXCEL_declare[$ed][2] . $i)->getValue();
									$EXCEL_data[$ed] =   iconv("UTF-8","EUC-KR",$EXCEL_data[$ed]);		

									//print_r($EXCEL_data[$ed].'==' );
									//print_r($EXCEL_declare[$ed][2].'-' );
									
									//sql 인젝션 공격 대비 공격문자 제거 
									$EXCEL_data[$ed] = SQL_Injection(str_replace("'","",$EXCEL_data[$ed]));

									//현재 DATA의 TYPE CHECKING( number 일때) 
									 if (!is_null($EXCEL_data[$ed])) {
										   if ($EXCEL_declare[$ed][3] == '1' ) {
												$EXCEL_data[$ed] = str_replace(",","",$EXCEL_data[$ed]);			
 										   }
									}

									//현재 DATA의 TYPE CHECKING( date일때 숫자만 취함) 
									 if (!is_null($EXCEL_data[$ed])) {
										   if ( $EXCEL_declare[$ed][3] == '2' ) {
												if(isValidDate($EXCEL_data[$ed])==1){
													$EXCEL_data[$ed] = exc_date($EXCEL_data[$ed]);	//--->엑셀 DATE변환 
												}		
										   }
									}
							}

							//---->증권번호가  없거나 합계라고 되어있으면  저장하지 않는다. 
							if (str_replace(" ","",$EXCEL_data[1]) == ""  ||  is_null($EXCEL_data[1]) || str_replace(" ", "", $EXCEL_data[1] ) == '합계' ){
								 continue;
							}
							//---->현대해상 경우 3라인도 타이틀이고   4라인도  3라인과같은 타이틀명이 그대로 들어옴. 
							if (str_replace(" ","",$EXCEL_data[1]) == "계약번호"  ||   str_replace(" ", "", $EXCEL_data[1] ) == '증권번호' ){
								 continue;
							}
							
							//-->계약상태 space제거한다 
							$EXCEL_data[19] = str_replace(" ","",$EXCEL_data[19])  ; 

							$EXCEL_data[23] = $pt['CODE'] ; //-->보험사
							$EXCEL_data[24] = $pt['INSILJ']; //-->상품구분 

							//---->원수사별 특별사항처리 START  				
							$EXCEL_data = code_special($mscon,$sheetname,$EXCEL_data);	
							//---->원수사별 특별사항처리 END 


							//엑셀전체 필드저장.
							$e_text = "";
							$e_text_tit = "";
							$e_text_col = "";
							for ($ei=0; $ei <=200  ; $ei++ ) {  //--여기는 배열 첨자 때문에 0부터.
									$e_text_tit = $pt[$EXCEL_all_col[$ei]];
									$e_text_col =  $objWorksheet->getCell($EXCEL_all_col[$ei] . $i)->getValue();
									$e_text_col  =   iconv("UTF-8","EUC-KR",$e_text_col );	
									$e_text_col = SQL_Injection(str_replace("'","",$e_text_col));		
									


									//--->exc date변환 e_save_text_col은 전 타이틀이다.						
									$exc_bit = 'N';
								
									$result = strpos($e_text_tit ,'일자');
									if($result === false) {
									}else{
										$exc_bit = 'Y';    
									} 
									$result = strpos($e_text_tit ,'시기');
									if($result === false) {
									}else{
										$exc_bit = 'Y';    
									} 
									
									$result = strpos($e_text_tit ,'일');
									if($result === false) {
									}else{
										$exc_bit = 'Y';    
									} 



									$result = strpos($e_text_tit ,'종기일');
									if($result === false) {
									}else{
										$exc_bit = 'Y';    
									} 
									$result = strpos($e_text_tit ,'영수일');
									if($result === false) {
									}else{
										$exc_bit = 'Y';    
									} 
									$result = strpos($e_text_tit ,'개시일');
									if($result === false) {
									}else{
										$exc_bit = 'Y';    
									} 
									$result = strpos($e_text_tit ,'입금일');
									if($result === false) {
									}else{
										$exc_bit = 'Y';    
									} 
									$result = strpos($e_text_tit ,'지급일');
									if($result === false) {
									}else{
										$exc_bit = 'Y';    
									} 

									if ($exc_bit  == 'Y') {
										if(isValidDate($e_text_col)==1){
											$e_text_col = exc_date($e_text_col);	//--->엑셀 DATE변환 
										}		
									}


									$e_text = $e_text.$e_text_tit.'.***.'.$e_text_col.'.****.';		
									if ( $maxCol  == $EXCEL_all_col[$ei] ) {
										 break;
									}	
							}
							//숫자필드 null시 zero 배열순회
							$num = [29, 30,31,32, 78, 79, 81, 83, 85, 87,89];
							foreach ($num as $num) {
								if (empty($EXCEL_data[$num])) {
									$EXCEL_data[$num] =0;
								}										 
							}
						
 							//-->기존계약 입력 존재여부를 조사하여 있으면 update한다. (INSERT직전에 한다) 					
							$sql = "select COUNT(*) CNT   from KWN where scode = '".$_SESSION['S_SCODE']."' and KCODE = '".$EXCEL_data[1]."' and INSCODE = '".$pt['CODE']."' ";
							$result  = sqlsrv_query( $mscon, $sql );
							$row =  sqlsrv_fetch_array($result); 
							$CNT = $row["CNT"];
							if ($CNT > 0 ) {
								$up_tot_cnt = $up_tot_cnt + 1;
								$rt = kwn_update($mscon,$EXCEL_data,$upldate,$pt['GUBUN'],$pt['GUBUNSUB'],$LS_UPLNUM,$i ,  $e_text);		//--->현존 data에 새로운 업로드번호 부여 (data가 수정 되었기 때문에)
								continue;
							}
							//print_r($EXCEL_data);
							//---->한건의data입력 시작 start. 
							$kwn_excel = "						   
												   '$EXCEL_data[1]','$EXCEL_data[2]','$EXCEL_data[3]',dbo.ENCRYPTKEY('$EXCEL_data[4]'),'$EXCEL_data[5]','$EXCEL_data[6]','$EXCEL_data[7]','$EXCEL_data[8]','$EXCEL_data[9]','$EXCEL_data[10]',
												   '$EXCEL_data[11]','$EXCEL_data[12]','$EXCEL_data[13]','$EXCEL_data[14]','$EXCEL_data[15]','$EXCEL_data[16]','$EXCEL_data[17]','$EXCEL_data[18]','$EXCEL_data[19]','$EXCEL_data[20]',
												   '$EXCEL_data[21]','$EXCEL_data[22]','$EXCEL_data[23]','$EXCEL_data[24]', '$EXCEL_data[25]','$EXCEL_data[26]','$EXCEL_data[27]','$EXCEL_data[28]', '$EXCEL_data[29]','$EXCEL_data[30]' , 
												   '$EXCEL_data[31]' , '$EXCEL_data[32]' ,'$EXCEL_data[33]' ,'$EXCEL_data[34]','$EXCEL_data[35]','$EXCEL_data[36]','$EXCEL_data[37]','$EXCEL_data[38]','$EXCEL_data[39]','$EXCEL_data[40]', 
													'$EXCEL_data[41]',dbo.ENCRYPTKEY('$EXCEL_data[42]'),'$EXCEL_data[43]','$EXCEL_data[44]',dbo.ENCRYPTKEY('$EXCEL_data[45]'),'$EXCEL_data[46]','$EXCEL_data[47]','$EXCEL_data[48]' , '$EXCEL_data[49]','$EXCEL_data[50]',
												   '$EXCEL_data[51]','$EXCEL_data[52]','$EXCEL_data[53]','$EXCEL_data[54]','$EXCEL_data[55]','$EXCEL_data[56]',dbo.ENCRYPTKEY('$EXCEL_data[57]'),'$EXCEL_data[58]','$EXCEL_data[59]','$EXCEL_data[60]',  
												   '$EXCEL_data[61]','$EXCEL_data[62]','$EXCEL_data[63]','$EXCEL_data[64]','$EXCEL_data[65]','$EXCEL_data[66]','$EXCEL_data[67]','$EXCEL_data[68]','$EXCEL_data[69]','$EXCEL_data[70]',
												   '$EXCEL_data[71]','$EXCEL_data[72]' , '$EXCEL_data[73]','$EXCEL_data[74]','$EXCEL_data[75]','$EXCEL_data[76]','$EXCEL_data[77]','$EXCEL_data[78]' ,'$EXCEL_data[79]' , '$EXCEL_data[80]',
												   '$EXCEL_data[81]','$EXCEL_data[82]',  '$EXCEL_data[83]' ,'$EXCEL_data[84]'  ,'$EXCEL_data[85]' ,'$EXCEL_data[86]', '$EXCEL_data[87]' ,'$EXCEL_data[88]','$EXCEL_data[89]' ,'$EXCEL_data[90]',
												   '$EXCEL_data[91]','$EXCEL_data[92]','$EXCEL_data[93]','$EXCEL_data[94]','$EXCEL_data[95]','$EXCEL_data[96]' , '$EXCEL_data[97]','$EXCEL_data[98]','$EXCEL_data[99]','$EXCEL_data[100]',
												   '$EXCEL_data[101]' 
												  ";											  
							$LS_SUGI  = '2'; //엑셀등록 
							$sql  = "
									select  a.bscode,
											a.skey,
											b.bonbu,
											b.jisa,
											b.jijum,
											b.team,
											c.skey bonbuid,
											d.skey jisaid,
											f.skey jijumid,
											e.skey teamid,
											b.jik
									from inswon a
										left outer join swon b on a.scode = b.scode and a.skey = b.skey
										left outer join swon c on a.scode = c.scode and b.bonbu = c.bonbu and c.jik = '5001'
										left outer join swon d on a.scode = d.scode and b.jisa = d.jisa and c.jik = '4001'
										left outer join swon f on a.scode = f.scode and b.jijum = f.jijum and c.jik = '3001'
										left outer join swon e on a.scode = e.scode and b.team = e.team and c.jik = '2001'
									where a.scode = '".$_SESSION['S_SCODE']."'
									  and a.inscode = '".$pt['CODE']."'
									  and a.bscode = '".$EXCEL_data[27]."'	";
							$result  = sqlsrv_query( $mscon, $sql );
							$row =  sqlsrv_fetch_array($result); 

							$LS_GSKEY	=	$row['skey'];
							$LS_BONBU	=	$row['bonbu'];
							$LS_JISA	=	$row['jisa'];
							$LS_JIJUM	=	$row['jijum'];
							$LS_TEAM	=	$row['team'];

							$bonbuid=	$row['bonbuid'];
							$jisaid	=	$row['jisaid'];
							$jijumid=	$row['jijumid'];
							$teamid	=	$row['teamid'];
							$sjik	=	$row['jik'];


							$sql = "
									insert into kwn( ".$kwn_item." )
									values('".$_SESSION['S_SCODE']."', ".$kwn_excel.", '".$LS_GSKEY."', '".$LS_GSKEY."','".$LS_BONBU."'  ,'".$LS_JISA."', '".$LS_JIJUM."' ,'".$LS_TEAM."' 
									,'".$bonbuid."' ,'".$jisaid."' ,'".$jijumid."' ,'".$teamid."' ,'".$sjik."' ,'".$LS_SUGI."' ,getdate(),'".$_SESSION['S_SKEY']."' ,getdate(),'".$_SESSION['S_SKEY']."','".$upldate."', '".$pt['GUBUN']."','".$pt['GUBUNSUB']."',  ".$LS_UPLNUM." ,'".$i ."','".$e_text."'  )
									";
							sqlsrv_query($mscon,"BEGIN TRAN");
							$result =  sqlsrv_query( $mscon, $sql );
							if ($result == false){
								sqlsrv_query($mscon,"ROLLBACK");
								$err = $err.' err Line->'. $i;
								$ins_tot_err_cnt =  $ins_tot_err_cnt + 1 ;



								//print_r($sql);
								//exit; 
							}else{
								$ins_tot_cnt = $ins_tot_cnt + 1; 
								sqlsrv_query($mscon,"COMMIT");
							}

							//--원수사에서 상품코드가  오면 코드입력한다.
							$EXCEL_data = item_seting_yescode($mscon,$EXCEL_data);
							
					} //-->하나의 shhet대한 
					
					//--->하나의 sheet가 종료되면 업로드히스토리 입력하기.					
					if ( $ins_tot_err_cnt == 0 ) {
						$bigo ='성공';
					}else{
						$bigo =$err;
					}

					$bigo  =	"St:[".$sheetname ."] ".$bigo ;
					$bigo = substr($bigo, 0, 3450); 

					$exe_file_name = $file_ori.'[Rpa : '.$pt['CODE'].'-'.$pt['GUBUN'].'-'.$pt['GUBUNSUB'].']';  

					$sql = "
					insert into UPLOAD_HISTORY( 	SCODE, UPLDATE  , GUBUN  , GUBUNSUB, UPLNUM      , FILENAME  , CODE     , CNT         ,UCNT      , FCNT       , BIGO  ,YYMM,     IDATE       , ISWON  )
					values('".$_SESSION['S_SCODE']."','$upldate', '".$pt['GUBUN']."','".$pt['GUBUNSUB']."', $LS_UPLNUM,'$exe_file_name'   ,'".$pt['CODE']."' ,'$ins_tot_cnt' ,'$up_tot_cnt' ,'$ins_tot_err_cnt','$bigo', '$yymm' ,getdate(),'".$_SESSION['S_SKEY']."')";
				
					//print_r($sql);
					sqlsrv_query($mscon,"BEGIN TRAN");
					$result =  sqlsrv_query( $mscon, $sql );
					if ($result == false){
						sqlsrv_query($mscon,"ROLLBACK");
					}else{
						sqlsrv_query($mscon,"COMMIT");
					}
			}//-->전체sheet for end.
			unlink($del_file); //작업파일삭제 
		} //file_no end 
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);

		$message ='원수사  계약엑셀파일 업로드 완료처리 !';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "");
		echo json_encode($returnJson);
		exit;	

} catch (Exception $e) {
		$message ='원수사  계약업로드 처리중 error가 발생하였습니다'. $e;
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "");
		echo json_encode($returnJson);
		exit;		
} //try end...

} // if ($_POST['type'] == 'in' )  에대한 end 

//===============================================================================================================//
//   file delete에대한  rtn 
//===============================================================================================================//
if ($_POST['type'] == 'del' ) { 
try {
	
		if($_SESSION['S_SCODE'] == null ||  $del_upldate == null || $del_uplnum == null ){
			sqlsrv_free_stmt($result);
			sqlsrv_close($mscon);
			$message = '삭제할 수 없는 data입니다 ';
			$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
			echo json_encode($returnJson);
			exit;	
		}
		if (empty($del_upldate) ||empty($del_gubun) ||empty($del_gubunsub) ||empty($del_uplnum)  ) {
			sqlsrv_free_stmt($result);
			sqlsrv_close($mscon);
			$message = '삭제할 수 없는 data입니다 ';
			$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
			echo json_encode($returnJson);
			exit;	
		}

		//업로드 경과시간이 3시간 이상이면 삭제불가  
		$sql = "SELECT DATEDIFF(HOUR,   IDATE, getdate()) AS time_cha	  FROM UPLOAD_HISTORY  where scode = '".$_SESSION['S_SCODE']."' and upldate = '".$del_upldate."' and gubun = '".$del_gubun."' and gubunsub = '".$del_gubunsub."' and uplnum = '".$del_uplnum."'";

		$result  = sqlsrv_query( $mscon, $sql );
		$row =  sqlsrv_fetch_array($result); 
		$time_cha = $row["time_cha"];	
		if ( $time_cha >  '23'  ) {
			sqlsrv_free_stmt($result);
			sqlsrv_close($mscon);
			$message = '해당히스토리는 업로드 후 3시간이상 경과되어 삭제불가합니다. ';
			$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
			echo json_encode($returnJson);
			exit;	
		} 

		// 계약서  삭제 
		$sql = "delete from kwn where scode = '".$_SESSION['S_SCODE']."' and upldate = '".$del_upldate."' and gubun = '".$del_gubun."' and gubunsub = '".$del_gubunsub."' and uplnum = '".$del_uplnum." '";
		sqlsrv_query($mscon,"BEGIN TRAN");
		$result =  sqlsrv_query( $mscon, $sql );

		if ($result == false){
			sqlsrv_query($mscon,"ROLLBACK");
			sqlsrv_free_stmt($result);
			sqlsrv_close($mscon);
			$message = '계약파일 삭제중 ERROR가 발생하였습니다! ';
			$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
			echo json_encode($returnJson);
			exit;	

		}
		sqlsrv_query($mscon,"COMMIT");

		// 업로드히스토리 삭제  
		$sql = "delete from UPLOAD_HISTORY where scode = '".$_SESSION['S_SCODE']."' and upldate = '".$del_upldate."' and gubun = '".$del_gubun."' and gubunsub = '".$del_gubunsub."' and uplnum = '".$del_uplnum." '";
		sqlsrv_query($mscon,"BEGIN TRAN");
		$result =  sqlsrv_query( $mscon, $sql );
		
		if ($result == false){
			sqlsrv_query($mscon,"ROLLBACK");
			sqlsrv_free_stmt($result);
			sqlsrv_close($mscon);
			$message = '업로드 히스토리 삭제중 ERROR가 발생하였습니다! ';
			$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
			echo json_encode($returnJson);
			exit;	
		}
		sqlsrv_query($mscon,"COMMIT");
		
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = '선택하신 계약data를 삭제완료하였습니다.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	


} catch (Exception $e) {
		$message ='원수사  계약 엑셀파일 삭제 처리중 error가 발생하였습니다'. $e;
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "");
		echo json_encode($returnJson);
		exit;		
} //try end...

} //if ($_POST['type'] == 'del' ) end 
?> 

