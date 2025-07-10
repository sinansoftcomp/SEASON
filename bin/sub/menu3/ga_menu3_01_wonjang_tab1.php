<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

// 기본 페이지 셋팅
$page = ($_GET['page']) ? $_GET['page'] : 1;
$page_row	= 7;

$limit1 = ($page-1)*$page_row+1;
$limit2 = $page*$page_row;

// bit = 2(계약)
$sql	= "
	select *
	from(
		select 
				a.kcode,
				a.num,
				a.tondat,
				a.tontim,
				a.gubun,
				b.subnm,
				case when isnull(a.uswon,'') = '' then c.sname else d.sname end tsname,
				case when isnull(a.udate,'') = '' then convert(varchar(30),a.idate,120) else convert(varchar(30),a.udate,120) end tdate,
				a.tontxt,
				row_number()over(order by a.num desc) rnum
		from atongha a
			left outer join common b on a.scode = b.scode and a.gubun = b.codesub and b.code = 'COM008'
			left outer join swon c on a.scode = c.scode and a.iswon = c.skey
			left outer join swon d on a.scode = d.scode and a.uswon = d.skey
		where a.scode = '".$_SESSION['S_SCODE']."'
		  and a.bit = '2'
		  and a.kcode = '".$_GET['kcode']."'
		 ) p WHERE rnum between ".$limit1." AND ".$limit2 ;
// and a.kcode = '".$_GET['kcode']."'
$qry	= sqlsrv_query( $mscon, $sql );
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listData[]	= $fet;
}


 // 데이터 총 건수
 //검색 데이터 구하기 
$sql= "
	select
		count(*) CNT
	from atongha a
	where a.scode = '".$_SESSION['S_SCODE']."'
	  and a.bit = '2'  
	  and a.kcode = '".$_GET['kcode']."'
	   " ;

$qry = sqlsrv_query( $mscon, $sql );
$totalResult  = sqlsrv_fetch_array($qry);

sqlsrv_free_stmt($qry);
sqlsrv_close($mscon);

// 페이지 클래스 시작
// 로드
include_once($conf['rootDir'].'/include/class/Pagination.php');

// 설정
$pagination = new Pagination(array(
		'base_url' => $_SERVER['PHP_SELF']."?kcode=".$_GET['kcode'],
		'per_page' => $page_row,
		'total_rows' => $totalResult['CNT'],
		'cur_page' => $page,
));
//border-top: 0px solid #47474a;
?>
<style>
.tab_con_wrap .tit_wrap {margin-top: 10px;}




</style>


<div>

	<div class="tit_wrap" style="margin-top:10px">
		<span class="btn_wrap">			
			<a href="#" class="btn_s navy" style="width:95px" onclick="atongha_new('<?=$_GET['kcode']?>','');">진행사항등록</a>
		</span>
	</div>

	<div class="tb_type02" style="margin-top:10px;max-height:300px;">
		<table class="gridhover">

			<colgroup>
				<col width="12%">
				<col width="10%">
				<col width="12%">						
				<col width="12%">							
				<col width="auto">
			</colgroup>

			<thead>
			<tr>
				<th align="center">일자</th>
				<th align="right">시간</th>
				<th align="right">구분</th>
				<th align="right">상담사원</th>						
				<th align="right">상담내용</th>
			</tr>
			</thead>
			<tbody>
				<?if(!empty($listData)){?>
				<?foreach($listData as $key => $val){extract($val);?>
				<tr class="rowData" style="cursor:pointer;" onclick="atongha_new('<?=$kcode?>','<?=$num?>')">
					<td align="center"><?if(trim($tondat)) echo date("Y-m-d",strtotime($tondat));?></td>
					<td align="center"><?=$tontim?></td>
					<td align="center"><?=$subnm?></td>							
					<td align="center"><?=$tsname?></td>
					<td align="left" style="width:870px" class="textover" title="<?=$tontxt?>"><?=$tontxt?></td>
				</tr>
				<?}}else{?>
					<tr>
						<td style="color:#8C8C8C" colspan=11>검색된 데이터가 없습니다</td>
					</tr>
				<?}?>
			</tbody>
		</table>
	</div>
</div>

<div style="text-align: center">		
	<ul class="pagination pagination-sm maintab1" style="margin: 10px">
	  <?=$pagination->create_links();?>
	</ul>
</div>

<!-- 모달 -->
<div id="modal" class="layerBody_atongha">

</div>


<script type="text/javascript">

// 진행사항등록 모달창
function atongha_new(gcode,num){

	$.ajaxLoding('../menu2/ga_menu2_01_atongha_pop.php',$('.layerBody_atongha'),$('#modal'),'&bit=2&gcode='+gcode+'&num='+num);	
}


$(document).ready(function(){

	// 탭 페이징
	$(".maintab1 a").click(function(){
		var res = $(this).attr("href").split("?");
		if(res[0] && res[1]){
			//alert(res[0]+"//"+res[1]);
			ajaxLodingTarget(res[0],res[1],event,$('.BaseTab1'));    
		}
		return false;
	});

	
});

</script>
