<?
			 
require_once $_SERVER['DOCUMENT_ROOT']."/bin/include/lib/PHPExcel/Classes/PHPExcel.php"; 
require_once $_SERVER['DOCUMENT_ROOT']."/bin/include/lib/PHPExcel/Classes/PHPExcel/IOFactory.php";  // IOFactory.php을 불러와야 하며, 경로는 사용자의 설정에 맞게 수정해야 한다.					
					

	//=================================================================================================================//
	//-------> 필드별 상세 처리 
	//=================================================================================================================//
	function pattern_ser ($objWorksheet,$mscon){


		$EXCEL_all_col =['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
		'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ',
		'BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ',
		'CA','CB','CC','CD','CE','CF','CG','CH','CI','CJ','CK','CL','CM','CN','CO','CP','CQ','CR','CS','CT','CU','CV','CW','CX','CY','CZ'];
		//-->일단 타이틀을 담는다 
		$EXCEL_data_tit= array();
		$maxRow		= $objWorksheet->getHighestRow();
		$maxCol        = $objWorksheet->getHighestColumn();

		
		for ($i = 1 ; $i <= 20 ; $i++) {		//첫행부터 검사 (타이틀 라인찿기)
				//엑셀타이틀과 TABLE 타이틀 비교하여 불일치 할때 처리불가 
				unset( $EXCEL_data_tit ); //배열초기화 
				$yes_bit = 'Y';
				for ($ed=0;  $ed <= 200  ; $ed++ ) {    //나중에 필드 숫자만큼  하기 
						
						$tit=	 $objWorksheet->getCell($EXCEL_all_col[$ed] . $i)->getValue();
						$tit =	 iconv("UTF-8","EUC-KR",$tit);    
						$tit =  preg_replace('/\s+/', '',$tit); 
 
						$EXCEL_data_tit[$EXCEL_all_col[$ed]] = $tit;         // 생성된 연관 배열에 요소 추가

						//-->최종열까지 타이틀 정보수집이 끝나면 
						if ( $maxCol  == $EXCEL_all_col[$ed] ) {
							//print_r(' $EXCEL_data_tit[$ed]oooooooooooooooooooooooooooooooooo'); 
							 $yes_bit = 'Y';
							 break;
						}	
						//---값이없으면
						if ($tit  == "" || is_null($tit )) {
							$yes_bit = 'N';
							 break;					  							 
						}						 
				}
				if ($yes_bit == 'Y') {
					$tit_line   = $i ;  //타이틀이 있는 행번호 
					$EXCEL_data_tit['tit_line'] = $i;         // 생성된 연관 배열에 요소 추가
					break;	
				}	
		}
		//print_r(EXCEL_data_tit);
		//======================================================================
		//-->타이틀 완성후 패턴찾기  
		//======================================================================
		$EXCEL_declare = array( // 1차원 배열을 1개 갖는 2차원 배열 선언
			array()
		);
 
		$sql = "select *  from UPLOAD_EXCEL where scode = '".$_SESSION['S_SCODE']."' and gubun = 'P' ";
		$qry  = sqlsrv_query( $mscon, $sql );
		$ptData	= array();
		while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
			$ptData[] = $fet;
		}
 

		//print_r($ptData);

 		//--->해당열의 타이틀과 같은 패턴을 찾는다.
		for ($db_i = 0; $db_i < count($ptData) ;$db_i++ ) {
				$ex_i=0; 
				$ex_seq =1; 
				$ybit = 'N';
				unset( $EXCEL_declare ); //배열초기화 
				print_r('*==========START=========================*'); 
				for ($i = 1 ; $i <= 81 ; $i++) {		//2열부터 데이터시작
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
									print_r('<-----DB--');
								    print_r($EXCEL_declare[$ex_seq ][1]);
									print_r('------');
									print_r($EXCEL_data_tit[$EXCEL_declare[$ex_seq ][2]]);
									print_r('=');									
									print_r($EXCEL_declare[$ex_seq ][2]);								 
							  		print_r('----->');

								//-정의타이틀 									           	위 루틴 엑셀추출 타이틀

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
					//다음패턴 
					$EXCEL_data_tit['YN'] = 'N' ;         //패턴찾기 성공여부 
				}
		}

return  ($EXCEL_data_tit)  ;
} // code_special function end 
?>