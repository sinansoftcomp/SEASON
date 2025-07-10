<?
			 
require_once $_SERVER['DOCUMENT_ROOT']."/bin/include/lib/PHPExcel/Classes/PHPExcel.php"; 
require_once $_SERVER['DOCUMENT_ROOT']."/bin/include/lib/PHPExcel/Classes/PHPExcel/IOFactory.php";  // IOFactory.php을 불러와야 하며, 경로는 사용자의 설정에 맞게 수정해야 한다.					
					

	//=================================================================================================================//
	//-------> 필드별 상세 처리 
	//=================================================================================================================//
	function pattern_ser ($objWorksheet,$mscon,$col_cnt){

		//---> 업로드된 엑셀의 타이틀만 ?는다 
		$EXCEL_all_col =['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
		'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ',
		'BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ',
		'CA','CB','CC','CD','CE','CF','CG','CH','CI','CJ','CK','CL','CM','CN','CO','CP','CQ','CR','CS','CT','CU','CV','CW','CX','CY','CZ'];
		//-->일단 타이틀을 담는다 
		$EXCEL_data_tit= array();
		$maxRow		= $objWorksheet->getHighestRow();
		$maxCol        = $objWorksheet->getHighestColumn();
		$ser_tit_yes  = 'N';		
		
		for ($i = 1 ; $i <= 20 ; $i++) {		//첫행부터 35행 검사 (타이틀 라인?기)
				//엑셀타이틀과 TABLE 타이틀 비교하여 불일치 할때 처리불가 
				unset( $EXCEL_data_tit ); //배열초기화 
				$yes_bit = 'Y';
				$idaum_line = 1;
				$space_col = 0 ; //타이틀중간에 공란이나 셀병합한 타이틀이 있을경우를 대비해 공란의 개수를 카운트 하는 변수  
				
				for ($ed=0;  $ed <= $col_cnt  ; $ed++ ) {    //나중에 필드 숫자만큼  하기 

						$tit=	 $objWorksheet->getCell($EXCEL_all_col[$ed] . $i)->getValue();
						$tit =	 iconv("UTF-8","EUC-KR",$tit);    
						$tit =  preg_replace('/\s+/', '',$tit); 

						$idaum  =  $i + 1;  //-->다음행을 읽는다!
						$tit2 =	 $objWorksheet->getCell($EXCEL_all_col[$ed] . $idaum)->getValue();
						$tit2 =	 iconv("UTF-8","EUC-KR",$tit2);    
						$tit2 =  preg_replace('/\s+/', '',$tit2);										
						
						if ($tit == '증권번호' || $tit == '계약번호'  ) {       //둘중하나가 있으면 그 라인이 타이틀이다  이라인을 통과하면 안됨 (다음이 빈칸이어도 증권번호가 없으면 다음PRG에서 통과한다)
							//--->다음라인이 타이틀 자격이 있는가? DATA가 10개 이상이면 TIT자격이 없다 
							$idaum_tit_bit = 'N';
							$idaum_tit_cnt = 0;
							for ($i_ed=0;  $i_ed <= $col_cnt  ; $i_ed++ ) {  
									$t = $objWorksheet->getCell($EXCEL_all_col[$i_ed] . $idaum)->getValue();
									$t =	 iconv("UTF-8","EUC-KR",$t);    
									if(!empty($t) ) 	{
										$idaum_tit_cnt = $idaum_tit_cnt + 1;	
									}	
									if (str_replace(' ','',$t) =='합' || str_replace(' ','',$t) == '합계' ) {
										$idaum_tit_cnt = 100;
									}
									if ( $maxCol  == $EXCEL_all_col[$i_ed] ) {
										 break;
									}	
							}

							if ($idaum_tit_cnt < 10) {
								$idaum_tit_bit = 'Y';
							}

							$ser_tit_yes  = 'Y';	
							if (empty($tit2)   &&  $idaum_tit_bit == 'Y') {              // 이조건이면 타이틀이 2개다.(자기라인이 증권번호 다음라인이 빈칸이고 DATA가 10개미만이면) 
								$idaum_line = 2;
							}
						} 
						
						//--->타이틀이 2라인이고   
						if ($idaum_line == 2) {
								if (!empty($tit) && !empty($tit2)) {
									$EXCEL_data_tit[$EXCEL_all_col[$ed]] = $tit2;         // 2라인선택 
								}else if (!empty($tit2)) {
									$EXCEL_data_tit[$EXCEL_all_col[$ed]] = $tit2;         //2라인선택 
								}else if (!empty($tit)) {
									$EXCEL_data_tit[$EXCEL_all_col[$ed]] = $tit;         //1라인선택 
								}
						}
						//--->타이틀 1라인 구성 
						if ($idaum_line == 1) {
								$EXCEL_data_tit[$EXCEL_all_col[$ed]] = $tit;         //현재행을 타이틀로한다.
						}

						//-->최종열까지 타이틀 정보수집이 끝나면 
						if ( $maxCol  == $EXCEL_all_col[$ed] ) {
							 $yes_bit = 'Y';
							 break;
						}	
						//---값이없으면(45칸까지 타이틀이 있으면 타이틀로 인정한다 45칸이후에 빈칸있슴.)
						if (($tit  == "" || is_null($tit )) &&   $ed  <=35  && $ser_tit_yes  == 'N' ) {
							$space_col = $space_col + 1 ; 
							if ($space_col > 2) {    //타이틀 공란이 2개까지 허용하고 초과하면 다음작업을 수행한다. 
								$yes_bit = 'N';
								 break;				
							}		
						}				
				}
				if ($yes_bit == 'Y') {
					$tit_line   = $i ;  //타이틀이 있는 행번호 
					$EXCEL_data_tit['tit_line'] = $tit_line;         // 생성된 연관 배열에 요소 추가
					break;	
				}	
		} //-->타이틀이 한줄일 때 일단락 한다.

		//======================================================================
		//-->타이틀 완성후 패턴찾기  
		//======================================================================
		$EXCEL_declare = array( // 1차원 배열을 1개 갖는 2차원 배열 선언
			array()
		);
 
		$sql = "select *  from UPLOAD_EXCEL where scode = '".$_SESSION['S_SCODE']."' and gubun = 'S'    order by upcnt desc";

		$qry  = sqlsrv_query( $mscon, $sql );
		$ptData	= array();
		while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
			$ptData[] = $fet;
		}
 		//print_r($ptData);
		//exit;


 		//--->해당열의 타이틀과 같은 패턴을 찾는다.
		$b_Status = '';  //타이틀 불일치 추적 
		
		for ($db_i = 0; $db_i < count($ptData) ;$db_i++ ) {
				$b_Status = $b_Status.'<br> 비교패턴 :  '. $db_i.' : '. $ptData[$db_i]['CODE'] .'-' . $ptData[$db_i]['GUBUN'] .'-' . $ptData[$db_i]['GUBUNSUB'] .'-' . $ptData[$db_i]['GNAME'] . '  : '  ;

				$ex_i=0; 
				$ex_seq =1; 
				$ybit = 'N';
				unset( $EXCEL_declare ); //배열초기화 
	
				//print_r('*****==========START=========================*****'); 
				for ($i = 1 ; $i <= $col_cnt ; $i++) {		//2열부터 데이터시작
					$ex_i       =   $ex_i + 1;

					//sql table layout 필드 지정. 	
					$A = "A".$ex_seq; 
					$B = "B".$ex_seq; 
					$C = "C".$ex_seq; 

					 if ($ex_i  == 1) {
						 $EXCEL_declare[$ex_seq ][1] =  preg_replace('/\s+/', '',$ptData[$db_i][$A]);
						 //print_r($ptData[$db_i][$A] );
					 }
					 if ($ex_i  == 2) {
						 $EXCEL_declare[$ex_seq ][2] = preg_replace('/\s+/', '', $ptData[$db_i][$B]);
						  //-->열을 지정하는 값이 있다면  정의된 열(동현이개발한)의  타이틀과 엑셀의 타이틀을 비교한다.
						  if (!empty($EXCEL_declare[$ex_seq ][2])) {							
								/*
								print_r('-DB SETUP---->');
								print_r($EXCEL_declare[$ex_seq ][1]);
								print_r('=');
								print_r($EXCEL_data_tit[$EXCEL_declare[$ex_seq ][2]]);
								print_r('<---END-.');
								 */ 
								 //-->타이틀 불일치시 타이틀 error 추적하라고 비교결과 리턴시켜 줌 
								$b_Status =  $b_Status. '-start-'.$EXCEL_declare[$ex_seq ][1].'='. $EXCEL_data_tit[$EXCEL_declare[$ex_seq ][2]] .'-end-';
							
								//-DB정의타이틀 									           	위 루틴 엑셀추출 타이틀
								if ($EXCEL_declare[$ex_seq ][1] !=  $EXCEL_data_tit[$EXCEL_declare[$ex_seq ][2]]    ) {
									$ybit = 'N';
									 break;	//-->타이틀이 틀리면 다음정의 
								}else{
									$ybit = 'Y';
								}
						  }
					 }
					 if ($ex_i  == 3) {
						 $EXCEL_declare[$ex_seq ][3] = preg_replace('/\s+/', '', $ptData[$db_i][$C]);
					 }
					//3개필드 마다 필드가 반복된다.
					if ($ex_i  == 3) {
						$ex_i = 0;
						$ex_seq  =  $ex_seq + 1;
					}
				}
				//-->for중 정의된 패턴을 찾았다면 
				if ($ybit =='Y') {
					//--->대소문구분함 . 
					$EXCEL_data_tit['CODE'] = $ptData[$db_i]['CODE'] ;         //보험사
					$EXCEL_data_tit['GUBUN'] = $ptData[$db_i]['GUBUN'] ;         //업무명  (계약,입금,수수료)
					$EXCEL_data_tit['GUBUNSUB'] = $ptData[$db_i]['GUBUNSUB'] ;         //업무명세구분 
					$EXCEL_data_tit['YN'] = 'Y' ;         //패턴찾기 성공여부 
					break;
				}else{	
					//다음패턴 으로 찾기
					$EXCEL_data_tit['CODE'] = '' ;                 //보험사
					$EXCEL_data_tit['GUBUN'] = '';              //업무명  (계약,입금,수수료)
					$EXCEL_data_tit['GUBUNSUB'] = '' ;      //업무명세구분 
					$EXCEL_data_tit['YN'] = 'N' ;                   //패턴찾기 성공여부 
				}
		}
		$EXCEL_data_tit['b_Status']  = $b_Status;  //타이틀 불일치 error추적하라고 
return  ($EXCEL_data_tit)  ;
} // code_special function end 
?>