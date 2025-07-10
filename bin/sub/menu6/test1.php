<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");
?>

<div class="menu_group_top">
	<div class="menu_group">
		
		<form name="newFrm" method="POST" id='ttt' action="http://www.gaplus.net/bin/sub/menu6/test_yoyul.php">
			<a class="btn_s white mgl20" onclick="yoyul_send();"><i class="fa-solid fa-calculator fa-lg mgr3"></i>요율계산</a>
		</form>
	</div>
</div>

<script type="text/javascript">

function yoyul_send(){

	//var queryString = $("form[name=yoyulform]").serialize() ;

	$.ajax({

			url:"/bin/sub/menu6/test_yoyul.php",

			success: function (data) {
				console.log("성공");
			},
			error: function (request, status, error) {
				console.log("실패");
			}
	});	

	//$('#tt').trigger('click');
	//$("#ttt").submit();

// http://gaplus.net/bin/sub/menu6/test_yoyul.php



}

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>