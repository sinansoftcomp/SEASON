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
				a.seq,
				a.gubun,
				a.hdate,
				b.kdate,
				b.inscode,
				e.name insname,
				b.itemnm,
				a.hbit,
				b.kstbit,
				case when isnull(a.uswon,'') = '' then c.sname else d.sname end tsname,
				case when isnull(a.udate,'') = '' then convert(varchar(30),a.idate,120) else convert(varchar(30),a.udate,120) end tdate,
				row_number()over(order by a.seq desc) rnum
		from hymst a
			left outer join kwn b on a.scode = b.scode and a.kcode = b.kcode 
			left outer join swon c on a.scode = c.scode and a.iswon = c.skey
			left outer join swon d on a.scode = d.scode and a.uswon = d.skey
			left outer join inssetup e on a.scode = e.scode and b.inscode = e.inscode
		where a.scode = '".$_SESSION['S_SCODE']."'
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
	from hymst a
		left outer join kwn b on a.scode = b.scode and a.kcode = b.kcode 
		left outer join swon c on a.scode = c.scode and a.iswon = c.skey
		left outer join swon d on a.scode = d.scode and a.uswon = d.skey
	where a.scode = '".$_SESSION['S_SCODE']."'
	  and a.kcode = '".$_GET['kcode']."' " ;

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

?>
<style>
.tab_con_wrap .tit_wrap {margin-top: 10px;}




</style>


<div>

	<div class="tit_wrap" style="margin-top:10px">
		<span class="btn_wrap">
			<a href="#" class="btn_s navy" style="width:70px" onclick="hymst_new('1','<?=$_GET['kcode']?>','');">해약</a>
			<a href="#" class="btn_s navy" style="width:70px;margin-left:5px" onclick="hymst_new('2','<?=$_GET['kcode']?>','');">청약철회</a>
			<a href="#" class="btn_s navy" style="width:70px;margin-left:5px" onclick="hymst_new('3','<?=$_GET['kcode']?>','');">민원해지</a>
			<a href="#" class="btn_s navy" style="width:70px;margin-left:5px" onclick="hymst_new('4','<?=$_GET['kcode']?>','');">품질보증</a>
			<a href="#" class="btn_s navy" style="width:70px;margin-left:5px" onclick="hymst_new('5','<?=$_GET['kcode']?>','');">위법계약</a>
			<a href="#" class="btn_s navy" style="width:70px;margin-left:5px" onclick="hymst_new('6','<?=$_GET['kcode']?>','');">반송해지</a>
			<a href="#" class="btn_s navy" style="width:70px;margin-left:5px" onclick="hymst_new('7','<?=$_GET['kcode']?>','');">보험취소</a>
			<a href="#" class="btn_s navy" style="width:70px;margin-left:5px" onclick="hymst_new('20','<?=$_GET['kcode']?>','');">부활</a>
		</span>
	</div>

	<div class="tb_type02" style="margin-top:10px;max-height:300px">
		<table class="gridhover">

			<colgroup>
				<col width="10%">
				<col width="10%">
				<col width="10%">						
				<col width="15%">							
				<col width="auto">
				<col width="10%">
				<col width="10%">
			</colgroup>

			<thead>
			<tr>
				<th align="center">해약/해지일자</th>
				<th align="center">구분</th>
				<th align="center">계약일자</th>
				<th align="center">보험사</th>						
				<th align="center">상품명</th>
				<th align="center">등록/수정일시</th>
				<th align="center">등록/수정사원</th>
			</tr>
			</thead>
			<tbody>
				<?if(!empty($listData)){?>
				<?foreach($listData as $key => $val){extract($val);?>
				<tr class="rowData" style="cursor:pointer;" onclick="hymst_new('<?=$gubun?>','<?=$kcode?>','<?=$seq?>');">
					<td align="center"><?if(trim($hdate)) echo date("Y-m-d",strtotime($hdate));?></td>
					<td align="center"><?=$conf['hymst_gubun'][$gubun]?></td>
					<td align="center"><?if(trim($kdate)) echo date("Y-m-d",strtotime($kdate));?></td>			
					<td align="left"><?=$insname?></td>
					<td align="left"><?=$itemnm?></td>
					<td align="center"><?=$tdate?></td>
					<td align="center"><?=$tsname?></td>
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
	<ul class="pagination pagination-sm maintab3" style="margin: 10px">
	  <?=$pagination->create_links();?>
	</ul>
</div>

<!-- 모달 -->
<div id="modal3" class="layerBody_hymst">

</div>


<script type="text/javascript">

// 진행사항등록 모달창
function hymst_new(gubun,kcode,seq){

	$.ajaxLoding('ga_menu3_01_wonjang_tab3_modal.php',$('.layerBody_hymst'),$('#modal3'),'&gubun='+gubun+'&kcode='+kcode+'&seq='+seq);	
}


$(document).ready(function(){

	// 탭 페이징
	$(".maintab3 a").click(function(){
		var res = $(this).attr("href").split("?");
		if(res[0] && res[1]){
			//alert(res[0]+"//"+res[1]);
			ajaxLodingTarget(res[0],res[1],event,$('.BaseTab3'));    
		}
		return false;
	});

	
});

</script>
