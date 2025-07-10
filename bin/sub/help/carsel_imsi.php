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
			From (
					select 
							a.year,
							a.quater,
							a.car_code,
							a.car_grade,
							a.car_nm,
							a.hi_repair,
							row_number()over(order by a.car_code) rnum
					from cardtd a
					Where ltrim(a.car_nm)  Like '%".$srchText."%' 
					  and gubun = '2'
				 ) P
			WHERE rnum between ".$limit1." AND ".$limit2  ;

$result	= sqlsrv_query( $mscon, $sql );
$listData = array();
while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
	$listData[]	= $row;
}


$sql	= "Select  Count(*) cnt
			from cardtd a
			Where ltrim(a.car_nm)  Like '%".$srchText."%' 
			  and gubun = '2' ";

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
.pop_btn{height:24px; line-height:22px;}
</style>

<div class="box_wrap sel_btn">
	<form name="searchFrm" method="get" action="<?$_SERVER['PHP_SELF']?>">
		<input type="hidden" name='row' value='<?=$_GET['row']?>'>

		<input type="text" style="width:250px;font-size:12px;text-align:center;height:20px;margin-top:0px;" placeholder="차명을 입력하세요. ex)쏘렌토, 벤츠.." name="srchText" id="srchText" class="srchText"  value=<?=$_GET['srchText']?>>
		<a href="#" class="btn_s white pop_btn" id="SearchBtn">검색</a>
		<a href="#" class="btn_s white pop_btn" onclick="self.close();">닫기</a>

    </form>
</div>

<div class="tit_wrap" style="padding:0 20px">
	<div class="tb_type01 tb_fix" style="height:300px;">
		<table class="gridhover">
			<colgroup>
				<col width="10%">
				<col width="10%">
				<col width="15%">
				<col width="15%">
				<col width="auto">
			</colgroup>
			<thead>
			<tr>
				<th>년도</th>
				<th>분기</th>
				<th>차명코드</th>
				<th>차량등급</th>		
				<th>차명</th>		
			</tr>
			</thead>
			<tbody>
			<?if(!empty($listData)){?>
			<?foreach($listData as $key => $val){extract($val);?>
			<tr class="rowData" rol-data1='<?=$car_code?>' rol-data2='<?=$car_grade?>' rol-data3='<?=$car_nm?>' rol-data4='<?=$hi_repair?>'>
				<td><?=$year?></td>
				<td><?=$quater?></td>
				<td><?=$car_code?></td>
				<td><?=$car_grade?></td>
				<td align="left"><?=$car_nm?></td>				
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

	window.resizeTo("700", "500");                             // 윈도우 리사이즈

$(document).ready(function(){
	$("input[name='srchText']").focus();

	$("#SearchBtn").on("click", function(){	
		$("form[name='searchFrm']").submit();
	});

	$(".rowData").click(function(){
		var idx=$(".rowData").index($(this));
		var car_code	= $(".rowData").eq(idx).attr("rol-data1");
		var car_grade	= $(".rowData").eq(idx).attr("rol-data2");
		var car_nm		= $(".rowData").eq(idx).attr("rol-data3");
		var hi_repair	= $(".rowData").eq(idx).attr("rol-data4");

		var bae_gi, caryeartxt, cardate, hyoung_sik, amt, fuel, car_part, people_num ;


		opener.setCarValue('D', car_code, car_grade, bae_gi, caryeartxt, cardate, hyoung_sik, car_nm, 0, fuel, hi_repair, car_part, people_num, 'N');
		self.close();

	});

});

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>