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
					<span><i class="fa-solid fa-comment font_topcolor mgr3"></i>차량등록일</span>
				</div>
			</div>

		<!-- //box_gray -->
			<div class="tb_type01 view">
				<p class="mt10">

				- 해당차량의 차량등록일을 입력합니다.<br>
				- 차량연식 입력시, 개시일기준으로 자동 세팅됩니다.<br>
				- 차량연식과 등록일의 년도가 다를 경우, 별도로 수정해야합니다.<br>
				<b class="font_red">- 차량등록일 기준으로 전방충돌방지,차선이탈방지 특별요율을 적용합니다.</p><br>

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