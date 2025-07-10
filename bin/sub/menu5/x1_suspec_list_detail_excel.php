<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

require_once __DIR__.'/../../../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;

$objPHPExcel = new Spreadsheet();
$sheet = $objPHPExcel->getActiveSheet();

$objPHPExcel->getProperties()->setCreator("GAPLUS")
                             ->setLastModifiedBy("GAPLUS")
                             ->setTitle("GAPLUS")
                             ->setSubject("GAPLUS")
                             ->setDescription("GAPLUS")
                             ->setKeywords("GAPLUS")
                             ->setCategory("GAPLUS");


$sql= "
		select  case when c.kname = null then e.kname else c.kname end kname, 
				case when c.itemnm =null then e.itemnm else c.itemnm end itemnm,
				d.sname , b.name , a.kcode , a.mmcnt , a.mamt , a.hwanamt , a.suamt,a.sbit,a.inscode
		from sudet a  left outer join inssetup b on a.scode = b.scode and a.inscode = b.inscode
					left outer join ins_ipmst c on a.scode = c.scode and a.ipdate=c.ipdate and a.gubun=c.gubun and a.GUBUNSUB=c.GUBUNSUB and a.ino=c.ino and a.iseq=c.iseq
					left outer join swon d on a.scode = d.scode and a.skey = d.skey
					left outer join kwn e on a.scode = e.scode and a.kcode = e.kcode
		where a.scode = '".$_SESSION['S_SCODE']."' and a.yymm = '".$_GET['yymm']."' and a.skey = '".$_GET['skey']."' and a.sbit in('001', '002','003') and a.suamt <> 0 
		order by a.sbit , a.inscode
	";
$qry	= sqlsrv_query( $mscon, $sql );
$listData = array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listData[]	= $fet;
}

$sql= "
		select sum(a.mamt) mamt , sum(a.hwanamt) hwanamt , sum(a.suamt) suamt
		from sudet a  left outer join inssetup b on a.scode = b.scode and a.inscode = b.inscode
					left outer join kwn c on a.scode = c.scode and a.kcode = c.kcode
					left outer join swon d on a.scode = d.scode and a.skey = d.skey
		where a.scode = '".$_SESSION['S_SCODE']."' and a.yymm = '".$_GET['yymm']."' and a.skey = '".$_GET['skey']."' and a.sbit in('001', '002','003') and a.suamt <> 0 
	";
$qry	= sqlsrv_query( $mscon, $sql );
$listData_tot = array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listData_tot[]	= $fet;
}

sqlsrv_free_stmt($result);
sqlsrv_close($mscon);



//------------------------------------------------------------------------------//
//								엑셀작업 시작										//
//------------------------------------------------------------------------------//


//--------------------------------타이틀셋팅
$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A1', iconv("EUCKR","UTF-8",'계약자'))
    ->setCellValue('B1', iconv("EUCKR","UTF-8",'사원명'))
    ->setCellValue('C1', iconv("EUCKR","UTF-8",'수수료명'))
    ->setCellValue('D1', iconv("EUCKR","UTF-8",'제휴사명'))
	->setCellValue('E1', iconv("EUCKR","UTF-8",'증권번호'))
	->setCellValue('F1', iconv("EUCKR","UTF-8",'상품명'))
	->setCellValue('G1', iconv("EUCKR","UTF-8",'회차'))
	->setCellValue('H1', iconv("EUCKR","UTF-8",'납입보험료'))
	->setCellValue('I1', iconv("EUCKR","UTF-8",'환산보험료'))
	->setCellValue('J1', iconv("EUCKR","UTF-8",'지급액'));

//----------------------------------------


//----------------------------------------시트명 셋팅
$sheetTitle = iconv("EUCKR", "UTF-8", '설계사별급여명세서');
$sheetTitle = substr($sheetTitle, 0, 31); // Truncate to 31 characters
$invalidCharacters = array('\\', '/', '?', '*', '[', ']', ':');
$sheetTitle = str_replace($invalidCharacters, '', $sheetTitle);
$objPHPExcel->getActiveSheet()->setTitle($sheetTitle);
//----------------------------------------


//--------------------------------합계부분 셋팅

$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('G2', iconv("EUCKR","UTF-8",'합계'))
    ->setCellValue('H2', number_format($listData_tot[0]['mamt']))
	->setCellValue('I2', number_format($listData_tot[0]['hwanamt']))
	->setCellValue('J2', number_format($listData_tot[0]['suamt']));
//----------------------------------------


//----------------------------------------내용셋팅
foreach($listData as $key => $val){
	extract($val);

	//$yymm	= (trim($yymm))	? date("Y-m",strtotime($yymm."01")) : "";
	if($sbit=="001"){
		$sbit = "신계약수수료";
	}else if($sbit=="002") {
		$sbit = "유지수수료";
	}else if($sbit=="003") {
		$sbit = "오버라이딩";
	}

	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.($key+3), iconv("EUCKR","UTF-8",$kname))
			->setCellValueExplicit("B".($key+3), iconv("EUCKR","UTF-8",$sname),\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING)
            ->setCellValue('C'.($key+3), iconv("EUCKR","UTF-8",$sbit))
            ->setCellValue('D'.($key+3), iconv("EUCKR","UTF-8",$name))
			->setCellValueExplicit("E".($key+3), iconv("EUCKR","UTF-8",$kcode),\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING)
			->setCellValue('F'.($key+3), iconv("EUCKR","UTF-8",$itemnm))
			->setCellValue('G'.($key+3), number_format($mmcnt))
			->setCellValue('H'.($key+3), number_format($mamt))
			->setCellValue('I'.($key+3), number_format($hwanamt))
			->setCellValue('J'.($key+3), number_format($suamt));

			$objPHPExcel->getActiveSheet()->getStyle('G'.($key+3))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('H'.($key+3))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('I'.($key+3))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('J'.($key+3))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);


}
//---------------------------------------------

//---------------------------------------------열 너비셋팅
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(80);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);

//---------------------------------------------


$objPHPExcel->getActiveSheet()->getStyle('A1:J1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFC7C7C7');
$objPHPExcel->getActiveSheet()->getStyle('A1:J1')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK);
$objPHPExcel->getActiveSheet()->getStyle('A1:J1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A1:J1')->getFont()->setBold(true);

$objPHPExcel->getActiveSheet()->getStyle('A2:J2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFAECDC');
$objPHPExcel->getActiveSheet()->getStyle('A2:J2')->getFont()->getColor()->setARGB('FFF15F5F');
$objPHPExcel->getActiveSheet()->getStyle('A2:J2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->getStyle('A2:J2')->getFont()->setBold(true);

//$objPHPExcel->getActiveSheet()->getStyle('D1')->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);		// 셀 오른쪽에 테두리


$filename = iconv("EUCKR","UTF-8","설계사별급여명세서_".$_GET['yymm']);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="'.$filename.'.xls"');


$writer = new Xls($objPHPExcel);
$writer->save('php://output');
exit;

?>
