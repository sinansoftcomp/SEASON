<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

$fyymmdd=  substr($_GET['SDATE1'],0,4).substr($_GET['SDATE1'],5,2); 

// 기본 페이지 셋팅
$page = ($_GET['page']) ? $_GET['page'] : 1;
$page_row	= 100;

$limit1 = ($page-1)*$page_row+1;
$limit2 = $page*$page_row;

$where = "";

if($_REQUEST['id']){
	
	$Ngubun = substr($_REQUEST['id'],0,2);

	if($Ngubun == 'N1'){
		$where  .= "" ;
	}else if($Ngubun == 'N2'){
		$inscode = substr($_REQUEST['id'],2,10);
		$where  .= " and a.inscode = '".$inscode."' " ;
	}
}

if($_REQUEST['nmyn']){
	if($_REQUEST['nmyn'] == 'Y'){
		$where .= " and isnull(a.ksman,'') <> '' ";
	}else if($_REQUEST['nmyn'] == 'N'){
		$where .= " and isnull(a.ksman,'') = '' ";
	}
}

$sql	= "
	select *
	from(
		select a.yymm,a.kcode,a.inscode,b.name,a.item,a.itemnm,a.ksman,
				c.skey,c.sname , e.bname , f.jsname , g.jname , h.tname,row_number() over(order by a.inscode,a.kcode) rnum
		from ins_ipmst a left outer join inssetup b on a.scode = b.scode and a.inscode = b.inscode and b.useyn = 'Y'
							left outer join inswon cc on a.scode = cc.scode and a.ksman = cc.bscode
							left outer join swon c on a.scode = c.scode and cc.skey = c.skey
							left outer join bonbu e on c.scode = e.scode and c.bonbu = e.bcode
							left outer join jisa  f on c.scode = f.scode and c.jisa = f.jscode
							left outer join jijum g on c.scode = g.scode and c.jijum = g.jcode
							left outer join team h  on c.scode = h.scode and c.team = h.tcode
		where a.scode = '".$_SESSION['S_SCODE']."' and a.yymm = '".$fyymmdd."' and isnull(a.nmgubun,'') = 'Y' $where
		group by a.yymm,a.kcode,a.inscode,b.name,a.item,a.itemnm,a.ksman,c.skey,c.sname , e.bname , f.jsname , g.jname , h.tname
	 ) p WHERE rnum between ".$limit1." AND ".$limit2 ;

$qry	= sqlsrv_query( $mscon, $sql );
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listData[]	= $fet;
}
/*
echo "<pre>";
echo $sql;
echo "</pre>";
*/
 // 데이터 총 건수
 //검색 데이터 구하기 
$sql= "
	select count(*) CNT
	from(
		select a.yymm,a.kcode,a.inscode,b.name,a.item,a.itemnm,a.ksman
		from ins_ipmst a left outer join inssetup b on a.scode = b.scode and a.inscode = b.inscode and b.useyn = 'Y'
		where a.scode = '".$_SESSION['S_SCODE']."' and a.yymm = '".$fyymmdd."' and isnull(a.nmgubun,'') = 'Y' $where
		group by a.yymm,a.kcode,a.inscode,b.name,a.item,a.itemnm,a.ksman
		) aa
	" ;

$qry = sqlsrv_query( $mscon, $sql );
$totalResult  = sqlsrv_fetch_array($qry);


// 비매칭 여부체크
$sql= "
	select count(*) CNT
	from(
		select a.yymm,a.kcode,a.inscode,b.name,a.item,a.itemnm,a.ksman
		from ins_ipmst a left outer join inssetup b on a.scode = b.scode and a.inscode = b.inscode and b.useyn = 'Y'
		where a.scode = '".$_SESSION['S_SCODE']."' and a.yymm = '".$fyymmdd."' and isnull(a.nmgubun,'') = 'Y' 
		group by a.yymm,a.kcode,a.inscode,b.name,a.item,a.itemnm,a.ksman
		) aa
	" ;

$qry = sqlsrv_query( $mscon, $sql );
$totalResult_nm  = sqlsrv_fetch_array($qry);


// 보험사 가져오기
$sql= "select inscode, name from inssetup(nolock) where scode = '".$_SESSION['S_SCODE']."' and useyn = 'Y' order by num, inscode";
$qry= sqlsrv_query( $mscon, $sql );
$insData	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
  $insData[] = $fet;
}

