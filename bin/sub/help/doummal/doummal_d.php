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
					<span><i class="fa-solid fa-comment font_topcolor mgr3"></i>1년간 사고점수</span>
				</div>
			</div>

		<!-- //box_gray -->
			<div class="tb_type01 view">
				<p class="mt10">
				<b>	현대해상, 흥국화재, 삼성화재</b>의 경우<br>
				<b class="font_red">직전 1년간 사고점수</b>로 요율을 산정함.<br>
				<b class="font_blue">(2건이상 사고인 경우 최고 점수를 기준으로 함.)</b><br><br>
				* 개발원에서 제공되는 1년간 사고점수는 사고점수의 합이므로 주의요망 !!
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