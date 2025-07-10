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

// ������ Ʈ�� ���ý� �Ҽ�����(swon ��Ī : s2 - kdman(����α���)) 
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
	�⵵ / �˻����� / �� ��ȸ�� ���� End
------------------------------------------------------ */

$sql= "
		select aa.*,bb.gamt1+bb.gamt2 totgamt 
		from(
			select scode , yymm , skey , sname,bname,jsname,jname,tname,
					sum(cnt_001) cnt_001 ,sum(suamt_001) suamt_001 , sum(cnt_002) cnt_002 ,sum(suamt_002) suamt_002 , sum(cnt_003) cnt_003 ,sum(suamt_003)  suamt_003 ,
					ROW_NUMBER() over(order by yymm desc,bnum,jsnum,jnum,tnum ,jik desc,tbit,skey ) rnum
			from(
				select aa.scode , aa.yymm ,aa.skey , aa.sname , aa.bname , aa.jsname , aa.jname , aa.tname ,
						case when sbit = '001' then cnt else 0 end 'cnt_001' ,
						case when sbit = '001' then suamt else 0 end 'suamt_001' ,
						case when sbit = '002' then cnt else 0 end 'cnt_002' ,
						case when sbit = '002' then suamt else 0 end 'suamt_002' ,
						case when sbit = '003' then cnt else 0 end 'cnt_003' ,
						case when sbit = '003' then suamt else 0 end 'suamt_003' ,
						bnum,jsnum,jnum,tnum,jik,tbit
				from(
					select a.scode , a.yymm , a.skey ,a.sbit , count(*) cnt , sum(suamt) suamt ,
							c.sname , e.bname , f.jsname , g.jname , h.tname,
							e.num bnum , f.num jsnum , g.num jnum , h.num tnum,c.jik,
							case when tbit = '1' then '1' when tbit = '2' then '3' when tbit = '3' then '2' when tbit = '4' then '4' end tbit
					from sudet a left outer join inssetup b on a.scode = b.scode and a.inscode = b.inscode
								left outer join swon c on a.scode = c.scode and a.skey = c.skey
								left outer join bonbu e on c.scode = e.scode and c.bonbu = e.bcode
								left outer join jisa  f on c.scode = f.scode and c.jisa = f.jscode
								left outer join jijum g on c.scode = g.scode and c.jijum = g.jcode
								left outer join team h  on c.scode = h.scode and c.team = h.tcode
					where a.SCODE =  '".$_SESSION['S_SCODE']."' and a.suamt <> 0  and  a.YYMM >= '".$FYYMM."'  and   a.YYMM <= '".$TYYMM."' $where
					group by a.scode , a.yymm , a.skey ,a.sbit , c.sname , e.bname , f.jsname , g.jname , h.tname,e.num,f.num,g.num,h.num,c.jik,c.tbit
					) aa
				) aa
			group by scode , yymm , skey , sname,bname,jsname,jname,tname,bnum,jsnum,jnum,tnum,jik,tbit
			) aa left outer join sumst bb on aa.scode = bb.scode and aa.yymm=bb.yymm and aa.skey=bb.skey
	"
	;
$qry	= sqlsrv_query( $mscon, $sql );
$listData = array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listData[]	= $fet;
}

//--->�����Լ����� ����縦 Ÿ��Ʋ�� �����ϱ����� �ش���� ������ �������� ������ �ʿ��� �հ��ʵ� ��ġ�ϱ�����  ORDER BY D.NUM 
$sql ="
		select sum(cnt_001) cnt_001 ,sum(suamt_001) suamt_001 , sum(cnt_002) cnt_002 ,sum(suamt_002) suamt_002 , sum(cnt_003) cnt_003 ,sum(suamt_003)  suamt_003 ,
				sum(suamt_001+suamt_002+suamt_003) totamt
		from(
			select 	case when sbit = '001' then cnt else 0 end 'cnt_001' ,
					case when sbit = '001' then suamt else 0 end 'suamt_001' ,
					case when sbit = '002' then cnt else 0 end 'cnt_002' ,
					case when sbit = '002' then suamt else 0 end 'suamt_002' ,
					case when sbit = '003' then cnt else 0 end 'cnt_003' ,
					case when sbit = '003' then suamt else 0 end 'suamt_003' 
			from(
				select a.scode , a.yymm , a.sbit , count(*) cnt , sum(suamt) suamt
				from sudet a left outer join inssetup b on a.scode = b.scode and a.inscode = b.inscode
							left outer join swon c on a.scode = c.scode and a.skey = c.skey
							left outer join bonbu e on c.scode = e.scode and c.bonbu = e.bcode
							left outer join jisa  f on c.scode = f.scode and c.jisa = f.jscode
							left outer join jijum g on c.scode = g.scode and c.jijum = g.jcode
							left outer join team h  on c.scode = h.scode and c.team = h.tcode
				where a.SCODE =  '".$_SESSION['S_SCODE']."' and a.suamt <> 0  and  a.YYMM >= '".$FYYMM."'  and   a.YYMM <= '".$TYYMM."' $where
				group by a.scode , a.yymm , a.sbit
				) aa
			) aa
		";
