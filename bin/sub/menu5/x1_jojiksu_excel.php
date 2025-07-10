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
use PhpOffice\PhpSpreadsheet\Style\Protection;

$objPHPExcel = new Spreadsheet();
$sheet = $objPHPExcel->getActiveSheet();

$objPHPExcel->getProperties()->setCreator("GAPLUS")
                             ->setLastModifiedBy("GAPLUS")
                             ->setTitle("GAPLUS")
                             ->setSubject("GAPLUS")
                             ->setDescription("GAPLUS")
                             ->setKeywords("GAPLUS")
                             ->setCategory("GAPLUS");


$fyymmdd=  substr($_GET['SDATE1'],0,4).substr($_GET['SDATE1'],5,2); 

$where = "";

if($_REQUEST['id']){
	
	$Ngubun = substr($_REQUEST['id'],0,2);

	if($Ngubun == 'N1'){
		$where  .= "" ;
	}else if($Ngubun == 'N2'){
		$inscode = substr($_REQUEST['id'],2,10);
		$where  .= " and a.inscode = '".$inscode."' " ;
	}
}

if($_REQUEST['nmyn']){
	if($_REQUEST['nmyn'] == 'Y'){
		$where .= " and isnull(a.ksman,'') <> '' ";
	}else if($_REQUEST['nmyn'] == 'N'){
		$where .= " and isnull(a.ksman,'') = '' ";
	}
}

$sql= "
		select a.yymm,a.kcode,a.inscode,b.name,a.item,a.itemnm,a.ksman,
				c.skey,c.sname , e.bname , f.jsname , g.jname , h.tname,row_number() over(order by a.inscode,a.kcode) rnum
		from ins_ipmst a left outer join inssetup b on a.scode = b.scode and a.inscode = b.inscode and b.useyn = 'Y'
							left outer join inswon cc on a.scode = cc.scode and a.ksman = cc.bscode
							left outer join swon c on a.scode = c.scode and cc.skey = c.skey
							left outer join bonbu e on c.scode = e.scode and c.bonbu = e.bcode
							left outer join jisa  f on c.scode = f.scode and c.jisa = f.jscode
							left outer join jijum g on c.scode = g.scode and c.jijum = g.jcode
							left outer join team h  on c.scode = h.scode and c.team = h.tcode
		where a.scode = '".$_SESSION['S_SCODE']."' and a.yymm = '".$fyymmdd."' and isnull(a.nmgubun,'') = 'Y' $where
		group by a.yymm,a.kcode,a.inscode,b.name,a.item,a.itemnm,a.ksman,c.skey,c.sname , e.bname , f.jsname , g.jname , h.tname
	 ";

$qry	= sqlsrv_query( $mscon, $sql );
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listData[]	= $fet;
}
/*
echo "<pre>";
echo $sql;
echo "</pre>";
*/
 // 데이터 총 건수
 //검색 데이터 구하기 
$sql= "

	select count(*) CNT
	from(
		select a.yymm,a.kcode,a.inscode,b.name,a.item,a.itemnm,a.ksman
		from ins_ipmst a left outer join inssetup b on a.scode = b.scode and a.inscode = b.inscode and b.useyn = 'Y'
		where a.scode = '".$_SESSION['S_SCODE']."' and a.yymm = '".$fyymmdd."' and isnull(a.nmgubun,'') = 'Y' $where
		group by a.yymm,a.kcode,a.inscode,b.name,a.item,a.itemnm,a.ksman
		) aa
	" ;

$qry = sqlsrv_query( $mscon, $sql );
$totalResult  = sqlsrv_fetch_array($qry);


//------------------------------------------------------------------------------//
//								엑셀작업 시작										//
//------------------------------------------------------------------------------//


