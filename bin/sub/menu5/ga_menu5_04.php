<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

$where = " ";

if($_GET['searchF2Text']){
	$where .= " and swon.sname like replace('%".$_GET['searchF2Text']."%','-','') ";
}

if($_GET['tbit']){
	$where .= " and swon.tbit = '".$_GET['tbit']."' ";
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
	select *, ROW_NUMBER()over(order by skey) rnum 
	from(
		select aa.scode , aa.skey , swon.sname , 
				a.jsyymm jsyymm1 , a.mjiyul mjiyul1 , a.ujiyul ujiyul1 , a.jjiyul jjiyul1,
				b.jsyymm jsyymm2 , b.mjiyul mjiyul2 , b.ujiyul ujiyul2 , b.jjiyul jjiyul2,
				c.jsyymm jsyymm3 , c.mjiyul mjiyul3 , c.ujiyul ujiyul3 , c.jjiyul jjiyul3
		from (select scode , skey from sjiyul group by scode , skey) aa
				left outer join swon on aa.scode = swon.scode and aa.skey = swon.skey
				left outer join (
					select *
					from(
						select *, rank() over(partition by scode , skey ,insilj order by jsyymm desc) cnt
						from sjiyul
						) sjiyul
					where sjiyul.cnt = 1 and sjiyul.insilj = '1') a  on aa.scode = a.scode and aa.skey = a.skey
				left outer join (
					select *
					from(
						select *, rank() over(partition by scode , skey ,insilj order by jsyymm desc) cnt
						from sjiyul
						) sjiyul
					where sjiyul.cnt = 1 and sjiyul.insilj = '2') b on aa.scode = b.scode and aa.skey = b.skey
				left outer join (
					select *
					from(
						select *, rank() over(partition by scode , skey ,insilj order by jsyymm desc) cnt
						from sjiyul
						) sjiyul
					where sjiyul.cnt = 1 and sjiyul.insilj = '3') c on aa.scode = c.scode and aa.skey = c.skey
		where aa.scode = '".$_SESSION['S_SCODE']."' ".$where." 
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
		count(*) CNT
	from(
		select aa.scode , aa.skey , swon.sname , 
				a.jsyymm jsyymm1 , a.mjiyul mjiyul1 , a.ujiyul ujiyul1 , a.jjiyul jjiyul1,
				b.jsyymm jsyymm2 , b.mjiyul mjiyul2 , b.ujiyul ujiyul2 , b.jjiyul jjiyul2,
				c.jsyymm jsyymm3 , c.mjiyul mjiyul3 , c.ujiyul ujiyul3 , c.jjiyul jjiyul3
		from (select scode , skey from sjiyul group by scode , skey) aa
				left outer join swon on aa.scode = swon.scode and aa.skey = swon.skey
				left outer join (
					select *
					from(
						select *, rank() over(partition by scode , skey ,insilj order by jsyymm desc) cnt
						from sjiyul
						) sjiyul
					where sjiyul.cnt = 1 and sjiyul.insilj = '1') a  on aa.scode = a.scode and aa.skey = a.skey
				left outer join (
					select *
					from(
						select *, rank() over(partition by scode , skey ,insilj order by jsyymm desc) cnt
						from sjiyul
						) sjiyul
					where sjiyul.cnt = 1 and sjiyul.insilj = '2') b on aa.scode = b.scode and aa.skey = b.skey
				left outer join (
					select *
					from(
						select *, rank() over(partition by scode , skey ,insilj order by jsyymm desc) cnt
						from sjiyul
						) sjiyul
					where sjiyul.cnt = 1 and sjiyul.insilj = '3') c on aa.scode = c.scode and aa.skey = c.skey
		where aa.scode = '".$_SESSION['S_SCODE']."' ".$where." 
	) p " ;

$qry =  sqlsrv_query($mscon, $sql);
$totalResult =  sqlsrv_fetch_array($qry); 
/*
echo "<pre>";
echo $sql;
echo "</pre>";
*/
// ������ Ŭ���� ����
// �ε�
include_once($conf['rootDir'].'/include/class/Pagination.php');

// ����
$pagination = new Pagination(array(
		'base_url' => $_SERVER['PHP_SELF']."&searchF2Text=".$_GET['searchF2Text']."&tbit=".$_GET['tbit'],
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
			<legend>����� ������</legend>
			<h2 class="tit_big">����� ������</h2>
			
			<!-- �˻����� -->
			<div class="box_wrap sel_btn">
				<form name="searchFrm" method="get" action="<?$_SERVER['PHP_SELF']?>">
					<fieldset>
						<legend>�������� ��ȸ</legend>
						<div class="row">

							<input type="text" id="" name="" readonly="" class="sel_text" value="�����">
							<input type="text" name="searchF2Text" id="searchF2Text" style="width:125px" value="<?=$_GET['searchF2Text']?>" >

							<select name="tbit" id="tbit" style="width:120px;margin-left:10px">
							  <option value="">��������</option>
							  <?foreach($conf['swon_tbit'] as $key => $val){?>
							  <option value="<?=$key?>" <?if($_GET['tbit']==$key) echo "selected"?>><?=$val?></option>
							  <?}?>
							</select>

							<a href="#" class="btn_s navy btn_search">��ȸ</a>
						</div>
					</fieldset>
				</form>
			</div><!-- // box_wrap -->

			<div class="tit_wrap mt20;margin-top:25px">
				<div class="tit_wrap">
					<h3 class="tit_sub">����� ����������Ʈ</h3>
					<span class="btn_wrap">
						<a href="#" class="btn_s navy" style="min-width:100px;" onclick="sjiyulPopOpen('','','');">���������</a>
					</span>
				</div>

			<div class="tb_type01" style="overflow-y:auto;overflow-x:auto;">

				<table class="gridhover">

					<colgroup>
						<col width="auto">
						<col width="9%">
						<col width="7%">
						<col width="7%">
						<col width="7%">							
						<col width="9%">											
						<col width="7%">
						<col width="7%">
						<col width="7%">
						<col width="9%">
						<col width="7%">
					</colgroup>

					<thead>
					<tr>
						<th align="right" rowspan=2 style=" border-right: 1px solid #c7c7c7;">�����</th>
						<th align="center" colspan=4 style=" border-right: 1px solid #c7c7c7;">�Ϲ�</th>
						<th align="center" colspan=4 style=" border-right: 1px solid #c7c7c7;">���պ� ���</th>
						<th align="center" colspan=4>�ڵ���</th>
					</tr>
					<tr>
						<th align="right">������ۿ�</th>
						<th align="right">����������</th>
						<th align="right">����������</th>						
						<th align="right" style=" border-right: 1px solid #c7c7c7;">����������</th>	
						
						<th align="right">������ۿ�</th>
						<th align="right">����������</th>
						<th align="right">����������</th>
						<th align="right" style=" border-right: 1px solid #c7c7c7;">����������</th>

						<th align="right">������ۿ�</th>
						<th align="right">����������</th>
						<th align="right">����������</th>
						<th align="right">����������</th>
					</tr>
					</thead>
					<tbody>
						<?if(!empty($listData)){?>
						<?foreach($listData as $key => $val){extract($val);?>
						<tr class="rowData" rol-date='<?=$skey?>' rol-date2='<?=$insilj?>' rol-date3='<?=$seq?>' style="cursor:pointer;">
							<td align="center"><?=$sname?> (<?=$skey?>)</td>
							<td align="center"><?if(trim($jsyymm1)) echo date("Y-m",strtotime($jsyymm1."01"));?></td>
							<td align="center"><?=$mjiyul1?><?if($mjiyul1){?>%<?}?></td>
							<td align="center"><?=$ujiyul1?><?if($ujiyul1){?>%<?}?></td>
							<td align="center"><?=$jjiyul1?><?if($jjiyul1){?>%<?}?></td>
							
							<td align="center"><?if(trim($jsyymm2)) echo date("Y-m",strtotime($jsyymm2."01"));?></td>
							<td align="center"><?=$mjiyul2?><?if($mjiyul2){?>%<?}?></td>
							<td align="center"><?=$ujiyul2?><?if($ujiyul2){?>%<?}?></td>
							<td align="center"><?=$jjiyul2?><?if($jjiyul2){?>%<?}?></td>

							<td align="center"><?if(trim($jsyymm3)) echo date("Y-m",strtotime($jsyymm3."01"));?></td>
							<td align="center"><?=$mjiyul3?><?if($mjiyul3){?>%<?}?></td>
							<td align="center"><?=$ujiyul3?><?if($ujiyul2){?>%<?}?></td>
							<td align="center"><?=$jjiyul3?><?if($jjiyul2){?>%<?}?></td>

						</tr>
						<?}}else{?>
							<tr>
								<td style="color:#8C8C8C" colspan=11>�˻��� �����Ͱ� �����ϴ�</td>
							</tr>
						<?}?>
					</tbody>
					<thead>
						<?if(!empty($listData)){?>
							<tr>
								<th class="font_red" style="padding-top:6px;padding-bottom:6px;font-weight:800;">[ ��ȸ�Ǽ� ]</th>
								<th class="font_red" style="padding-top:6px;padding-bottom:6px;font-weight:800;"><?=$totalResult['CNT']?>��</th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></td>							
								<th></td>
								<th></td>
								<th></td>
								<th></td>
								<th></td>
								<th></td>								
							</tr>
						<?}?>
					</thead>
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

// ����
function sjiyulPopOpen(skey,insilj,seq){
	var left = Math.ceil((window.screen.width - 1300)/2);
	var top = Math.ceil((window.screen.height - 600)/2);
	var popOpen	= window.open("<?=$conf['homeDir']?>/sub/menu5/ga_menu5_04_pop.php?skey="+skey+"&insilj="+insilj+"&seq="+seq,"ipgo","width=1400px,height=510px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
	popOpen.focus();
}

$(document).ready(function(){
	var sdate1	= $("#SDATE1").val();
	var sdate2	= $("#SDATE2").val();
	var idv		= '<?=$IDV?>';
	var fyy		= '<?=$_GET['FYY']?>';
	var bit		= '<?=$_GET['BIT']?>';

	// �˻���ư Ŭ���� class on Ȱ��/��Ȱ��
	if(idv){
		$(".box_wrap.sel_btn a").removeClass('on');
		$("#"+idv).addClass('on');
	}else{
		if(fyy){
			$(".box_wrap.sel_btn a").removeClass('on');
		}
	}

	// ��ȸ
	$(".btn_search").click(function(){
		$("form[name='searchFrm']").attr("method","get");
		$("form[name='searchFrm']").attr("target","");
		$("form[name='searchFrm']").submit();
	}); 

	// Enter �̺�Ʈ
	$("#searchF1Text").keydown(function(key) {
		if (key.keyCode == 13) {
			$("form[name='searchFrm']").attr("method","get");
			$("form[name='searchFrm']").attr("target","");
			$("form[name='searchFrm']").submit();
		}
	});
	// Enter �̺�Ʈ
	$("#searchF2Text").keydown(function(key) {
		if (key.keyCode == 13) {
			$("form[name='searchFrm']").attr("method","get");
			$("form[name='searchFrm']").attr("target","");
			$("form[name='searchFrm']").submit();
		}
	});


	$(".rowData > td").click(function(){
		var trData = $(this).parent();
		var idx    = $(".rowData").index($(trData));
		var skey  = $(".rowData").eq(idx).attr('rol-date'); //����ڵ�
		var insilj  = $(".rowData").eq(idx).attr('rol-date2'); //��ǰ����
		var seq  = $(".rowData").eq(idx).attr('rol-date3'); //����
		sjiyulPopOpen(skey,insilj,seq);

	})

	$( window ).resize(function() {
		
		windowResize($(this));

	});
	
	var windowResize	= function(win){
		if($(win).height()>1000){
			$(".tb_type01").height($(win).height()-200);
		}else{
			$(".tb_type01").height(610);
		}
		
	};
	windowResize($( window ));

});

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>