sqlsrv_free_stmt($qry);
sqlsrv_close($mscon);

// 페이지 클래스 시작
// 로드
include_once($conf['rootDir'].'/include/class/Pagination.php');

// 설정
$pagination = new Pagination(array(
		'base_url' => $_SERVER['PHP_SELF']."?code=".$_REQUEST['id']."&SDATE1=".$_GET['SDATE1']."&nmyn=".$_REQUEST['nmyn'],
		'per_page' => $page_row,
		'total_rows' => $totalResult['CNT'],
		'cur_page' => $page,
));
?>
<style>
body{background-image: none;}
</style>


<div class="tb_type01 kwndatalist div_grid" style="overflow-y:auto;">	
	<table class="gridhover">
		<colgroup>
			<col style="width:8%">
			<col style="width:12%">
			
			<col style="width:8%">
			<col style="width:10%">
			<col style="width:12%">
			<col style="width:auto">

			<col style="width:15%">
		</colgroup>
		<thead>
		<tr>				
			<th sortData="yymm">정산월</th>
			<th sortData="kcode">증권번호</th>	

			<th sortData="name">보험사명</th>	
			<th sortData="item">사원코드</th>
			<th sortData="itemnm">사원명</th>
			<th sortData="itemnm">소속</th>

			<th sortData="ksman">사용인코드</th>
		</tr>
		</thead>
		<tbody>
		<?if(!empty($listData)){?>
		<?foreach($listData as $key => $val){extract($val);?>
		<tr class="rowData" rol-date1='<?=$yymm?>' rol-date2='<?=$kcode?>'>
			<td align="center"><?=date("Y-m",strtotime($yymm.'01'))?></td>
			<td style="text-align:left"><?=$kcode?></td>

			<td style="text-align:left"><?=$name?></td>
			<td style="text-align:left"><?=$skey?></td>
			<td style="text-align:left"><?=$sname?></td>		

			<?$sosok = substr($bname,0,4).'>'. substr($jsname,0,4).'>'. substr($jname,0,8).'>'. substr($tname,0,4)   ?>
			<?$sosok = str_replace('>>','>',$sosok)?> 
			<?$sosok = str_replace('>>','>',$sosok)?> 
			<td align="left"><?=$sosok?></td>	

			<td><?=$ksman?></td>
		</tr>
		<?}}?>
		</tbody>
	</table>
</div>
<div style="text-align: center">		
	<ul class="pagination pagination-sm nmswon" style="margin: 5px 5px 0 5px">
	  <?=$pagination->create_links();?>
	</ul>
</div>

<script type="text/javascript">

function excelupGPopOpen(yymm,kcode){

	var left = Math.ceil((window.screen.width - 400)/2);
	var top = Math.ceil((window.screen.height - 400)/2);
	var popOpen	= window.open("<?=$conf['homeDir']?>/sub/menu5/ga_menu5_06_pop2.php?yymm="+yymm+"&kcode="+kcode,"excelup2","width=550px,height=220px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
	//popOpen.focus();
}

$(document).ready(function(){

	if('<?=$totalResult_nm["CNT"]?>' == 0){
		alert("비매칭적용버튼을 클릭 후 진행해주세요.");
	}

	//$('#yymm_nm').val('<?=$fyymmdd?>');
	$('#excelcnt').val('<?=$totalResult["CNT"]?>');

	// page 함수 ajax페이지 존재시 별도 처리
	$(".nmswon a").click(function(){
		//alert('asdsadsadsadsadsa');
		var res = $(this).attr("href").split("?");
		if(res[0] && res[1]){
			//alert(res[0]+"//"+res[1]);
			// data_right_jojik div id값 적용
			ajaxLodingTarget(res[0],res[1],event,$('#nmswon')); 
		}
		return false;
	});



	$(".rowData > td").click(function(){
		var trData = $(this).parent();
		var idx    = $(".rowData").index($(trData));
		var yymm  = $(".rowData").eq(idx).attr('rol-date1'); 
		var kcode  = $(".rowData").eq(idx).attr('rol-date2'); 

		
		excelupGPopOpen(yymm,kcode);

	})
	
});

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>