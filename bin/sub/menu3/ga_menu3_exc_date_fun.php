<?
require_once $_SERVER['DOCUMENT_ROOT']."/bin/include/lib/PHPExcel/Classes/PHPExcel.php"; 
require_once $_SERVER['DOCUMENT_ROOT']."/bin/include/lib/PHPExcel/Classes/PHPExcel/IOFactory.php";  // IOFactory.php�� �ҷ��;� �ϸ�, ��δ� ������� ������ �°� �����ؾ� �Ѵ�.

//=================================================================================================================//
//-------> ����date�ʵ� ����ȭ(�� �Լ��� �ݾ��� ������ �ȵǸ� �ʵ尡�� dtae�� Ȯ�� �� ���� ���´�   )
//=================================================================================================================//
function exc_date ($date){
				
		$date = preg_replace('/[^0-9]*/s', '',$date);													
		$date_yy =  substr($date,0,4); 
		$date_mm =  substr($date,4,2); 
		$date_dd =  substr($date,6,2); 								
		if (!checkdate( $date_mm, $date_dd, $date_yy)) {   //GET���� �Ϲ�DATEŸ���� �ƴϸ� 
			$date_conver  = PHPExcel_Style_NumberFormat :: toFormattedString($date , PHPExcel_Style_NumberFormat :: FORMAT_DATE_YYYYMMDD2);  // ����date type--> y-m-d�� ��ȯ 
			if ($date_conver <= '1971-01-01'  ||  $date_conver >= '2210-01-01')  {
				$date = $date;   // �Ѱ踦 �����  �޾ƿ� �״�� 
			}else{
				$date = $date_conver; 
			}
		}
		$date = preg_replace('/[^0-9]*/s', '',$date);
		return $date;

} // code_special function end 

function isValidDate($date, $format = 'Y-m-d') {

	$date = preg_replace('/[^0-9]*/s', '',$date);

    // ���ڿ� ���� Ȯ��
    if (strlen($date) === 8) {
        return 1;
    } else {
        return 2;
    }
}

?>