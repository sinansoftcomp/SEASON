<?
//error_reporting(E_ALL); ini_set('display_errors', 1);
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

$FYYMM   = substr($_GET['SDATE1'],0,4).substr($_GET['SDATE1'],5,2);
$TYYMM  =  substr($_GET['SDATE2'],0,4).substr($_GET['SDATE2'],5,2);

$where = "";

// 조직도 트리 선택시 소속정보(swon 별칭 : s2 - kdman(사용인기준)) 
if($_GET['id']){
	
	$Ngubun = substr($_GET['id'],0,2);

	if($Ngubun == 'N1'){
		$bonbu = substr($_GET['id'],2,10);
		$where  .= " and e.bcode = '".$bonbu."' " ;
	}else if($Ngubun == 'N2'){
		$jisa = substr($_GET['id'],2,10);
		$where  .= " and f.jscode = '".$jisa."' " ;
	}else if($Ngubun == 'N3'){
		$jijum = substr($_GET['id'],2,10);
		$where  .= " and g.jcode = '".$jijum."' " ;
	}else if($Ngubun == 'N4'){
		$team = substr($_GET['id'],2,10);
		$where  .= " and h.tcode = '".$team."' " ;
	}else if($Ngubun == 'N5'){
		$ksman = substr($_GET['id'],2,10);
		$where  .= " and c.skey = '".$ksman."' " ;
	}
}
/* ------------------------------------------------------
	년도 / 검색일자 / 월 조회값 생성 End
------------------------------------------------------ */


// 기본 페이지 셋팅
$page = ($_GET['page']) ? $_GET['page'] : 1;
$page_row	= 300;
$limit1 = ($page-1)*$page_row+1;
$limit2 = $page*$page_row;


$sql= "
	select *
	from(
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
	) p
	where rnum between ".$limit1." AND ".$limit2." "
	;
/*
echo '<pre>';
echo $sql; 
echo '</pre>';
*/

$qry	= sqlsrv_query( $mscon, $sql );
$listData = array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listData[]	= $fet;
}

//--->수수입수수료 보험사를 타이틀로 구성하기위한 해당월의 보험사명 순서별로 리턴이 필요함 합계필드 일치하기위함  ORDER BY D.NUM 
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

/*
echo '<pre>';
echo $sql; 
echo '</pre>';
*/


$sql ="
		select sum(gamt1+gamt2) totgamt , sum(kamt1+kamt2+kamt3+kamt4+kamt5+kamt6+kamt7+kamt8+kamt9+kamt10+kamt11+kamt12+kamt13+kamt14+kamt15+kamt16+kamt17+kamt18+kamt19+kamt20)-sum(gamt1+gamt2) totkamt
		from sumst a 
					left outer join swon c on a.scode = c.scode and a.skey = c.skey
					left outer join bonbu e on c.scode = e.scode and c.bonbu = e.bcode
					left outer join jisa  f on c.scode = f.scode and c.jisa = f.jscode
					left outer join jijum g on c.scode = g.scode and c.jijum = g.jcode
					left outer join team h  on c.scode = h.scode and c.team = h.tcode
		where a.SCODE =  '".$_SESSION['S_SCODE']."' and  a.YYMM >= '".$FYYMM."'  and   a.YYMM <= '".$TYYMM."' $where
		group by a.scode , a.yymm 
		";

$qry	= sqlsrv_query( $mscon, $sql );
$listinsTot_tot = array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listinsTot_tot[]	= $fet;
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
/*
echo '<pre>';
 echo $sql; 
echo '</pre>';
 */

$qry =  sqlsrv_query($mscon, $sql);
$totalResult =  sqlsrv_fetch_array($qry); 


// 페이지 클래스 시작
// 로드
include_once($conf['rootDir'].'/include/class/Pagination.php');

// 설정
$pagination = new Pagination(array(
		'base_url' => $_SERVER['PHP_SELF']."?SDATE1=". $_GET['SDATE1']."&SDATE2=". $_GET['SDATE2']."&id=".$_GET['id'],
		'per_page' => $page_row,
		'total_rows' => $totalResult['CNT'],
		'cur_page' => $page,
));

sqlsrv_free_stmt($result);
sqlsrv_close($mscon);

?>
<style>
body{background-image: none;}
.rowspan th {
    padding: 0px 0;
}

</style>

