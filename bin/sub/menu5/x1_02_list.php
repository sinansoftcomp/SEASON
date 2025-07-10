<?
error_reporting(E_ALL); ini_set('display_errors', 1);
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

$FYYMM   = substr($_REQUEST['SDATE1'],0,4).substr($_REQUEST['SDATE1'],5,2);
$TYYMM  =  substr($_REQUEST['SDATE2'],0,4).substr($_REQUEST['SDATE2'],5,2);

$where = " ";

// 조직도 트리 선택시 소속정보(swon 별칭 : s2 - kdman(사용인기준)) 
if(isset($_REQUEST['id'])){
	
	$Ngubun = substr($_REQUEST['id'],0,2);

	if($Ngubun == 'N1'){
		$where  .= "" ;
	}else {
		$inscode= substr($_REQUEST['id'],2,10);
		$where  .= " and A.INSCODE = '".$inscode."' " ;
	}
}

// 수당규정. 추후에 COMPANY테이블에서 가져올것.
$X = "X1";

if(isset($_REQUEST['id'])){
	$where .= " and a.inscode = '".$inscode."'";

	$sql= "
			select a.scode , a.inscode , a.datacode , a.dataname , a.gubun , b.name
			from inscharge_nameset a left outer join inssetup b on a.scode = b.scode and a.inscode = b.inscode
			where a.SCODE =  '".$_SESSION['S_SCODE']."' and a.inscode = '".$inscode."'
			order by convert(int,substring(a.datacode,8,2))
			";
	$qry	= sqlsrv_query( $mscon, $sql );
	$listData_name = array();
	while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
		$listData_name[]	= $fet;
	}

	$sql= "
			select a.name
			from inssetup a 
			where a.SCODE =  '".$_SESSION['S_SCODE']."' and a.inscode = '".$inscode."'
			";
	$qry	= sqlsrv_query( $mscon, $sql );
	$listData_insname = array();
	while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
		$listData_insname[]	= $fet;
	}

}else{
	$where .= " and a.inscode = '' ";
}


// 기본 페이지 셋팅
$page = ($_REQUEST['page']) ? $_REQUEST['page'] : 1;
$page_row	= 100;
$limit1 = ($page-1)*$page_row+1;
$limit2 = $page*$page_row;


