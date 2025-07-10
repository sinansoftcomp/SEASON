<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");


$where = " ";

//PRINT_r($_REQUEST);

$FYYMM   = substr($_REQUEST['SDATE1'],0,4).substr($_REQUEST['SDATE1'],5,2).substr($_REQUEST['SDATE1'],8,2);
$TYYMM  =  substr($_REQUEST['SDATE2'],0,4).substr($_REQUEST['SDATE2'],5,2).substr($_REQUEST['SDATE2'],8,2);

if($_REQUEST['datekind']=="k_idate"){
	$where .= " and convert(varchar(8),a.idate,112) between '".$FYYMM."' and '".$TYYMM."' ";
}else if($_REQUEST['datekind']=="k_fdate") {
	$where .= " and a.fdate between '".$FYYMM."' and '".$TYYMM."' ";
}else if($_REQUEST['datekind']=="k_tdate") {
	$where .= " and a.tdate between '".$FYYMM."' and '".$TYYMM."' ";
}



// 조직도 트리 선택시 소속정보(swon 별칭 : s2 - kdman(사용인기준)) 
if(isset($_REQUEST['id'])){
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
		$where .= " and b.skey = '".$scode."'";
	}
}

if(isset($_REQUEST['rbit'])){
	if($_REQUEST['rbit']){
		$where .= " and a.rbit= '".$_REQUEST['rbit']."' ";
	}
}


if(isset($_REQUEST['jumin'])){
	$jumin = iconv("UTF-8","EUCKR",$_REQUEST['jumin']);
	$where .= " and (dbo.decryptkey(a.jumin) like '%".$jumin."%' or a.pname like '%".$jumin."%') ";
}


if(isset($_REQUEST['carnumber'])){
	$where .= " and a.carnumber like '%".iconv("UTF-8","EUCKR",$_REQUEST['carnumber'])."%' ";
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
		select *, ROW_NUMBER()over(order by idate desc) rnum 
		from(
			select a.scode,a.carseq,a.caruse,a.pname,dbo.decryptkey(a.jumin) jumin,a.carnumber,a.fdate,a.tdate,a.bfins,a.carcode,a.cargrade,a.baegicc,a.caryear,a.cardate,a.car_kind,a.carname,a.people_numcc,a.ext_bupum_txt,a.
					ext_bupum,a.add_bupum,a.carprice1,a.carprice,a.fuel,a.hi_repair,a.buy_type,a.guipcarrer,a.traffic,a.lawcodecnt,a.halin,a.special_code,a.special_code1,a.ncr_code,a.ncr_code2,a.
					ss_point,a.ss_point3,a.car_guip,a.car_own,a.careercode3,a.otheracc,a.ijumin,a.fetus,a.icnt,a.tmap_halin,a.car_own_halin,a.religionchk,a.eco_mileage,a.jjumin,a.j_name,a.lowestjumin,a.
					l_name,a.c_jumin,a.c_name,a.devide_num,a.muljuk,a.milegbn,a.milekm,a.nowkm,a.dambo2,a.dambo3,a.dambo4,a.dambo5,a.dambo6,a.goout,a.carage,a.carfamily,a.kcode,a.kdman,a.rateapi,a.
					ratedt,a.inyn,a.indt,a.upmu,a.rbit,a.selins,a.reday,a.rehour,a.bigo,a.cnum,a.agins,a.agno,a.memo,a.idate,convert(varchar(8),a.idate,112) idatevc,a.iswon,aa.skey,b.sname,c.bname,d.jsname,e.jname,f.tname
			from carest a left outer join inswon aa on a.scode = aa.scode and a.kdman = aa.bscode
						left outer join swon b on aa.scode = b.scode and aa.skey = b.skey
						left outer join bonbu c on b.scode = c.scode and b.bonbu = c.bcode
						left outer join jisa d on b.scode = d.scode and b.jisa = d.jscode
						left outer join jijum e on b.scode = e.scode and b.jijum = e.jcode
						left outer join team f on b.scode = f.scode and b.team = f.tcode
			where a.scode = '".$_SESSION['S_SCODE']."'  ".$where."
		) tbl
	) p
		where rnum between ".$limit1." AND ".$limit2 ;
$qry	= sqlsrv_query( $mscon, $sql );
$listData = array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listData[]	= $fet;
}


// 데이터 총 건수
$sql= "
			select count(*) CNT
			from carest a left outer join inswon aa on a.scode = aa.scode and a.kdman = aa.bscode
						left outer join swon b on aa.scode = b.scode and aa.skey = b.skey
						left outer join bonbu c on b.scode = c.scode and b.bonbu = c.bcode
						left outer join jisa d on b.scode = d.scode and b.jisa = d.jscode
						left outer join jijum e on b.scode = e.scode and b.jijum = e.jcode
						left outer join team f on b.scode = f.scode and b.team = f.tcode
			where a.scode = '".$_SESSION['S_SCODE']."'  ".$where."
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
		'base_url' => $_SERVER['PHP_SELF']."?SDATE1=".$_REQUEST['SDATE1']."&SDATE2=".$_REQUEST['SDATE2']."&datekind=".$_REQUEST['datekind']."&id=".$_REQUEST['id']."&rbit=".$_REQUEST['rbit']."&jumin=".$jumin."&carnumber=".$_REQIEST['carnumber'],
		'per_page' => $page_row,
		'total_rows' => $totalResult['CNT'],
		'cur_page' => $page,
));

