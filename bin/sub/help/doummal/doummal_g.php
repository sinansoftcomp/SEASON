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
					<span><i class="fa-solid fa-comment font_topcolor mgr3"></i>그 외 보유차량</span>
				</div>
			</div>

		<!-- //box_gray -->
			<div class="tb_type01 view">
				<p class="mt10">
				* <b>피보험 대상 차량 외</b>에 보유하고 있는 차량정보를 선택한다.<br>
				* 만약, 피보험 대상 차량 외에 보유하고 있는 차량 대수가 2대 이상이고<br>
				&nbsp&nbsp&nbsp<b> "승용, 업무용소형"</b> 과 <b>"1,2,3종 승합/화물"</b> 에 속하는 차량을<br>
				&nbsp&nbsp&nbsp모두 보유한 경우, 승용,업무용소형 차종의 보유대수를 확인 후<br>
				&nbsp&nbsp&nbsp<b>1대~5대 이상을 구분하여 "승용, 업무용소형"</b>을 선택한다.<br><br>

				<b class="font_red">* 업무용소형은 경화물, 경승합, 4종화물을 의미한다.</b>

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