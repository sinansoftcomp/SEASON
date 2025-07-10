<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

?>
<style>
body{background-image: none;}
.box_wrap{margin-bottom:0px}
.tb_type01 th, .tb_type01 td {padding: 4px 0;}
</style>

<div class="box_wrap sel_btn">
	<form name="searchFrm" method="get" action="<?$_SERVER['PHP_SELF']?>">
		<input type="hidden" name='row' value='<?=$_GET['row']?>'>

		<input type="text" style="width:220px;font-size:12px;text-align:center;height:26px;margin-top:0px;border:1px solid #d5d5d5" placeholder=" 사원번호 OR 사원명" name="srchText" id="srchText" class="srchText"  value=<?=$_GET['srchText']?>>
		<a href="#" class="btn_s white" id="SearchBtn">검색</a>
		<a href="#" class="btn_s white" onclick="senddata();">적용</a>
		<a href="#" class="btn_s white" onclick="self.close();">닫기</a>
		<input type="hidden" name="arrdata" id="arrdata" value="<?=$_GET['arrdata']?>" style="width:50px;">
		<input type="text" name="arrdatanm" id="arrdatanm" value="<?=$_GET['arrdatanm']?>" style="width:300px;border:0;font-weight:600;margin-left:10px;">
    </form>
</div>

<div id="swonlist">

<div>

<script type="text/javascript">

window.resizeTo("800", "800");                             // 윈도우 리사이즈

// 선택된 대상 부모창으로 전달
function senddata(){
	var arrdata		= $("#arrdata").val();
	var arrdatanm	= $("#arrdatanm").val();

	if(arrdata.length <= 0){
		alert('사원선택 후 적용해주시기 바랍니다.');
		return;
	}

	opener.setSwonValue(arrdata,arrdatanm);
	self.close();
}


$(document).ready(function(){
	$("input[name='srchText']").focus();


	// 검색버튼
	$("#SearchBtn").on("click", function(){	

		ajaxLodingTarket('ga_menu7_06_swon_search_list.php',$('#swonlist'),'&srchText='+$("#srchText").val());
	});

	// 리스트 디폴트 조회
	ajaxLodingTarket('ga_menu7_06_swon_search_list.php',$('#swonlist'),'');

});

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>