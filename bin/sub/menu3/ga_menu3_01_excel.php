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


$sdate1	= $_REQUEST['SDATE1'];
$sdate2	= $_REQUEST['SDATE2'];

$where = "";

// 조직도 트리 선택시 소속정보(swon 별칭 : s2 - kdman(사용인기준)) 
if($_REQUEST['id']){
	
	$Ngubun = substr($_REQUEST['id'],0,2);

	if($Ngubun == 'N1'){
		$bonbu = substr($_REQUEST['id'],2,10);
		$where  .= " and s2.bonbu = '".$bonbu."' " ;
	}else if($Ngubun == 'N2'){
		$jisa = substr($_REQUEST['id'],2,10);
		$where  .= " and s2.jisa = '".$jisa."' " ;
	}else if($Ngubun == 'N3'){
		$jijum = substr($_REQUEST['id'],2,10);
		$where  .= " and s2.jijum = '".$jijum."' " ;
	}else if($Ngubun == 'N4'){
		$team = substr($_REQUEST['id'],2,10);
		$where  .= " and s2.team = '".$team."' " ;
	}else if($Ngubun == 'N5'){
		$ksman = substr($_REQUEST['id'],2,10);
	}
}

// 인코딩변환(조회시 post처리 / 페이징은 get처리하다보니 한글인코딩 변환이 상황에 따라 변환..리퀘스트 처리 아니면 이렇게할 필요x)
if($_REQUEST['pageyn'] == 'Y'){
	$searchF1Text = $_REQUEST['searchF1Text'];
}else{
	$searchF1Text = iconv("UTF-8","EUCKR",$_REQUEST['searchF1Text']);
}

if($_REQUEST['searchF1'] && $_REQUEST['searchF1Text']){
	if($_REQUEST['searchF1'] == 'tel'){
		$where  .= " and (a.tel like replace('%".$_REQUEST['searchF1Text']."%','-','') or a.htel like replace('%".$_REQUEST['searchF1Text']."%','-','')) ";
	}else if($_REQUEST['skey'] && $_REQUEST['searchF1'] == 's1'){	//	모집사원
		$where  .= " and a.gskey = '".$_REQUEST['skey']."' ";	
	}else if($_REQUEST['skey'] && $_REQUEST['searchF1'] == 's2'){	//	사용인
		$where  .= " and a.kskey = '".$_REQUEST['skey']."' ";	
	}else{		
		$where  .= " and ".$_REQUEST['searchF1']." like '%".(string)$searchF1Text."%' ";	
	}
}

// 보험사
if($_REQUEST['inscode']){
	$where  .= " and a.inscode = '".$_REQUEST['inscode']."' " ;
}


// 상품군
if($_REQUEST['insilj']){
	$where  .= " and a.insilj = '".$_REQUEST['insilj']."' " ;
}

// 계약상태(조회시 post처리 / 페이징은 get처리하다보니 한글인코딩 변환이 상황에 따라 변환..리퀘스트 처리 아니면 이렇게할 필요x)
if($_REQUEST['pageyn'] == 'Y'){
	$kstbit = $_REQUEST['kstbit'];
}else{
	$kstbit = iconv("UTF-8","EUCKR",$_REQUEST['kstbit']);
}
if($_REQUEST['kstbit']){
	$where  .= " and replace(a.kstbit,' ','') = '".$kstbit."' " ;
}

