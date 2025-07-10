<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

// 수당규정. 추후에 COMPANY테이블에서 가져올것.
$X = "X1";

$where = " ";
// 조직도 트리 선택시 소속정보(swon 별칭 : s2 - kdman(사용인기준)) 
if($_REQUEST['id']){
	$Ngubun           = substr($_REQUEST['id'],0,2);

	if($Ngubun == 'N1'){
		$bonbu = substr($_REQUEST['id'],2,10);
		$where .= " and b.bonbu = '".$bonbu."' ";
	}else if($Ngubun == 'N2'){
		$jisa = substr($_REQUEST['id'],2,10);
		$where .= " and b.jisa = '".$jisa."'";
	}else if($Ngubun == 'N3'){
		$jijum = substr($_REQUEST['id'],2,10);
		$where .= " and b.jijum = '".$jijum."'";
	}else if($Ngubun == 'N4'){
		$team = substr($_REQUEST['id'],2,10);
		$where .= " and b.team = '".$team."'";
	}else if($Ngubun == 'N5'){
		$scode = substr($_REQUEST['id'],2,10);
		$where .= " and a.skey = '".$scode."'";
	}
}

$search_swon = iconv("UTF-8","EUCKR",$_REQUEST['searchF2Text']); 
$search_swon= preg_replace("/\s+/", "", $search_swon);  

if($_REQUEST['searchF2Text']){
	$where .= " and b.sname like '%".$search_swon."%' ";
}
if($_REQUEST['insilj']){
	$where .= " and a.insilj = '".$_REQUEST['insilj']."' ";
}
if($_REQUEST['code']){
	$where .= " and a.inscode = '".$_REQUEST['code']."'";
}

// 기본 페이지 셋팅
$page = ($_REQUEST['page']) ? $_REQUEST['page'] : 1;
$page_row	= 100;
$limit1 = ($page-1)*$page_row+1;
$limit2 = $page*$page_row;

for($i=1;$i<=100;$i++){
	//$select .= "jiyul".$i." ,";
	$select .= "isnull(jiyul".$i.",0.000) jiyul".$i." ,";
}

//검색 데이터 구하기 
$sql= "
	select * 
	from(
		select *, ROW_NUMBER()over(order by bnum,jsnum,jnum,tnum,jik,tbitnum,skey,seq) rnum
		from(
			select a.scode,a.skey,a.inscode,a.insilj,a.seq,a.jsyymm,a.jeyymm , b.sname , b.jik,b.tbit,h.subnm,
					case when a.inscode = '00000' then '통합' else g.name end insname,
					".$select."
					case when isnull(b.bonbu,'') != '' then substring(c.bname,1,4) else '' end +
					case when isnull(b.bonbu,'') != '' and (isnull(b.jisa,'') != '' or isnull(b.jijum,'') != '' or isnull(b.team,'') != '')  then ' > ' else '' end +
					case when isnull(b.jisa,'') != '' then d.jsname else '' end +
					case when isnull(b.jisa,'') != '' and isnull(b.jijum,'') != '' then ' > ' else '' end +
					case when isnull(b.jijum,'') != '' then e.jname else '' end +
					case when isnull(b.jijum,'') != '' and isnull(b.team,'') != '' then ' > ' else '' end +
					case when isnull(b.team,'') != '' then f.tname else '' end as sosok,
					c.num bnum , d.num jsnum , e.num jnum , f.num tnum,
					case when tbit = '1' then '1' when tbit = '2' then '3' when tbit = '3' then '2' when tbit = '4' then '4' end tbitnum
			from ".$X."_sjirule a  left outer join swon b on a.scode = b.scode and a.skey = b.skey
							left outer join bonbu c on b.scode = c.scode and b.bonbu = c.bcode
							left outer join jisa d on b.scode = d.scode and b.jisa = d.jscode
							left outer join jijum e on b.scode = e.scode and b.jijum = e.jcode
							left outer join team f on b.scode = f.scode and b.team = f.tcode
							left outer join insmaster g on a.inscode = g.code
							left outer join common h  on a.scode = h.scode and h.CODE = 'COM006' and  b.POS = h.CODESUB	
			where a.scode = '".$_SESSION['S_SCODE']."' ".$where."
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

// 데이터 총 건수
$sql= "
		select count(*) CNT 
		from ".$X."_sjirule a  left outer join swon b on a.scode = b.scode and a.skey = b.skey
						left outer join bonbu c on b.scode = c.scode and b.bonbu = c.bcode
						left outer join jisa d on b.scode = d.scode and b.jisa = d.jscode
						left outer join jijum e on b.scode = e.scode and b.jijum = e.jcode
						left outer join team f on b.scode = f.scode and b.team = f.tcode
						left outer join insmaster g on a.inscode = g.code
						left outer join common h  on a.scode = h.scode and h.CODE = 'COM006' and  b.POS = h.CODESUB	
		where a.scode = '".$_SESSION['S_SCODE']."' ".$where."
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
		'base_url' => $_SERVER['PHP_SELF']."?searchF2Text=".iconv("EUC-KR", "UTF-8", $_REQUEST['searchF2Text'])."&insilj=".$_REQUEST['insilj']."&id=".$_REQUEST['id'],
		'per_page' => $page_row,
		'total_rows' => $totalResult['CNT'],
		'cur_page' => $page,
));

