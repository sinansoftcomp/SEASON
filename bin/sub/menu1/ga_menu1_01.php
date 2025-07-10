<?
//error_reporting(E_ALL); ini_set('display_errors', 1);

include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

/* ------------------------------------------------------------
	Date 초기값 세팅
------------------------------------------------------------ */
if (isset($_GET['SDATE1'])) {
	$sdate1 =  $_GET['SDATE1'];
	$sdate2 =  $_GET['SDATE2'];
}else{
	$sdate1 =  date("Y-m-01");
	$sdate2 =  date("Y-m-d");
}

/* ------------------------------------------------------------
	End Date 초기값 세팅
------------------------------------------------------------ */
// 첫번째커밋dddddddddddddfaadfdf
$where = "";

if(isset($_GET['searchF1'])){
	$searchF1 = $_GET['searchF1'];
}else{
	$searchF1 = "a.kname";
}


if(isset($_GET['searchF1Text'])){
	$searchF1Text = $_GET['searchF1Text'];
}else{
	$searchF1Text = "";
}

if(isset($_GET['searchF1']) && isset($_GET['searchF1Text'])){
	$where  .= " and ".$_GET['searchF1']." like '%".$_GET['searchF1Text']."%' ";
}
$wherescript = Encrypt_where($where,$secret_key,$secret_iv);
// 기본 페이지 셋팅
$page = (isset($_GET['page'])) ? $_GET['page'] : 1;
$page_row	= 50;

$limit1 = ($page-1)*$page_row+1;
$limit2 = $page*$page_row;

//검색 데이터 구하기 
$sql= "
	select *
	from(
		select 
			a.KCODE
			,dbo.DECRYPTKEY(a.KPASS) KPASS
			,a.KNAME
			,a.COMPNAME
			,a.DNAME
			,substring(a.COMPNUM,1,3)+'-'+substring(a.COMPNUM,4,2)+'-'+substring(a.COMPNUM,6,5) COMPNUM
			,a.COMPYN
			,a.TAXEMAIL
			,a.ADDR1
			,a.ADDR2
			,a.BCOLOR
			,a.BPOST
			,a.BADDR1
			,a.BADDR2
			,a.TAXPOST
			,a.TAXADDR1
			,a.TAXADDR2
			,a.UPTAE
			,a.UPJONG
			,a.TEL
			,a.HTEL
			,a.MESSYN
			,a.HTEL2
			,a.POINT
			,a.ACCOUNT
			,a.ACCDATE
			,convert(varchar,a.UDATE,21) UDATE,
			row_number()over(order by a.KCODE desc) rnum
		from kwn(nolock) a
		where a.kdate between '".str_replace('-','',$sdate1)."' and '".str_replace('-','',$sdate2)."' ".$where."	
		) p
	where rnum between ".$limit1." AND ".$limit2 ;

$qry	= sqlsrv_query( $mscon, $sql );
$listData = array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listData[]	= $fet;
}

// 데이터 총 건수
//검색 데이터 구하기 
$sql= "
		select count(*) CNT
		from kwn(nolock) a
		where a.kdate between '".str_replace('-','',$sdate1)."' and '".str_replace('-','',$sdate2)."' ".$where." " ;

$qry =  sqlsrv_query($mscon, $sql);
$totalResult =  sqlsrv_fetch_array($qry); 


// 페이지 클래스 시작
// 로드
include_once($conf['rootDir'].'/include/class/Pagination.php');

// 설정
$pagination = new Pagination(array(
		'base_url' => $_SERVER['PHP_SELF']."?SDATE1=".$sdate1."&SDATE2=".$sdate2."&searchF1=".$searchF1."&searchF1Text=".$searchF1Text,
		'per_page' => $page_row,
		'total_rows' => $totalResult['CNT'],
		'cur_page' => $page,
));

sqlsrv_free_stmt($result);
sqlsrv_close($mscon);

?>

<!-- html영역 -->
<style>
body{background-image: none;}

</style>

