<?
			 
require_once $_SERVER['DOCUMENT_ROOT']."/bin/include/lib/PHPExcel/Classes/PHPExcel.php"; 
require_once $_SERVER['DOCUMENT_ROOT']."/bin/include/lib/PHPExcel/Classes/PHPExcel/IOFactory.php";  // IOFactory.php�� �ҷ��;� �ϸ�, ��δ� ������� ������ �°� �����ؾ� �Ѵ�.					
					

	//=================================================================================================================//
	//-------> �ʵ庰 �� ó�� 
	//=================================================================================================================//
	function pattern_ser ($objWorksheet,$mscon,$col_cnt){

		//---> ���ε�� ������ Ÿ��Ʋ�� ?�´� 
		$EXCEL_all_col =['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
		'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ',
		'BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ',
		'CA','CB','CC','CD','CE','CF','CG','CH','CI','CJ','CK','CL','CM','CN','CO','CP','CQ','CR','CS','CT','CU','CV','CW','CX','CY','CZ'];
		//-->�ϴ� Ÿ��Ʋ�� ��´� 
		$EXCEL_data_tit= array();
		$maxRow		= $objWorksheet->getHighestRow();
		$maxCol        = $objWorksheet->getHighestColumn();
		$ser_tit_yes  = 'N';		
		
		for ($i = 1 ; $i <= 20 ; $i++) {		//ù����� 35�� �˻� (Ÿ��Ʋ ����?��)
				//����Ÿ��Ʋ�� TABLE Ÿ��Ʋ ���Ͽ� ����ġ �Ҷ� ó���Ұ� 
				unset( $EXCEL_data_tit ); //�迭�ʱ�ȭ 
				$yes_bit = 'Y';
				$idaum_line = 1;
				$space_col = 0 ; //Ÿ��Ʋ�߰��� �����̳� �������� Ÿ��Ʋ�� ������츦 ����� ������ ������ ī��Ʈ �ϴ� ����  
				
				for ($ed=0;  $ed <= $col_cnt  ; $ed++ ) {    //���߿� �ʵ� ���ڸ�ŭ  �ϱ� 

						$tit=	 $objWorksheet->getCell($EXCEL_all_col[$ed] . $i)->getValue();
						$tit =	 iconv("UTF-8","EUC-KR",$tit);    
						$tit =  preg_replace('/\s+/', '',$tit); 

						$idaum  =  $i + 1;  //-->�������� �д´�!
						$tit2 =	 $objWorksheet->getCell($EXCEL_all_col[$ed] . $idaum)->getValue();
						$tit2 =	 iconv("UTF-8","EUC-KR",$tit2);    
						$tit2 =  preg_replace('/\s+/', '',$tit2);										
						
						if ($tit == '���ǹ�ȣ' || $tit == '����ȣ'  ) {       //�����ϳ��� ������ �� ������ Ÿ��Ʋ�̴�  �̶����� ����ϸ� �ȵ� (������ ��ĭ�̾ ���ǹ�ȣ�� ������ ����PRG���� ����Ѵ�)
							//--->���������� Ÿ��Ʋ �ڰ��� �ִ°�? DATA�� 10�� �̻��̸� TIT�ڰ��� ���� 
							$idaum_tit_bit = 'N';
							$idaum_tit_cnt = 0;
							for ($i_ed=0;  $i_ed <= $col_cnt  ; $i_ed++ ) {  
									$t = $objWorksheet->getCell($EXCEL_all_col[$i_ed] . $idaum)->getValue();
									$t =	 iconv("UTF-8","EUC-KR",$t);    
									if(!empty($t) ) 	{
										$idaum_tit_cnt = $idaum_tit_cnt + 1;	
									}	
									if (str_replace(' ','',$t) =='��' || str_replace(' ','',$t) == '�հ�' ) {
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
							if (empty($tit2)   &&  $idaum_tit_bit == 'Y') {              // �������̸� Ÿ��Ʋ�� 2����.(�ڱ������ ���ǹ�ȣ ���������� ��ĭ�̰� DATA�� 10���̸��̸�) 
								$idaum_line = 2;
							}
						} 
						
						//--->Ÿ��Ʋ�� 2�����̰�   
						if ($idaum_line == 2) {
								if (!empty($tit) && !empty($tit2)) {
									$EXCEL_data_tit[$EXCEL_all_col[$ed]] = $tit2;         // 2���μ��� 
								}else if (!empty($tit2)) {
									$EXCEL_data_tit[$EXCEL_all_col[$ed]] = $tit2;         //2���μ��� 
								}else if (!empty($tit)) {
									$EXCEL_data_tit[$EXCEL_all_col[$ed]] = $tit;         //1���μ��� 
								}
						}
						//--->Ÿ��Ʋ 1���� ���� 
						if ($idaum_line == 1) {
								$EXCEL_data_tit[$EXCEL_all_col[$ed]] = $tit;         //�������� Ÿ��Ʋ���Ѵ�.
						}

						//-->���������� Ÿ��Ʋ ���������� ������ 
						if ( $maxCol  == $EXCEL_all_col[$ed] ) {
							 $yes_bit = 'Y';
							 break;
						}	
						//---���̾�����(45ĭ���� Ÿ��Ʋ�� ������ Ÿ��Ʋ�� �����Ѵ� 45ĭ���Ŀ� ��ĭ�ֽ�.)
						if (($tit  == "" || is_null($tit )) &&   $ed  <=35  && $ser_tit_yes  == 'N' ) {
							$space_col = $space_col + 1 ; 
							if ($space_col > 2) {    //Ÿ��Ʋ ������ 2������ ����ϰ� �ʰ��ϸ� �����۾��� �����Ѵ�. 
								$yes_bit = 'N';
								 break;				
							}		
						}				
				}
				if ($yes_bit == 'Y') {
					$tit_line   = $i ;  //Ÿ��Ʋ�� �ִ� ���ȣ 
					$EXCEL_data_tit['tit_line'] = $tit_line;         // ������ ���� �迭�� ��� �߰�
					break;	
				}	
		} //-->Ÿ��Ʋ�� ������ �� �ϴܶ� �Ѵ�.

		//======================================================================
		//-->Ÿ��Ʋ �ϼ��� ����ã��  
		//======================================================================
		$EXCEL_declare = array( // 1���� �迭�� 1�� ���� 2���� �迭 ����
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


 		//--->�ش翭�� Ÿ��Ʋ�� ���� ������ ã�´�.
		$b_Status = '';  //Ÿ��Ʋ ����ġ ���� 
		
		for ($db_i = 0; $db_i < count($ptData) ;$db_i++ ) {
				$b_Status = $b_Status.'<br> ������ :  '. $db_i.' : '. $ptData[$db_i]['CODE'] .'-' . $ptData[$db_i]['GUBUN'] .'-' . $ptData[$db_i]['GUBUNSUB'] .'-' . $ptData[$db_i]['GNAME'] . '  : '  ;

				$ex_i=0; 
				$ex_seq =1; 
				$ybit = 'N';
				unset( $EXCEL_declare ); //�迭�ʱ�ȭ 
	
				//print_r('*****==========START=========================*****'); 
				for ($i = 1 ; $i <= $col_cnt ; $i++) {		//2������ �����ͽ���
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
								/*
								print_r('-DB SETUP---->');
								print_r($EXCEL_declare[$ex_seq ][1]);
								print_r('=');
								print_r($EXCEL_data_tit[$EXCEL_declare[$ex_seq ][2]]);
								print_r('<---END-.');
								 */ 
								 //-->Ÿ��Ʋ ����ġ�� Ÿ��Ʋ error �����϶�� �񱳰�� ���Ͻ��� �� 
								$b_Status =  $b_Status. '-start-'.$EXCEL_declare[$ex_seq ][1].'='. $EXCEL_data_tit[$EXCEL_declare[$ex_seq ][2]] .'-end-';
							
								//-DB����Ÿ��Ʋ 									           	�� ��ƾ �������� Ÿ��Ʋ
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
					//�������� ���� ã��
					$EXCEL_data_tit['CODE'] = '' ;                 //�����
					$EXCEL_data_tit['GUBUN'] = '';              //������  (���,�Ա�,������)
					$EXCEL_data_tit['GUBUNSUB'] = '' ;      //���������� 
					$EXCEL_data_tit['YN'] = 'N' ;                   //����ã�� �������� 
				}
		}
		$EXCEL_data_tit['b_Status']  = $b_Status;  //Ÿ��Ʋ ����ġ error�����϶�� 
return  ($EXCEL_data_tit)  ;
} // code_special function end 
?>