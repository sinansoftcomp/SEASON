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

 
$sdate1 =  date("Y-m-d");
$sdate2 =  date("Y-m-d");

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
					<fieldset>
						<legend>고객관리 기간별 검색</legend>
						<div>
							<span class="ser_font"> 정산월</span> 
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
									<a class="btn_s white"	name="mp"   id="mp"   onclick="d_ser('MP');">전월</a>
									<a class="btn_s white"		name="md"   id="md"     onclick="d_ser('MD');">당월</a>
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
									<a class="btn_s white hover_btn btn_search" style="width:200px;margin: 0px;" onclick="common_ser();">수입수수료조회</a>
									<a class="btn_s white" id="btn_ins" style="width:200px;" onclick="common_ser_notser('');">수입수수료 비매칭사용인 조회</a>													
								</span>
							</p>
						</div>
					</fieldset>
				</form>
			</div><!-- // box_wrap -->

			<div class="data_left_jojik" > <!--data_left start -->
				<div class="tb_type01 kwndatalist div_grid" style="overflow-y:auto;" >
					<table class="gridhover">
						<colgroup>											
							<col width="100%">
						</colgroup>
						<thead>
						<tr>
							<th align="center">조직도</th>
						</tr>
						</thead>		
						<th class="summary sticky" style="height:15px;"> </th>		
					</table>
					<div   id="tree-container">	</div>
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

 
// 공통조회 함수(bin/js/common.js 호출)
function common_ser(){
	$("#div_load_image").show();
	var formData = $("#searchFrm").serialize();
	// ajax 폼데이터 전송
	ajaxLodingForm('ga_menu3_74_list.php',$('#kwnlist'),formData);
}

//--->비매칭사원조회
function common_ser_notser(){
	$("#div_load_image").show();
	var formData = $("#searchFrm").serialize();
	ajaxLodingForm('ga_menu3_74_list_2.php',$('#kwnlist'),formData);
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
			common_ser();
		}
 }

// 조직도
function get_jstree() {
	$("#tree-container").jstree({  
		'core': {
			'data' : {
				"url"	 : "/bin/sub/help/jojik_tree_json.php",
				"dataType" : "json"	
			}
		} 
		}).on("loaded.jstree",function(e,data){
			 $('#tree-container').jstree('open_all');				// 전체열기
			//$('#tree-container').jstree("open_node", '<?=$fbonbu?>');	// 본부정보 가져와서 1뎁스만 오픈
	});
}

// Node 선택했을 때.
$('#tree-container').on("select_node.jstree", function (e, data) {
	var id = data.instance.get_node(data.selected).id;

	$("#id").val(id);
	common_ser(); 
});


$(document).ready(function(){	
	$("#div_load_image").show();
	var formData = $("#searchFrm").serialize();
	// 계약상세정보 리스트
	ajaxLodingTarket('ga_menu3_74_list.php',$('#kwnlist'),formData);

	// 조직도 호출(/bin/include/source/bottom 에 존재)
	get_jstree();

	//window.parent.postMessage("수수료관리 > 수입수수료명세서", "*");   // '*' on any domain 부모로 보내기..        
	
	// 조회버튼 클릭 시 
	$(".btn_search").click(function(){
		common_ser();
		//$(this).removeClass("on");
	}); 

});

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>