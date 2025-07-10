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
							a.inscode code,
							a.bscode,
							c.sname,
							b.name,
							case when isnull(c.bonbu,'') != '' then substring(d.bname,1,2) else '' end +
							case when isnull(c.bonbu,'') != '' and (isnull(c.jisa,'') != '' or isnull(c.team,'') != '')  then ' > ' else '' end +
							case when isnull(c.jisa,'') != '' then substring(e.jsname,1,4) else '' end +
							case when isnull(c.jisa,'') != '' and isnull(c.jijum,'') != '' then ' > ' else '' end +
							case when isnull(c.jijum,'') != '' then substring(g.jname,1,4) else '' end +
							case when isnull(c.jijum,'') != '' and isnull(c.team,'') != '' then ' > ' else '' end +
							case when isnull(c.team,'') != '' then f.tname else '' end as sosok,
							c.jik,
							row_number()over(order by a.skey, a.bscode) rnum
					from inswon a
						left outer join inssetup b on a.scode = b.scode and a.inscode = b.inscode
						left outer join swon c on a.scode = c.scode and a.skey = c.skey
						left outer join bonbu d on a.scode = d.scode and c.bonbu = d.bcode
						left outer join jisa e on a.scode = e.scode and c.jisa = e.jscode
						left outer join jijum g on a.scode = g.scode and c.jijum = g.jcode
						left outer join team f on a.scode = f.scode and c.team = f.tcode
					Where a.scode = '".$_SESSION['S_SCODE']."'
					  and (ltrim(a.bscode) Like '%".$_GET['srchText']."%'  or ltrim(c.sname)  Like '%".$srchText."%') ".$where."
				 ) P
			WHERE rnum between ".$limit1." AND ".$limit2  ;

$result	= sqlsrv_query( $mscon, $sql );
$listData = array();
while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
	$listData[]	= $row;
}



$sql	= "Select  Count(*) cnt
			from inswon a
				left outer join inssetup b on a.scode = b.scode and a.inscode = b.inscode
				left outer join swon c on a.scode = c.scode and a.skey = c.skey
				left outer join bonbu d on a.scode = d.scode and c.bonbu = d.bcode
				left outer join jisa e on a.scode = e.scode and c.jisa = e.jscode
				left outer join team f on a.scode = f.scode and c.team = f.tcode
			Where a.scode = '".$_SESSION['S_SCODE']."'
			  and (ltrim(a.bscode) Like '%".$_GET['srchText']."%'  or ltrim(c.sname)  Like '%".$srchText."%') ".$where." ";

$qry =  sqlsrv_query($mscon, $sql);
$totalResult =  sqlsrv_fetch_array($qry); 
$totalpage = ceil($totalResult['cnt'] / $page_row);

// 페이지 클래스 시작
include_once($conf['rootDir'].'/include/class/pagination.php');

// 설정
$pagination = new Pagination(array(
		'base_url' => $_SERVER['PHP_SELF']."?srchText=".$_GET['srchText']."&inscode=".$inscode,
		'per_page' => $page_row,
		'total_rows' => $totalResult['cnt'],
		'cur_page' => $page,
));

?>
<style>
body{background-image: none;}
.box_wrap{margin-bottom:0px;padding:15px 20px;}
.tb_type01 th, .tb_type01 td {padding: 4px 0;}
</style>

<div class="box_wrap sel_btn">
	<form name="searchFrm" method="get" action="<?$_SERVER['PHP_SELF']?>">
		<input type="hidden" name='row' value='<?=$_GET['row']?>'>
		<input type="hidden" name='inscode' value='<?=$_GET['inscode']?>'>

		<input type="text" style="width:220px;font-size:12px;text-align:center;height:20px;margin-top:0px;" placeholder=" 보험사사원번호 OR 사원명" name="srchText" id="srchText" class="srchText"  value=<?=$_GET['srchText']?>>
		<a href="#" class="btn_s white" id="SearchBtn">검색</a>
		<a href="#" class="btn_s white" onclick="self.close();">닫기</a>

    </form>
</div>

<div class="tit_wrap" style="padding:0 20px">
	<div class="tb_type01 tb_fix">
		<table class="gridhover">
			<colgroup>
				<col width="15%">
				<col width="15%">
				<col width="20%">
				<col width="auto">
				<col width="15%">
			</colgroup>
			<thead>
			<tr>
				<th>보험사사번</th>
				<th>사원명</th>
				<th>보험사</th>
				<th>소속정보</th>
				<th>직위</th>				
			</tr>
			</thead>
			<tbody>
			<?if(!empty($listData)){?>
			<?foreach($listData as $key => $val){extract($val);?>
			<tr class="rowData" rol-data1='<?=$bscode?>' rol-data2='<?=$sname?>' rol-data3='<?=$sosok?>'>
				<td align="left"><?=$bscode?></td>
				<td><?=$sname?></td>
				<td><?=$name?></td>
				<td align="left"><?=$sosok?></td>
				<td align="left"><?=$conf['jik'][$jik]?></td>				
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

	window.resizeTo("800", "800");                             // 윈도우 리사이즈

$(document).ready(function(){
	$("input[name='srchText']").focus();

	$("#SearchBtn").on("click", function(){	
		$("form[name='searchFrm']").submit();
	});

	$(".rowData").click(function(){
		var idx=$(".rowData").index($(this));
		var code	= $(".rowData").eq(idx).attr("rol-data1");
		var name	= $(".rowData").eq(idx).attr("rol-data2");
		var sosok	= $(".rowData").eq(idx).attr("rol-data3");

		var row = "<?=$_GET['row']?>";

		opener.setInsSwonValue(row,code,name,sosok);  
		self.close();

	});

});

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>