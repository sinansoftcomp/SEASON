<?
//error_reporting(E_ALL); ini_set('display_errors', 1);
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
$page_row	= 500;
$limit1 = ($page-1)*$page_row+1;
$limit2 = $page*$page_row;

$sql ="
		select scode,sucode,suname,useyn
		from suname_set
		where scode = '".$_SESSION['S_SCODE']."' and substring(sucode,1,1) = 'K'
		ORDER BY substring(sucode,1,1) desc , convert(int,substring(sucode,5,2)) asc
		";
$qry	= sqlsrv_query( $mscon, $sql );
$listData_Ksuname = array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listData_Ksuname[]	= $fet;
}

/*
echo '<pre>';
echo $sql; 
echo '</pre>';
*/
$sql ="
		select scode,sucode,suname,useyn
		from suname_set
		where scode = '".$_SESSION['S_SCODE']."' and useyn = 'Y' and substring(sucode,1,1) = 'G'
		ORDER BY substring(sucode,1,1) desc , convert(int,substring(sucode,5,2)) asc
		";
$qry	= sqlsrv_query( $mscon, $sql );
$listData_Gsuname = array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listData_Gsuname[]	= $fet;
}

$kamt0="";
$gamt0="";
for($i = 1; $i <= 20; $i++){
	$kamt0 .= "sum(kamt".$i.") kamt".$i." ,";
	if($i <= 10){
		if($i == 10){
			$gamt0 .= "sum(gamt".$i.") gamt".$i." ";
		}else{
			$gamt0 .= "sum(gamt".$i.") gamt".$i." ,";
		}
	}
}

$kamt="";
$gamt="";
$sum_kamt="";
$sum_gamt="";

for($i = 1; $i <= 20; $i++){

	if($listData_Ksuname[$i-1]['sucode'] == 'KAMT'.(string)$i and $listData_Ksuname[$i-1]['useyn']=='Y'){
		$kamt .= " kamt".$i.",";
	}

	if($i == 20){
		$sum_kamt .= " isnull(kamt".$i.",0) sum_kamt";
	}else{
		$sum_kamt .= " isnull(kamt".$i.",0) +";
	}
	
	if($i <= 10){
		if($i == 10){
			$sum_gamt .= " isnull(gamt".$i.",0) sum_gamt";
		}else{
			$sum_gamt .= " isnull(gamt".$i.",0) +";
		}
	}
}

$gamt="";
for($i=1;$i<=10;$i++){
	if(isset($listData_Gsuname[$i-1]['sucode']) and isset($listData_Gsuname[$i-1]['useyn'])){
		if($listData_Gsuname[$i-1]['sucode'] == 'GAMT'.(string)$i and $listData_Gsuname[$i-1]['useyn']=='Y'){
			$gamt .= " gamt".$i.",";
		}
	}
}

$sql= "
	select *
	from(

		select *,row_number()over(order by  yymm desc,bnum,jsnum,jnum,tnum ,jik desc,tbit,skey ) rnum
		from(
			select a.scode,a.yymm,a.skey,c.sname,e.bname,f.jsname,g.jname,h.tname,
					".$kamt.$gamt."
					".$sum_kamt.",".$sum_gamt.",
					e.num bnum , f.num jsnum , g.num jnum , h.num tnum,c.jik,
					case when tbit = '1' then '1' when tbit = '2' then '3' when tbit = '3' then '2' when tbit = '4' then '4' end tbit
			from sumst a left outer join swon c on a.scode = c.scode and a.skey = c.skey
						left outer join bonbu e on c.scode = e.scode and c.bonbu = e.bcode
						left outer join jisa  f on c.scode = f.scode and c.jisa = f.jscode
						left outer join jijum g on c.scode = g.scode and c.jijum = g.jcode
						left outer join team h  on c.scode = h.scode and c.team = h.tcode	
			where a.SCODE =  '".$_SESSION['S_SCODE']."'  and  a.YYMM >= '".$FYYMM."'  and   a.YYMM <= '".$TYYMM."' $where
			) aa


	) p
	where rnum between ".$limit1." AND ".$limit2 ;
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
		select *,kamt1+kamt2+kamt3+kamt4+kamt5+kamt6+kamt7+kamt8+kamt9+kamt10+kamt11+kamt12+kamt13+kamt14+kamt15+kamt16+kamt17+kamt18+kamt19+kamt20 totkamt
		from(		
			select  ".$kamt0.$gamt0."
			from sumst a 
			where a.SCODE =  '".$_SESSION['S_SCODE']."'  and  a.YYMM >= '".$FYYMM."'  and   a.YYMM <= '".$TYYMM."' 
			) aa
		";
$qry	= sqlsrv_query( $mscon, $sql );
$listinsTot = array();

while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listinsTot[]	= $fet;
}
/*
// 데이터 총 건수
//검색 데이터 구하기 
$sql= "
		select count(*) CNT
		from(
			select a.scode , a.yymm , a.skey , c.sname , b.name , e.bname , f.jsname , g.jname , h.tname , sum(a.suamt) suamt
			from sudet a left outer join inssetup b on a.scode = b.scode and a.inscode = b.inscode
						left outer join swon c on a.scode = c.scode and a.skey = c.skey
						left outer join bonbu e on c.scode = e.scode and c.bonbu = e.bcode
						left outer join jisa  f on c.scode = f.scode and c.jisa = f.jscode
						left outer join jijum g on c.scode = g.scode and c.jijum = g.jcode
						left outer join team h  on c.scode = h.scode and c.team = h.tcode

			where a.SCODE =  '".$_SESSION['S_SCODE']."'   and  a.YYMM >= '".$FYYMM."'  and   a.YYMM <= '".$TYYMM."' $where
			group by a.scode , a.yymm , a.skey , c.sname , b.name , e.bname , f.jsname , g.jname , h.tname
			) aa
		PIVOT(sum(suamt) for name in ( ".$instit." )) AS PVT
		  " ;

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
*/