//검색 데이터 구하기 
$sql= "
		select 
				a.kcode,
				a.insilj,
				a.inscode,
				f.name insname,
				case when isnull(s2.bonbu,'') != '' then substring(b.bname,1,2) else '' end +
				case when isnull(s2.bonbu,'') != '' and (isnull(s2.jisa,'') != '' or isnull(s2.team,'') != '')  then ' > ' else '' end +
				case when isnull(s2.jisa,'') != '' then substring(c.jsname,1,2) else '' end +
				case when isnull(s2.jisa,'') != '' and isnull(s2.jijum,'') != '' then ' > ' else '' end +
				case when isnull(s2.jijum,'') != '' then substring(d.jname,1,4) else '' end +
				case when isnull(s2.jijum,'') != '' and isnull(s2.team,'') != '' then ' > ' else '' end +
				case when isnull(s2.team,'') != '' then e.tname else '' end as sosok,
				a.ksman,
				a.kdman,
				s1.sname gskey_nm,
				s2.sname kskey_nm,
				dbo.GetCutStr(s1.sname,10,'..') gskey_Cnm,
				dbo.GetCutStr(s2.sname,10,'..') kskey_Cnm,
				a.kname,
				dbo.GetCutStr(a.kname,16,'..') kname_c,
				dbo.GetCutStr(a.pname,16,'..') pname_c,
				case when isnull(a.htel,'') != '' then a.htel else a.tel end telno,
				a.addr+' '+a.addr_dt addr,
				a.pname,
				a.kdate,
				a.fdate,
				a.tdate,
				a.item,
				a.itemnm,
				a.mamt,
				a.hamt,
				a.samt,
				a.kstbit,
				a.nbit,
				a.nterm,
				i.ipdate mx_ipdate,
				isnull(i.ncnt,0) mx_ncnt,
				i.istbit,
				a.agency,

				case when isnull(a.htel,'') <> '' and len(isnull(a.htel,'')) >= 10 and substring(isnull(a.htel,''),1,2) = '01'
								then 'Y' else 'N' end smsyn ,

				row_number()over(order by a.kdate desc, f.name, a.kname) rnum
		from kwn(nolock) a	
			left outer join inssetup(nolock) f on a.scode = f.scode and a.inscode = f.inscode
			left outer join inswon(nolock) is1 on a.scode = is1.scode and a.ksman = is1.bscode
			left outer join inswon(nolock) is2 on a.scode = is2.scode and a.kdman = is2.bscode
			left outer join swon(nolock) s1 on s1.scode = a.scode and s1.skey = is1.skey
			left outer join swon(nolock) s2 on s2.scode = a.scode and s2.skey = is2.skey
			left outer join bonbu(nolock) b on s2.scode = b.scode and s2.bonbu = b.bcode
			left outer join jisa(nolock) c on s2.scode = c.scode and s2.jisa = c.jscode
			left outer join jijum(nolock) d on s2.scode = d.scode and s2.jijum = d.jcode
			left outer join team(nolock) e on s2.scode = e.scode and s2.team = e.tcode
			left outer join (select *
							 from (select row_number()over(partition by kcode order by ipdate desc, ino desc, ncnt desc) num, *  from INS_SUNAB(nolock) where scode = '".$_SESSION['S_SCODE']."' ) tbl
							 where tbl.num = 1) i on a.inscode = i.inscode and  a.kcode = i.kcode
		where a.scode = '".$_SESSION['S_SCODE']."'
		  and a.kdate between '".str_replace('-','',$sdate1)."' and '".str_replace('-','',$sdate2)."'  ".$where;

$qry	= sqlsrv_query( $mscon, $sql );
$listData = array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listData[]	= $fet;
}

// 데이터 총 건수
//검색 데이터 구하기 
$sql= "
		select 
				count(*) CNT,
				sum(mamt) sum_mamt,
				sum(samt) sum_samt
		from kwn(nolock) a
			left outer join inssetup(nolock) f on a.scode = f.scode and a.inscode = f.inscode
			left outer join inswon(nolock) is1 on a.scode = is1.scode and a.ksman = is1.bscode
			left outer join inswon(nolock) is2 on a.scode = is2.scode and a.kdman = is2.bscode
			left outer join swon(nolock) s1 on s1.scode = a.scode and s1.skey = is1.skey
			left outer join swon(nolock) s2 on s2.scode = a.scode and s2.skey = is2.skey
			left outer join bonbu(nolock) b on s2.scode = b.scode and s2.bonbu = b.bcode
			left outer join jisa(nolock) c on s2.scode = c.scode and s2.jisa = c.jscode
			left outer join jijum(nolock) d on s2.scode = d.scode and s2.jijum = d.jcode
			left outer join team(nolock) e on s2.scode = e.scode and s2.team = e.tcode
		where a.scode = '".$_SESSION['S_SCODE']."' 
		  and a.kdate between '".str_replace('-','',$sdate1)."' and '".str_replace('-','',$sdate2)."' ".$where." " ;

