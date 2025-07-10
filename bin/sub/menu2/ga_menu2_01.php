<?
//error_reporting(E_ALL); ini_set('display_errors', 1);

include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

/*
	권한관리
	bin/include/source/auch_chk.php
*/
$pageTemp	= explode("/",$_SERVER['PHP_SELF']);
$auth = auth_Ser($_SESSION['S_MASTER'], $pageTemp[count($pageTemp)-1], $_SESSION['S_SKEY'], $mscon);
if($auth != "Y"){
	sqlsrv_close($mscon);
	alert('해당 메뉴에 대해 권한이 없습니다. 관리자에게 문의 바랍니다.');
	exit;
}

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

$where = "";

if(isset($_GET['btn'])){
	$btn = $_GET['btn'];
}else{
	$btn = "";
}

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
	if($_GET['searchF1'] == 'sjuno'){
		$where  .= " and (a.snum like '%".$_GET['searchF1Text']."%' or Cast(dbo.DECRYPTKEY(a.sjuno) as varchar) like '%".$_GET['searchF1Text']."%') ";
	}else if($_GET['searchF1'] == 'tel'){
		$where  .= " and (a.tel1+a.tel2+a.tel3 like replace('%".$_GET['searchF1Text']."%','-','') or a.htel1+a.htel2+a.htel3 like replace('%".$_GET['searchF1Text']."%','-','')) ";
	}else{		
		$where  .= " and ".$_GET['searchF1']." like '%".$_GET['searchF1Text']."%' ";	
	}
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
				a.scode,
				a.gcode,
				a.kname,
				a.sbit,
				dbo.decryptkey(a.sjuno) sjuno,
				a.snum,
				case when a.sbit = '1' then dbo.decryptkey(a.sjuno) else a.snum end secdata,
				a.comnm,
				a.cupnm,
				a.emailsel,
				a.email,
				a.telbit,
				a.tel1,
				a.tel2,
				a.tel3,
				a.tel1+'-'+a.tel2+'-'+a.tel3 tel,
				a.htel1,
				a.htel2,
				a.htel3,
				a.htel1+'-'+a.htel2+'-'+a.htel3 htel,
				case when len(isnull(a.htel1,'')+'-'+isnull(a.htel2,'')+'-'+isnull(a.htel3,'')) > 2 then isnull(a.htel1,'')+'-'+isnull(a.htel2,'')+'-'+isnull(a.htel3,'') 
					 else isnull(a.tel1,'')+'-'+isnull(a.tel2,'')+'-'+isnull(a.tel3,'') end totaltel,
				a.addbit,
				a.post,
				a.addr,
				a.addr_dt,
				a.psrate,
				a.bigo,
				a.sugi,
				a.kdate,
				convert(varchar,a.idate,21) idate,
				a.iswon,
				b.sname isname,
				a.udate,
				a.uswon,
				c.sname usname,
				a.ksman,
				e.sname ksname,

				case when isnull(e.bonbu,'') != '' then substring(f.bname,1,2) else '' end +
				case when isnull(e.bonbu,'') != '' and (isnull(e.jisa,'') != '' or isnull(e.team,'') != '')  then ' > ' else '' end +
				case when isnull(e.jisa,'') != '' then substring(g.jsname,1,4) else '' end +
				case when isnull(e.jisa,'') != '' and isnull(e.jijum,'') != '' then ' > ' else '' end +
				case when isnull(e.jijum,'') != '' then substring(h.jname,1,4) else '' end +
				case when isnull(e.jijum,'') != '' and isnull(e.team,'') != '' then ' > ' else '' end +
				case when isnull(e.team,'') != '' then i.tname else '' end as sosok,

				case when isnull(a.htel1,'')+isnull(a.htel2,'')+isnull(a.htel3,'') <> '' and len(isnull(a.htel1,'')+isnull(a.htel2,'')+isnull(a.htel3,'')) >= 10 and substring(isnull(a.htel1,''),1,2) = '01'
								then 'Y' else 'N' end smsyn ,

				row_number()over(order by a.kdate desc, a.gcode desc) rnum
		from kwngo(nolock) a
			left outer join swon(nolock) b on a.scode = b.scode and a.iswon = b.skey
			left outer join swon(nolock) c on a.scode = c.scode and a.uswon = c.skey
			left outer join swon(nolock) e on a.scode = e.scode and a.ksman = e.skey
			left outer join bonbu(nolock) f on e.scode = f.scode and e.bonbu = f.bcode
			left outer join jisa(nolock) g on e.scode = g.scode and e.jisa = g.jscode
			left outer join jijum(nolock) h on e.scode = h.scode and e.jijum = h.jcode
			left outer join team(nolock) i on e.scode = i.scode and e.team = i.tcode
			
		where a.scode = '".$_SESSION['S_SCODE']."'
		  and a.kdate between '".str_replace('-','',$sdate1)."' and '".str_replace('-','',$sdate2)."'  ".$where."
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
		select 
				count(*) CNT
		from kwngo(nolock) a
			left outer join swon(nolock) b on a.scode = b.scode and a.iswon = b.skey
			left outer join swon(nolock) c on a.scode = c.scode and a.uswon = c.skey
		where a.scode = '".$_SESSION['S_SCODE']."' 
		  and a.kdate between '".str_replace('-','',$sdate1)."' and '".str_replace('-','',$sdate2)."' ".$where." " ;