$qry	= sqlsrv_query( $mscon, $sql );
$listinsTot = array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listinsTot[]	= $fet;
}

$sql ="
		select sum(gamt1+gamt2) totgamt , sum(kamt1+kamt2+kamt3+kamt4+kamt5+kamt6+kamt7+kamt8+kamt9+kamt10+kamt11+kamt12+kamt13+kamt14+kamt15+kamt16+kamt17+kamt18+kamt19+kamt20)-sum(gamt1+gamt2) totkamt
		from sumst a 
		where a.SCODE =  '".$_SESSION['S_SCODE']."' and  a.YYMM >= '".$FYYMM."'  and   a.YYMM <= '".$TYYMM."' $where
		group by a.scode , a.yymm 
		";
$qry	= sqlsrv_query( $mscon, $sql );
$listinsTot_tot = array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listinsTot_tot[]	= $fet;
}

// ������ �� �Ǽ�
//�˻� ������ ���ϱ� 
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
//								�����۾� ����										//
//------------------------------------------------------------------------------//


//--------------------------------Ÿ��Ʋ����
$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A1', iconv("EUCKR","UTF-8",'�����'))
    ->setCellValue('B1', iconv("EUCKR","UTF-8",'���'))
    ->setCellValue('C1', iconv("EUCKR","UTF-8",'�����'))
    ->setCellValue('D1', iconv("EUCKR","UTF-8",'�Ҽ�'));

$objPHPExcel->getActiveSheet()->mergeCells('A1:A2');
$objPHPExcel->getActiveSheet()->mergeCells('B1:B2');
$objPHPExcel->getActiveSheet()->mergeCells('C1:C2');
$objPHPExcel->getActiveSheet()->mergeCells('D1:D2');

$objPHPExcel->getActiveSheet()->setCellValue('E1', iconv("EUCKR","UTF-8","�Ű�������"));
$objPHPExcel->getActiveSheet()->setCellValue('E2', iconv("EUCKR","UTF-8","�Ǽ�"));
$objPHPExcel->getActiveSheet()->setCellValue('F2', iconv("EUCKR","UTF-8","�ݾ�"));
$objPHPExcel->getActiveSheet()->mergeCells('E1:F1');

$objPHPExcel->getActiveSheet()->setCellValue('G1', iconv("EUCKR","UTF-8","����������"));
$objPHPExcel->getActiveSheet()->setCellValue('G2', iconv("EUCKR","UTF-8","�Ǽ�"));
$objPHPExcel->getActiveSheet()->setCellValue('H2', iconv("EUCKR","UTF-8","�ݾ�"));
$objPHPExcel->getActiveSheet()->mergeCells('G1:H1');

$objPHPExcel->getActiveSheet()->setCellValue('I1', iconv("EUCKR","UTF-8","�����������"));
$objPHPExcel->getActiveSheet()->setCellValue('I2', iconv("EUCKR","UTF-8","�Ǽ�"));
$objPHPExcel->getActiveSheet()->setCellValue('J2', iconv("EUCKR","UTF-8","�ݾ�"));
$objPHPExcel->getActiveSheet()->mergeCells('I1:J1');

$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('K1', iconv("EUCKR","UTF-8",'�������޾�'))
    ->setCellValue('L1', iconv("EUCKR","UTF-8",'����'))
    ->setCellValue('M1', iconv("EUCKR","UTF-8",'�����޾�'));
$objPHPExcel->getActiveSheet()->mergeCells('K1:K2');
$objPHPExcel->getActiveSheet()->mergeCells('L1:L2');
$objPHPExcel->getActiveSheet()->mergeCells('M1:M2');

//----------------------------------------


//----------------------------------------��Ʈ�� ����
$sheetTitle = iconv("EUCKR", "UTF-8", '����纰�޿�����');
$sheetTitle = substr($sheetTitle, 0, 31); // Truncate to 31 characters
$invalidCharacters = array('\\', '/', '?', '*', '[', ']', ':');
$sheetTitle = str_replace($invalidCharacters, '', $sheetTitle);
$objPHPExcel->getActiveSheet()->setTitle($sheetTitle);
//----------------------------------------


//--------------------------------�հ�κ� ����

