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





$FYYMM   = substr($_REQUEST['SDATE1'],0,4).substr($_REQUEST['SDATE1'],5,2);
$TYYMM  =  substr($_REQUEST['SDATE2'],0,4).substr($_REQUEST['SDATE2'],5,2);

$where = "";

// 조직도 트리 선택시 소속정보(swon 별칭 : s2 - kdman(사용인기준)) 
if($_REQUEST['id']){
	
	$Ngubun = substr($_REQUEST['id'],0,2);

	if($Ngubun == 'N1'){
		$bonbu = substr($_REQUEST['id'],2,10);
		$where  .= " and e.bcode = '".$bonbu."' " ;
	}else if($Ngubun == 'N2'){
		$jisa = substr($_REQUEST['id'],2,10);
		$where  .= " and f.jscode = '".$jisa."' " ;
	}else if($Ngubun == 'N3'){
		$jijum = substr($_REQUEST['id'],2,10);
		$where  .= " and g.jcode = '".$jijum."' " ;
	}else if($Ngubun == 'N4'){
		$team = substr($_REQUEST['id'],2,10);
		$where  .= " and h.tcode = '".$team."' " ;
	}else if($Ngubun == 'N5'){
		$ksman = substr($_REQUEST['id'],2,10);
		$where  .= " and c.skey = '".$ksman."' " ;
	}
}
/* ------------------------------------------------------
	년도 / 검색일자 / 월 조회값 생성 End
------------------------------------------------------ */


$sql= "
		select b.NAME
		from sudet a left outer join inssetup b on a.scode = b.scode and a.inscode = b.inscode
					left outer join swon c on a.scode = c.scode and a.skey = c.skey
					left outer join bonbu e on c.scode = e.scode and c.bonbu = e.bcode
					left outer join jisa  f on c.scode = f.scode and c.jisa = f.jscode
					left outer join jijum g on c.scode = g.scode and c.jijum = g.jcode
					left outer join team h  on c.scode = h.scode and c.team = h.tcode	
		where a.SCODE =  '".$_SESSION['S_SCODE']."'  and  a.YYMM >= '".$FYYMM."'  and   a.YYMM <= '".$TYYMM."' $where
		group by a.inscode,b.name
		order by b.name
		 " ;

$qry= sqlsrv_query( $mscon, $sql );
$titList[]	=array();
$instit="";
$instit_cnt = 0; 
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$titList[]	= $fet['NAME'];
	$instit =   $instit.'['.$fet['NAME'].']';
	$instit_cnt =  $instit_cnt + 1;    //보험사 타이틀 갯수,  나중에 타이틀은 루핑 돌라고 
}

$instit = str_replace("][","],[",$instit); //--->타이틀을 sql 	PVT(크로스탭에서 사용한다) 	--타이틀 보험사가 동적이다. 


$select_sum = "";
for($i=1; $i<=$instit_cnt; $i++){
	$select_sum .= "SUM(CASE WHEN insilj = '1' AND insname = '".$titList[$i]."' THEN suamt ELSE 0 END) AS '".$titList[$i]."_일반', ";
	$select_sum .= "SUM(CASE WHEN insilj = '2' AND insname = '".$titList[$i]."' THEN suamt ELSE 0 END) AS '".$titList[$i]."_장기', ";
	$select_sum .= "SUM(CASE WHEN insilj = '3' AND insname = '".$titList[$i]."' THEN suamt ELSE 0 END) AS '".$titList[$i]."_자동차', ";
}