$qry =  sqlsrv_query($mscon, $sql);
$totalResult =  sqlsrv_fetch_array($qry); 



//------------------------------------------------------------------------------//
//								엑셀작업 시작										//
//------------------------------------------------------------------------------//


//--------------------------------타이틀셋팅
$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A1', iconv("EUCKR","UTF-8",'증권번호'))
    ->setCellValue('B1', iconv("EUCKR","UTF-8",'보험사'))
    ->setCellValue('C1', iconv("EUCKR","UTF-8",'보험구분'))
    ->setCellValue('D1', iconv("EUCKR","UTF-8",'소속'))
	->setCellValue('E1', iconv("EUCKR","UTF-8",'모집사원'))
	->setCellValue('F1', iconv("EUCKR","UTF-8",'사용인'))
	->setCellValue('G1', iconv("EUCKR","UTF-8",'계약자'))
	->setCellValue('H1', iconv("EUCKR","UTF-8",'피보험자'))
	->setCellValue('I1', iconv("EUCKR","UTF-8",'보험료'))
	->setCellValue('J1', iconv("EUCKR","UTF-8",'수정보험료'))
	->setCellValue('K1', iconv("EUCKR","UTF-8",'상품'))
	->setCellValue('L1', iconv("EUCKR","UTF-8",'계약일자'))
	->setCellValue('M1', iconv("EUCKR","UTF-8",'계약개시일자~종료일자'))
	->setCellValue('N1', iconv("EUCKR","UTF-8",'납입회차'))
	->setCellValue('O1', iconv("EUCKR","UTF-8",'최종납입일'))
	->setCellValue('P1', iconv("EUCKR","UTF-8",'계약상태'))
	->setCellValue('Q1', iconv("EUCKR","UTF-8",'수납상태'))
	->setCellValue('R1', iconv("EUCKR","UTF-8",'수수료계약상태'))
	->setCellValue('S1', iconv("EUCKR","UTF-8",'모집사원코드'))
	->setCellValue('T1', iconv("EUCKR","UTF-8",'사용인코드'))
	->setCellValue('U1', iconv("EUCKR","UTF-8",'납입방법'))
	->setCellValue('V1', iconv("EUCKR","UTF-8",'납입기간'))
	->setCellValue('W1', iconv("EUCKR","UTF-8",'전화번호'))
	->setCellValue('X1', iconv("EUCKR","UTF-8",'주소'));
//----------------------------------------


//----------------------------------------시트명 셋팅
$sheetTitle = iconv("EUCKR", "UTF-8", '계약관리현황');
$sheetTitle = substr($sheetTitle, 0, 31); // Truncate to 31 characters
$invalidCharacters = array('\\', '/', '?', '*', '[', ']', ':');
$sheetTitle = str_replace($invalidCharacters, '', $sheetTitle);
$objPHPExcel->getActiveSheet()->setTitle($sheetTitle);
//----------------------------------------

//--------------------------------합계부분 셋팅

$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('D2', iconv("EUCKR","UTF-8",'합계'))
    ->setCellValue('E2', number_format($totalResult['CNT']))
	->setCellValue('I2', number_format($totalResult['sum_mamt']))
	->setCellValue('J2', number_format($totalResult['sum_samt']));
//----------------------------------------