<div class="tb_type01 kwndatalist div_grid rowspan" style="overflow-y:auto;">	
	<table id="sort_table_swonlist" class="gridhover" style="min-width: 1500px;">
		<colgroup>
			<col width="70px">
			<col width="80px">
			<col width="80px">
			<col width="110px">

			<col width="80px">
			<col width="100px">
			<col width="80px">
			<col width="100px">
			<col width="80px">
			<col width="100px">

			<col width="100px">
			<col width="100px">
			<col width="100px">
		</colgroup>

		<thead>
			<tr class="rowTop">
				<th rowspan=2 align="center">정산월</th>
				<th rowspan=2 align="center">사원</th>
				<th rowspan=2 align="center">사원명</th>    
				<th rowspan=2 align="center" style=" border-right: 1px solid #c7c7c7;">소속</th>				

				<th colspan=2 align="center" style=" border-right: 1px solid #c7c7c7;">신계약수수료</th>
				<th colspan=2 align="center" style=" border-right: 1px solid #c7c7c7;">유지수수료</th>
				<th colspan=2 align="center" style=" border-right: 1px solid #c7c7c7;">지사장수수료</th>

				<th rowspan=2 align="center">세전지급액</th>
				<th rowspan=2 align="center">세액</th>
				<th rowspan=2 align="center">실지급액</th>
			</tr>
			<tr>				    
				<th align="center">건수</th>
				<th align="center" style=" border-right: 1px solid #c7c7c7;">금액</th>
				<th align="center">건수</th>
				<th align="center" style=" border-right: 1px solid #c7c7c7;">금액</th>
				<th align="center">건수</th>
				<th align="center" style=" border-right: 1px solid #c7c7c7;">금액</th>
			</tr>
		</thead>

		<tbody>

			<tr class="summary sticky" style="top:39px;">
				<th></th>
				<th></th>
				<th></th>
				<th class="sumtext" style="border-right: 1px solid #c7c7c7;"><?= ' 합 계 ' ?></th>							

				<th class="sum01" align="right"  ><?=number_format($listinsTot[0]['cnt_001'] )?></th>							
				<th class="sum01" style=" border-right: 1px solid #c7c7c7;" align="right"  ><?=number_format($listinsTot[0]['suamt_001'] )?></th>							
				<th class="sum01" align="right"  ><?=number_format($listinsTot[0]['cnt_002'] )?></th>							
				<th class="sum01" style=" border-right: 1px solid #c7c7c7;" align="right"  ><?=number_format($listinsTot[0]['suamt_002'] )?></th>							
				<th class="sum01" align="right"  ><?=number_format($listinsTot[0]['cnt_003'] )?></th>							
				<th class="sum01" style=" border-right: 1px solid #c7c7c7;" align="right"  ><?=number_format($listinsTot[0]['suamt_003'] )?></th>	
				<th class="sum01" align="right"  ><?=number_format($listinsTot[0]['totamt'] )?></th>	
				<th class="sum01" align="right"  ><?=number_format($listinsTot_tot[0]['totgamt'] )?></th>	
				<th class="sum01" align="right"  ><?=number_format($listinsTot_tot[0]['totkamt'] )?></th>	
			</tr>

			<?if(!empty($listData)){?>

			<?foreach($listData as $key => $val){extract($val);?>
			<tr onclick="suspecPopOpen('<?=$skey?>','<?=$yymm?>')">
				<td align="center"><?=date("Y-m",strtotime($yymm."01"))?></td>
				<td align="left"><?=$skey?></td>
				<td align="left"><?=$sname?></td>
				<?$sosok = substr($bname,0,4).'>'. substr($jsname,0,4).'>'. substr($jname,0,8).'>'. substr($tname,0,4)   ?>
				<?$sosok = str_replace('>>','>',$sosok)?> 
				<?$sosok = str_replace('>>','>',$sosok)?> 
				<td align="left" style=" border-right: 1px solid #c7c7c7;"><?=$sosok?></td>

				<td align="right"><?=number_format($cnt_001)?></td>
				<td align="right" style=" border-right: 1px solid #c7c7c7;"><?=number_format($suamt_001)?></td>
				<td align="right"><?=number_format($cnt_002)?></td>
				<td align="right" style=" border-right: 1px solid #c7c7c7;"><?=number_format($suamt_002)?></td>
				<td align="right"><?=number_format($cnt_003)?></td>
				<td align="right" style=" border-right: 1px solid #c7c7c7;"><?=number_format($suamt_003)?></td>
				<td align="right"><?=number_format($suamt_001+$suamt_002+$suamt_003)?></td>
				<td align="right"><?=number_format($totgamt)?></td>
				<td align="right"><?=number_format($suamt_001+$suamt_002+$suamt_003-$totgamt)?></td>
			</tr>
			<?}}else{?>
				<tr>
					<td style="color:#8C8C8C" colspan=24>검색된 데이터가 없습니다</td>
				</tr>
			<?}?>
		</tbody>
	</table>
</div><!-- // tb_type01 -->

<div style="text-align: center">		
	<ul class="pagination pagination-sm kwnlist" style="margin: 5px">
	  <?=$pagination->create_links();?>
	</ul>
</div>

<script type="text/javascript">


// 헤더 클릭
$(".rowTop > th").click(function(){
	var trData = $(this).parent();

	var idx = $(trData).find("th").index($(this));
	// include/bottom.php 참조
	sortTable("sort_table_swonlist", idx, 3);
})
 

$(document).ready(function(){


	$('#excelcnt').val('<?=$totalResult["CNT"]?>');


	var page="";
	if("<?=$_GET['page']?>"){
		page = "<?=$_GET['page']?>";
	}else{
		page = "1";
	}
	$("#page").val(page);
	// page 함수 ajax페이지 존재시 별도 처리
	$(".kwnlist a").click(function(){
		$('#page').val('Y');
		var res = $(this).attr("href").split("?");
		if(res[0] && res[1]){
			//alert(res[0]+"//"+res[1]);
			 //data_right_jojik div id값 적용
			ajaxLodingTarget(res[0],res[1],event,$('#suspec'));    
		}
		return false;
	}); 
});

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>