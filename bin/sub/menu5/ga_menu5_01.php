<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

if(!$_GET['SDATE1']){
	$sdate1 = date("Y-m");
}else{
	$sdate1 = $_GET['SDATE1'];
}

$where = " ";

if($_GET['searchF2Text']){
	$where .= " and swon.sname like '%".$_GET['searchF2Text']."%' ";
}


// �⺻ ������ ����
$page = ($_GET['page']) ? $_GET['page'] : 1;
$page_row	= 15;

$limit1 = ($page-1)*$page_row+1;
$limit2 = $page*$page_row;

//�˻� ������ ���ϱ� 
$sql= "
select *
from(
	select *, ROW_NUMBER()over(order by yymm,skey) rnum 
	from(
		select sumst.scode,sumst.yymm,sumst.skey,sumst.bonbu,sumst.jisa,sumst.team,sumst.jik,swon.sname,
				bonbu.bname,jisa.jsname,team.tname,

				case when isnull(sumst.bonbu,'') != '' then bonbu.bname else '' end +
				case when isnull(sumst.bonbu,'') != '' and (isnull(sumst.jisa,'') != '' or isnull(sumst.team,'') != '')  then ' > ' else '' end +
				case when isnull(sumst.jisa,'') != '' then jisa.jsname else '' end +
				case when isnull(sumst.jisa,'') != '' and isnull(sumst.team,'') != '' then ' > ' else '' end +
				case when isnull(sumst.team,'') != '' then team.tname else '' end as sosok,

				sumst.kamt1,sumst.kamt2,sumst.kamt3,sumst.kamt4,sumst.kamt5,
				sumst.kamt6,sumst.kamt7,sumst.kamt8,sumst.kamt9,sumst.kamt10,
				sumst.kamt11,sumst.kamt12,sumst.kamt13,sumst.kamt14,sumst.kamt15,
				sumst.kamt16,sumst.kamt17,sumst.kamt18,sumst.kamt19,sumst.kamt20,
				sumst.gamt1,sumst.gamt2,sumst.gamt3,sumst.gamt4,sumst.gamt5,
				sumst.gamt6,sumst.gamt7,sumst.gamt8,sumst.gamt9,sumst.gamt10,
				sumst.gamt11,sumst.gamt12,sumst.gamt13,sumst.gamt14,sumst.gamt15,
				isnull(sumst.kamt1,0)+isnull(sumst.kamt2,0)+isnull(sumst.kamt3,0)+isnull(sumst.kamt4,0)-isnull(sumst.gamt1,0)-isnull(sumst.gamt2,0) totamt,
				sumst.pbit,sumst.gbit,sumst.jdate,
				convert(varchar,sumst.idate,120) idate , sumst.iswon,
				convert(varchar,sumst.udate,120) udate , sumst.uswon
		from sumst left outer join swon on sumst.scode = swon.scode and sumst.skey = swon.skey
					left outer join bonbu on sumst.scode = bonbu.scode and sumst.bonbu = bonbu.bcode
					left outer join jisa on sumst.scode = jisa.scode and sumst.jisa = jisa.jscode
					left outer join team on sumst.scode = team.scode and sumst.team = team.tcode
		where sumst.scode = '".$_SESSION['S_SCODE']."' and yymm = replace('".$sdate1."','-','') ".$where."
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
// ������ �� �Ǽ�
//�˻� ������ ���ϱ� 
$sql= "
	select 
		count(*) CNT , sum(totamt) kamt
	from(
		select *,isnull(sumst.kamt1,0)+isnull(sumst.kamt2,0)+isnull(sumst.kamt3,0)+isnull(sumst.kamt4,0)-isnull(sumst.gamt1,0)-isnull(sumst.gamt2,0) totamt
		from sumst 
		where sumst.scode = '".$_SESSION['S_SCODE']."' and yymm = replace('".$sdate1."','-','') ".$where."
	) p " ;

$qry =  sqlsrv_query($mscon, $sql);
$totalResult =  sqlsrv_fetch_array($qry); 

// ������ Ŭ���� ����
// �ε�
include_once($conf['rootDir'].'/include/class/Pagination.php');

// ����
$pagination = new Pagination(array(
		'base_url' => $_SERVER['PHP_SELF']."?SDATE1=".$_GET['SDATE1']."&searchF2Text=".$_GET['searchF2Text'],
		'per_page' => $page_row,
		'total_rows' => $totalResult['CNT'],
		'cur_page' => $page,
));

sqlsrv_free_stmt($result);
sqlsrv_close($mscon);

?>

<!-- html���� -->
<style>
body{background-image: none;}
.container{margin:0px 0px 0px 10px;}
.box_wrap {margin-bottom:10px}
.tb_type01 th, .tb_type01 td {padding: 8px 0}
</style>

<div class="container">
	<div class="content_wrap">
		<fieldset>
			<legend>����� ������Ȳ</legend>
			<h2 class="tit_big">����� ������Ȳ</h2>
			
			<!-- �˻����� -->
			<div class="box_wrap sel_btn">
				<form name="searchFrm" method="get" action="<?$_SERVER['PHP_SELF']?>">
					<fieldset>
						<legend>������� ��ȸ</legend>
						<div class="row">

							<input type="text" id="" name="" readonly="" class="sel_text" value="������">
							<span class="input_type date ml10" style="width:114px" >
								<input type="text" class="Cal_ym" placeholder="YYYY-MM-DD" id="SDATE1" name="SDATE1" value="<?=$sdate1?>" readonly>
							</span> 

							<input type="text" id="" name="" readonly="" class="sel_text" value="�����">
							<input type="text" name="searchF2Text" id="searchF2Text" style="width:125px" value="<?=$_GET['searchF2Text']?>" >

							<a href="#" class="btn_s navy btn_search">��ȸ</a>
						</div>
					</fieldset>
				</form>
			</div><!-- // box_wrap -->

			<div class="tit_wrap mt20;margin-top:25px">

			<div class="tb_type01" style="overflow-y:auto;">

				<table class="gridhover">

					<colgroup>
						<col width="10%">
						<col width="auto">
						<col width="14%">						
						<col width="8%">											
						<col width="8%">
						<col width="8%">
						<col width="8%">
						<col width="8%">
						<col width="8%">
						<col width="8%">
						<col width="8%">

					</colgroup>

					<thead>
					<tr>
						<th align="center">������</th>						
						<th align="center">�����</th>
						<th align="center">�Ҽ�</th>					
						<th align="center">��å</th>
						<th align="center">��������</th>
						<th align="center">��������</th>	
						<th align="center">��������</th>	
						<th align="center">��������</th>	
						<th align="center">�ҵ漼</th>	
						<th align="center">�ֹμ�</th>	
						<th align="center">���ޱݾ�</th>	
					</tr>
					</thead>
					<thead>
						<?if(!empty($listData)){?>
							<tr>
							<th class="font_red" style="padding-top:6px;padding-bottom:6px;font-weight:800;">[ ��ȸ�Ǽ� ]</th>
							<th class="font_red" style="padding-top:6px;padding-bottom:6px;font-weight:800;"><?=$totalResult['CNT']?>��</th>
							<th></th>
							<th></th>
							<th></th>
							<th></td>							
							<th></td>
							<th></td>
							<th></td>
							<th class="font_red" style="padding-top:6px;padding-bottom:6px;font-weight:800;">[ �հ�ݾ� ]</th>			
							<th class="font_red" style="padding-right:10px;padding-top:6px;padding-bottom:6px;font-weight:800;text-align:right"><?=number_format($totalResult['kamt']).' ��'?></th>
							</tr>
						<?}?>
					</thead>
					<tbody>
						<?if(!empty($listData)){?>
						<?foreach($listData as $key => $val){extract($val);?>
						<tr class="rowData" rol-date='<?=$skey?>' rol-date2='<?=$insilj?>' rol-date3='<?=$seq?>' style="cursor:pointer;">
							<td align="center"><?if(trim($yymm)) echo date("Y-m",strtotime($yymm));?></td>
							<td align="center"><?=$sname?> (<?=$skey?>)</td>
							<td align="center"><?=$sosok?></td>
							<td align="center"><?=$conf['jik'][$jik]?></td>
							<td align="right" class="font_blue"><?=number_format($kamt1).' ��'?></td>							
							<td align="right" class="font_blue"><?=number_format($kamt2).' ��'?></td>
							<td align="right" class="font_blue"><?=number_format($kamt3).' ��'?></td>							
							<td align="right" class="font_blue"><?=number_format($kamt4).' ��'?></td>							
							<td align="right" class="font_blue"><?=number_format($gamt1).' ��'?></td>
							<td align="right" class="font_blue"><?=number_format($gamt2).' ��'?></td>
							<td align="right" class="font_red"><?=number_format($totamt).' ��'?></td>
						</tr>
						<?}}else{?>
							<tr>
								<td style="color:#8C8C8C" colspan=11>�˻��� �����Ͱ� �����ϴ�</td>
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

	// ��ȸ
	$(".btn_search").click(function(){
		$("form[name='searchFrm']").attr("method","get");
		$("form[name='searchFrm']").attr("target","");
		$("form[name='searchFrm']").submit();
	}); 

	// Enter �̺�Ʈ
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