<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

//var_dump( $_SESSION );

/* ------------------------------------------------------------
	Date 초기값 세팅
------------------------------------------------------------ */
if ($_GET['SDATE1']) {
	$sdate1 =  $_GET['SDATE1'];
	$sdate2 =  $_GET['SDATE2'];
}else{
	$sdate1 =  date("Y-m-01");
	$sdate2 =  date("Y-m-d");
}

/* ------------------------------------------------------------
	End Date 초기값 세팅
------------------------------------------------------------ */

// 전체보험사
$sql= "select inscode code, name, gubun from inssetup where scode = '".$_SESSION['S_SCODE']."' and useyn = 'Y' order by name";
$qry= sqlsrv_query( $mscon, $sql );
$instot	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$instot[] = $fet;
}


// 계약상태값(한글상태값 그대로 조회)
$sql= "select codesub code, subnm name from common where scode = '".$_SESSION['S_SCODE']."' and code = 'COM010' order by num ";
$qry= sqlsrv_query( $mscon, $sql );
$selkstbit	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$selkstbit[] = $fet;
}


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
body{background-image: none;}

.tb_type01 table thead th {
    border-bottom: 1px solid #c7c7c7;
    background: #f9f9f9;
    font-size: 13px;
    font-weight: 600;
}

.tb_type01 td {
    background: #fff;
    font-size: 12px;
	font-weight: 500;
    border-bottom: 1px solid #e9e9e9;
}

</style>
					

<div class="container">
	<div class="content_wrap">
		<fieldset>

			<!-- 검색조건 -->
			<div class="box_wrap sel_btn">
				<form name="searchFrm" id="searchFrm" method="get" action="<?$_SERVER['PHP_SELF']?>">
				<input type="hidden" name="id" id="id" value="">
				<input type="hidden" name="page" id="page" value="">
					<fieldset>
						<legend>고객관리 기간별 검색</legend>
						<div class="row">
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
							

						</div>

							<select name="inscode" id="inscode" class="srch_css" style="margin-left:0;width:141px">				
							  <option value="">보험사</option>
							  <?foreach($instot as $key => $val){?>
							  <option value="<?=$val['code']?>"><?=$val['name']?></option>
							  <?}?>
							</select>
							<select name="insilj" id="insilj" class="srch_css" style="margin-left:21px;width:141px"> 
								<option value="">상품군</option>
								<?foreach($conf['insilj'] as $key => $val){?>
								<option value="<?=$key?>"><?=$val?></option>
								<?}?>
							</select>	
							<select name="kstbit" id="kstbit" class="srch_css" style="width:115px;margin-left:10px"> 
								<option value="">계약상태</option>
								<?foreach($selkstbit as $key => $val){?>
								<option value="<?=$val['code']?>"><?=$val['name']?></option>
								<?}?>
							</select>	


							<select name="searchF1" id="searchF1" class="srch_css" onchange="fn_srch(this.value);" style="width:114px;margin-left:8px">
								<option value="a.kname">계약자명</option>
								<option value="a.kcode">증권번호</option>
								<option value="s1">모집사원</option>
								<option value="s2">사용인</option>
								<option value="tel">연락처</option>
							</select>
							<input type="hidden" name="skey" id="skey" value="<?=$skey?>">
							<input type="text" name="searchF1Text" id="searchF1Text" class="srch_css" style="height:20px;width:161px;margin-left:3px" value="<?=$searchF1Text?>" onkeyup="enterkey()">
							<!--btn_off 클래스 사용시 버튼 클릭해도 색상잔여 x-->
							<span class="btn_wrap" >
								<a class="btn_s white hover_btn btn_search btn_off" style="width:113px;margin:0" onclick="common_ser();">조회</a>
								<a class="btn_s white btn_off" id="btn_ins" style="width:113px;" onclick="KwnIns('','');">계약등록</a>
								<a class="btn_s white btn_off" id="btn_ins" style="width:113px;" onclick="KwnIns('','');">SMS전송</a>
							</span>
							<span class="btn_topwrap">
								
							</span>


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
						<tr >
							<th align="center">조직도</th>
						</tr>
						</thead>	
						
						<th class="summary sticky" style="height:14px;"> </th>
						</tr>
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

// 계약등록
function KwnIns(inscode,kcode){	

	var left = Math.ceil((window.screen.width - 1200)/2);
	var top = Math.ceil((window.screen.height - 950)/2);
	var page= document.getElementById('page').value;

	var popOpen	= window.open("<?=$conf['homeDir']?>/sub/menu3/ga_menu3_01_pop.php?inscode="+inscode+"&kcode="+kcode+"&page="+page,"KwnDt","width=1250px,height=860px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");

	popOpen.focus();		
}

// 엔터 누를시 검색
function enterkey() {
	if (window.event.keyCode == 13) {
		common_ser();   	
    }

	// 값 삭제 시 사원키값 초기화 
	var data = $('#searchF1Text').val();
	if(data.replace(/\s/g, "").length == 0){
		$("#skey").val('');
	}
}


// 모집사원 및 관리사원 조회 시 사원팝업(보험사원 기준 조회) 
function fn_srch(val){

	if(val == 's1' || val == 's2'){								// 모집사원 및 관리사원 조회 시 팝업조회 / 인풋박스 미입력상태 처리
		$("#searchF1Text").attr("readonly",true);
		$("#searchF1Text").css("backgroundColor","#EAEAEA");

		var left = Math.ceil((window.screen.width - 800)/2);
		var top = Math.ceil((window.screen.height - 800)/2);
		var popOpen	= window.open("<?=$conf['homeDir']?>/sub/help/ga_swon_search.php","swonpop","width=500px,height=400px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
		popOpen.focus();
	}else{		// 그 외 사원조회 히든값 클리어 / 인풋박스 입력가능상태
		$("#skey").val('');
		$('#searchF1Text').val('');

		$("#searchF1Text").attr("readonly",false);
		$("#searchF1Text").css("backgroundColor","#fff");
	}

}


function setSwonValue(row,code,name){
	$("#skey").val(code);
	$('#searchF1Text').val(name);
}


// 공통조회 함수(bin/js/common.js 호출)
function common_ser(){
	$("#div_load_image").show();
	var formData = $("#searchFrm").serialize();

	//console.log(formData);

	// ajax 폼데이터 전송
	ajaxLodingForm('ga_menu3_12_list.php',$('#kwnlist'),formData);

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
	$('#page').val("");
	$("#id").val(id);
	common_ser();
 
});


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


$(document).ready(function(){	
	// 계약상세정보 리스트
	ajaxLodingTarket('ga_menu3_12_list.php',$('#kwnlist'),'&SDATE1=<?=$sdate1?>&SDATE2=<?=$sdate2?>');

	// 조직도 호출(/bin/include/source/bottom 에 존재)
	get_jstree();

	window.parent.postMessage("계약수납관리 > 계약관리현황", "*");   // '*' on any domain 부모로 보내기..        

});

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>