$sql = "

	WITH PivotData AS (
		SELECT 
			a.scode, a.yymm, a.skey, c.sname, a.inscode, b.name AS insname, 
			e.bname, f.jsname, g.jname, h.tname, a.insilj, a.suamt, 
			e.num AS bnum, f.num AS jsnum, g.num AS jnum, h.num AS tnum, c.jik,
			CASE 
				WHEN tbit = '1' THEN '1' 
				WHEN tbit = '2' THEN '3' 
				WHEN tbit = '3' THEN '2' 
				WHEN tbit = '4' THEN '4' 
			END AS tbit
		FROM sudet a
		LEFT JOIN inssetup b ON a.scode = b.scode AND a.inscode = b.inscode
		LEFT JOIN swon c ON a.scode = c.scode AND a.skey = c.skey
		LEFT JOIN bonbu e ON c.scode = e.scode AND c.bonbu = e.bcode
		LEFT JOIN jisa f ON c.scode = f.scode AND c.jisa = f.jscode
		LEFT JOIN jijum g ON c.scode = g.scode AND c.jijum = g.jcode
		LEFT JOIN team h ON c.scode = h.scode AND c.team = h.tcode
		WHERE a.SCODE = '".$_SESSION['S_SCODE']."' AND a.YYMM >= '".$FYYMM."' AND a.YYMM <= '".$TYYMM."' $where
	),
	Pivoted AS (
		SELECT 
			scode, yymm, skey, sname, bname, jsname, jname, tname, 
			".$select_sum."
			ROW_NUMBER() OVER (ORDER BY yymm DESC, bnum, jsnum, jnum, tnum, jik DESC, tbit, skey) AS rnum
		FROM PivotData
		GROUP BY scode, yymm, skey, sname, bname, jsname, jname, tname, bnum, jsnum, jnum, tnum, jik, tbit
	),
	TotalAmount AS (
		SELECT 
			a.scode, a.yymm, a.skey, SUM(a.suamt) AS totsuamt
		FROM sudet a
				LEFT JOIN inssetup b ON a.scode = b.scode AND a.inscode = b.inscode
				LEFT JOIN swon c ON a.scode = c.scode AND a.skey = c.skey
				LEFT JOIN bonbu e ON c.scode = e.scode AND c.bonbu = e.bcode
				LEFT JOIN jisa f ON c.scode = f.scode AND c.jisa = f.jscode
				LEFT JOIN jijum g ON c.scode = g.scode AND c.jijum = g.jcode
				LEFT JOIN team h ON c.scode = h.scode AND c.team = h.tcode
		WHERE a.SCODE = '".$_SESSION['S_SCODE']."' AND a.YYMM >= '".$FYYMM."' AND a.YYMM <= '".$TYYMM."' $where
		GROUP BY a.scode, a.yymm, a.skey
	)
	SELECT p.*
	FROM (
		SELECT aa.*, bb.totsuamt
		FROM Pivoted aa
		LEFT JOIN TotalAmount bb ON aa.scode = bb.scode AND aa.yymm = bb.yymm AND aa.skey = bb.skey
	) p
	ORDER BY rnum;
		
";
/*
echo "<pre>";
echo $sql;
echo "</pre>";
*/
$qry	= sqlsrv_query( $mscon, $sql );
$listData = array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listData[]	= $fet;
}

//--->수수입수수료 보험사를 타이틀로 구성하기위한 해당월의 보험사명 순서별로 리턴이 필요함 합계필드 일치하기위함  ORDER BY D.NUM 
$sql ="
		SELECT  b.NAME,
				sum(suamt) catotal
		from sudet a left outer join inssetup b on a.scode = b.scode and a.inscode = b.inscode
					left outer join swon c on a.scode = c.scode and a.skey = c.skey
					left outer join bonbu e on c.scode = e.scode and c.bonbu = e.bcode
					left outer join jisa  f on c.scode = f.scode and c.jisa = f.jscode
					left outer join jijum g on c.scode = g.scode and c.jijum = g.jcode
					left outer join team h  on c.scode = h.scode and c.team = h.tcode	
		where a.SCODE =  '".$_SESSION['S_SCODE']."'   and  a.YYMM >= '".$FYYMM."'  and   a.YYMM <= '".$TYYMM."' $where
		group by   b.NAME, b.NUM 
		ORDER BY b.NAME
		";
$qry	= sqlsrv_query( $mscon, $sql );
$listinsTot = array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listinsTot[]	= $fet;
}

$listinsTot_tot = 0; 
for($i = 0; $i <  $instit_cnt ; $i++) {
	$listinsTot_tot = $listinsTot_tot +$listinsTot[$i]['catotal']  ; 
} 

