<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

if(!$_GET['SDATE1']){
	$sdate1 = date("Y-m");
}else{
	$sdate1 = $_GET['SDATE1'];
}

$sbit	= $_GET['sbit'];
$where="";

if($_GET['sbit']){
	$where .= " and sbit= '".$sbit."' ";
}

// 기본 페이지 셋팅
$page = ($_GET['page']) ? $_GET['page'] : 1;
$page_row	= 15;

$limit1 = ($page-1)*$page_row+1;
$limit2 = $page*$page_row;

//검색 데이터 구하기 
$sql= "
select *
from(
	select *, ROW_NUMBER()over(order by yymm,sbit) rnum 
	from(
		select yymm,sbit,sum(kamt) kamt , count(*) cnt
		from sudet
		where scode = '".$_SESSION['S_SCODE']."'  and yymm = replace('".$sdate1."','-','') ".$where."
		group by scode,yymm,sbit
	) tbl
) p
	where rnum between ".$limit1." AND ".$limit2 ;


$qry	= sqlsrv_query( $mscon, $sql );
$listData = array();
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
	select 
		count(*) CNT , sum(kamt) kamt
	from(
		select yymm,sbit,sum(kamt) kamt , count(*) cnt
		from sudet
		where scode = '".$_SESSION['S_SCODE']."'  and yymm = replace('".$sdate1."','-','') ".$where."
		group by scode,yymm,sbit
	) p " ;

$qry =  sqlsrv_query($mscon, $sql);
$totalResult =  sqlsrv_fetch_array($qry); 

// 페이지 클래스 시작
// 로드
include_once($conf['rootDir'].'/include/class/Pagination.php');

// 설정
$pagination = new Pagination(array(
		'base_url' => $_SERVER['PHP_SELF']."?SDATE1=".$_GET['SDATE1']."&sbit=".$_GET['sbit'],
		'per_page' => $page_row,
		'total_rows' => $totalResult['CNT'],
		'cur_page' => $page,
));

sqlsrv_free_stmt($result);
sqlsrv_close($mscon);

?>

<!-- html영역 -->
<style>
body{background-image: none;}
.container{margin:0px 0px 0px 10px;}
.box_wrap {margin-bottom:10px}
.tb_type01 th, .tb_type01 td {padding: 8px 0}
</style>

<div class="container">
	<div class="content_wrap">
		<fieldset>
			<legend>수당별 현황</legend>
			<h2 class="tit_big">수당별 현황</h2>
			
			<!-- 검색조건 -->
			<div class="box_wrap sel_btn">
				<form name="searchFrm" method="get" action="<?$_SERVER['PHP_SELF']?>">
					<fieldset>
						<legend>수당별 정보 조회</legend>
						<div class="row">

							<input type="text" id="" name="" readonly="" class="sel_text" value="정산년월">
							<span class="input_type date ml10" style="width:114px" >
								<input type="text" class="Cal_ym" placeholder="YYYY-MM-DD" id="SDATE1" name="SDATE1" value="<?=$sdate1?>" readonly>
							</span> 

							<select name="sbit" id="sbit" style="width:120px;margin-left:10px">
							  <option value="">수당구분</option>
							  <?foreach($conf['ins_sbit'] as $key => $val){?>
							  <option value="<?=$key?>" <?if($_GET['sbit']==$key) echo "selected"?>><?=$val?></option>
							  <?}?>
							</select>

							<a href="#" class="btn_s navy btn_search">조회</a>
						</div>
					</fieldset>
				</form>
			</div><!-- // box_wrap -->

			<div class="tit_wrap mt20;margin-top:25px">

			<div class="tb_type01" style="overflow-y:auto;">

				<table class="gridhover">

					<colgroup>
						<col width="10%">											
						<col width="10%">
						<col width="10%">
						<col width="10%">
						<col width="10%">
						<col width="10%">
						<col width="10%">
						<col width="10%">
						<col width="10%">
						<col width="10%">
					</colgroup>

					<thead>
					<tr>
						<th align="right">정산년월</th>
						<th align="right">수당구분</th>
						<th align="right">수당건수</th>
						<th align="right">수당총액</th>
						<th align="right"></th>
						<th align="right"></th>
						<th align="right"></th>
						<th align="right"></th>
						<th align="right"></th>
						<th align="right"></th>
					</tr>
					</thead>
					<thead>
						<?if(!empty($listData)){?>
							<tr>
							<th class="font_red" style="padding-top:6px;padding-bottom:6px;font-weight:800;">[ 조회건수 ]</th>
							<th class="font_red" style="padding-top:6px;padding-bottom:6px;font-weight:800;"><?=$totalResult['CNT']?>건</th>
							<th class="font_red" style="padding-top:6px;padding-bottom:6px;font-weight:800;">[ 합계금액 ]</th>			
							<th class="font_red" style="padding-right:10px;padding-top:6px;padding-bottom:6px;font-weight:800;text-align:right"><?=number_format($totalResult['kamt']).' 원'?></th>
							<th align="right"></th>
							<th align="right"></th>
							<th align="right"></th>
							<th align="right"></th>
							<th align="right"></th>
							<th align="right"></th>
							</tr>
						<?}?>
					</thead>
					<tbody>
						<?if(!empty($listData)){?>
						<?foreach($listData as $key => $val){extract($val);?>
						<tr class="rowData" rol-date='<?=$skey?>' rol-date2='<?=$insilj?>' rol-date3='<?=$seq?>' style="cursor:pointer;">
							<td align="center"><?if(trim($yymm)) echo date("Y-m",strtotime($yymm));?></td>
							<td align="center"><?=$conf['ins_sbit'][$sbit]?></td>
							<td align="right" class="font_blue"><?=$cnt.' 건'?></td>					
							<td align="right" class="font_blue"><?=number_format($kamt).' 원'?></td>	
							<th align="right" style="border-bottom:1px solid #e9e9e9"></th>
							<th align="right" style="border-bottom:1px solid #e9e9e9"></th>
							<th align="right" style="border-bottom:1px solid #e9e9e9"></th>
							<th align="right" style="border-bottom:1px solid #e9e9e9"></th>
							<th align="right" style="border-bottom:1px solid #e9e9e9"></th>
							<th align="right" style="border-bottom:1px solid #e9e9e9"></th>
						</tr>
						<?}}else{?>
							<tr>
								<td style="color:#8C8C8C" colspan=11>검색된 데이터가 없습니다</td>
							</tr>
						<?}?>
					</tbody>
				</table>

			</div><!-- // tb_type01 -->

			<div style="text-align: center">		
				<ul class="pagination pagination-sm" style="margin: 10px">
				  <?=$pagination->create_links();?>
				</ul>
			</div>
	
		</fieldset>
	</div><!-- // content_wrap -->
</div>
<!-- // container -->
<!-- // wrap -->
<script type="text/javascript">

$(document).ready(function(){
	var sdate1	= $("#SDATE1").val();

	// 조회
	$(".btn_search").click(function(){
		$("form[name='searchFrm']").attr("method","get");
		$("form[name='searchFrm']").attr("target","");
		$("form[name='searchFrm']").submit();
	}); 

	// Enter 이벤트
	$("#searchF2Text").keydown(function(key) {
		if (key.keyCode == 13) {
			$("form[name='searchFrm']").attr("method","get");
			$("form[name='searchFrm']").attr("target","");
			$("form[name='searchFrm']").submit();
		}
	});


});

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>