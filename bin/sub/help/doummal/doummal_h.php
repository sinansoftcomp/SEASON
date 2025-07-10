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
					<span><i class="fa-solid fa-comment font_topcolor mgr3"></i>직전 3년 가입경력</span>
				</div>
			</div>

		<!-- //box_gray -->
			<div class="tb_type01 view">
				<p class="mt10">

				* 적용대상 : <b>DB손보 - 개인용, 업무용 개인소유</b><br>
				* 내&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp용<br>
				- DB 개인용의 경우, 3년간 사고요율을 평가하는 가입경력 기준을 직전 3년 가입경력으로 함.<br>
				- 직전 3년 가입경력은 <b>직전3년동안 실제로 보험에 가입한 기간</b><br>
				- DB세부기준 : <b>할인할증 평가기준 과거3년의 평가기간 초일부터 갱신계약 책임개시일까지</b><br>
				<b class="font_blue">* 차량 보유대수가 1대인 경우 피보험자 가입경력 기준 / 2대 이상인 경우 피차 가입경력 기준</b><br>
				<b class="font_blue">* 차량 보유대수에 포함되는 차량은 개인용차량 과 업무용 개인소형 차량에 한함</b><br>
				<b class="font_red">* 개발원에서 미제공 되는 데이터로 꼭 수동입력해야함.</b><br>

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