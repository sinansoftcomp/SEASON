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
					<span><i class="fa-solid fa-comment font_topcolor mgr3"></i>자녀할인특약</span>
				</div>
			</div>
			<div class="tb_type01 view">
				<p class="mt10">

				* 적용 보험 종목 : 개인용 또는 DB손보 업무용 소형차종(4종화물,경화물,3종승합,경승합)<br><br>
				* 적용 원수사별 상세내용<br>
				</p>
			</div>
		<!-- //box_gray -->
			<div class="tb_type01 view">
				<table>
					<colgroup>
						<col width="23%">
						<col width="24%">
						<col width="auto">
					</colgroup>
					<thead>
						<th align="center">원수사</th>
						<th align="center">운전자한정제한</th>
						<th align="center">할인대상</th>
					</thead>

					<tbody>
						<tr>
							<td align="center">현대(개인용)</td>
							<td align="center">제한없음</td>
							<td align="left">개시일기준, <b>만 6세이하 자녀</b>가 있는 고객 또는 <b>태아</b>가 있는 고객<br>
								<span class="font_red">** 2024년 3월 16일 기준 자녀 수(현재 1명과 2명이상구분)에 따라 구분</span>
							</td>
						</tr>
						<tr>
							<td align="center">삼성(개인용)</td>
							<td align="center">제한없음(단,자녀한정 가입불가)</td>
							<td align="left">개시일기준, <b>만 6세이하 자녀가</b> 있는 고객 또는 <b>태아</b>가 있는 고객<br>
								<span class="font_red">** 개시일 2024년 4월 11일 부터,</span><br>
								<span class="font_red">&nbsp&nbsp&nbsp&nbsp&nbsp<b>만 15세이하 자녀</b>가 있는 고객 또는 <b>태아</b>가 있는 고객 가입가능</span><br>
								<span class="font_red">** 2024년 4월 11일 기준 자녀 수(2명과 3명이상)에 따라 추가 할인(다자녀추가할인)</span>
							</td>
						</tr>
						<tr>
							<td align="center">DB(개인용)</td>
							<td align="center">제한없음(단, 지정1인한정 가입불가)</td>
							<td align="left">개시일기준, <b>만 6세이하 자녀</b>가 있는 고객 또는 <b>태아</b>가 있는 고객<br>
								<span class="font_red">** 개시일 2023년 10월 16일 부터,</span><br>
								<span class="font_red">&nbsp&nbsp&nbsp&nbsp&nbsp<b>만 11세이하 자녀</b>가 있는 고객 또는 <b>태아</b>가 있는 고객 가입가능</span>
							</td>
						</tr>
						<tr>
							<td align="center">DB<br>(업무용개인소유-소형차종)</td>
							<td align="center">1인한정, 부부한정</td>
							<td align="left">개시일기준, <b>만 6세이하 자녀</b>가 있는 고객 또는 <b>태아</b>가 있는 고객</td>
						</tr>
						<tr>
							<td rowspan=2 align="center">한화(개인용)</td>
							<td align="center">1인한정, 부부한정</td>
							<td align="left">개시일기준, <b>만 8세이하 자녀</b>가 있는 고객 또는 <b>태아</b>가 있는 고객<br>
								<span class="font_red">** 개시일 2023년 11월 1일 부터,</span><br>
								<span class="font_red">&nbsp&nbsp&nbsp&nbsp&nbsp<b>만 10세이하 자녀</b>가 있는 고객 또는 <b>태아</b>가 있는 고객 가입가능</span>									
							</td>
						</tr>
						<tr>
							<td align="center">1인한정, 부부한정 - 이외</td>
							<td align="left"><span class="font_red">개시일 2023년 11월 1일 부터, <b>만 1세이하 자녀</b>가 있는 고객 또는 <b>태아</b>가 있는 고객</span></td>
						</tr>
						<tr>
							<td rowspan=2 align="center">KB(개인용)</td>
							<td align="center">1인한정, 부부한정</td>
							<td align="left"> 개시일기준, <b>만 9세이하 자녀</b>가 있는 고객 또는 <b>태아</b>가 있는 고객</td>
						</tr>
						<tr>
							<td align="center">1인한정, 부부한정 - 이외</td>
							<td align="left"> 개시일기준, <b>만 3세이하 자녀</b>가 있는 고객 또는 <b>태아</b>가 있는 고객</td>
						</tr>
						<tr>
							<td align="center">메리츠,롯데(개인용)</td>
							<td align="center">1인한정, 부부한정</td>
							<td align="left">개시일기준, <b>만 7세이하 자녀</b>가 있는 고객 또는 <b>태아</b>가 있는 고객</td>
						</tr>
						<tr>
							<td align="center">흥국(개인용)</td>
							<td align="center">1인한정, 부부한정</td>
							<td align="left">개시일기준, <b>만 12세이하 자녀</b>가 있는 고객 또는 <b>태아</b>가 있는 고객</td>
						</tr>
					</tbody>
				</table>
				<p class="mt10">

				* DB손보의 경우, 보험계약체결시, 개시일 기준 출생여부가 명확하지 않은 경우 (예: 출산예정일이 보험기간시작일 며칠 전/후)<br>
				&nbsp&nbsp&nbsp태아로 분류함. 더 자세한 사항은 각 원수사 전산에서 확인 바람.<br>
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
