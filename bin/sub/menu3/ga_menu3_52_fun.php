<?
	//=================================================================================================================//
	//-------> 필드별 상세 처리 
	//=================================================================================================================//
	function code_special ($mscon,$sheetname,$EXCEL_data){

			//개약 개시일자등 8자리로 통합 (한화손보등 공통)
			$EXCEL_data[20] = substr($EXCEL_data[20], 0, 8);
			$EXCEL_data[21] = substr($EXCEL_data[21], 0, 8);
			$EXCEL_data[22] = substr($EXCEL_data[22], 0, 8);
 
			//--->원수사별 처리
			if ( str_replace(" ","",$EXCEL_data[23]) == '00022') {     //---> DB손해보험 --> 일반,장기,자동차의 패턴이 똑같아서 시트명으로 상품군을 입력한다 (각각처리 어떤놈이 바뀔지 모른다)
					if (str_replace(" ","",$sheetname)  == '일반') {
						$EXCEL_data[24] = '1';
						return $EXCEL_data;
					}
					if (str_replace(" ","",$sheetname)  == '장기') {
						$EXCEL_data[24] = '2';
						return $EXCEL_data;
					}
					if (str_replace(" ","",$sheetname)  == '자동차') {
						$EXCEL_data[24] = '3';
						return $EXCEL_data;
					}
			}

			if ( str_replace(" ","",$EXCEL_data[23]) == '00021') {     //---> 삼성화재 --> 일반,장기,자동차의 패턴이 똑같아서 시트명으로 상품군을 입력한다 (각각처리 어떤놈이 바뀔지 모른다)
					if (str_replace(" ","",$sheetname)  == '일반') {
						$EXCEL_data[24] = '1';
						return $EXCEL_data;
					}
					if (str_replace(" ","",$sheetname)  == '장기') {
						$EXCEL_data[24] = '2';
						return $EXCEL_data;
					}
					if (str_replace(" ","",$sheetname)  == '자동차') {
						$EXCEL_data[24] = '3';
						return $EXCEL_data;
					}
			}
 			

			if ( str_replace(" ","",$EXCEL_data[23]) == '00018') {     //---> 현대해상 --> 일반,장기,자동차의 패턴이 똑같아서 시트명으로 상품군을 입력한다 (각각처리 어떤놈이 바뀔지 모른다)
					if (str_replace(" ","",$sheetname)  == '일반') {
						$EXCEL_data[24] = '1';
						return $EXCEL_data;
					}
					if (str_replace(" ","",$sheetname)  == '장기') {
						$EXCEL_data[24] = '2';
						return $EXCEL_data;
					}
					if (str_replace(" ","",$sheetname)  == '자동차') {
						$EXCEL_data[24] = '3';
						return $EXCEL_data;
					}
			}
 			

			//-->만약 수정한 사항이 없다면 받은필드 그대로 돌려준다 (중요)
			return $EXCEL_data;

	} // code_special function end 
		
	//=================================================================================================================//
	//-------> 신규계약 입력시 기존에있는 증권번호가 입력 되었다면 
	//=================================================================================================================//
	function kwn_update ($mscon,$EXCEL_data,$upldate,$GUBUN,$GUBUNSUB,$LS_UPLNUM,$i,  $e_text){

				if($_SESSION['S_SCODE'] == null ){
				return 'N'; 	
				}
				if (empty($EXCEL_data[23])  ||  empty($EXCEL_data[23]) )  {
				return 'N';	
				}
				//--->수정전 kwn_his에 DATA 저장 
				$sql =  " insert into  KWN_HIS SELECT * FROM KWN
										WHERE scode =            '".$_SESSION['S_SCODE']."'
												and   INSCODE=   '".$EXCEL_data[23]."'
												and  kcode =         '".$EXCEL_data[1]."' ";   	
													
 				sqlsrv_query($mscon,"BEGIN TRAN");
				$result =  sqlsrv_query( $mscon, $sql );
				if ($result == false){
					sqlsrv_query($mscon,"ROLLBACK");
					return 'N';
				}else{
					sqlsrv_query($mscon,"COMMIT");
				}		

				//--->수정시 기존 계약을 수기로 수정한 그 기록을 남겨두고 원수사에서 들어온 필드만 수정한다.(다른 수정필드 보호)
				$table_arr = array('0','1','KNAME','SBIT','SJUNO','SNUM','COMNM','CUPNM','EMAILSEL','EMAIL','TELBIT','TEL','HTELBIT','HTEL','ADDBIT','POST','ADDR','ADDR_DT','BIGO',
															'KSTBIT','KDATE','FDATE','TDATE','INSCODE','INSILJ','ITEM','ITEMNM','KSMAN','KDMAN','MAMT','HAMT','SAMT','SRATE','INSTERM','INSTBIT','FBIT',
															'NBIT','NTERM','KDAY','KSBIT','KSGUBUN','BANK','SYJUNO','SYJ','CARD','CARDNUM','CYJ','VCBANK','VCNO','PBDATE','AGENCY','RCODE','PAYBIT',
															'BIGO2','REL','PNAME','PSBIT','PSJUNO','PSNUM','PCOMNM','PCUPNM','PEMAILSEL','PEMAIL','PTELBIT','PTEL','PHTELBIT','PHTEL','PADDBIT','PPOST',
															'PADDR','PADDR_DT','PBIGO','CARNUM','CARVIN','CARJONG','CARYY','CARCODE','CARKIND','CARGAMT','CARTAMT','CARSUB1','CARSAMT1','CARSUB2',
															'CARSAMT2','CARSUB3','CARSAMT3','CARSUB4','CARSAMT4','CARSUB5','CARSAMT5','CAROBJ','CARTY','CARPAY1','CARPAY2','CARBAE','CARBODY1',
															'CARBODY2','CARBODY3','CARLOSS','CARACAMT','CARINS','CAREMG');  					 

				$up_kwn_item = '';
				for ($i=2; $i <= 101 ; $i++ ) {
					//전화번호는 업데이트 안함..
					if($i == 9 || $i == 10 || $i == 11 || $i == 12 ) {
						continue;
					}
					
					if (!empty($EXCEL_data[$i])) {    //---> 다른 수정필드 보호
							if ($i == 4  || $i == 42 || $i == 45 ||$i == 57  ) {
									$up_kwn_item = $up_kwn_item.$table_arr[$i]." = "." dbo.ENCRYPTKEY('".$EXCEL_data[$i]."')," ;
							}else{ 
									$up_kwn_item = $up_kwn_item.$table_arr[$i]." =  ". "'". $EXCEL_data[$i]. "'," ;
							}
					}
				}

				//-->수정하기 
				$sql = " UPDATE kwn	  SET	" . $up_kwn_item ."
							UPLDATE				= '$upldate',    
							GUBUN                   =  '$GUBUN',
							GUBUNSUB 	    = '$GUBUNSUB',
							UPLNUM            ='$LS_UPLNUM' ,
							UPLSEQ					  ='$i' ,
							 ORIDATA				= '$e_text'
				WHERE scode =            '".$_SESSION['S_SCODE']."'
						and   INSCODE=   '".$EXCEL_data[23]."'
						and  kcode =         '".$EXCEL_data[1]."' ";   	

 				sqlsrv_query($mscon,"BEGIN TRAN");
				$result =  sqlsrv_query( $mscon, $sql );
				if ($result == false){
					//print_r($sql);
					sqlsrv_query($mscon,"ROLLBACK");
					return 'N';
				}else{
					sqlsrv_query($mscon,"COMMIT");
					return 'Y';
				}			
 		
			return "Y";
	} // item_seting_nocode  function end 
	//=================================================================================================================//
	//-------> 원수사에서 상품코드 insert 
	//=================================================================================================================//
	function item_seting_yescode ($mscon,$EXCEL_data){

			if($_SESSION['S_SCODE'] == null ){
					return $EXCEL_data;
			}
 
			$sql = "
				  SELECT  *
						FROM    item a
						WHERE  a.SCODE  =  '".$_SESSION['S_SCODE']."'    AND a.inscode = '".$EXCEL_data[23]."'  and   ITEM 	= '".$EXCEL_data[25]."' 
						"; 
			$result  = sqlsrv_query( $mscon, $sql );
			$row =  sqlsrv_fetch_array($result); 
			if (is_null($row['SCODE'])) {																	
				$sql = "
				insert into ITEM( SCODE  ,                         INSCODE  ,                 ITEM ,                              NAME   ,                    NAMEAB   ,                  KIND   ,            BBIT  , JBIT   , ISCODE , NBIT , BIGO  , SUGI , IDATE  , ISWON  , UDATE   , USWON )
				values('".$_SESSION['S_SCODE']."','$EXCEL_data[23]','$EXCEL_data[25]',' $EXCEL_data[26]',' $EXCEL_data[26]'                ,'9'  ,  '$EXCEL_data[24]'  ,'99','','99' ,'계약업로드중 자동입력','2',getdate(),'".$_SESSION['S_SKEY']."',getdate(),'".$_SESSION['S_SKEY']."')";

				sqlsrv_query($mscon,"BEGIN TRAN");
				$result =  sqlsrv_query( $mscon, $sql );
				if ($result == false){
					sqlsrv_query($mscon,"ROLLBACK");
					return $EXCEL_data;
				}else{
					sqlsrv_query($mscon,"COMMIT");
					return $EXCEL_data;
				}						
			}				
			return $EXCEL_data;
	} // item_seting_nocode  function end 

?>