<div class="container">
	<div class="content_wrap">
		<fieldset>
			<legend>고객관리</legend>
			
			<!-- 검색조건 -->
			<div class="box_wrap sel_btn">
				<form name="searchFrm" method="get" action="<?$_SERVER['PHP_SELF']?>">
				<input type="hidden" name="btn"  id="btn"  value="">
					<fieldset>
						<legend>고객관리 기간별 검색</legend>
						<div class="">
							<span class="ser_font"> 가입일자</span> 
							<span class="input_type date" style="width:114px">
								<input type="text" class="Calnew" placeholder="YYYY-MM-DD" id="SDATE1" name="SDATE1" value="<?=$sdate1?>" readonly>
							</span> 
							<span class="dash"> ~ </span>
							<span class="input_type date" style="width:114px">
								<input type="text" class="Calnew" placeholder="YYYY-MM-DD" id="SDATE2" name="SDATE2" value="<?=$sdate2?>" readonly>
							</span>

							<select name="searchF1" id="searchF1" class="srch_css" style="width:98px;height:28px;margin-left:10px; padding-left:0px">
								<option value="a.kname" <?if($searchF1=="a.kname") echo "selected"?>>고객명</option>
								<option value="a.compnum"   <?if($searchF1=="a.compnum") echo "selected"?>>사업자번호</option>
								<option value="a.htel"   <?if($searchF1=="a.htel") echo "selected"?>>휴대폰번호</option>
							</select>
							<input type="text" name="searchF1Text" id="searchF1Text" style="width:125px;height:26px;border:1px solid #b7b7b7" value="<?=$searchF1Text?>" >

							<span class="btn_wrap" style="margin-left:10px">
								<a class="btn_s white hover_btn btn_search btn_off" style="width:80px;margin:0px" onclick="common_ser();">조회</a>
								<a class="btn_s white btn_off excelBtn" style="width:80px;">엑셀</a>
							</span>

						</div>

					</fieldset>
				</form>
			</div><!-- // box_wrap -->

			<div id="kwngo_sort" class="tb_type01 div_grid" style="overflow-y:auto;">
				<table class="gridhover">
					<colgroup>
						<col width="120px">
						<col width="150px">
						<col width="130px">
						<col width="110px">
						<col width="110px">
						<col width="100px">
						<col width="150px">

						<col width="150px">
						<col width="110px">
						<col width="110px">

						<col width="100px">
						<col width="140px">
						<col width="auto">
					</colgroup>
					<thead>
					<tr class="rowTop">
						<th align="left">고객ID</th>
						<th align="center">상호명</th>
						<th align="center">고객명</th>
						<th align="center">대표자명</th>
						<th align="center">사업자번호</th>
						<th align="center">사업자인증여부</th>
						<th align="center">시도</th>

						<th align="center">군</th>
						<th align="center">전화번호</th>
						<th align="center">휴대폰번호</th>						

						<th align="center">적립금</th>
						<th align="center">가상계좌번호</th>
						<th align="center">등록/수정일시</th>
					</tr>
					</thead>
					<tbody>
						<?if(!empty($listData)){?>
						<?foreach($listData as $key => $val){extract($val);
							if($sbit == '1'){
								$sec_data = substr($secdata,0,6).'-'.substr($secdata,6,7);
							}else{
								$sec_data = substr($secdata,0,3).'-'.substr($secdata,3,2).'-'.substr($secdata,5,5);
							}

							if($totaltel == '--'){
								$totaltel = '';
							}

						
						?>
						<tr class="rowData" rol-date='<?=$KCODE?>'>
							<td align="center"><?=$KCODE?></td>
							<td align="left"><?=$COMPNAME?></td>
							<td align="left"><?=$KNAME?></td>
							<td align="left"><?=$DNAME?></td>
							<td align="center"><?=$COMPNUM?></td>	
							<td align="center"><?if($COMPYN=='Y'){?><i class="fa fa-genderless font_blue" aria-hidden="true"></i><?}else{?><i class="fa fa-times font_red" aria-hidden="true"></i><?}?></td>
							<td align="center"><?=$ADDR1?></td>

							<td align="center"><?=$ADDR2?></td>
							<td align="left"><?=$TEL?></td>
							<td align="left"><?=$HTEL?></td>

							<td align="right"><?=number_format($POINT)?></td>
							<td align="center"><?=$ACCOUNT?></td>
							<td align="center"><?=$UDATE?></td>	
						</tr>
						<?}}else{?>
							<tr>
								<td style="color:#8C8C8C" colspan=13>검색된 데이터가 없습니다</td>
							</tr>
						<?}?>
					</tbody>
				</table>
			</div><!-- // tb_type01 -->

			<div style="text-align: center">		
				<ul class="pagination pagination-sm" style="margin: 5px 5px 0 5px">
				  <?=$pagination->create_links();?>
				</ul>
			</div>

		</fieldset>
	</div><!-- // content_wrap -->
</div>
<!-- // container -->
<!-- // wrap -->
<script type="text/javascript">

// 고객등록
function KwngoIns(gcode){

	var left = Math.ceil((window.screen.width - 1000)/2);
	var top = Math.ceil((window.screen.height - 830)/2);
	var popOpen	= window.open("<?=$conf['homeDir']?>/sub/menu2/ga_menu2_01_pop.php?gcode="+gcode,"KwngoDt","width=1000px,height=760px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
	popOpen.focus();
}

// 헤더 클릭
$(".rowTop > th").click(function(){
	var trData = $(this).parent();

	var idx = $(trData).find("th").index($(this));

	// include/bottom.php 참조
	sortTable("kwngo_sort", idx, '');
})


// 공통조회 함수(bin/js/common.js 호출)
function common_ser(){ 
		$("#div_load_image").show();
		$("form[name='searchFrm']").attr("method","get");
		$("form[name='searchFrm']").attr("target","");
		$("form[name='searchFrm']").submit();
}

$(document).ready(function(){

	$(".excelBtn").click(function(){
		if($('#excelcnt').val() == 0 ){
			alert('내려받을 데이터가 존재하지 않습니다.');
		}else{
			if(confirm("엑셀로 내려받으시겠습니까?")){
				//$("#div_load_image").show();
				$("form[name='searchFrm']").attr("action","ga_menu2_01_excel.php");
				$("form[name='searchFrm']").submit();
				$("form[name='searchFrm']").attr("action","<?$_SERVER['PHP_SELF']?>");
			}
		}
	});

	//window.parent.postMessage("고객관리 > 영업중고객관리", "*");   // '*' on any domain 부모로 보내기..        

	// 리스트 클릭시 상세내용 조회
	$(".rowData > td").click(function(){
		var trData = $(this).parent();
		var idx    = $(".rowData").index($(trData));

		var gcode  = $(".rowData").eq(idx).attr('rol-date');
		KwngoIns(gcode);
	})

	// 월 선택시 해당월에 클릭넘겨받기(function d_ser 에서 변수 넘김)
	var btn		= '<?=$_GET['btn']?>';
	if(btn){
		$(".box_wrap.sel_btn a").removeClass('on');
		$("#"+btn).addClass('on');
	}


});

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>