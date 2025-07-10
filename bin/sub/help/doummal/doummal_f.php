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
					<span><i class="fa-solid fa-comment font_topcolor mgr3"></i>차량 가입경력</span>
				</div>
			</div>

		<!-- //box_gray -->
			<div class="tb_type01 view">
				<p class="mt10 font_red"><b>적용대상 차량의 가입기간 으로 산정함.</b></p>
				* 신차 또는 중고차 구매후 보험 최초 가입시 해당 차량가입 경력은 1년미만임<br>
				* 단, 신규가입이 아닌 차량대체를 통한 배서가 발생한 경우, 기존 차량의 가입경력을 승계함<br>
				* 차량가입경력이 1년미만인 경우, <b>"9개월이상"</b>을 선택해야 하는 경우는 아래와 같다.<br>
				&nbsp&nbsp&nbsp- 해당차량의 차량가입경력이 9개월이상~1년미만 이거나,<br>
				&nbsp&nbsp&nbsp- 해당차량이 개시일 기준으로 9개월이전에 보험가입한 사실이 있는 경우
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