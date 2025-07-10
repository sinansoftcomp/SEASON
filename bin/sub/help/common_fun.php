<?
	//=================================================================================================================//
	//-------> 필드별 상세 처리 
	//=================================================================================================================//
	function date_proc ($date_arr){
	

			//-->월말일 
			$day_count = date('t', strtotime("2010-09-01"));
			//echo  $day_count;
			//-->전달 
			$junmm = date('Y-m-t', strtotime('-1 month', strtotime(date('Y-m-d'))));
			echo $junmm.'<br>';
			//-->다음달 
			$hhmm = date('Y-m-t', strtotime('+1 month', strtotime(date('Y-m-d'))));
			echo $hhmm;

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
 
			return $date_arr;

	} // code_special function end 
	
	
?>