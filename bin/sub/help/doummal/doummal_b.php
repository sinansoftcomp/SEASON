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
					<span><i class="fa-solid fa-comment font_topcolor mgr3"></i>3년간사고요율</span>
				</div>
			</div>

		<!-- //box_gray -->
			<div class="tb_type01 view">
				<p class="mt10">
				<b>평가대상 기간 3년동안 사고 건수와 최근 1년간 사고건수</b><br><br>
				<p class="font_red">* 삼성 실가입경력 폐지</p>
				* 메리츠의 경우 종피보험 가입경력자와 경력단절이 있는 가입경력자의 경우를 구분하여 사고요율을 적용함.<br>
				&nbsp&nbsp&nbsp종피보험자로 가입경력을 쌓다가 처음으로 주피보험자로 보험을 가입하는 경우,<br>
				&nbsp&nbsp&nbsp해당차량 보험가입 1년이내(ZZZ) 중, 종피보험가입경력을 선택하면 됨.<br>
				&nbsp&nbsp&nbsp ZZZ그룹내의 구분은 메리츠화재에서만 구분됨.
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