$qry =  sqlsrv_query($mscon, $sql);
$totalResult =  sqlsrv_fetch_array($qry); 


// SMS건수체크
$sql= "
		select 
				count(*) CNT
		from kwngo(nolock) a
		where a.scode = '".$_SESSION['S_SCODE']."' 
		  and a.kdate between '".str_replace('-','',$sdate1)."' and '".str_replace('-','',$sdate2)."' ".$where."
		  and isnull(htel1,'')+isnull(htel2,'')+isnull(htel3,'') <> '' and len(isnull(htel1,'')+isnull(htel2,'')+isnull(htel3,'')) >= 10 and substring(isnull(htel1,''),1,2) = '01' " ;
$qry =  sqlsrv_query($mscon, $sql);
$totalResult_sms =  sqlsrv_fetch_array($qry); 

// 페이지 클래스 시작
// 로드
include_once($conf['rootDir'].'/include/class/Pagination.php');

// 설정
$pagination = new Pagination(array(
		'base_url' => $_SERVER['PHP_SELF']."?SDATE1=".$sdate1."&SDATE2=".$sdate2."&btn=".$btn."&searchF1=".$searchF1."&searchF1Text=".$searchF1Text,
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
							<button type="button" class="btn_prev" name="yp" id="yp" onclick="d_ser('YP');"><span class="blind">이전</span></button>

							<span class="input_type date" style="width:114px">
								<input type="text" class="Calnew" placeholder="YYYY-MM-DD" id="SDATE1" name="SDATE1" value="<?=$sdate1?>">
							</span> 
							<span class="dash"> ~ </span>
							<span class="input_type date" style="width:114px">
								<input type="text" class="Calnew" placeholder="YYYY-MM-DD" id="SDATE2" name="SDATE2" value="<?=$sdate2?>">
							</span>

							<button type="button" class="btn_next" name="yn" id="yn" onclick="d_ser('YN');"><span class="blind">다음</span></button>

							<p class="response_block" style="margin-left:10px">
								<span class="btn_wrap">
									<a class="btn_s white"	name="mp"   id="mp" onclick="d_ser('MP');">전월</a>
									<a class="btn_s white on"		name="md"   id="md" onclick="d_ser('MD');">당월</a>
								</span>
							
							
								<span class="btn_wrap" style="margin-left:10px">							
									<a class="btn_s white" name="m1"  id="m1" onclick="d_ser('M1');">1월</a>
									<a class="btn_s white" name="m2"  id="m2" onclick="d_ser('M2');">2월</a>
									<a class="btn_s white" name="m3"  id="m3" onclick="d_ser('M3');">3월</a>
									<a class="btn_s white" name="m4"  id="m4" onclick="d_ser('M4');">4월</a>
									<a class="btn_s white" name="m5"  id="m5" onclick="d_ser('M5');">5월</a>
									<a class="btn_s white" name="m6"  id="m6" onclick="d_ser('M6');">6월</a>
									<a class="btn_s white" name="m7"  id="m7" onclick="d_ser('M7');">7월</a>
									<a class="btn_s white" name="m8"  id="m8" onclick="d_ser('M8');">8월</a>
									<a class="btn_s white" name="m9"  id="m9" onclick="d_ser('M9');">9월</a>
									<a class="btn_s white" name="m10"  id="m10" onclick="d_ser('M10');">10월</a>
									<a class="btn_s white" name="m11"  id="m11" onclick="d_ser('M11');">11월</a>
									<a class="btn_s white" name="m12"  id="m12" onclick="d_ser('M12');">12월</a>
								</span>
							</p>

							<select name="searchF1" id="searchF1" class="srch_css" style="width:98px;height:28px;margin-left:10px">
								<option value="a.kname" <?if($searchF1=="a.kname") echo "selected"?>>고객명</option>
								<option value="sjuno"   <?if($searchF1=="sjuno") echo "selected"?>>주민/사업자번호</option>
								<option value="e.sname"   <?if($searchF1=="e.sname") echo "selected"?>>담당사원</option>
								<option value="tel"   <?if($searchF1=="tel") echo "selected"?>>연락처</option>
							</select>
							<input type="text" name="searchF1Text" id="searchF1Text" style="width:125px;height:26px;border:1px solid #b7b7b7" value="<?=$searchF1Text?>" >

							<span class="btn_wrap" style="margin-left:10px">
								<a class="btn_s white hover_btn btn_search btn_off" style="width:80px;margin:0px" onclick="common_ser();">조회</a>
								<a class="btn_s white btn_off" id="btn_ins" style="width:80px;" onclick="KwngoIns('');">고객등록</a>
								<a class="btn_s white btn_off" id="" style="width:80px;" onclick="smspop();">SMS전송</a>
								<a class="btn_s white btn_off excelBtn" style="width:80px;">엑셀</a>
							</span>

						</div>

					</fieldset>
				</form>
			</div><!-- // box_wrap -->

			<div id="kwngo_sort" class="tb_type01 div_grid" style="overflow-y:auto;">
				<table class="gridhover">
					<colgroup>
						<col width="100px">
						<col width="80px">
						<col width="130px">
						<col width="110px">
						<col width="110px">
						<col width="120px">
						<col width="100px">

						<col width="120px">
						<col width="160px">
						<col width="80px">

						<col width="200px">
						<col width="120px">
						<col width="auto">
					</colgroup>
					<thead>
					<tr class="rowTop">
						<th align="left">고객명</th>
						<th align="center">고객구분</th>
						<th align="center">주민등록/사업자번호</th>
						<th align="center">연락처</th>
						<th align="center">SMS가능여부</th>
						<th align="center">직장명</th>
						<th align="center">영업일자</th>

						<th align="center">담당사원</th>
						<th align="center">담당사원 소속</th>
						<th align="center">계약가능성</th>						

						<th align="center">등록일시</th>
						<th align="center">등록사원</th>
						<th align="center">비고</th>
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
						<tr class="rowData" rol-date='<?=$gcode?>'>
							<td align="left"><?=$kname?></td>
							<td align="left"><?=$conf['sbit'][$sbit]?></td>
							<td align="left"><?=$sec_data?></td>
							<td align="center"><?=$totaltel?></td>
							<td align="center"><?if($smsyn=='Y'){?><i class="fa fa-genderless font_blue" aria-hidden="true"></i><?}else{?><i class="fa fa-times font_red" aria-hidden="true"></i><?}?></td>	
							<td align="left"><?=$comnm?></td>
							<td><?if(trim($kdate)) echo date("Y-m-d",strtotime($kdate))?></td>

							<td align="center"><?=$ksname?></td>
							<td align="left"><?=$sosok?></td>
							<td align="right"><?=number_format($psrate).'%'?></td>

							<td align="center"><?=$idate?></td>
							<td align="center"><?=$isname?></td>
							<td align="left"><?=$bigo?></td>	
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

// SMS전송
function smspop(){

	var sdate1 = '<?=$sdate1?>';
	var sdate2 = '<?=$sdate2?>';
	var where = '<?=$wherescript?>';
	var cnt = '<?=$totalResult_sms["CNT"]?>';
	var sms_type = 'sms_kwngo_list';

	if(cnt == 0){
		alert("전송할 데이터가 없습니다.");
		return false;
	}

	var left = Math.ceil((window.screen.width - 500)/2);
	var top = Math.ceil((window.screen.height - 400)/2);
	var popOpen	= window.open("<?=$conf['homeDir']?>/sub/help/sms_pop.php?sdate1="+sdate1+"&sdate2="+sdate2+"&where="+where+"&cnt="+cnt+"&sms_type="+sms_type,"smskwngo1","width=500px,height=400px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
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
 
//--->기간선택
function d_ser(bit){
		// 버튼 클릭데이터 넘겨주기
		var lower_str = bit.toLowerCase();	// 소문자변환
		$("#btn").val(lower_str);

		var  sdate1	= document.getElementById('SDATE1').value;
		var  sdate2	= document.getElementById('SDATE2').value;
		var  str_date = bit + '&' + sdate1 + '&' + sdate2 ;
		
		//--선택한 일자 가져오기
		str_date = date_on	(str_date);  //common.js 참조  bin>js>common.js

		var bdate = str_date.split('&');
		$("form[name='searchFrm'] input[name='SDATE1']").val(bdate[0]); 
		$("form[name='searchFrm'] input[name='SDATE2']").val(bdate[1]); 
		
		//--->선택이 빨리 바뀌면 SERVER 부하걸림 
		if (bit != 'YP' && bit != 'YN' ){
			common_ser();
		}
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