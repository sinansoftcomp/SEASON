<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

$page = ($_GET['page']) ? $_GET['page'] : 1;
$page_row	= $conf['pageRow']; // �������� ������ rows��  �⺻ conf 25��
$page_row	= "20"; // �������� ������ ���� 20���� �����Ұ�� �̷������� �ϸ� ��

$limit1 = ($page-1)*$page_row+1;
$limit2 = $page*$page_row;

$srchText	=	str_replace("-","",$_GET['srchText']);

$sql	= "Select *
			From (
					select level,name,fdate,tdate,useyn,bigo,num,code,row_number() over (order by name) as rnum
					from v_jojik
					where scode = '".$_SESSION['S_SCODE']."' and (ltrim(code) Like '%".$_GET['srchText']."%'  or ltrim(name)  Like '%".$srchText."%') and level like '%".$_GET['j_level']."%'
				 ) P
			WHERE rnum between ".$limit1." AND ".$limit2  ;
/*
echo "<pre>";
echo $sql;
echo "</pre>";
*/

$result	= sqlsrv_query( $mscon, $sql );
$listData = array();
while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
	$listData[]	= $row;
}

$sql	= "			select count(*) cnt from
					v_jojik
					where scode = '".$_SESSION['S_SCODE']."' and (ltrim(code) Like '%".$_GET['srchText']."%'  or ltrim(name)  Like '%".$srchText."%') and level like '%".$_GET['j_level']."%'
					";

$qry =  sqlsrv_query($mscon, $sql);
$totalResult =  sqlsrv_fetch_array($qry); 
$totalpage = ceil($totalResult['cnt'] / $page_row);

// ������ Ŭ���� ����
include_once($conf['rootDir'].'/include/class/pagination.php');

// ����
$pagination = new Pagination(array(
		'base_url' => $_SERVER['PHP_SELF']."?SDATE1=".$sdate1."&SDATE2=".$sdate2."&SBIT=".$SBIT."&srchText=".$srchText."&j_level=".$_GET['j_level'],
		'per_page' => $page_row,
		'total_rows' => $totalResult['cnt'],
		'cur_page' => $page,
));

?>
<style>
body{background-image: none;}
.box_wrap{margin-bottom:0px}
.tb_type01 th, .tb_type01 td {padding: 4px 0;}
</style>

<div class="box_wrap sel_btn">
	<form name="searchFrm" method="get" action="<?$_SERVER['PHP_SELF']?>">
		<input type="hidden" name='row' value='<?=$_GET['row']?>'>
		<select name="j_level" id="j_level" style="width:120px;margin-left:10px">
		  <option value="">��������</option>
		  <?foreach($conf['jojiklevel'] as $key => $val){?>
		  <option value="<?=$key?>" <?if($_GET['j_level']==$key) echo "selected"?>><?=$val?></option>
		  <?}?>
		</select>
		<input type="text" style="width:220px;font-size:12px;text-align:center;height:26px;margin-top:0px;border-radius:5px 5px 5px 5px;" placeholder=" �����ڵ� OR ������" name="srchText" id="srchText" class="srchText"  value=<?=$_GET['srchText']?>>
		<a href="#" class="btn_s white" id="SearchBtn">�˻�</a>
		<a href="#" class="btn_s white" onclick="self.close();">�ݱ�</a>

    </form>
</div>

<div class="tit_wrap">
	<div class="tb_type01 tb_fix">
		<table class="gridhover">
			<colgroup>
				<col width="10%">
				<col width="auto">
				<col width="30%">
				<col width="17%">
				<col width="17%">
			</colgroup>
			<thead>
			<tr>
				<th>No</th>
				<th>�����ڵ�</th>
				<th>���θ�</th>
				<th>��������</th>
				<th>��뿩��</th>
			</tr>
			</thead>
			<tbody>
			<?if(!empty($listData)){?>
			<?foreach($listData as $key => $val){extract($val);?>
			<tr rol-data1='<?=$code?>' rol-data2='<?=$name?>' rol-data3='<?=$level?>' class="rowData"  >
				<td align="center"><?=number_format($rnum)?></td>
				<td align="center"><?=$code?></td>
				<td align="center"><?=$name?></td>
				<td align="center"><?=$conf['jojiklevel'][$level]?></td>
				<td align="center"><?=$conf['useyn'][$useyn]?></td>
			</tr>
			<?}}?>
			</tbody>
		</table>
	</div>
</div>

<div style="text-align: center">		
	<ul class="pagination pagination-sm" style="margin: 10px">
	  <?=$pagination->create_links();?>
	</ul>
</div>	


<script type="text/javascript">

	//window.resizeTo("600", "780");                             // ������ ��������

$(document).ready(function(){
	$("input[name='srchText']").focus();

	$("#SearchBtn").on("click", function(){	
		$("form[name='searchFrm']").submit();
	});

	$(".rowData").click(function(){
		var idx=$(".rowData").index($(this));
		var code = $(".rowData").eq(idx).attr("rol-data1");
		var name = $(".rowData").eq(idx).attr("rol-data2");
		var level = $(".rowData").eq(idx).attr("rol-data3");
		
		opener.setCustValue(code,name,level);
		self.close();

	});

});

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>