?>

<!-- html영역 -->
<style>
body{background-image: none;}
.container{margin:0px 0px 0px 10px;}
.box_wrap {margin-bottom:10px}
.tb_type01 th, .tb_type01 td {padding: 8px 0}

table.gridhover thead { position: sticky; top: 0;} 
/*
table.gridhover th:first-child{position: -webkit-sticky; position: sticky; left: 0;}
table.gridhover td:first-child{position: -webkit-sticky; position: sticky; left: 0;}
*/
}

</style>


<div class="tb_type01 kwndatalist div_grid" style="overflow-y:auto;">	
		<input type="hidden" name="type" id="type" value="">
		<input type="hidden" name="skey_del" id="skey_del" value="">
		<input type="hidden" name="inscode_del" id="inscode_del" value="">
		<input type="hidden" name="insilj_del" id="insilj_del" value="">
		<input type="hidden" name="seq_del" id="seq_del" value="">
		<table  class="gridhover" id="sort_table_sjiyul"  style="width: 8500px;">
			<colgroup>
				<col width="150px">
				<col width="220px">						
				<col width="80px">											
				<col width="90px">
				<col width="150px">
				<col width="90px">
				<col width="100px">
				<col width="100px">
				<?for($i=1;$i<=100;$i++){?>
				<col width="auto">
				<?}?>
			</colgroup>
			<thead>
			<tr class="rowTop">
				<th align="center">사원명</th>						
				<th align="center">소속</th>					
				<th align="center">직급</th>
				<th align="center">재직구분</th>
				<th align="center">보험사명</th>
				<th align="center">상품보종</th>
				<th align="center">시작일자</th>
				<th align="center">종료일자</th>
				<?for($i=1;$i<=100;$i++){?>
				<th align="center"><?=$i?>회차</th>
				<?}?>						
			</tr>
			</thead>
			<tbody>
				<?if(!empty($listData)){?>
				<?foreach($listData as $key => $val){extract($val);?>
				<tr class="rowData" data1='<?=$skey?>' data2='<?=$inscode?>' data3='<?=$insilj?>' data4='<?=$seq?>' style="cursor:pointer;">
					<td align="left"></i><?=$sname?> ( <?=$skey?> )</td>
					<td align="left"><?=$sosok?></td>
					<td align="center"><?=$subnm?></td>
					<td align="center"><?=$conf['swon_tbit'][$tbit]?></td>
					<td align="left"><?=$insname?></td>
					<td align="left" <?if($insilj=="1"){?>style="color:#5587ED"<?}else if($insilj=="2"){?>style="color:#E0844F"<?}else if($insilj=="3"){?>style="color:#747474"<?}?>
					><?=$conf['insilj'][$insilj]?></td>
					<td><? if(trim($jsyymm)) echo  date("Y-m-d",strtotime($jsyymm));?></td>
					<td><? if(trim($jeyymm)) echo  date("Y-m-d",strtotime($jeyymm));?></td>
					<?for($i=1;$i<=100;$i++){?>
					<td align="right"><?=$listData[$key]["jiyul".$i]?></td>
					<?}?>	
				</tr>
				<?}}else{?>
					<tr>
						<td style="color:#8C8C8C" colspan=11>검색된 데이터가 없습니다</td>
					</tr>
				<?}?>
			</tbody>
		</table>
</div>

<div style="text-align: center">		
	<ul class="pagination pagination-sm sjirulelist" style="margin: 5px">
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
	$(".sjirulelist a").click(function(){
		//alert('asdsadsadsadsadsa');
		var res = $(this).attr("href").split("?");
		if(res[0] && res[1]){
			//alert(res[0]+"//"+res[1]);
			// data_right_jojik div id값 적용
			ajaxLodingTarget(res[0],res[1],event,$('#sjirulelist')); 
		}
		return false;
	});

	$(".rowData > td").click(function(){
		var trData = $(this).parent();
		var idx    = $(".rowData").index($(trData));
		var skey  = $(".rowData").eq(idx).attr('data1');	//사원코드
		var inscode  = $(".rowData").eq(idx).attr('data2');	//보험사코드
		var insilj  = $(".rowData").eq(idx).attr('data3');	//상품보종
		var seq  = $(".rowData").eq(idx).attr('data4');		//순번
		sjirulePopOpen(skey,inscode,insilj,seq,page);

	});

});

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>