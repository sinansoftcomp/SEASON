<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

$page = ($_GET['page']) ? $_GET['page'] : 1;
$page_row	= $conf['pageRow']; // 페이지당 보여줄 rows수  기본 conf 25줄
$page_row	= "20"; // 페이지당 보여줄 수를 20개로 수정할경우 이런식으로 하면 됨

$limit1 = ($page-1)*$page_row+1;
$limit2 = $page*$page_row;


if($_GET['level']=="1"){
	$jcode="본부코드";
	$jname="본부명";
}else if($_GET['level']=="2") {
	$jcode="지사코드";
	$jname="지사명";

}else if($_GET['level']=="3") {
	$jcode="지점코드";
	$jname="지점명";
}else if($_GET['level']=="4") {
	$jcode="팀코드";
	$jname="팀명";
}


$srchText	=	str_replace("-","",$_GET['srchText']);

$sql	= "Select *
			From (
					select a.level,a.name,a.fdate,a.tdate,a.useyn,a.bigo,a.num,a.code, 
							isnull(b.code,'') upcode , isnull(b.name,'') upname , isnull(c.code,'') upupcode , isnull(c.name,'') upupname , isnull(d.code,'') upupupcode , isnull(d.name,'') upupupname ,
							row_number() over (order by a.name) as rnum
					from v_jojik a left outer join v_jojik b on a.scode = b.scode and a.upcode = b.code
								left outer join v_jojik c on a.scode = c.scode and b.upcode=c.code
								left outer join v_jojik d on a.scode = d.scode and c.upcode=d.code
					where a.scode = '".$_SESSION['S_SCODE']."' and (ltrim(a.code) Like '%".$_GET['srchText']."%'  or ltrim(a.name)  Like '%".$srchText."%') and a.level = '".$_GET['level']."'
							
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
					where scode = '".$_SESSION['S_SCODE']."' and (ltrim(code) Like '%".$_GET['srchText']."%'  or ltrim(name)  Like '%".$srchText."%') and level = '".$_GET['level']."'
					";

$qry =  sqlsrv_query($mscon, $sql);
$totalResult =  sqlsrv_fetch_array($qry); 
$totalpage = ceil($totalResult['cnt'] / $page_row);

// 페이지 클래스 시작
include_once($conf['rootDir'].'/include/class/pagination.php');

// 설정
$pagination = new Pagination(array(
		'base_url' => $_SERVER['PHP_SELF']."?SDATE1=".$sdate1."&SDATE2=".$sdate2."&SBIT=".$SBIT."&srchText=".$srchText."&level=".$_GET['level'],
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
		<input type="hidden" name='level' value='<?=$_GET['level']?>'>																									
		<input type="text" style="width:220px;font-size:12px;text-align:center;height:20px;margin-top:0px;" placeholder= '<?=$jcode." or ".$jname?>' name="srchText" id="srchText" class="srchText"  value=<?=$_GET['srchText']?>>
		<a href="#" class="btn_s white" id="SearchBtn">검색</a>
		<a href="#" class="btn_s white" onclick="self.close();">닫기</a>

    </form>
</div>

<div class="tit_wrap" style="padding:0 10px">
	<div class="tb_type01 tb_fix">
		<table class="gridhover">
			<colgroup>
				<col width="7%">
				<col width="auto">
				<col width="30%">
				<col width="17%">
				<col width="17%">
			</colgroup>
			<thead>
			<tr>
				<th>No</th>
				<th><?=$jcode?></th>
				<th><?=$jname?></th>
				<th>조직레벨</th>
				<th>사용여부</th>
			</tr>
			</thead>
			<tbody>
			<?if(!empty($listData)){?>
			<?foreach($listData as $key => $val){extract($val);?>
			<tr rol-data1='<?=$code?>' rol-data2='<?=$name?>' rol-data3='<?=$level?>' 
				rol-data4='<?=$upcode?>'rol-data5='<?=$upname?>'rol-data6='<?=$upupcode?>'rol-data7='<?=$upupname?>' 
				rol-data8='<?=$upupupcode?>'rol-data9='<?=$upupupname?>' class="rowData"  >
				<td align="left"><?=number_format($rnum)?></td>
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

	//window.resizeTo("600", "780");                             // 윈도우 리사이즈

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
		var upcode = $(".rowData").eq(idx).attr("rol-data4");
		var upname = $(".rowData").eq(idx).attr("rol-data5");
		var upupcode = $(".rowData").eq(idx).attr("rol-data6");
		var upupname = $(".rowData").eq(idx).attr("rol-data7");
		var upupupcode = $(".rowData").eq(idx).attr("rol-data8");
		var upupupname = $(".rowData").eq(idx).attr("rol-data9");

		opener.setCustValue(code,name,level,upcode,upname,upupcode,upupname,upupupcode,upupupname);
		self.close();

	});

});

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>