<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

if(!$_GET['SDATE1']){
	$sdate1 = date("Y-m");
}else{
	$sdate1 = $_GET['SDATE1'];
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
	select *, ROW_NUMBER()over(order by yymm,insilj) rnum 
	from(
		select sudet.scode,sudet.yymm,sudet.kcode,sudet.kamt,
				kwn.kname,kwn.sbit,kwn.bigo,kwn.inscode,insmaster.name,kwn.insilj,kwn.itemnm,kwn.kdate,
				swon.skey,swon.sname
		from sumst left outer join 
							(select scode,yymm,kcode,skey,sum(kamt) kamt
							 from sudet
							 group by scode,yymm,kcode,skey) sudet on sumst.scode = sudet.scode and sumst.yymm = sudet.yymm and sumst.skey = sudet.skey
					left outer join kwn on sudet.scode = kwn.scode and sudet.kcode = kwn.kcode
					left outer join insmaster on kwn.inscode = insmaster.code
					left outer join swon on kwn.scode = swon.scode and kwn.gskey = swon.skey	
		where sudet.scode = '".$_SESSION['S_SCODE']."' and sudet.yymm = replace('".$sdate1."','-','')
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
		count(*) CNT , sum(kamt) kamt
	from(
		select sudet.scode,sudet.yymm,sudet.kcode,sudet.kamt,
				kwn.kname,kwn.sbit,kwn.bigo,kwn.inscode,insmaster.name,kwn.insilj,kwn.itemnm,kwn.kdate,
				swon.skey,swon.sname
		from sumst left outer join 
							(select scode,yymm,kcode,skey,sum(kamt) kamt
							 from sudet
							 group by scode,yymm,kcode,skey) sudet on sumst.scode = sudet.scode and sumst.yymm = sudet.yymm and sumst.skey = sudet.skey
					left outer join kwn on sudet.scode = kwn.scode and sudet.kcode = kwn.kcode
					left outer join insmaster on kwn.inscode = insmaster.code
					left outer join swon on kwn.scode = swon.scode and kwn.gskey = swon.skey	
		where sudet.scode = '".$_SESSION['S_SCODE']."' and sudet.yymm = replace('".$sdate1."','-','')
	) p " ;

$qry =  sqlsrv_query($mscon, $sql);
$totalResult =  sqlsrv_fetch_array($qry); 

// ������ Ŭ���� ����
// �ε�
include_once($conf['rootDir'].'/include/class/Pagination.php');

// ����
$pagination = new Pagination(array(
		'base_url' => $_SERVER['PHP_SELF']."?SDATE1=".$_GET['SDATE1'],
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
			<legend>��ະ ������Ȳ</legend>
			<h2 class="tit_big">��ະ ������Ȳ</h2>
			
			<!-- �˻����� -->
			<div class="box_wrap sel_btn">
				<form name="searchFrm" method="get" action="<?$_SERVER['PHP_SELF']?>">
					<fieldset>
						<legend>���������� ��ȸ</legend>
						<div class="row">

							<input type="text" id="" name="" readonly="" class="sel_text" value="������">
							<span class="input_type date ml10" style="width:114px" >
								<input type="text" class="Cal_ym" placeholder="YYYY-MM-DD" id="SDATE1" name="SDATE1" value="<?=$sdate1?>" readonly>
							</span> 

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
						<col width="10%">
						<col width="9%">
						<col width="9%">							
						<col width="9%">
						<col width="7%">											
						<col width="auto">
						<col width="10%">
						<col width="10%">
					</colgroup>

					<thead>
					<tr>
						<th align="right">������</th>
						<th align="right">����ȣ</th>
						<th align="right">����ڸ�</th>
						<th align="right">�������</th>
						<th align="right">������</th>						
						<th align="right">��ǰ��</th>
						<th align="right">��ǰ��</th>
						<th align="right">���������</th>	
						<th align="right">�����</th>	

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
							<th></th>
							<th></th>
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
							<td align="center"><?=$kcode?></td>
							<td align="center"><?=$kname?></td>
							<td align="center"><?if(trim($kdate)) echo date("Y-m-d",strtotime($kdate));?></td>
							<td align="center"><?=$name?></td>
							<td align="center" <?if($insilj=="1"){?>style="color:#5587ED"<?}else if($insilj=="2"){?>style="color:#E0844F"<?}else if($insilj=="3"){?>style="color:#747474"<?}?>
							><?=$conf['insilj'][$insilj]?></td>
							<td align="left"><?=$itemnm?></td>
							<td align="center"><?=$sname?> (<?=$skey?>)</td>
							<td align="right" class="font_blue"><?=number_format($kamt).' ��'?></td>							
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