// 데이터 총 건수
//검색 데이터 구하기 
$sql= "
		select count(*) CNT
		from(
			select scode,yymm,skey
			from sudet a
			where a.SCODE =  '".$_SESSION['S_SCODE']."'   and  a.YYMM >= '".$FYYMM."'  and   a.YYMM <= '".$TYYMM."'
			group by scode,yymm,skey
			) aa
		  " ;

$qry =  sqlsrv_query($mscon, $sql);
$totalResult =  sqlsrv_fetch_array($qry); 



//------------------------------------------------------------------------------//
//								엑셀작업 시작										//
//------------------------------------------------------------------------------//


//--------------------------------타이틀셋팅
$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A1', iconv("EUCKR","UTF-8",'정산월'))
    ->setCellValue('B1', iconv("EUCKR","UTF-8",'사원'))
    ->setCellValue('C1', iconv("EUCKR","UTF-8",'사원명'))
    ->setCellValue('D1', iconv("EUCKR","UTF-8",'소속'))
    ->setCellValue('E1', iconv("EUCKR","UTF-8",'수수료합계'));

$objPHPExcel->getActiveSheet()->mergeCells('A1:A2');
$objPHPExcel->getActiveSheet()->mergeCells('B1:B2');
$objPHPExcel->getActiveSheet()->mergeCells('C1:C2');
$objPHPExcel->getActiveSheet()->mergeCells('D1:D2');
$objPHPExcel->getActiveSheet()->mergeCells('E1:E2');

// 셀 오른쪽에 테두리
$objPHPExcel->getActiveSheet()->getStyle('E1:E2')->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);

$a = 'F';
for($i = 1; $i <= $instit_cnt; $i++){
    $objPHPExcel->getActiveSheet()->setCellValue($a.'2', iconv("EUCKR","UTF-8","일반"));
	$as = $a;
    $a++;
    $objPHPExcel->getActiveSheet()->setCellValue($a.'2', iconv("EUCKR","UTF-8","장기"));
    $a++;
    $objPHPExcel->getActiveSheet()->setCellValue($a.'2', iconv("EUCKR","UTF-8","자동차"));
	$objPHPExcel->getActiveSheet()->getStyle($a.'1'.':'.$a.'2')->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);		// 셀 오른쪽에 테두리
	$al = $a;
    $a++;

	$objPHPExcel->getActiveSheet()->setCellValue($as.'1', iconv("EUCKR","UTF-8",$titList[$i]));
	$objPHPExcel->getActiveSheet()->mergeCells($as.'1'.':'.$al.'1');
}
//----------------------------------------


//----------------------------------------시트명 셋팅
$sheetTitle = iconv("EUCKR", "UTF-8", '설계사별수수료(사원별)');
$sheetTitle = substr($sheetTitle, 0, 31); // Truncate to 31 characters
$invalidCharacters = array('\\', '/', '?', '*', '[', ']', ':');
$sheetTitle = str_replace($invalidCharacters, '', $sheetTitle);
$objPHPExcel->getActiveSheet()->setTitle($sheetTitle);
//----------------------------------------


//--------------------------------합계부분 셋팅

$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('D3', iconv("EUCKR","UTF-8",'합계'))
    ->setCellValue('E3', number_format($listinsTot_tot));

$objPHPExcel->getActiveSheet()->getStyle('E3')->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);		// 셀 오른쪽에 테두리

$a = 'F';
for($i = 1; $i <= $instit_cnt; $i++){
	$as = $a;
    $a++;
    $a++;
	$al = $a;
    $a++;

	$objPHPExcel->getActiveSheet()->getStyle($al.'3')->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);		// 셀 오른쪽에 테두리
	$objPHPExcel->getActiveSheet()->setCellValue($as.'3', number_format($listinsTot[$i-1]['catotal']));
	$objPHPExcel->getActiveSheet()->mergeCells($as.'3'.':'.$al.'3');
}

//----------------------------------------


