<?
	//=================================================================================================================//
	//-------> �ʵ庰 �� ó�� 
	//=================================================================================================================//
	function date_proc ($date_arr){
	

			//-->������ 
			$day_count = date('t', strtotime("2010-09-01"));
			//echo  $day_count;
			//-->���� 
			$junmm = date('Y-m-t', strtotime('-1 month', strtotime(date('Y-m-d'))));
			echo $junmm.'<br>';
			//-->������ 
			$hhmm = date('Y-m-t', strtotime('+1 month', strtotime(date('Y-m-d'))));
			echo $hhmm;

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
 
			return $date_arr;

	} // code_special function end 
	
	
?>