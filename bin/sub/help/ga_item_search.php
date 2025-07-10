<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

$page = ($_GET['page']) ? $_GET['page'] : 1;
$page_row	= $conf['pageRow']; // 페이지당 보여줄 rows수  기본 conf 25줄
$page_row	= "20"; // 페이지당 보여줄 수를 20개로 수정할경우 이런식으로 하면 됨

$limit1 = ($page-1)*$page_row+1;
$limit2 = $page*$page_row;

$inscode	=	$_GET['inscode'];	// 보험사
$srchText	=	str_replace("-","",$_GET['srchText']);

$where	=	"";

if($inscode){
	$where	.=	" and a.inscode = '".$_GET['inscode']."' ";
}


$sql	= "Select rnum,
				  *
			From (
					select 
							a.item,
							a.inscode,
							b.name insnm,
							a.name,
							a.kind,
							a.bbit,
							a.jbit,
							a.iscode,
							a.nbit,
							row_number()over(order by a.inscode, a.item) rnum
					from item a
						left outer join inssetup b on a.scode = b.scode and a.inscode = b.inscode
					Where a.scode = '".$_SESSION['S_SCODE']."'
					  and ltrim(a.name)  Like '%".$srchText."%' ".$where."
				 ) P
			WHERE rnum between ".$limit1." AND ".$limit2  ;

$result	= sqlsrv_query( $mscon, $sql );
$listData = array();
while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
	$listData[]	= $row;
}


$sql	= "Select  Count(*) cnt
			from item a
			Where a.scode = '".$_SESSION['S_SCODE']."'
			  and ltrim(a.name)  Like '%".$srchText."%' ".$where." ";

$qry =  sqlsrv_query($mscon, $sql);
$totalResult =  sqlsrv_fetch_array($qry); 
$totalpage = ceil($totalResult['cnt'] / $page_row);

// 페이지 클래스 시작
include_once($conf['rootDir'].'/include/class/pagination.php');

// 설정
$pagination = new Pagination(array(
		'base_url' => $_SERVER['PHP_SELF']."?srchText=".$_GET['srchText'],
		'per_page' => $page_row,
		'total_rows' => $totalResult['cnt'],
		'cur_page' => $page,
));


// 전체보험사
$sql= "select inscode code, name, gubun from inssetup where scode = '".$_SESSION['S_SCODE']."' and useyn = 'Y' order by name";
$qry= sqlsrv_query( $mscon, $sql );
$instot	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
  $instot[] = $fet;
}

?>
<style>
body{background-image: none;}
.box_wrap{margin-bottom:0px;padding:15px 20px;}
.tb_type01 th, .tb_type01 td {padding: 4px 0;}
</style>

<div class="box_wrap sel_btn">
	<form name="searchFrm" method="get" action="<?$_SERVER['PHP_SELF']?>">
		<input type="hidden" name='row' value='<?=$_GET['row']?>'>

		<input type="text" style="width:220px;font-size:12px;text-align:center;height:20px;margin-top:0px;" placeholder=" 상품명" name="srchText" id="srchText" class="srchText"  value=<?=$_GET['srchText']?>>
		<a href="#" class="btn_s white" id="SearchBtn">검색</a>
		<a href="#" class="btn_s white" onclick="self.close();">닫기</a>

    </form>
</div>

<div class="tit_wrap" style="padding:0 20px">
	<div class="tb_type01 tb_fix">
		<table class="gridhover">
			<colgroup>
				<col width="15%">
				<col width="auto">
				<col width="20%">
				<col width="15%">
			</colgroup>
			<thead>
			<tr>
				<th>보험사</th>
				<th>상품명</th>
				<th>보험사상품코드</th>
				<th>상품군</th>		
			</tr>
			</thead>
			<tbody>
			<?if(!empty($listData)){?>
			<?foreach($listData as $key => $val){extract($val);?>
			<tr rol-data1='<?=$item?>' rol-data2='<?=$name?>' rol-data3='<?=$bbit?>' rol-data2='<?=$iscode?>' class="rowData"  >
				<td align="left"><?=$insnm?></td>
				<td align="left"><?=$name?></td>
				<td><?=$iscode?></td>
				<td align="left"><?=$conf['insilj'][$bbit]?></td>				
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

	window.resizeTo("900", "800");                             // 윈도우 리사이즈

$(document).ready(function(){
	$("input[name='srchText']").focus();

	$("#SearchBtn").on("click", function(){	
		$("form[name='searchFrm']").submit();
	});

	$(".rowData").click(function(){
		var idx=$(".rowData").index($(this));
		var code	= $(".rowData").eq(idx).attr("rol-data1");
		var name	= $(".rowData").eq(idx).attr("rol-data2");
		var bbit	= $(".rowData").eq(idx).attr("rol-data3");
		var icode	= $(".rowData").eq(idx).attr("rol-data4");


		var row = "<?=$_GET['row']?>";

		opener.setItemValue(row,code,name, bbit, icode);
		self.close();

	});

});

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>