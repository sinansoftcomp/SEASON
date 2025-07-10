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

// 보험사 가져오기
$sql= "select inscode, name from inssetup(nolock) where scode = '".$_SESSION['S_SCODE']."' and useyn = 'Y' order by num, inscode";
$qry= sqlsrv_query( $mscon, $sql );
$insData	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
  $insData[] = $fet;
}

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
			<!--
			<legend>사원관리</legend>
			<h2 class="tit_big">사원관리</h2>
			--> 
			<div class="box_wrap sel_btn">
				<form name="searchFrm" id="searchFrm" method="get" action="<?$_SERVER['PHP_SELF']?>">
					<input type="hidden" name="id" id="id" value="">
					<select name="code" id="code" class="srch_css" style="width:120px;height:28px"> 		
					  <option value="">보험사</option>
					  <option value="00000" <?if($_GET['code']=="00000") echo "selected"?>>통합</option>
					  <?foreach($insData as $key => $val){?>
					  <option value="<?=$val['inscode']?>" <?if($_GET['code']==$val['inscode']) echo "selected"?>><?=$val['name']?></option>
					  <?}?>
					</select>

					<select name="insilj" id="insilj" class="srch_css" style="width:120px;height:28px">
					  <option value="">상품보종선택</option>
					  <option value="1" <?if($_GET['insilj']=="1") echo "selected"?>>일반</option>
					  <option value="2" <?if($_GET['insilj']=="2") echo "selected"?>>생손보 장기</option>
					  <option value="3" <?if($_GET['insilj']=="3") echo "selected"?>>자동차</option>
					</select>
					<span class="btn_wrap" style="margin-left: 10px;">				
						<a class="btn_s white btn_search btn_off"  style="margin: 0; min-width:100px;">조회</a>
						<a class="btn_s white btn_off"  style="margin: 0; min-width:100px;" onclick="btn_insert();">신규</a> 
					</span>	 
				</form>
			</div>
			
			<div class="data_left_jojik"> <!--data_left start -->
				<div class="tb_type01 div_grid" style="overflow-y:auto;" >
					<table class="gridhover">
						<colgroup>											
							<col width="100%">
						</colgroup>
						<thead>
						<tr>
							<th align="center">직급</th>
						</tr>
						</thead>	 
					</table>
					<div   id="tree-container" >	</div>
				</div><!-- // tb_type01 -->			
			</div><!--data_left end -->

			<div class="data_right_jojik" id="sjirulelist" style="padding: 0px;overflow-x:auto;"> <!--data_right start -->

			</div>  <!--data_right end -->

		</fieldset>
	</div> <!-- content_wrap end -->
</div> <!--container end-->

<span id="guide" style="color:#999;display:none"></span>
 
<script type="text/javascript">


function get_jstree() {
	$("#tree-container").jstree({  
		'core': {
			'data' : {
				"url"	 : "/bin/sub/help/jojik_pos_tree_json.php",
				"dataType" : "json"	
			}
		} 
		}).on("loaded.jstree",function(e,data){
			$('#tree-container').jstree('open_all');				// 전체열기
			//$('#tree-container').jstree("open_node", '000001');	// 본부정보 가져와서 1뎁스만 오픈
	});
}


// Node 선택했을 때.
$('#tree-container').on("select_node.jstree", function (e, data) {
	var id = data.instance.get_node(data.selected).id;
	$("#id").val(id);
	//alert(id);
	common_ser();
 
});

// 공통조회 함수(bin/js/common.js 호출)
function common_ser(){
	var formData = $("#searchFrm").serialize();
	// ajax 폼데이터 전송
	ajaxLodingForm('x1_00_list.php',$('#sjirulelist'),formData);
}


// 조회버튼 클릭 시 
$(".btn_search").click(function(){

 	common_ser();
}); 

function btn_insert(){
	sjirulePopOpen('','','','');
}


function sjirulePopOpen(jik,inscode,insilj,seq){
	//alert(select_code);
	var left = Math.ceil((window.screen.width - 1300)/2);
	var top = Math.ceil((window.screen.height - 700)/2);
	var popOpen	= window.open("<?=$conf['homeDir']?>/sub/menu5/x1_00_pop.php?jik="+jik+"&inscode="+inscode+"&insilj="+insilj+"&seq="+seq,"common","width=1450px,height=600px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
	//popOpen.focus();
}

$(document).ready(function(){
	// 계약상세정보 리스트
	ajaxLodingTarket('x1_00_list.php',$('#sjirulelist'),'');

	// 조직도 호출(/bin/include/source/bottom 에 존재)
	get_jstree();	
	//window.parent.postMessage("수입수수료관리 > GA지급수수료 규정", "*");   // '*' on any domain 부모로 보내기..        
 });

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>