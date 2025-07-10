<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

$sql= "
		select c.kname , d.sname , b.name , a.kcode , c.itemnm , a.mmcnt , a.mamt , a.hwanamt , a.suamt
		from sudet a  left outer join inssetup b on a.scode = b.scode and a.inscode = b.inscode
					left outer join kwn c on a.scode = c.scode and a.kcode = c.kcode
					left outer join swon d on a.scode = d.scode and a.skey = d.skey
		where a.scode = '".$_SESSION['S_SCODE']."' and a.yymm = '".$_GET['yymm']."' and a.skey = '".$_GET['SKEY']."' and a.sbit = '".$_GET['sbit']."' and a.suamt <> 0
		order by a.kcode
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
		where a.scode = '".$_SESSION['S_SCODE']."' and a.yymm = '".$_GET['yymm']."' and a.skey = '".$_GET['SKEY']."' and a.sbit = '".$_GET['sbit']."' and a.suamt <> 0
	";
$qry	= sqlsrv_query( $mscon, $sql );
$listData_tot = array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listData_tot[]	= $fet;
}

$sbitname = "";
if($_GET['sbit']=='003'){
	$sbitname = '본부장수수료' ;
}

sqlsrv_free_stmt($result);
sqlsrv_close($mscon);
?>

<style>
body{background-image: none;}
body { !important; -webkit-print-color-adjust:exact;}
@media print{
     @page{  size:auto; margin : 15mm;  }
 }
</style>

<div class="tit_wrap ipgopop" style="padding-top:10px">
	<div style="padding:0px 30px;">
		<div class="tit_wrap" >
			<h3 class="tit_sub"><?=$listData[0]['sname'].' '.$sbitname?>  상세내역</h3>
			<span class="btn_wrap" >
				<a class="btn_s white no-print excelBtn" style="min-width:100px;">엑셀</a>
				<a class="btn_s white no-print" onclick="jQuery('#print').print();" style="min-width:100px;">인쇄</a>
			</span>
		</div>

		
		<div class="tb_type01 rowspan" style="overflow-y:auto;margin-top:10px">	
			<table class="gridhover">
				<colgroup>											
					<col width="10%">
					<col width="10%">
					<col width="10%">
					<col width="10%">
					<col width="auto">
					<col width="5%">
					<col width="9%">
					<col width="9%">
					<col width="9%">
				</colgroup>
				<thead>
					<tr class="rowTop">
						<th align="center">계약자</th>
						<th align="center">사원명</th>
						<th align="center">제휴사명</th>
						<th align="center">증권번호</th>
						<th align="center">상품명</th>
						<th align="center">회차</th>
						<th align="center">납입보험료</th>
						<th align="center">환산보험료</th>
						<th align="center">지급액</th>
					</tr>
				</thead>			
				<tbody>
					<?if(!empty($listData)){?>
					<tr class="summary">
						<th></td>
						<th></td>
						<th></td>
						<th></td>
						<th></td>
						<th class="sumtext"><?= ' 합 계 ' ?></th>	
						<th class="sum01" align="right"><?=number_format($listData_tot[0]['mamt'])?></td>
						<th class="sum01" align="right"><?=number_format($listData_tot[0]['hwanamt'])?></td>
						<th class="sum01" align="right"><?=number_format($listData_tot[0]['suamt'])?></td>
					</tr>

					<?foreach($listData as $key => $val){extract($val);?>
					<tr class="rowData">
						<td align="left"><?=$listData[$key]['kname']?></td>
						<td align="left"><?=$listData[$key]['sname']?></td>
						<td align="left"><?=$listData[$key]['name']?></td>
						<td align="left"><?=$listData[$key]['kcode']?></td>
						<td align="left"><?=$listData[$key]['itemnm']?></td>
						<td align="right"><?=number_format($listData[$key]['mmcnt'])?></td>
						<td align="right"><?=number_format($listData[$key]['mamt'])?></td>
						<td align="right"><?=number_format($listData[$key]['hwanamt'])?></td>
						<td align="right"><?=number_format($listData[$key]['suamt'])?></td>
					</tr>
					<?}?>
					<?}else{?>
					<tr>
						<td style="color:#8C8C8C" colspan=8>검색된 데이터가 없습니다</td>
					</tr>
					<?}?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<form name="searchFrm" id="searchFrm" method="get" action="<?$_SERVER['PHP_SELF']?>">
	<input type="hidden" name="yymm" id="yymm" value="<?=$_GET['yymm']?>">
	<input type="hidden" name="skey" id="skey" value="<?=$_GET['SKEY']?>">
	<input type="hidden" name="sbit" id="sbit" value="<?=$_GET['sbit']?>">
</form>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript" src="<?=$conf['homeDir']?>/common/js/jQuery.print.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$(".excelBtn").click(function(){
		if(confirm("엑셀로 내려받으시겠습니까?")){
			$("form[name='searchFrm']").attr("action","x1_suspec_list_detail_jang_excel.php");
			$("form[name='searchFrm']").submit();
			$("form[name='searchFrm']").attr("action","<?$_SERVER['PHP_SELF']?>");
		}
	});
});	

</script>


<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>