//검색 데이터 구하기 
$sql= "
	select * 
	from(
		select a.scode,a.yymm,a.inscode , b.name , 
				a.dataset1 , a.dataset2 , a.dataset3 , a.dataset4 , a.dataset5 , a.dataset6 , a.dataset7 , a.dataset8 , a.dataset9 , a.dataset10 ,
				row_number()over(order by a.yymm desc , a.inscode asc) rnum
		from INSCHARGE_SET a left outer join inssetup b on a.scode=b.scode and a.inscode = b.inscode
		where a.SCODE =  '".$_SESSION['S_SCODE']."'  and  a.YYMM >= '".$FYYMM."'  and   a.YYMM <= '".$TYYMM."' $where 
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

// 데이터 총 건수
$sql= "
		select count(*) CNT 
		from INSCHARGE_SET a left outer join inssetup b on a.scode=b.scode and a.inscode = b.inscode
		where a.SCODE =  '".$_SESSION['S_SCODE']."'  and  a.YYMM >= '".$FYYMM."'  and   a.YYMM <= '".$TYYMM."' $where 
		" ;
$qry =  sqlsrv_query($mscon, $sql);
$totalResult =  sqlsrv_fetch_array($qry); 
$cnt = $totalResult['CNT'];

sqlsrv_free_stmt($qry);
sqlsrv_close($mscon);

// 페이지 클래스 시작
// 로드
include_once($conf['rootDir'].'/include/class/Pagination.php');

// 설정
$pagination = new Pagination(array(
		'base_url' => $_SERVER['PHP_SELF']."?id=".$_REQUEST['id'],
		'per_page' => $page_row,
		'total_rows' => $totalResult['CNT'],
		'cur_page' => $page,
));

?>

<!-- html영역 -->
<style>
body{background-image: none;}
</style>


<div class="tb_type01 kwndatalist div_grid" style="overflow-y:auto;">	
		<input type="hidden" name="type" id="type" value="">
		<input type="hidden" name="skey_del" id="skey_del" value="">
		<input type="hidden" name="inscode_del" id="inscode_del" value="">
		<input type="hidden" name="insilj_del" id="insilj_del" value="">
		<input type="hidden" name="seq_del" id="seq_del" value="">
		<table  class="gridhover" id="sort_table_sjiyul"  style="width: 3000px;">
			<colgroup>
				<col width="150px">
				<col width="150px">						
				<col width="150px">											
				<?for($i=1;$i<=count($listData_name);$i++){?>
				<col width="200px">
				<?}?>
				<col width="auto">
			</colgroup>
			<thead>
			<tr class="rowTop">
				<th align="center">마감월</th>
				<th align="center">보험사코드</th>						
				<th align="center">보험사명</th>					
				<?for($i=0;$i<count($listData_name);$i++){?>
					<?if(!empty($listData_name[$i]['dataname'])){?>
					<th align="center"><?=$listData_name[$i]['dataname']?></th>
					<?}else{?>
					<th align="center"><?=$listData_insname[0]['name']?><?=$i+1?></th>
					<?}?>
				<?}?>	
				<th></th>
			</tr>
			</thead>
			<tbody>
				<?if(!empty($listData)){?>
				<?foreach($listData as $key => $val){extract($val);?>
				<tr class="rowData" data1='<?=$yymm?>' data2='<?=$inscode?>' style="cursor:pointer;">
					<td align="center"><?=$yymm?></td>
					<td align="center"><?=$inscode?></td>
					<td align="left"><?=$name?></td>
					<?for($i=1;$i<=count($listData_name);$i++){?>
						<?if($listData_name[$i-1]['gubun'] == "1"){?>
						<td align="right"><?=number_format((int)$listData[$key]["dataset".$i])?></td>
						<?}else{?>
						<td align="right"><?=$listData[$key]["dataset".$i]?></td>
						<?}?>
					<?}?>	
					<td></td>
				</tr>
				<?}}else{?>
					<tr>
						<td style="color:#8C8C8C" colspan=14>검색된 데이터가 없습니다</td>
					</tr>
				<?}?>
			</tbody>
		</table>
</div>

<div style="text-align: center">		
	<ul class="pagination pagination-sm inscharge" style="margin: 5px">
	  <?=$pagination->create_links();?>
	</ul>
</div>

<!-- // container -->
<!-- // wrap -->
<script type="text/javascript">


// 헤더 클릭
$(".rowTop > th").click(function(){
	var trData = $(this).parent();

	var idx = $(trData).find("th").index($(this));

	// include/bottom.php 참조
	sortTable("sort_table_sjiyul", idx,1);
})
 


$(document).ready(function(){
	
	var page="";
	if("<?=$_REQUEST['page']?>"){
		page = "<?=$_REQUEST['page']?>";
	}else{
		page = "1";
	}
	$("#page").val(page);

	// page 함수 ajax페이지 존재시 별도 처리
	$(".inscharge a").click(function(){
		//alert('asdsadsadsadsadsa');
		var res = $(this).attr("href").split("?");
		if(res[0] && res[1]){
			//alert(res[0]+"//"+res[1]);
			// data_right_jojik div id값 적용
			ajaxLodingTarget(res[0],res[1],event,$('#inscharge')); 
		}
		return false;
	});

	$(".rowData > td").click(function(){
		var trData = $(this).parent();
		var idx    = $(".rowData").index($(trData));
		var yymm  = $(".rowData").eq(idx).attr('data1');	//마감월
		var inscode  = $(".rowData").eq(idx).attr('data2');	//보험사코드

		inschargePopOpen(yymm,inscode,page);

	});

});

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>