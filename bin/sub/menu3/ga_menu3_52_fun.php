<?
	//=================================================================================================================//
	//-------> �ʵ庰 �� ó�� 
	//=================================================================================================================//
	function code_special ($mscon,$sheetname,$EXCEL_data){

			//���� �������ڵ� 8�ڸ��� ���� (��ȭ�պ��� ����)
			$EXCEL_data[20] = substr($EXCEL_data[20], 0, 8);
			$EXCEL_data[21] = substr($EXCEL_data[21], 0, 8);
			$EXCEL_data[22] = substr($EXCEL_data[22], 0, 8);
 
			//--->�����纰 ó��
			if ( str_replace(" ","",$EXCEL_data[23]) == '00022') {     //---> DB���غ��� --> �Ϲ�,���,�ڵ����� ������ �Ȱ��Ƽ� ��Ʈ������ ��ǰ���� �Է��Ѵ� (����ó�� ����� �ٲ��� �𸥴�)
					if (str_replace(" ","",$sheetname)  == '�Ϲ�') {
						$EXCEL_data[24] = '1';
						return $EXCEL_data;
					}
					if (str_replace(" ","",$sheetname)  == '���') {
						$EXCEL_data[24] = '2';
						return $EXCEL_data;
					}
					if (str_replace(" ","",$sheetname)  == '�ڵ���') {
						$EXCEL_data[24] = '3';
						return $EXCEL_data;
					}
			}

			if ( str_replace(" ","",$EXCEL_data[23]) == '00021') {     //---> �Ｚȭ�� --> �Ϲ�,���,�ڵ����� ������ �Ȱ��Ƽ� ��Ʈ������ ��ǰ���� �Է��Ѵ� (����ó�� ����� �ٲ��� �𸥴�)
					if (str_replace(" ","",$sheetname)  == '�Ϲ�') {
						$EXCEL_data[24] = '1';
						return $EXCEL_data;
					}
					if (str_replace(" ","",$sheetname)  == '���') {
						$EXCEL_data[24] = '2';
						return $EXCEL_data;
					}
					if (str_replace(" ","",$sheetname)  == '�ڵ���') {
						$EXCEL_data[24] = '3';
						return $EXCEL_data;
					}
			}
 			

			if ( str_replace(" ","",$EXCEL_data[23]) == '00018') {     //---> �����ػ� --> �Ϲ�,���,�ڵ����� ������ �Ȱ��Ƽ� ��Ʈ������ ��ǰ���� �Է��Ѵ� (����ó�� ����� �ٲ��� �𸥴�)
					if (str_replace(" ","",$sheetname)  == '�Ϲ�') {
						$EXCEL_data[24] = '1';
						return $EXCEL_data;
					}
					if (str_replace(" ","",$sheetname)  == '���') {
						$EXCEL_data[24] = '2';
						return $EXCEL_data;
					}
					if (str_replace(" ","",$sheetname)  == '�ڵ���') {
						$EXCEL_data[24] = '3';
						return $EXCEL_data;
					}
			}
 			

			//-->���� ������ ������ ���ٸ� �����ʵ� �״�� �����ش� (�߿�)
			return $EXCEL_data;

	} // code_special function end 
		
	//=================================================================================================================//
	//-------> �ű԰�� �Է½� �������ִ� ���ǹ�ȣ�� �Է� �Ǿ��ٸ� 
	//=================================================================================================================//
	function kwn_update ($mscon,$EXCEL_data,$upldate,$GUBUN,$GUBUNSUB,$LS_UPLNUM,$i,  $e_text){

				if($_SESSION['S_SCODE'] == null ){
				return 'N'; 	
				}
				if (empty($EXCEL_data[23])  ||  empty($EXCEL_data[23]) )  {
				return 'N';	
				}
				//--->������ kwn_his�� DATA ���� 
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

				//--->������ ���� ����� ����� ������ �� ����� ���ܵΰ� �����翡�� ���� �ʵ常 �����Ѵ�.(�ٸ� �����ʵ� ��ȣ)
				$table_arr = array('0','1','KNAME','SBIT','SJUNO','SNUM','COMNM','CUPNM','EMAILSEL','EMAIL','TELBIT','TEL','HTELBIT','HTEL','ADDBIT','POST','ADDR','ADDR_DT','BIGO',
															'KSTBIT','KDATE','FDATE','TDATE','INSCODE','INSILJ','ITEM','ITEMNM','KSMAN','KDMAN','MAMT','HAMT','SAMT','SRATE','INSTERM','INSTBIT','FBIT',
															'NBIT','NTERM','KDAY','KSBIT','KSGUBUN','BANK','SYJUNO','SYJ','CARD','CARDNUM','CYJ','VCBANK','VCNO','PBDATE','AGENCY','RCODE','PAYBIT',
															'BIGO2','REL','PNAME','PSBIT','PSJUNO','PSNUM','PCOMNM','PCUPNM','PEMAILSEL','PEMAIL','PTELBIT','PTEL','PHTELBIT','PHTEL','PADDBIT','PPOST',
															'PADDR','PADDR_DT','PBIGO','CARNUM','CARVIN','CARJONG','CARYY','CARCODE','CARKIND','CARGAMT','CARTAMT','CARSUB1','CARSAMT1','CARSUB2',
															'CARSAMT2','CARSUB3','CARSAMT3','CARSUB4','CARSAMT4','CARSUB5','CARSAMT5','CAROBJ','CARTY','CARPAY1','CARPAY2','CARBAE','CARBODY1',
															'CARBODY2','CARBODY3','CARLOSS','CARACAMT','CARINS','CAREMG');  					 

				$up_kwn_item = '';
				for ($i=2; $i <= 101 ; $i++ ) {
					//��ȭ��ȣ�� ������Ʈ ����..
					if($i == 9 || $i == 10 || $i == 11 || $i == 12 ) {
						continue;
					}
					
					if (!empty($EXCEL_data[$i])) {    //---> �ٸ� �����ʵ� ��ȣ
							if ($i == 4  || $i == 42 || $i == 45 ||$i == 57  ) {
									$up_kwn_item = $up_kwn_item.$table_arr[$i]." = "." dbo.ENCRYPTKEY('".$EXCEL_data[$i]."')," ;
							}else{ 
									$up_kwn_item = $up_kwn_item.$table_arr[$i]." =  ". "'". $EXCEL_data[$i]. "'," ;
							}
					}
				}

				//-->�����ϱ� 
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
	//-------> �����翡�� ��ǰ�ڵ� insert 
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
				values('".$_SESSION['S_SCODE']."','$EXCEL_data[23]','$EXCEL_data[25]',' $EXCEL_data[26]',' $EXCEL_data[26]'                ,'9'  ,  '$EXCEL_data[24]'  ,'99','','99' ,'�����ε��� �ڵ��Է�','2',getdate(),'".$_SESSION['S_SKEY']."',getdate(),'".$_SESSION['S_SKEY']."')";

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