sqlsrv_free_stmt($qry);
sqlsrv_close($mscon);

?>
<style>
body{background-image: none;}
</style>

<div class="tb_type01 kwndatalist div_grid" style="overflow-y:auto;">	
	<table id="sort_table_swonlist" class="gridhover" style="min-width: 3200px; "  >
		<colgroup>
			<col width="90px">
			<col width="100px">
			<col width="100px">
			<col width="300px">
			<col width="140px">
			
			<?for($i=1; $i<=20; $i++){
				if($listData_Ksuname[$i-1]['sucode'] == 'KAMT'.(string)$i and $listData_Ksuname[$i-1]['useyn']=='Y'){?>
				<col width="140px">
			<?}}?>
			<?for($i=1; $i<=10; $i++){
				if($listData_Gsuname[$i-1]['sucode'] == 'GAMT'.(string)$i and $listData_Gsuname[$i-1]['useyn']=='Y'){?>
				<col width="140px">
			<?}}?>

			<col width="auto">

		</colgroup>

		<thead>
			<tr class="rowTop">
				<th align="center">정산월</th>
				<th align="center">사원</th>
				<th align="center">사원명</th>
				<th align="center">소속</th>				
				<th align="center">수수료합계</th>

				<?for($i=1; $i<=20; $i++){
					if($listData_Ksuname[$i-1]['sucode'] == 'KAMT'.(string)$i and $listData_Ksuname[$i-1]['useyn']=='Y'){?>
					<th><?=$listData_Ksuname[$i-1]['suname']?></th>
				<?}}?>	 
				<?for($i=1; $i<=10; $i++){
					if($listData_Gsuname[$i-1]['sucode'] == 'GAMT'.(string)$i and $listData_Gsuname[$i-1]['useyn']=='Y'){?>
					<th><?=$listData_Gsuname[$i-1]['suname']?></th>
				<?}}?>	 
				<th align="center"></th>	
			</tr>

		</thead>		

		<tbody>

			<tr  style="background-color: bisque;position: sticky;top:32px;">
				<th></th>
				<th></th>
				<th></th>
				<th class="font_red" style="text-align:center;font-weight:700;font-size: larger;color: hotpink;"><?= ' 합 계 ' ?></th>							
				<th align="right" class="font_red" style="font-weight:700;font-size: larger;color: hotpink;"><?=number_format($listinsTot[0]["totkamt"] )?></th>							
				<?for($i=1; $i<=20; $i++){
					if($listData_Ksuname[$i-1]['sucode'] == 'KAMT'.(string)$i and $listData_Ksuname[$i-1]['useyn']=='Y'){?>
					<th class="font_red" style="text-align:right;font-weight:700;font-size: larger;color: hotpink;"><?=number_format($listinsTot[0]["kamt".$i])?></th>
				<?}}?>	 
				<?for($i=1; $i<=10; $i++){
					if($listData_Gsuname[$i-1]['sucode'] == 'GAMT'.(string)$i and $listData_Gsuname[$i-1]['useyn']=='Y'){?>
					<th class="font_red" style="text-align:right;font-weight:700;font-size: larger;color: hotpink;"><?=number_format($listinsTot[0]["gamt".$i])?></th>
				<?}}?>	 
				<th></th>
			</tr>

			<?if(!empty($listData)){?>

			<?foreach($listData as $key => $val){extract($val);?>
			<tr class="rowData" rol-data='<?=$swonskey?>', rol-yymm='<?=$yymm?>'>
				<td align="center"><?=date("Y-m",strtotime($yymm))?></td>
				<td align="left"><?=$skey?></td>
				<td align="left"><?=$sname?></td>
				<?$sosok = substr($bname,0,4).'>'.$jsname.'>'.$jname.'>'.$tname   ?>
				<?$sosok = str_replace('>>','>',$sosok)?> 
				<?$sosok = str_replace('>>','>',$sosok)?> 
				<td align="left"><?=$sosok?></td>

				<td align="right" style="font-size: larger;color: hotpink;"><?=number_format($sum_kamt)?></td>

				<?for($i=1; $i<=20; $i++){
					if($listData_Ksuname[$i-1]['sucode'] == 'KAMT'.(string)$i and $listData_Ksuname[$i-1]['useyn']=='Y'){?>
					<td align="right"><?=number_format($listData[$key]["kamt".$i])?></td>
				<?}}?>
				<?if(isset($listData_Gsuname[$i-1]['sucode']) and isset($listData_Gsuname[$i-1]['useyn'])){?>
				<?for($i=1; $i<=10; $i++){
					if($listData_Gsuname[$i-1]['sucode'] == 'GAMT'.(string)$i and $listData_Gsuname[$i-1]['useyn']=='Y'){?>
					<td align="right"><?=number_format($listData[$key]["gamt".$i])?></td>
				<?}}?>
				<td><?=$listData[$key][$val]?></td>
				<?}?>
			</tr>
			<?}}else{?>
				<tr>
					<td style="color:#8C8C8C" colspan=24>검색된 데이터가 없습니다</td>
				</tr>
			<?}?>
		</tbody>
	</table>
</div><!-- // tb_type01 -->


<script type="text/javascript">


// 헤더 클릭
$(".rowTop > th").click(function(){
	var trData = $(this).parent();

	var idx = $(trData).find("th").index($(this));
	// include/bottom.php 참조
	sortTable("sort_table_swonlist", idx, 3);
})
 

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