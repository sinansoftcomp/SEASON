<?
			 
require_once $_SERVER['DOCUMENT_ROOT']."/bin/include/lib/PHPExcel/Classes/PHPExcel.php"; 
require_once $_SERVER['DOCUMENT_ROOT']."/bin/include/lib/PHPExcel/Classes/PHPExcel/IOFactory.php";  // IOFactory.php�� �ҷ��;� �ϸ�, ��δ� ������� ������ �°� �����ؾ� �Ѵ�.					
					

	//=================================================================================================================//
	//-------> �ʵ庰 �� ó�� 
	//=================================================================================================================//
	function pattern_ser ($objWorksheet,$mscon){


		$EXCEL_all_col =['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
		'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ',
		'BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ',
		'CA','CB','CC','CD','CE','CF','CG','CH','CI','CJ','CK','CL','CM','CN','CO','CP','CQ','CR','CS','CT','CU','CV','CW','CX','CY','CZ'];
		//-->�ϴ� Ÿ��Ʋ�� ��´� 
		$EXCEL_data_tit= array();
		$maxRow		= $objWorksheet->getHighestRow();
		$maxCol        = $objWorksheet->getHighestColumn();

		
		for ($i = 1 ; $i <= 20 ; $i++) {		//ù����� �˻� (Ÿ��Ʋ ���ΪO��)
				//����Ÿ��Ʋ�� TABLE Ÿ��Ʋ ���Ͽ� ����ġ �Ҷ� ó���Ұ� 
				unset( $EXCEL_data_tit ); //�迭�ʱ�ȭ 
				$yes_bit = 'Y';
				for ($ed=0;  $ed <= 200  ; $ed++ ) {    //���߿� �ʵ� ���ڸ�ŭ  �ϱ� 
						
						$tit=	 $objWorksheet->getCell($EXCEL_all_col[$ed] . $i)->getValue();
						$tit =	 iconv("UTF-8","EUC-KR",$tit);    
						$tit =  preg_replace('/\s+/', '',$tit); 
 
						$EXCEL_data_tit[$EXCEL_all_col[$ed]] = $tit;         // ������ ���� �迭�� ��� �߰�

						//-->���������� Ÿ��Ʋ ���������� ������ 
						if ( $maxCol  == $EXCEL_all_col[$ed] ) {
							//print_r(' $EXCEL_data_tit[$ed]oooooooooooooooooooooooooooooooooo'); 
							 $yes_bit = 'Y';
							 break;
						}	
						//---���̾�����
						if ($tit  == "" || is_null($tit )) {
							$yes_bit = 'N';
							 break;					  							 
						}						 
				}
				if ($yes_bit == 'Y') {
					$tit_line   = $i ;  //Ÿ��Ʋ�� �ִ� ���ȣ 
					$EXCEL_data_tit['tit_line'] = $i;         // ������ ���� �迭�� ��� �߰�
					break;	
				}	
		}
		//print_r(EXCEL_data_tit);
		//======================================================================
		//-->Ÿ��Ʋ �ϼ��� ����ã��  
		//======================================================================
		$EXCEL_declare = array( // 1���� �迭�� 1�� ���� 2���� �迭 ����
			array()
		);
 
		$sql = "select *  from UPLOAD_EXCEL where scode = '".$_SESSION['S_SCODE']."' and gubun = 'P' ";
		$qry  = sqlsrv_query( $mscon, $sql );
		$ptData	= array();
		while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
			$ptData[] = $fet;
		}
 

		//print_r($ptData);

 		//--->�ش翭�� Ÿ��Ʋ�� ���� ������ ã�´�.
		for ($db_i = 0; $db_i < count($ptData) ;$db_i++ ) {
				$ex_i=0; 
				$ex_seq =1; 
				$ybit = 'N';
				unset( $EXCEL_declare ); //�迭�ʱ�ȭ 
				print_r('*==========START=========================*'); 
				for ($i = 1 ; $i <= 81 ; $i++) {		//2������ �����ͽ���
					$ex_i       =   $ex_i + 1;

					//sql table layout �ʵ� ����. 	
					$A = "A".$ex_seq; 
					$B = "B".$ex_seq; 
					$C = "C".$ex_seq; 

					 if ($ex_i  == 1) {
						 $EXCEL_declare[$ex_seq ][1] =  preg_replace('/\s+/', '',$ptData[$db_i][$A]);
						 //print_r($ptData[$db_i][$A] );
					 }
					 if ($ex_i  == 2) {
						 $EXCEL_declare[$ex_seq ][2] = preg_replace('/\s+/', '', $ptData[$db_i][$B]);
						  //-->���� �����ϴ� ���� �ִٸ�  ���ǵ� ��(�����̰�����)��  Ÿ��Ʋ�� ������ Ÿ��Ʋ�� ���Ѵ�.
						  if (!empty($EXCEL_declare[$ex_seq ][2])) {
									print_r('<-----DB--');
								    print_r($EXCEL_declare[$ex_seq ][1]);
									print_r('------');
									print_r($EXCEL_data_tit[$EXCEL_declare[$ex_seq ][2]]);
									print_r('=');									
									print_r($EXCEL_declare[$ex_seq ][2]);								 
							  		print_r('----->');

								//-����Ÿ��Ʋ 									           	�� ��ƾ �������� Ÿ��Ʋ

								if ($EXCEL_declare[$ex_seq ][1] !=  $EXCEL_data_tit[$EXCEL_declare[$ex_seq ][2]]    ) {
									$ybit = 'N';
									 break;	//-->Ÿ��Ʋ�� Ʋ���� �������� 
								}else{
									$ybit = 'Y';
								}
						  }
					 }
					 if ($ex_i  == 3) {
						 $EXCEL_declare[$ex_seq ][3] = preg_replace('/\s+/', '', $ptData[$db_i][$C]);
					 }
					//3���ʵ� ���� �ʵ尡 �ݺ��ȴ�.
					if ($ex_i  == 3) {
						$ex_i = 0;
						$ex_seq  =  $ex_seq + 1;
					}
				}
				//-->for�� ���ǵ� ������ ã�Ҵٸ� 
				if ($ybit =='Y') {
					//--->��ҹ������� . 
					$EXCEL_data_tit['CODE'] = $ptData[$db_i]['CODE'] ;         //�����
					$EXCEL_data_tit['GUBUN'] = $ptData[$db_i]['GUBUN'] ;         //������  (���,�Ա�,������)
					$EXCEL_data_tit['GUBUNSUB'] = $ptData[$db_i]['GUBUNSUB'] ;         //���������� 
					$EXCEL_data_tit['YN'] = 'Y' ;         //����ã�� �������� 
					break;
				}else{	
					//�������� 
					$EXCEL_data_tit['YN'] = 'N' ;         //����ã�� �������� 
				}
		}

return  ($EXCEL_data_tit)  ;
} // code_special function end 
?>