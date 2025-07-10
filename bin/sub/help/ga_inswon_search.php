<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

$page = ($_GET['page']) ? $_GET['page'] : 1;
$page_row	= $conf['pageRow']; // 페이지당 보여줄 rows수  기본 conf 25줄
$page_row	= "20"; // 페이지당 보여줄 수를 20개로 수정할경우 이런식으로 하면 됨
$limit1 = ($page-1)*$page_row+1;
$limit2 = $page*$page_row;

$srchText	=	str_replace("-","",$_GET['srchText']);

$sql	= "Select rnum,
				  *
			From (select a.skey,
						a.sname,
						aa.bscode,f.name,aa.sgubun,
						row_number() over (order by aa.skey,aa.sgubun) as rnum
					from inswon aa left outer join swon a on aa.scode = a.scode and aa.skey = a.skey
						left outer join inssetup f on aa.scode = f.scode and aa.inscode = f.inscode and f.useyn='Y'
					Where aa.scode = '".$_SESSION['S_SCODE']."' and aa.inscode = '".$_GET['inscode']."'
					  and (ltrim(aa.skey) Like '%".$_GET['srchText']."%'  or ltrim(a.sname)  Like '%".$srchText."%')
				 ) P
			WHERE rnum between ".$limit1." AND ".$limit2  ;
$result	= sqlsrv_query( $mscon, $sql );
$listData = array();
while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
	$listData[]	= $row;
}

$sql	= "Select  Count(*) cnt
			from inswon aa left outer join swon a on aa.scode = a.scode and aa.skey = a.skey
			Where aa.scode = '".$_SESSION['S_SCODE']."' and aa.inscode = '".$_GET['inscode']."'
			  and (ltrim(aa.scode) Like '".$_GET['srchText']."%'  or ltrim(a.sname)  Like '%".$srchText."%')";

$qry =  sqlsrv_query($mscon, $sql);
$totalResult =  sqlsrv_fetch_array($qry); 
$totalpage = ceil($totalResult['cnt'] / $page_row);

// 페이지 클래스 시작
include_once($conf['rootDir'].'/include/class/pagination.php');

// 설정
$pagination = new Pagination(array(
		'base_url' => $_SERVER['PHP_SELF']."?srchText=".$_GET['srchText']."&inscode=".$_GET['inscode'],
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
		<input type="hidden" name='inscode' value='<?=$_GET['inscode']?>'>

		<input type="text" style="width:220px;font-size:12px;text-align:center;height:20px;margin-top:0px;" placeholder=" 사원번호 OR 사원명" name="srchText" id="srchText" class="srchText"  value=<?=$_GET['srchText']?>>
		<a href="#" class="btn_s white" id="SearchBtn">검색</a>
		<a href="#" class="btn_s white" onclick="self.close();">닫기</a>

    </form>
</div>

<div class="tit_wrap" style="padding:0 10px">
	<div class="tb_type01 tb_fix">
		<table class="gridhover">
			<colgroup>
				<col width="15%">
				<col width="20%">
				<col width="auto">
				<col width="20%">
				<col width="20%">
			</colgroup>
			<thead>
			<tr>
				<th>사원번호</th>
				<th>사원명</th>
				<th>원수사</th>
				<th>원수사사원코드</th>
				<th>구분</th>
			</tr>
			</thead>
			<tbody>
			<?if(!empty($listData)){?>
			<?foreach($listData as $key => $val){extract($val);?>
			<tr rol-data1='<?=$bscode?>' rol-data2='<?=$sname?>' class="rowData"  >
				<td align="left"><?=$skey?></td>
				<td><?=$sname?></td>
				<td align="left"><?=$name?></td>
				<td align="left"><?=$bscode?></td>
				<td align="left"><?if($sgubun=="1"){?>신<?}else{?>구<?}?></td>
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

	window.resizeTo("600", "800");                             // 윈도우 리사이즈

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

		opener.setSwonValue(row,code,name);
		self.close();

	});

});

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>