<?
//error_reporting(E_ALL); ini_set('display_errors', 1);

include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

/* ------------------------------------------------------------
	Date �ʱⰪ ����
------------------------------------------------------------ */
if (isset($_GET['SDATE1'])) {
	$sdate1 =  $_GET['SDATE1'];
	$sdate2 =  $_GET['SDATE2'];
}else{
	$sdate1 =  date("Y-m-01");
	$sdate2 =  date("Y-m-d");
}

/* ------------------------------------------------------------
	End Date �ʱⰪ ����
------------------------------------------------------------ */
// ù��°Ŀ��dddddddddddddfaadfdf
$where = "";

if(isset($_GET['searchF1'])){
	$searchF1 = $_GET['searchF1'];
}else{
	$searchF1 = "a.kname";
}


if(isset($_GET['searchF1Text'])){
	$searchF1Text = $_GET['searchF1Text'];
}else{
	$searchF1Text = "";
}

if(isset($_GET['searchF1']) && isset($_GET['searchF1Text'])){
	$where  .= " and ".$_GET['searchF1']." like '%".$_GET['searchF1Text']."%' ";
}
$wherescript = Encrypt_where($where,$secret_key,$secret_iv);
// �⺻ ������ ����
$page = (isset($_GET['page'])) ? $_GET['page'] : 1;
$page_row	= 50;

$limit1 = ($page-1)*$page_row+1;
$limit2 = $page*$page_row;

//�˻� ������ ���ϱ� 
$sql= "
	select *
	from(
		select 
			a.KCODE
			,dbo.DECRYPTKEY(a.KPASS) KPASS
			,a.KNAME
			,a.COMPNAME
			,a.DNAME
			,substring(a.COMPNUM,1,3)+'-'+substring(a.COMPNUM,4,2)+'-'+substring(a.COMPNUM,6,5) COMPNUM
			,a.COMPYN
			,a.TAXEMAIL
			,a.ADDR1
			,a.ADDR2
			,a.BCOLOR
			,a.BPOST
			,a.BADDR1
			,a.BADDR2
			,a.TAXPOST
			,a.TAXADDR1
			,a.TAXADDR2
			,a.UPTAE
			,a.UPJONG
			,a.TEL
			,a.HTEL
			,a.MESSYN
			,a.HTEL2
			,a.POINT
			,a.ACCOUNT
			,a.ACCDATE
			,convert(varchar,a.UDATE,21) UDATE,
			row_number()over(order by a.KCODE desc) rnum
		from kwn(nolock) a
		where a.kdate between '".str_replace('-','',$sdate1)."' and '".str_replace('-','',$sdate2)."' ".$where."	
		) p
	where rnum between ".$limit1." AND ".$limit2 ;

$qry	= sqlsrv_query( $mscon, $sql );
$listData = array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listData[]	= $fet;
}

// ������ �� �Ǽ�
//�˻� ������ ���ϱ� 
$sql= "
		select count(*) CNT
		from kwn(nolock) a
		where a.kdate between '".str_replace('-','',$sdate1)."' and '".str_replace('-','',$sdate2)."' ".$where." " ;

$qry =  sqlsrv_query($mscon, $sql);
$totalResult =  sqlsrv_fetch_array($qry); 


// ������ Ŭ���� ����
// �ε�
include_once($conf['rootDir'].'/include/class/Pagination.php');

