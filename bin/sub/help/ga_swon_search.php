<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

$page = ($_GET['page']) ? $_GET['page'] : 1;
$page_row	= $conf['pageRow']; // �������� ������ rows��  �⺻ conf 25��
$page_row	= "20"; // �������� ������ ���� 20���� �����Ұ�� �̷������� �ϸ� ��

$taxgubun=$_GET['taxgubun'];

$limit1 = ($page-1)*$page_row+1;
$limit2 = $page*$page_row;

$srchText	=	str_replace("-","",$_GET['srchText']);

$sql	= "Select rnum,
				  *
			From (select a.skey,
						a.sname,
						case when isnull(a.bonbu,'') != '' then substring(b.bname,1,2) else '' end +
						case when isnull(a.bonbu,'') != '' and (isnull(a.jisa,'') != '' or isnull(a.team,'') != '')  then ' > ' else '' end +
						case when isnull(a.jisa,'') != '' then substring(c.jsname,1,4) else '' end +
						case when isnull(a.jisa,'') != '' and isnull(a.jijum,'') != '' then ' > ' else '' end +
						case when isnull(a.jijum,'') != '' then substring(g.jname,1,4) else '' end +
						case when isnull(a.jijum,'') != '' and isnull(a.team,'') != '' then ' > ' else '' end +
						case when isnull(a.team,'') != '' then e.tname else '' end as sosok,
						a.jik,
						a.htel1+'-'+a.htel2+'-'+a.htel3 as htel,
						row_number() over (order by a.skey) as rnum
					from swon a
						left outer join bonbu b on a.scode = b.scode and a.bonbu = b.bcode
						left outer join jisa c on a.scode = c.scode and a.jisa = c.jscode
						left outer join jijum g on a.scode = g.scode and a.jijum = g.jcode
						left outer join team e on a.scode = e.scode and a.team = e.tcode
					Where a.scode = '".$_SESSION['S_SCODE']."'
					  and (ltrim(skey) Like '%".$_GET['srchText']."%'  or ltrim(sname)  Like '%".$srchText."%')
				 ) P
			WHERE rnum between ".$limit1." AND ".$limit2  ;


$result	= sqlsrv_query( $mscon, $sql );
$listData = array();
while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
	$listData[]	= $row;
}

$sql	= "Select  Count(*) cnt
			From swon
			Where scode = '".$_SESSION['S_SCODE']."'
			  and (ltrim(scode) Like '".$_GET['srchText']."%'  or ltrim(sname)  Like '%".$srchText."%')";

$qry =  sqlsrv_query($mscon, $sql);
$totalResult =  sqlsrv_fetch_array($qry); 
$totalpage = ceil($totalResult['cnt'] / $page_row);

// ������ Ŭ���� ����
include_once($conf['rootDir'].'/include/class/pagination.php');

// ����
$pagination = new Pagination(array(
		'base_url' => $_SERVER['PHP_SELF']."?srchText=".$_GET['srchText'],
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

		<input type="text" style="width:220px;font-size:12px;text-align:center;height:20px;margin-top:0px;" placeholder=" �����ȣ OR �����" name="srchText" id="srchText" class="srchText"  value=<?=$_GET['srchText']?>>
		<a href="#" class="btn_s white" id="SearchBtn">�˻�</a>
		<a href="#" class="btn_s white" onclick="self.close();">�ݱ�</a>

    </form>
</div>

<div class="tit_wrap" style="padding:0 10px">
	<div class="tb_type01 tb_fix">
		<table class="gridhover">
			<colgroup>
				<col width="10%">
				<col width="15%">
				<col width="auto">
				<col width="10%">
				<col width="15%">
			</colgroup>
			<thead>
			<tr>
				<th>�����ȣ</th>
				<th>�����</th>
				<th>�Ҽ�����</th>
				<th>����</th>
				<th>����ó</th>
			</tr>
			</thead>
			<tbody>
			<?if(!empty($listData)){?>
			<?foreach($listData as $key => $val){extract($val);?>
			<tr rol-data1='<?=$skey?>' rol-data2='<?=$sname?>' class="rowData"  >
				<td align="left"><?=$skey?></td>
				<td><?=$sname?></td>
				<td align="left"><?=$sosok?></td>
				<td><?=$conf['jik'][$jik]?></td>
				<td><?=$htel?></td>
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

	window.resizeTo("800", "800");                             // ������ ��������

$(document).ready(function(){
	$("input[name='srchText']").focus();

	$("#SearchBtn").on("click", function(){	
		$("form[name='searchFrm']").submit();
	});

	$(".rowData").click(function(){
		var idx=$(".rowData").index($(this));
		var code	= $(".rowData").eq(idx).attr("rol-data1");
		var name	= $(".rowData").eq(idx).attr("rol-data2");


		var row = "<?=$_GET['row']?>";

		//console.log(taxgubun);
		//return false;

		opener.setSwonValue(row,code,name);
		self.close();

	});

});

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>