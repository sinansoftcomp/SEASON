<?
require_once $_SERVER['DOCUMENT_ROOT']."/bin/include/lib/PHPExcel/Classes/PHPExcel.php"; 
require_once $_SERVER['DOCUMENT_ROOT']."/bin/include/lib/PHPExcel/Classes/PHPExcel/IOFactory.php";  // IOFactory.php을 불러와야 하며, 경로는 사용자의 설정에 맞게 수정해야 한다.

//=================================================================================================================//
//-------> 엑셀date필드 정규화(이 함수는 금액이 들어오면 안되며 필드가가 dtae가 확실 할 때만 들어온다   )
//=================================================================================================================//
function exc_date ($date){
				
		$date = preg_replace('/[^0-9]*/s', '',$date);													
		$date_yy =  substr($date,0,4); 
		$date_mm =  substr($date,4,2); 
		$date_dd =  substr($date,6,2); 								
		if (!checkdate( $date_mm, $date_dd, $date_yy)) {   //GET값이 일반DATE타입이 아니면 
			$date_conver  = PHPExcel_Style_NumberFormat :: toFormattedString($date , PHPExcel_Style_NumberFormat :: FORMAT_DATE_YYYYMMDD2);  // 엑셀date type--> y-m-d로 변환 
			if ($date_conver <= '1971-01-01'  ||  $date_conver >= '2210-01-01')  {
				$date = $date;   // 한계를 벗어나면  받아온 그대로 
			}else{
				$date = $date_conver; 
			}
		}
		$date = preg_replace('/[^0-9]*/s', '',$date);
		return $date;

} // code_special function end 

function isValidDate($date, $format = 'Y-m-d') {

	$date = preg_replace('/[^0-9]*/s', '',$date);

    // 문자열 길이 확인
    if (strlen($date) === 8) {
        return 1;
    } else {
        return 2;
    }
}

?>