//----------------------------------------내용셋팅
foreach($listData as $key => $val){
	extract($val);

	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.($key+3), iconv("EUCKR","UTF-8",$kcode))
			->setCellValue('B'.($key+3), iconv("EUCKR","UTF-8",$insname))
            ->setCellValue('C'.($key+3), iconv("EUCKR","UTF-8",$conf['insilj'][$insilj]))
            ->setCellValue('D'.($key+3), iconv("EUCKR","UTF-8",$sosok))
			->setCellValue('E'.($key+3), iconv("EUCKR","UTF-8",$gskey_Cnm))
			->setCellValue('F'.($key+3), iconv("EUCKR","UTF-8",$kskey_Cnm))
			->setCellValue('G'.($key+3), iconv("EUCKR","UTF-8",$kname_c))
			->setCellValue('H'.($key+3), iconv("EUCKR","UTF-8",$pname_c))
			->setCellValue('I'.($key+3), number_format($mamt))
			->setCellValue('J'.($key+3), number_format($samt))
			->setCellValue('K'.($key+3), iconv("EUCKR","UTF-8",$itemnm))
			->setCellValue('L'.($key+3), iconv("EUCKR","UTF-8",date("Y-m-d",strtotime($kdate))))
			->setCellValue('M'.($key+3), iconv("EUCKR","UTF-8",date("Y-m-d",strtotime($fdate)).'~'.date("Y-m-d",strtotime($tdate))))
			->setCellValue('N'.($key+3), number_format($mx_ncnt))
			->setCellValue('O'.($key+3), iconv("EUCKR","UTF-8",date("Y-m-d",strtotime($mx_ipdate))))
			->setCellValue('P'.($key+3), iconv("EUCKR","UTF-8",$kstbit))
			->setCellValue('Q'.($key+3), iconv("EUCKR","UTF-8",$istbit))
			->setCellValue('R'.($key+3), iconv("EUCKR","UTF-8",$kstbit))
			->setCellValue('S'.($key+3), iconv("EUCKR","UTF-8",$ksman))
			->setCellValue('T'.($key+3), iconv("EUCKR","UTF-8",$kdman))
			->setCellValue('U'.($key+3), iconv("EUCKR","UTF-8",$nbit))
			->setCellValue('V'.($key+3), iconv("EUCKR","UTF-8",$nterm))
			->setCellValue('W'.($key+3), iconv("EUCKR","UTF-8",$tel))
			->setCellValue('X'.($key+3), iconv("EUCKR","UTF-8",$addr));


			$objPHPExcel->getActiveSheet()->getStyle('A'.($key+3))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('B'.($key+3))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('C'.($key+3))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('D'.($key+3))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('E'.($key+3))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('F'.($key+3))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('G'.($key+3))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('H'.($key+3))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('I'.($key+3))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('J'.($key+3))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('K'.($key+3))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('L'.($key+3))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('M'.($key+3))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('N'.($key+3))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('O'.($key+3))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('P'.($key+3))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('Q'.($key+3))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('R'.($key+3))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('S'.($key+3))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('T'.($key+3))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('U'.($key+3))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('V'.($key+3))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('W'.($key+3))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('X'.($key+3))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

}
//---------------------------------------------

//---------------------------------------------열 너비셋팅
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(40);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(18);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(28);
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(30);
//---------------------------------------------

$objPHPExcel->getActiveSheet()->getStyle('A1:X1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFC7C7C7');
$objPHPExcel->getActiveSheet()->getStyle('A1:X1')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK);
$objPHPExcel->getActiveSheet()->getStyle('A1:X1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A1:X1')->getFont()->setBold(true);

$objPHPExcel->getActiveSheet()->getStyle('A2:X2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFAECDC');
$objPHPExcel->getActiveSheet()->getStyle('A2:X2')->getFont()->getColor()->setARGB('FFF15F5F');
$objPHPExcel->getActiveSheet()->getStyle('A2:X2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->getStyle('A2:X2')->getFont()->setBold(true);

$filename = iconv("EUCKR","UTF-8","계약관리현황".$sdate1."-".$sdate2);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="'.$filename.'.xls"');


$writer = new Xls($objPHPExcel);
$writer->save('php://output');
exit;
?>