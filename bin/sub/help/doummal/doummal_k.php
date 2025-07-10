<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");
?>

<style>
body{background-image: none;}

</style>

<div class="container container_bk">
	<div class="content_wrap">

			<div class="menu_group_top" style="border-bottom:0px solid;">
				<div class="menu_group">
					<span><i class="fa-solid fa-comment font_topcolor mgr3"></i>연료형태</span>
				</div>
			</div>

		<!-- //box_gray -->
			<div class="tb_type01 view">
				<p class="mt10">
				<b class="font_red">- 적용 차량의 연료형태를 선택합니다.</b><br>
				- 정규차명코드 적용시 자동 세팅됩니다.<br>
				- 임시차명코드 또는 수동으로 차량 정보 입력 시, 직접 입력 해야합니다.<br>
				</p>
			</div>
			<!-- // tb_type01 -->
	</div>

</div>

<!-- // popup_wrap -->

 </body>
</html>

<script type="text/javascript">

	
$(document).ready(function(){
	

});


</script>


<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>