if (is_resource($result)) {
    sqlsrv_free_stmt($result);
}		

if (is_resource($mscon)) {
    sqlsrv_close($mscon);
}

?>

<!-- html영역 -->
<style>
body{background-image: none;}
.container{margin:0px 0px 0px 10px;}
.box_wrap {margin-bottom:10px}
.tb_type01 th, .tb_type01 td {padding: 8px 0}
table.gridhover thead { position: sticky; top: 0;} 
}

</style>

<div class="tb_type01 kwndatalist div_grid" style="overflow-y:auto;">	
		<input type="hidden" name="type" id="type" value="">
		<input type="hidden" name="skey_del" id="skey_del" value="">
		<input type="hidden" name="inscode_del" id="inscode_del" value="">
		<input type="hidden" name="insilj_del" id="insilj_del" value="">
		<input type="hidden" name="seq_del" id="seq_del" value="">
		<table  class="gridhover" id="sort_table_sjiyul">
			<colgroup>
				<col width="120px">
				<col width="100px">	
				<col width="150px">

				<col width="130px">
				<col width="100px">
				<col width="130px">
				<col width="130px">

				<col width="120px">
				<col width="120px">
				<col width="220px">
				<col width="130px">
				<col width="150px">

			</colgroup>
			<thead>
			<tr class="rowTop">
				<th align="center">입력일</th>	
				<th align="center">담당사원명</th>					
				<th align="center">소속</th>
				<th align="center">설계상태</th>
				<th align="center">피보험자명</th>
				<th align="center">보험종목</th>
				<th align="center">생년월일/사업자번호</th>				
				<th align="center">보험시작일</th>
				<th align="center">보험종료일</th>
				<th align="center">차명</th>
				<th align="center">차량번호</th>			
				<th align="center">특약여부</th>
			</tr>
			</thead>
			<tbody>
				<?if(!empty($listData)){?>
				<?foreach($listData as $key => $val){extract($val);?>
				<tr class="rowData" rol-data='<?=$carseq?>'>
					<td><? if(trim($idatevc)) echo  date("Y-m-d",strtotime($idatevc));?></td>
					<td align="left"><?=$sname?></td>
					<?$sosok = substr($bname,0,4).'>'. substr($jsname,0,4).'>'. substr($jname,0,8).'>'. substr($tname,0,4)   ?>
					<?$sosok = str_replace('>>','>',$sosok)?> 
					<?$sosok = str_replace('>>','>',$sosok)?> 
					<td align="left" ><?=$sosok?></td>
					<td align="center"><?=$conf['rbit'][$rbit]?></td>
					<td align="left"><?=$pname?></td>
					<td align="center"><?=$conf['caruse'][$caruse]?></td>
					<td align="left"><?=$jumin?></td>				
					<td><? if(trim($fdate)) echo  date("Y-m-d",strtotime($fdate));?></td>
					<td><? if(trim($tdate)) echo  date("Y-m-d",strtotime($tdate));?></td>
					<td align="left"><?=$carname?></td>
					<td align="center"><?=$carnumber?></td>

					<?if($listData[$key]['ijumin']){$speciali="자녀 ,";}else{$speciali="";};?>
					<?if($listData[$key]['car_own_halin']){$specialc="다수차량할인 ,";}else{$specialc="";};?>
					<?if($listData[$key]['religionchk']){$specialr="종교단체";}else{$specialr="";};?>
					<td align="left"><?if(substr($speciali.$specialc.$specialr,-1)==","){echo substr($speciali.$specialc.$specialr,0,-1);}else{echo $speciali.$specialc.$specialr;}?></td>
				</tr>
				<?}}else{?>
					<tr>
						<td style="color:#8C8C8C" colspan=12>검색된 데이터가 없습니다</td>
					</tr>
				<?}?>
			</tbody>
		</table>
</div>

<div style="text-align: center">		
	<ul class="pagination pagination-sm carestlist" style="margin: 1px">
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

	// page 함수 ajax페이지 존재시 별도 처리
	$(".carestlist a").click(function(){
		var res = $(this).attr("href").split("?");
		if(res[0] && res[1]){
			//alert(res[0]+"//"+res[1]);
			// data_right_jojik div id값 적용
			ajaxLodingTarget(res[0],res[1],event,$('#carestlist'));    
		}
		return false;
	});	

	$(".rowData > td").click(function(){
		var trData = $(this).parent();
		var idx    = $(".rowData").index($(trData));

		var carseq  = $(".rowData").eq(idx).attr('rol-data');

		var left = Math.ceil((window.screen.width - 1800)/2);
		var top = Math.ceil((window.screen.height - 1000)/2);
		
		var popOpen	= window.open("<?=$conf['homeDir']?>/sub/menu6/ga_menu6_01.php?carseq="+carseq,"_blank","width=1800px,height=950px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
		popOpen.focus();
		
	});

});

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>