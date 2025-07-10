<?
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
 
$sdate1 =  date("Y-m-01");
$lastday = DATE('t', strtotime($sdate1));
$sdate2 =  date("Y-m-".$lastday);
/* ------------------------------------------------------------
	End Date 초기값 세팅
------------------------------------------------------------ */
// 조직도 첫번째 본부 트리 열기위한 값
$sql= "select top 1 'N1'+bcode fbonbu from bonbu where scode = '".$_SESSION['S_SCODE']."' order by num ";
$result  = sqlsrv_query( $mscon, $sql );
$row =  sqlsrv_fetch_array($result); 

$fbonbu	=	$row['fbonbu'];

sqlsrv_free_stmt($result);
sqlsrv_close($mscon);

?>

<!-- html영역 -->
<style>
</style>

<div class="container">
	<div class="content_wrap">
		<fieldset>

			<!-- 검색조건 -->
			<div class="box_wrap sel_btn">
				<form name="searchFrm" id="searchFrm" method="get" action="<?$_SERVER['PHP_SELF']?>">
 				<input type="hidden" name="id" id="id" value="">
				<input type="hidden" name="cnt" id="cnt" value="">
				<input type="hidden" name="page" id="page" value="">
					<fieldset>
						<legend>고객관리 기간별 검색</legend>
						<div class="row">
							<span  class="ser_font" style="font-size: large;"> 정산월</span> 
							<button type="button" class="btn_prev" name="yp" id="yp" onclick="d_ser('YP');"><span class="blind">이전</span></button>
							<span class="input_type date ml10" style="width:114px;margin-left: 0px;">
								<input type="text" class="Calnew" placeholder="YYYY-MM-DD" id="SDATE1" name="SDATE1" value="<?=$sdate1?>">
							</span> 
							<span class="dash"> ~ </span>
							<span class="input_type date" style="width:114px">
								<input type="text" class="Calnew" placeholder="YYYY-MM-DD" id="SDATE2" name="SDATE2" value="<?=$sdate2?>">
							</span>
							<button type="button" class="btn_next" name="yn" id="yn" onclick="d_ser('YN');"><span class="blind">다음</span></button>
							
							<p class="response_block" style="margin-left:10px">
								<span class="btn_wrap">
									<a href="#" class="btn_s white"	name="mp"   id="mp"   onclick="d_ser('MP');">전월</a>
									<a href="#" class="btn_s white"		name="md"   id="md"     onclick="d_ser('MD');">당월</a>
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
								<span class="btn_wrap">
									<a href="#" class="btn_s white hover_btn btn_search " style="width:130px;margin: 0px;" onclick="common_ser('A');">사원별조회</a>
									<a href="#" class="btn_s white hover_btn btn_search2 " style="width:140px;margin: 0px;" onclick="common_ser2('B');">지급수수료별조회</a>
									<a href="#" class="btn_s white hover_btn btn_search2 excelBtn" style="width:100px;margin: 0px;">엑셀</a>
								</span>
							</p>
						</div>
					</fieldset>
				</form>
			</div><!-- // box_wrap -->

			<div class="data_left_jojik" > <!--data_left start -->
				<div class="tb_type01 kwndatalist div_grid" style="overflow-y:auto;" >
		
					<div style="position:sticky;top:0px; z-index:1;">
						<table class="gridhover">
							<colgroup>											
								<col width="80%">
							</colgroup>

							<thead>
							<tr>
								<th class="jojik" align="center" style="height:25px"> 
									<a class="swon_all" onclick="get_jstree_refresh('1');"><span>전체</span></a>
									<a class="swon_jae" onclick="get_jstree_refresh('2');"><span class="hover">위촉<span></a>
									<a class="swon_tsa" onclick="get_jstree_refresh('3');"><span>해촉<span></a>
								</th>
							</tr>
							</thead>
						</table>
					</div>
					<div   id="tree-container" style="font-size: 12px;"></div>
					
				</div><!-- // tb_type01 -->			
			</div><!--data_left end -->

			<div class="data_right_jojik" id="kwnlist" style="padding: 0px;overflow-x:auto;"> <!--data_right start -->

			</div>  <!--data_right end -->


		</fieldset>
	</div><!-- // content_wrap -->
