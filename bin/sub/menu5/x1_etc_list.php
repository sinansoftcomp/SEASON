<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

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


// 기본 페이지 셋팅
$page = ($_REQUEST['page']) ? $_REQUEST['page'] : 1;
$page_row	= 300;
$limit1 = ($page-1)*$page_row+1;
$limit2 = $page*$page_row;


$sql= "
	select *
	from(
			select a.scode , a.yymm , a.skey ,a.seq, b.gubun , b.gubunnm , a.etcamt , a.bigo ,i.suname,
					c.sname , e.bname , f.jsname , g.jname , h.tname,ROW_NUMBER() over(order by a.yymm desc , e.num , f.num , g.num , h.num , c.jik desc ) rnum
			from sumst_etc a left outer join etc_set b on a.scode = b.scode and a.gubuncode = b.gubuncode
							left outer join swon c on a.scode = c.scode and a.skey = c.skey
							left outer join bonbu e on c.scode = e.scode and c.bonbu = e.bcode
							left outer join jisa  f on c.scode = f.scode and c.jisa = f.jscode
							left outer join jijum g on c.scode = g.scode and c.jijum = g.jcode
							left outer join team h  on c.scode = h.scode and c.team = h.tcode
							left outer join suname_set i on a.scode = i.scode and b.sucode = i.sucode
			where a.SCODE =  '".$_SESSION['S_SCODE']."' and  a.YYMM >= '".$FYYMM."'  and   a.YYMM <= '".$TYYMM."' $where
	) p
	where rnum between ".$limit1." AND ".$limit2." order by skey"
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
		'base_url' => $_SERVER['PHP_SELF']."?SDATE1=". $_REQUEST['SDATE1']."&SDATE2=". $_REQUEST['SDATE2']."&id=".$_REQUEST['id']."&page=Y",
		'per_page' => $page_row,
		'total_rows' => $totalResult['CNT'],
		'cur_page' => $page,
));

sqlsrv_free_stmt($result);
sqlsrv_close($mscon);

?>
<style>
body{background-image: none;}
</style>

<div class="tb_type01 kwndatalist div_grid rowspan" style="overflow-y:auto;">
	<table id="table_etc" class="gridhover" style="min-width: 1500px;">
		<colgroup>
			<col width="8%">
			<col width="9%">
			<col width="9%">
			<col width="10%">

			<col width="9%">
			<col width="9%">
			<col width="9%">
			<col width="10%">
			<col width="auto">
		</colgroup>

		<thead>
			<tr class="rowTop">
				<th align="center">정산월</th>
				<th align="center">사원</th>
				<th align="center">사원명</th>    
				<th align="center">소속</th>

				<th align="center">구분</th>
				<th align="center">지급_공제항목</th>
				<th align="center">지급_공제액</th>
				<th align="center">지급수수료_공제명칭</th>
				<th align="center">비고</th>
			</tr>
		</thead>

		<tbody>

			<?if(!empty($listData)){?>

			<?foreach($listData as $key => $val){extract($val);?>
			<tr onclick="suetcPopOpen('<?=$skey?>','<?=$yymm?>','<?=$seq?>')">
				<td align="center"><?=date("Y-m",strtotime($yymm."01"))?></td>
				<td align="left"><?=$skey?></td>
				<td align="left"><?=$sname?></td>
				<?$sosok = substr($bname,0,4).'>'. substr($jsname,0,4).'>'. substr($jname,0,8).'>'. substr($tname,0,4)   ?>
				<?$sosok = str_replace('>>','>',$sosok)?> 
				<?$sosok = str_replace('>>','>',$sosok)?> 
				<td align="left"><?=$sosok?></td>

				<td align="center"><?=$conf['suetc_gubun'][$gubun]?></td>
				<td align="left"><?=$gubunnm?></td>
				<td align="right"><?=number_format($etcamt)?></td>
				<td align="left"><?=$suname?></td>
				<td align="left"><?=$bigo?></td>
			</tr>
			<?}}else{?>
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


$(document).ready(function(){

	// page 함수 ajax페이지 존재시 별도 처리
	$(".kwnlist a").click(function(){
		$('#page').val('Y');
		var res = $(this).attr("href").split("?");
		if(res[0] && res[1]){
			//alert(res[0]+"//"+res[1]);
			 //data_right_jojik div id값 적용
			ajaxLodingTarget(res[0],res[1],event,$('#kwnlist'));    
		}
		return false;
	});
  
});

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>