//----------------------------------------내용셋팅
foreach($listData as $key => $val){
	extract($val);

	$yymm	= (trim($yymm))	? date("Y-m",strtotime($yymm."01")) : "";
	$sosok = substr($bname,0,4).'>'. substr($jsname,0,4).'>'. substr($jname,0,4).'>'. substr($tname,0,4);
	$sosok = str_replace('>>','>',$sosok);
	$sosok = str_replace('>>','>',$sosok);
	$totsuamt	= ($totsuamt) ? number_format($totsuamt) : "0";

	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.($key+4), iconv("EUCKR","UTF-8",$yymm))
            //->setCellValue('B'.($key+4), iconv("EUCKR","UTF-8",$skey))
			->setCellValueExplicit("B".($key+4), iconv("EUCKR","UTF-8",$skey),\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING)
            ->setCellValue('C'.($key+4), iconv("EUCKR","UTF-8",$sname))
            ->setCellValue('D'.($key+4), iconv("EUCKR","UTF-8",$sosok))
			->setCellValue('E'.($key+4), iconv("EUCKR","UTF-8",$totsuamt));

			// 셀 오른쪽 정렬
			$objPHPExcel->getActiveSheet()->getStyle('E'.($key+4))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
			// 셀 오른쪽에 테두리
			$objPHPExcel->getActiveSheet()->getStyle('E'.($key+4))->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);

	$a = 'F';
	for($i = 1; $i <= $instit_cnt; $i++){
		$objPHPExcel->getActiveSheet()->setCellValue($a.($key+4), number_format($listData[$key][$titList[$i].'_일반']));
		$objPHPExcel->getActiveSheet()->getStyle($a.($key+4))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
		$a++;
		$objPHPExcel->getActiveSheet()->setCellValue($a.($key+4), number_format($listData[$key][$titList[$i].'_장기']));
		$objPHPExcel->getActiveSheet()->getStyle($a.($key+4))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
		$a++;
		$objPHPExcel->getActiveSheet()->setCellValue($a.($key+4), number_format($listData[$key][$titList[$i].'_자동차']));
		$objPHPExcel->getActiveSheet()->getStyle($a.($key+4))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle($a.($key+4))->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);	// 셀 오른쪽에 테두리
		$a++;
	}
}
//---------------------------------------------

//---------------------------------------------열 너비셋팅
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);

$a = 'F';
for($i = 1; $i <= $instit_cnt; $i++){
	$objPHPExcel->getActiveSheet()->getColumnDimension($a)->setWidth(12);
	$a++;
	$objPHPExcel->getActiveSheet()->getColumnDimension($a)->setWidth(12);
	$a++;
	$objPHPExcel->getActiveSheet()->getColumnDimension($a)->setWidth(12);
	if($i == $instit_cnt){
		$lasth = $a;
	}
	$a++;
}
//---------------------------------------------


$objPHPExcel->getActiveSheet()->getStyle('A1:'.$lasth.'1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFC7C7C7');
$objPHPExcel->getActiveSheet()->getStyle('A1:'.$lasth.'1')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK);
$objPHPExcel->getActiveSheet()->getStyle('A1:'.$lasth.'1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A1:'.$lasth.'1')->getFont()->setBold(true);

$objPHPExcel->getActiveSheet()->getStyle('A2:'.$lasth.'2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFC7C7C7');
$objPHPExcel->getActiveSheet()->getStyle('A2:'.$lasth.'2')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK);
$objPHPExcel->getActiveSheet()->getStyle('A2:'.$lasth.'2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A2:'.$lasth.'2')->getFont()->setBold(true);

$objPHPExcel->getActiveSheet()->getStyle('A3:'.$lasth.'3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFAECDC');
$objPHPExcel->getActiveSheet()->getStyle('A3:'.$lasth.'3')->getFont()->getColor()->setARGB('FFF15F5F');
$objPHPExcel->getActiveSheet()->getStyle('A3:'.$lasth.'3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->getStyle('A3:'.$lasth.'3')->getFont()->setBold(true);

$filename = iconv("EUCKR","UTF-8","설계사별수수료".$FYYMM."-".$TYYMM);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="'.$filename.'.xls"');


$writer = new Xls($objPHPExcel);
$writer->save('php://output');

?>
