<?
	//=================================================================================================================//
	//-------> �ʵ庰 �� ó�� 
	//=================================================================================================================//
	function code_special ($code, $EXCEL_declare,$exc_data){
					
			//$code   = �������ڵ�
			// $EXCEL_declare =���������ϴ� ���� �� 
			// $exc_data  = ���� data
			/*
			echo $code."<br>";
			echo $EXCEL_declare."<br>";
			echo $exc_data."<br>";
			*/ 

			//--->�����纰 ó��
			if ( $code == '401') {    // �ϳ������� )
						if ($EXCEL_declare == 'A') {
							//ó���ϰ�	
							$exc_data = $exc_data;
							return $exc_data;
						}
						if ($EXCEL_declare == 'C') {
							//ó���ϰ�	
							$exc_data = $exc_data;
							return $exc_data;
						}
			}

			if ( $code == '402') { // ������� 
						if ($EXCEL_declare == 'A') {
							//ó���ϰ�	
							$exc_data = $exc_data;
							return $exc_data;
						}
						if ($EXCEL_declare == 'C') {
							//ó���ϰ�	
							$exc_data = $exc_data;
							return $exc_data;
						}
			}

			//-->���� ������ ������ ���ٸ� �����ʵ� �״�� �����ش� 
			return $exc_data;

	} // code_special function end 
	
	//=================================================================================================================//
	//-------> �����翡�� ��ǰ�ڵ尡 �ȿ��� 
	//=================================================================================================================//
	function item_seting_nocode ($mscon,$code,$EXCEL_data){
			//-->��ǰ��
			$LS_ITEMNM  = preg_replace("/\s+/", "", $EXCEL_data[26]); 
			$sql = "
				  SELECT  *
						FROM    item a
						WHERE  a.SCODE  =  '".$_SESSION['S_SCODE']."'    AND a.inscode = '".$code."'  and   REPLACE(a.NAME,  ' ', '') 	= '".$LS_ITEMNM."' 
						"; 
			$result  = sqlsrv_query( $mscon, $sql );
			$row =  sqlsrv_fetch_array($result); 
			if (is_null($row['SCODE'])) {																	
					   //insert ��ȣ���ϱ� (��ǰ������ �˻��Ͽ� ��ǰ�ڵ尡 ���ٸ� ) 
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
							values('".$_SESSION['S_SCODE']."','$code','$LS_UPLNUM',' $EXCEL_data[26]',' $EXCEL_data[26]'   ,'99' ,'9' ,'99','','99' ,'�����ε��� �ڵ��Է�(��ǰ������ �˻�)','2',getdate(),'".$_SESSION['S_SKEY']."',getdate(),'".$_SESSION['S_SKEY']."')";
						
							sqlsrv_query("BEGIN TRAN");
							$result =  sqlsrv_query( $mscon, $sql );
							if ($result == false){
								sqlsrv_query("ROLLBACK");
								return $EXCEL_data;
							}else{
								sqlsrv_query("COMMIT");
								//-->��ǰ�ڵ常 seting �Ѵ�.
								$EXCEL_data[25] = $LS_UPLNUM;
							    return $EXCEL_data;
							}						
			}else{										
 				     //��ǰ������ �˻��Ͽ� ��ǰ�ڵ尡 �ִٸ�  
					 // --> �������� data������ ������ �� �� (��ǰ����)
					 if (is_null($EXCEL_data[24]) || $EXCEL_data[24] == "")  {
							$EXCEL_data[24] =  $row['BBIT']; 
					 }
					 //--> ��ǰ�ڵ� 
					 if (is_null($EXCEL_data[25]) || $EXCEL_data[25] == "")  {
							$EXCEL_data[25] =  $row['ITEM']; 
					 }

					 //---->���Թ�� 
					 if (is_null($EXCEL_data[36]) || $EXCEL_data[36] == "" ) {
							$EXCEL_data[36] =  $row['NBIT']; 
					 }			
			}				
			return $EXCEL_data;
	} // item_seting_nocode  function end 

	//=================================================================================================================//
	//-------> �����翡�� ��ǰ�ڵ尡 ���� 
	//=================================================================================================================//
	function item_seting_yescode ($mscon,$code,$EXCEL_data){
			//-->��ǰ��
			$LS_ITEMNM  = preg_replace("/\s+/", "", $EXCEL_data[25]); 
			$sql = "
				  SELECT  *
						FROM    item a
						WHERE  a.SCODE  =  '".$_SESSION['S_SCODE']."'    AND a.inscode = '".$code."'  and   a.item	= '".$LS_ITEMNM."' 
						"; 
			$result  = sqlsrv_query( $mscon, $sql );
			$row =  sqlsrv_fetch_array($result); 
			 //��ǰ������ �˻��Ͽ� ��ǰ�ڵ尡 �ִٸ�  

			 // --> �������� data������ ������ �� �� (��ǰ����)
			 if (is_null($EXCEL_data[24]) || $EXCEL_data[24] == "")  {
					$EXCEL_data[24] =  $row['BBIT']; 
			 }
			 //--> ��ǰ�ڵ� 
			 if (is_null($EXCEL_data[25]) || $EXCEL_data[25] == "")  {
					$EXCEL_data[25] =  $row['ITEM']; 
			 }
			 //--> ��ǰ�� 
			 if (is_null($EXCEL_data[26]) || $EXCEL_data[26] == "")  {
					$EXCEL_data[26] =  $row['NAME']; 
			 }
			 //---->���Թ�� 
			 if (is_null($EXCEL_data[36]) || $EXCEL_data[36] == "" ) {
					$EXCEL_data[36] =  $row['NBIT']; 
			 }			
						
			return $EXCEL_data;
	} // item_seting_yescode   function end 




?>