</div>
<!-- // container -->
<!-- // wrap -->
<script type="text/javascript">

var button = ""; // 무슨 버튼 클릭했는지 알기위해 선언
 
// 공통조회 함수(bin/js/common.js 호출)
function common_ser(bit){
	button = bit;
	
	/*
	var formData = $("#searchFrm").serialize();
	// ajax 폼데이터 전송
	ajaxLodingForm('x1_swonsu_list.php',$('#kwnlist'),formData);
	*/

	var  sdate1	= document.getElementById('SDATE1').value;
	var  sdate2	= document.getElementById('SDATE2').value;
	var  id		= document.getElementById('id').value;
	var  page		= document.getElementById('page').value;

	ajaxLodingTarket('x1_swonsu_list.php',$('#kwnlist'),'&SDATE1='+sdate1+'&SDATE2='+sdate2+'&id='+id+'&page='+page);
}

// 공통조회 함수(bin/js/common.js 호출)
function common_ser2(bit){
	button = bit;
	
	$("#div_load_image").show();
	var formData = $("#searchFrm").serialize();
	// ajax 폼데이터 전송
	ajaxLodingForm('x1_swonsu_list2.php',$('#kwnlist'),formData);
}

//--->기간선택
function d_ser(bit){
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
			if(button == "B"){
				common_ser2();
				button="B";
			}else{
				common_ser();
				button="A";
			}
		}
 }

// 조직도
function get_jstree(sbit) {
	$("#tree-container").jstree({  
		'core': {
			'data' : {
				"url"	 : "/bin/sub/help/jojik_swon_tree_json.php?sbit="+ sbit,
				"dataType" : "json"	
			}
		} 
		}).on("loaded.jstree",function(e,data){
			 $('#tree-container').jstree('open_all');				// 전체열기
			//$('#tree-container').jstree("open_node", '<?=$fbonbu?>');	// 본부정보 가져와서 1뎁스만 오픈
	});
}
function get_jstree_refresh(sbit) {
    $('#tree-container').jstree("destory"); 
	$('#tree-container').jstree(true).settings.core.data.url =  "/bin/sub/help/jojik_swon_tree_json.php?sbit="+ sbit;
	$('#tree-container').jstree(true).refresh();
}
// Node 선택했을 때.
$('#tree-container').on("select_node.jstree", function (e, data) {
	var id = data.instance.get_node(data.selected).id;

	$("#id").val(id);

	if(button == "B"){
		common_ser2();
		button="B";
	}else{
		common_ser();
		button="A";
	}
});

$(window).on("load", function() {
	$(".btn_search").trigger("click");
});

$(document).ready(function(){	

	  $(".excelBtn").click(function(){

		if($('#cnt').val() == 0 ){
			alert('내려받을 데이터가 존재하지 않습니다.');
		}else{
			if(confirm("엑셀로 내려받으시겠습니까?")){
				//$("#div_load_image").show();
				$("form[name='searchFrm']").attr("action","x1_swonsu_excel.php");
				$("form[name='searchFrm']").submit();
				$("form[name='searchFrm']").attr("action","<?$_SERVER['PHP_SELF']?>");
			}
		}

	  });

	// 계약상세정보 리스트
	//ajaxLodingTarket('x1_swonsu_list.php',$('#kwnlist'),'');

	// 조직도 호출(/bin/include/source/bottom 에 존재)
	get_jstree('2');

	//window.parent.postMessage("수수료관리 > 설계사별수수료현황", "*");   // '*' on any domain 부모로 보내기..        



});

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>