//--------------------------------타이틀셋팅
$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A1', iconv("EUCKR","UTF-8",'정산월'))
    ->setCellValue('B1', iconv("EUCKR","UTF-8",'증권번호'))
    ->setCellValue('C1', iconv("EUCKR","UTF-8",'사용인코드'))
    ->setCellValue('D1', iconv("EUCKR","UTF-8",'보험사명'))
    ->setCellValue('E1', iconv("EUCKR","UTF-8",'상품코드'))
    ->setCellValue('F1', iconv("EUCKR","UTF-8",'상품명'));
//----------------------------------------


//----------------------------------------시트명 셋팅
$sheetTitle = iconv("EUCKR", "UTF-8", '비매칭사원리스트');
$sheetTitle = substr($sheetTitle, 0, 31); // Truncate to 31 characters
$invalidCharacters = array('\\', '/', '?', '*', '[', ']', ':');
$sheetTitle = str_replace($invalidCharacters, '', $sheetTitle);
$objPHPExcel->getActiveSheet()->setTitle($sheetTitle);
//----------------------------------------



//----------------------------------------내용셋팅
foreach($listData as $key => $val){
	extract($val);

	$yymm	= (trim($yymm))	? date("Y-m",strtotime($yymm."01")) : "";

	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.($key+2), iconv("EUCKR","UTF-8",$yymm))
			->setCellValueExplicit("B".($key+2), iconv("EUCKR","UTF-8",$kcode),\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING)
			->setCellValue('C'.($key+2), iconv("EUCKR","UTF-8",$ksman))
            ->setCellValue('D'.($key+2), iconv("EUCKR","UTF-8",$name))
            ->setCellValueExplicit("E".($key+2), iconv("EUCKR","UTF-8",$item),\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING)
			->setCellValue('F'.($key+2), iconv("EUCKR","UTF-8",$itemnm));
			
			if($key+2 == 2){
				$objPHPExcel->getActiveSheet()->getStyle('C'.($key+2))->getBorders()->getTop()->setBorderStyle(Border::BORDER_THICK);
			}
			if($key+2 == $totalResult['CNT']+1){
				$objPHPExcel->getActiveSheet()->getStyle('C'.($key+2))->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THICK);
			}


			$objPHPExcel->getActiveSheet()->getStyle('C'.($key+2))->getBorders()->getRight()->setBorderStyle(Border::BORDER_THICK);		// 셀 오른쪽에 테두리
			$objPHPExcel->getActiveSheet()->getStyle('C'.($key+2))->getBorders()->getLeft()->setBorderStyle(Border::BORDER_THICK);		// 셀 왼쪽에 테두리

}
//---------------------------------------------


//---------------------------------------------열 너비셋팅
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(60);

$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(20);
//---------------------------------------------
$objPHPExcel->getActiveSheet()->getStyle('A1:F1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFC7C7C7');
$objPHPExcel->getActiveSheet()->getStyle('A1:F1')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK);
$objPHPExcel->getActiveSheet()->getStyle('A1:F1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A1:F1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A1:F1')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A2:B'.(string)($totalResult['CNT']+1))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFEFEFEF');
$objPHPExcel->getActiveSheet()->getStyle('D2:F'.(string)($totalResult['CNT']+1))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFEFEFEF');
//---------------------------------------------사용인코드부분빼고 수정못하게 막기
$objPHPExcel->getActiveSheet()->getStyle('A1:C1')->getProtection()->setLocked(Protection::PROTECTION_PROTECTED);
$objPHPExcel->getActiveSheet()->getStyle('A1:F1')->getProtection()->setLocked(Protection::PROTECTION_PROTECTED);
$objPHPExcel->getActiveSheet()->getStyle('C2:C'.(string)($totalResult['CNT']+1))->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
$objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);
//---------------------------------------------

$filename = iconv("EUCKR","UTF-8","비매칭사원리스트".$fyymmdd);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="'.$filename.'.xls"');


$writer = new Xls($objPHPExcel);
$writer->save('php://output');
exit;
?>
