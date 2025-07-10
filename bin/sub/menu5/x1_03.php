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
	echo "<script> alert('해당 메뉴에 대해 권한이 없습니다. 관리자에게 문의 바랍니다.'); self.close(); </script>";
}
 
$sdate1 =  date("Y-m-01");
$lastday = DATE('t', strtotime($sdate1));
$sdate2 =  date("Y-m-".$lastday);
/* ------------------------------------------------------------
	End Date 초기값 세팅
------------------------------------------------------------ */

$sql= "
		select top 1 'N2'+inscode code
		from inssetup
		WHERE scode = '".$_SESSION['S_SCODE']."' and useyn = 'Y'
		order by inscode 
		" ;
$qry =  sqlsrv_query($mscon, $sql);
$totalResult =  sqlsrv_fetch_array($qry); 
$topcode = $totalResult['code'];

sqlsrv_free_stmt($result);
sqlsrv_close($mscon);

?>

<!-- html영역 -->
<style>
body{background-image: none;}
</style>

<div class="container">
	<div class="content_wrap">


		<div class="tit_wrap">
			<h3 class="tit_sub" style="margin-left:20px">수수료 기초설정</h3>
			<!--<span class="btn_wrap" style="padding-right:20px">-->
			<span style = "margin-left:328px;">
				<a class="btn_s white hover_btn btn_search btn_off" style="width:100px;margin:0px; " onclick="inscharge_update('save');">저장</a>
				<a class="btn_s white btn_off" style="width:100px;margin-left:-4px" onclick="inscharge_update('reset');">초기화</a>
				<a class="btn_s white btn_off" style="width: 100px;margin-left:-4px" onclick="self.close();">닫기</a>
			</span>
		</div>

		<div class="data_left_jojik" > 
			<div class="tb_type01 kwndatalist" style="overflow-y:hidden;" >
				<table class="gridhover">
					<colgroup>											
						<col width="100%">
					</colgroup>

					<thead>
						<tr class="rowTop">
							<th align="center">원수사</th>
						</tr>
					</thead>							
					</tr>

				</table>
				<div   id="tree-container" style="font-size: 12px"  >	</div>
			</div>		
		</div>

		<div class="data_right_jojik" id="inscharge" style="padding: 0px;overflow-x:auto;"> 

		</div>  

	</div>
</div>

<script type="text/javascript">

// 공통조회 함수(bin/js/common.js 호출)
function common_ser(id){
	//$("#div_load_image").show();
	//var formData = $("#searchFrm").serialize();
	var formData = "id="+id;
	// ajax 폼데이터 전송
	ajaxLodingTarket('x1_03_data.php',$('#inscharge'),formData);
}

// 조직도
function get_jstree() {
	$("#tree-container").jstree({  
		'core': {
			'data' : {
				"url"	 : "/bin/sub/help/jojik_inssetup_tree_json.php",
				"dataType" : "json"	
			}
		} 
		}).on("loaded.jstree",function(e,data){
			 $('#tree-container').jstree('open_all');				// 전체열기
			 $('#tree-container').jstree($("#"+"<?=$topcode?>"+"_anchor").trigger("click"));
	});
}

// Node 선택했을 때.
$('#tree-container').on("select_node.jstree", function (e, data) {
	var id = data.instance.get_node(data.selected).id;
	$("#id").val(id);

	if(id == "N10000"){
		alert("원수자를 선택해주세요.");
		return false;
	}
	common_ser(id);
});

$(document).ready(function(){	
	get_jstree();
	//window.parent.postMessage("수수료 > 수수료 기초설정", "*");   // '*' on any domain 부모로 보내기..        
});

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>