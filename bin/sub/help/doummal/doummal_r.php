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
					<span><i class="fa-solid fa-comment font_topcolor mgr3"></i>종교 단체</span>
				</div>
			</div>

		<!-- //box_gray -->
			<div class="tb_type01 view">
				<p class="mt10">
				<b class="font_red">- 사업자번호 4~5번째자리 숫자가 '89'가 아닌 종교단체 법인 경우, 체크해야함</b><br>
				- DB손보는 사업자번호 구분이 종교단체가 아니어도 종교단체로 판단되는 경우,<br>
				  종교단체요율을 적용함.<br>
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