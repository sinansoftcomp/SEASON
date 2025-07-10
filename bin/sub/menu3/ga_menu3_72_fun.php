<?
	//=================================================================================================================//
	//-------> 필드별 상세 처리 
	//=================================================================================================================//
	function code_special ($code, $EXCEL_declare,$exc_data){
					
			//$code   = 원수사코드
			// $EXCEL_declare =현재진행하는 엑셀 열 
			// $exc_data  = 엑셀 data
			/*
			echo $code."<br>";
			echo $EXCEL_declare."<br>";
			echo $exc_data."<br>";
			*/ 

			//--->원수사별 처리
			if ( $code == '401') {    // 하나생명보험 )
						if ($EXCEL_declare == 'A') {
							//처리하고	
							$exc_data = $exc_data;
							return $exc_data;
						}
						if ($EXCEL_declare == 'C') {
							//처리하고	
							$exc_data = $exc_data;
							return $exc_data;
						}
			}

			if ( $code == '402') { // 동양생명 
						if ($EXCEL_declare == 'A') {
							//처리하고	
							$exc_data = $exc_data;
							return $exc_data;
						}
						if ($EXCEL_declare == 'C') {
							//처리하고	
							$exc_data = $exc_data;
							return $exc_data;
						}
			}

			//-->만약 수정한 사항이 없다면 받은필드 그대로 돌려준다 
			return $exc_data;

	} // code_special function end 
	
	//=================================================================================================================//
	//-------> 원수사에서 상품코드가 안오면 
	//=================================================================================================================//
	function item_seting_nocode ($mscon,$code,$EXCEL_data){
			//-->상품명
			$LS_ITEMNM  = preg_replace("/\s+/", "", $EXCEL_data[26]); 
			$sql = "
				  SELECT  *
						FROM    item a
						WHERE  a.SCODE  =  '".$_SESSION['S_SCODE']."'    AND a.inscode = '".$code."'  and   REPLACE(a.NAME,  ' ', '') 	= '".$LS_ITEMNM."' 
						"; 
			$result  = sqlsrv_query( $mscon, $sql );
			$row =  sqlsrv_fetch_array($result); 
			if (is_null($row['SCODE'])) {																	
					   //insert 번호구하기 (상품명으로 검색하여 상품코드가 없다면 ) 
						$sql = "select max(item) UPLNUM  from  item where scode = '".$_SESSION['S_SCODE']."' and inscode = '".$code."' and SUBSTRING(ITEM,1,1) = 'A' ";
						$result  = sqlsrv_query( $mscon, $sql );
						$row =  sqlsrv_fetch_array($result); 
						$cnt = $row["UPLNUM"];	
						 
						if (is_null($row["UPLNUM"])  ) {
							$LS_UPLNUM	 = 'A'.$code.'0001';
						}else{
							$NUM   = substr($row["UPLNUM"], -4)  + 1; 
							$NUM   = substr('0000'.$NUM,-4); 
							$LS_UPLNUM =     'A'.$code.$NUM;			
						}
						$sql = "
						insert into ITEM( SCODE  , INSCODE  , ITEM , NAME   ,  NAMEAB   , KIND   , BBIT  , JBIT   , ISCODE , NBIT , BIGO  , SUGI , IDATE  , ISWON  , UDATE   , USWON )
							values('".$_SESSION['S_SCODE']."','$code','$LS_UPLNUM',' $EXCEL_data[26]',' $EXCEL_data[26]'   ,'99' ,'9' ,'99','','99' ,'계약업로드중 자동입력(상품명으로 검색)','2',getdate(),'".$_SESSION['S_SKEY']."',getdate(),'".$_SESSION['S_SKEY']."')";
						
							sqlsrv_query("BEGIN TRAN");
							$result =  sqlsrv_query( $mscon, $sql );
							if ($result == false){
								sqlsrv_query("ROLLBACK");
								return $EXCEL_data;
							}else{
								sqlsrv_query("COMMIT");
								//-->상품코드만 seting 한다.
								$EXCEL_data[25] = $LS_UPLNUM;
							    return $EXCEL_data;
							}						
			}else{										
 				     //상품명으로 검색하여 상품코드가 있다면  
					 // --> 엑셀에서 data수집를 못했을 때 만 (상품구분)
					 if (is_null($EXCEL_data[24]) || $EXCEL_data[24] == "")  {
							$EXCEL_data[24] =  $row['BBIT']; 
					 }
					 //--> 상품코드 
					 if (is_null($EXCEL_data[25]) || $EXCEL_data[25] == "")  {
							$EXCEL_data[25] =  $row['ITEM']; 
					 }

					 //---->납입방법 
					 if (is_null($EXCEL_data[36]) || $EXCEL_data[36] == "" ) {
							$EXCEL_data[36] =  $row['NBIT']; 
					 }			
			}				
			return $EXCEL_data;
	} // item_seting_nocode  function end 

	//=================================================================================================================//
	//-------> 원수사에서 상품코드가 오면 
	//=================================================================================================================//
	function item_seting_yescode ($mscon,$code,$EXCEL_data){
			//-->상품명
			$LS_ITEMNM  = preg_replace("/\s+/", "", $EXCEL_data[25]); 
			$sql = "
				  SELECT  *
						FROM    item a
						WHERE  a.SCODE  =  '".$_SESSION['S_SCODE']."'    AND a.inscode = '".$code."'  and   a.item	= '".$LS_ITEMNM."' 
						"; 
			$result  = sqlsrv_query( $mscon, $sql );
			$row =  sqlsrv_fetch_array($result); 
			 //상품명으로 검색하여 상품코드가 있다면  

			 // --> 엑셀에서 data수집를 못했을 때 만 (상품구분)
			 if (is_null($EXCEL_data[24]) || $EXCEL_data[24] == "")  {
					$EXCEL_data[24] =  $row['BBIT']; 
			 }
			 //--> 상품코드 
			 if (is_null($EXCEL_data[25]) || $EXCEL_data[25] == "")  {
					$EXCEL_data[25] =  $row['ITEM']; 
			 }
			 //--> 상품명 
			 if (is_null($EXCEL_data[26]) || $EXCEL_data[26] == "")  {
					$EXCEL_data[26] =  $row['NAME']; 
			 }
			 //---->납입방법 
			 if (is_null($EXCEL_data[36]) || $EXCEL_data[36] == "" ) {
					$EXCEL_data[36] =  $row['NBIT']; 
			 }			
						
			return $EXCEL_data;
	} // item_seting_yescode   function end 




?>