$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('D3', iconv("EUCKR","UTF-8",'�հ�'))
    ->setCellValue('E3', number_format($listinsTot[0]['cnt_001']))
	->setCellValue('F3', number_format($listinsTot[0]['suamt_001']))
	->setCellValue('G3', number_format($listinsTot[0]['cnt_002']))
	->setCellValue('H3', number_format($listinsTot[0]['suamt_002']))
	->setCellValue('I3', number_format($listinsTot[0]['cnt_003']))
	->setCellValue('J3', number_format($listinsTot[0]['suamt_003']))
	->setCellValue('K3', number_format($listinsTot[0]['totamt']))
	->setCellValue('L3', number_format($listinsTot_tot[0]['totgamt']))
	->setCellValue('M3', number_format($listinsTot_tot[0]['totkamt']));
	$objPHPExcel->getActiveSheet()->getStyle('D1:D3')->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);		
	$objPHPExcel->getActiveSheet()->getStyle('F1:F3')->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);		
	$objPHPExcel->getActiveSheet()->getStyle('H1:H3')->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);		
	$objPHPExcel->getActiveSheet()->getStyle('J1:J3')->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);		
//----------------------------------------


//----------------------------------------�������
foreach($listData as $key => $val){
	extract($val);

	$yymm	= (trim($yymm))	? date("Y-m",strtotime($yymm."01")) : "";
	$sosok = substr($bname,0,4).'>'. substr($jsname,0,4).'>'. substr($jname,0,4).'>'. substr($tname,0,4);
	$sosok = str_replace('>>','>',$sosok);
	$sosok = str_replace('>>','>',$sosok);

	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.($key+4), iconv("EUCKR","UTF-8",$yymm))
            //->setCellValue('B'.($key+4), iconv("EUCKR","UTF-8",$skey))
			->setCellValueExplicit("B".($key+4), iconv("EUCKR","UTF-8",$skey),\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING)
            ->setCellValue('C'.($key+4), iconv("EUCKR","UTF-8",$sname))
            ->setCellValue('D'.($key+4), iconv("EUCKR","UTF-8",$sosok))
			->setCellValue('E'.($key+4), number_format($cnt_001))
			->setCellValue('F'.($key+4), number_format($suamt_001))
			->setCellValue('G'.($key+4), number_format($cnt_002))
			->setCellValue('H'.($key+4), number_format($suamt_002))
			->setCellValue('I'.($key+4), number_format($cnt_003))
			->setCellValue('J'.($key+4), number_format($suamt_003))
			->setCellValue('K'.($key+4), number_format($suamt_001+$suamt_002+$suamt_003))
			->setCellValue('L'.($key+4), number_format($totgamt))
			->setCellValue('M'.($key+4), number_format($suamt_001+$suamt_002+$suamt_003-$totgamt));

			$objPHPExcel->getActiveSheet()->getStyle('D'.($key+4))->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);		// ������ �׵θ�
			$objPHPExcel->getActiveSheet()->getStyle('F'.($key+4))->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);		
			$objPHPExcel->getActiveSheet()->getStyle('H'.($key+4))->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);		
			$objPHPExcel->getActiveSheet()->getStyle('J'.($key+4))->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);		

			$objPHPExcel->getActiveSheet()->getStyle('E'.($key+4))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('F'.($key+4))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('G'.($key+4))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('H'.($key+4))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('I'.($key+4))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('J'.($key+4))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('K'.($key+4))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('L'.($key+4))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('M'.($key+4))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

}
//---------------------------------------------

//---------------------------------------------�� �ʺ����
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);


//---------------------------------------------


$objPHPExcel->getActiveSheet()->getStyle('A1:M1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFC7C7C7');
$objPHPExcel->getActiveSheet()->getStyle('A1:M1')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK);
$objPHPExcel->getActiveSheet()->getStyle('A1:M1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A1:M1')->getFont()->setBold(true);

$objPHPExcel->getActiveSheet()->getStyle('A2:M2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFC7C7C7');
$objPHPExcel->getActiveSheet()->getStyle('A2:M2')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK);
$objPHPExcel->getActiveSheet()->getStyle('A2:M2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A2:M2')->getFont()->setBold(true);

$objPHPExcel->getActiveSheet()->getStyle('A3:M3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFAECDC');
$objPHPExcel->getActiveSheet()->getStyle('A3:M3')->getFont()->getColor()->setARGB('FFF15F5F');
$objPHPExcel->getActiveSheet()->getStyle('A3:M3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->getStyle('A3:M3')->getFont()->setBold(true);

//$objPHPExcel->getActiveSheet()->getStyle('D1')->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);		// �� �����ʿ� �׵θ�

$filename = iconv("EUCKR","UTF-8","����纰������".$FYYMM."-".$TYYMM);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="'.$filename.'.xls"');


$writer = new Xls($objPHPExcel);
$writer->save('php://output');
exit;
?>