// ����
$pagination = new Pagination(array(
		'base_url' => $_SERVER['PHP_SELF']."?SDATE1=".$sdate1."&SDATE2=".$sdate2."&searchF1=".$searchF1."&searchF1Text=".$searchF1Text,
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

</style>

<div class="container">
	<div class="content_wrap">
		<fieldset>
			<legend>������</legend>
			
			<!-- �˻����� -->
			<div class="box_wrap sel_btn">
				<form name="searchFrm" method="get" action="<?$_SERVER['PHP_SELF']?>">
				<input type="hidden" name="btn"  id="btn"  value="">
					<fieldset>
						<legend>������ �Ⱓ�� �˻�</legend>
						<div class="">
							<span class="ser_font"> ��������</span> 
							<span class="input_type date" style="width:114px">
								<input type="text" class="Calnew" placeholder="YYYY-MM-DD" id="SDATE1" name="SDATE1" value="<?=$sdate1?>" readonly>
							</span> 
							<span class="dash"> ~ </span>
							<span class="input_type date" style="width:114px">
								<input type="text" class="Calnew" placeholder="YYYY-MM-DD" id="SDATE2" name="SDATE2" value="<?=$sdate2?>" readonly>
							</span>

							<select name="searchF1" id="searchF1" class="srch_css" style="width:98px;height:28px;margin-left:10px; padding-left:0px">
								<option value="a.kname" <?if($searchF1=="a.kname") echo "selected"?>>����</option>
								<option value="a.compnum"   <?if($searchF1=="a.compnum") echo "selected"?>>����ڹ�ȣ</option>
								<option value="a.htel"   <?if($searchF1=="a.htel") echo "selected"?>>�޴�����ȣ</option>
							</select>
							<input type="text" name="searchF1Text" id="searchF1Text" style="width:125px;height:26px;border:1px solid #b7b7b7" value="<?=$searchF1Text?>" >

							<span class="btn_wrap" style="margin-left:10px">
								<a class="btn_s white hover_btn btn_search btn_off" style="width:80px;margin:0px" onclick="common_ser();">��ȸ</a>
								<a class="btn_s white btn_off excelBtn" style="width:80px;">����</a>
							</span>

						</div>

					</fieldset>
				</form>
			</div><!-- // box_wrap -->

			<div id="kwngo_sort" class="tb_type01 div_grid" style="overflow-y:auto;">
				<table class="gridhover">
					<colgroup>
						<col width="120px">
						<col width="150px">
						<col width="130px">
						<col width="110px">
						<col width="110px">
						<col width="100px">
						<col width="150px">

						<col width="150px">
						<col width="110px">
						<col width="110px">

						<col width="100px">
						<col width="140px">
						<col width="auto">
					</colgroup>
					<thead>
					<tr class="rowTop">
						<th align="left">��ID</th>
						<th align="center">��ȣ��</th>
						<th align="center">����</th>
						<th align="center">��ǥ�ڸ�</th>
						<th align="center">����ڹ�ȣ</th>
						<th align="center">�������������</th>
						<th align="center">�õ�</th>

						<th align="center">��</th>
						<th align="center">��ȭ��ȣ</th>
						<th align="center">�޴�����ȣ</th>						

						<th align="center">������</th>
						<th align="center">������¹�ȣ</th>
						<th align="center">���/�����Ͻ�</th>
					</tr>
					</thead>
					<tbody>
						<?if(!empty($listData)){?>
						<?foreach($listData as $key => $val){extract($val);
							if($sbit == '1'){
								$sec_data = substr($secdata,0,6).'-'.substr($secdata,6,7);
							}else{
								$sec_data = substr($secdata,0,3).'-'.substr($secdata,3,2).'-'.substr($secdata,5,5);
							}

							if($totaltel == '--'){
								$totaltel = '';
							}

						
						?>
						<tr class="rowData" rol-date='<?=$KCODE?>'>
							<td align="center"><?=$KCODE?></td>
							<td align="left"><?=$COMPNAME?></td>
							<td align="left"><?=$KNAME?></td>
							<td align="left"><?=$DNAME?></td>
							<td align="center"><?=$COMPNUM?></td>	
							<td align="center"><?if($COMPYN=='Y'){?><i class="fa fa-genderless font_blue" aria-hidden="true"></i><?}else{?><i class="fa fa-times font_red" aria-hidden="true"></i><?}?></td>
							<td align="center"><?=$ADDR1?></td>

							<td align="center"><?=$ADDR2?></td>
							<td align="left"><?=$TEL?></td>
							<td align="left"><?=$HTEL?></td>

							<td align="right"><?=number_format($POINT)?></td>
							<td align="center"><?=$ACCOUNT?></td>
							<td align="center"><?=$UDATE?></td>	
						</tr>
						<?}}else{?>
							<tr>
								<td style="color:#8C8C8C" colspan=13>�˻��� �����Ͱ� �����ϴ�</td>
							</tr>
						<?}?>
					</tbody>
				</table>
			</div><!-- // tb_type01 -->

			<div style="text-align: center">		
				<ul class="pagination pagination-sm" style="margin: 5px 5px 0 5px">
				  <?=$pagination->create_links();?>
				</ul>
			</div>

		</fieldset>
	</div><!-- // content_wrap -->
</div>
<!-- // container -->
<!-- // wrap -->
<script type="text/javascript">

// �����
function KwngoIns(gcode){

	var left = Math.ceil((window.screen.width - 1000)/2);
	var top = Math.ceil((window.screen.height - 830)/2);
	var popOpen	= window.open("<?=$conf['homeDir']?>/sub/menu2/ga_menu2_01_pop.php?gcode="+gcode,"KwngoDt","width=1000px,height=760px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
	popOpen.focus();
}

// ��� Ŭ��
$(".rowTop > th").click(function(){
	var trData = $(this).parent();

	var idx = $(trData).find("th").index($(this));

	// include/bottom.php ����
	sortTable("kwngo_sort", idx, '');
})


// ������ȸ �Լ�(bin/js/common.js ȣ��)
function common_ser(){ 
		$("#div_load_image").show();
		$("form[name='searchFrm']").attr("method","get");
		$("form[name='searchFrm']").attr("target","");
		$("form[name='searchFrm']").submit();
}

$(document).ready(function(){

	$(".excelBtn").click(function(){
		if($('#excelcnt').val() == 0 ){
			alert('�������� �����Ͱ� �������� �ʽ��ϴ�.');
		}else{
			if(confirm("������ ���������ðڽ��ϱ�?")){
				//$("#div_load_image").show();
				$("form[name='searchFrm']").attr("action","ga_menu2_01_excel.php");
				$("form[name='searchFrm']").submit();
				$("form[name='searchFrm']").attr("action","<?$_SERVER['PHP_SELF']?>");
			}
		}
	});

	//window.parent.postMessage("������ > �����߰�����", "*");   // '*' on any domain �θ�� ������..        

	// ����Ʈ Ŭ���� �󼼳��� ��ȸ
	$(".rowData > td").click(function(){
		var trData = $(this).parent();
		var idx    = $(".rowData").index($(trData));

		var gcode  = $(".rowData").eq(idx).attr('rol-date');
		KwngoIns(gcode);
	})

	// �� ���ý� �ش���� Ŭ���Ѱܹޱ�(function d_ser ���� ���� �ѱ�)
	var btn		= '<?=$_GET['btn']?>';
	if(btn){
		$(".box_wrap.sel_btn a").removeClass('on');
		$("#"+btn).addClass('on');
	}


});

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>