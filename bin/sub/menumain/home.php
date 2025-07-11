<?

include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

// 일주일 매출 구글차트 그래프 적용(임시)
$graph_rows = [];
$graph_rows_php = [];

$sql = "SELECT order_date, order_amt, order_cnt, cancel_amt, cancel_cnt FROM order_summary_by_day";
$qry = sqlsrv_query($mscon, $sql);

while($row = sqlsrv_fetch_array($qry, SQLSRV_FETCH_ASSOC)) {
	$tooltip_order = "<div style=\"padding:5px; white-space: nowrap;\">주문금액: " . number_format($row['order_amt']) . "<br>건수: {$row['order_cnt']}건</div>";
	$tooltip_cancel = "<div style=\"padding:5px; white-space: nowrap;\">취소금액: " . number_format($row['cancel_amt']) . "<br>건수: {$row['cancel_cnt']}건</div>";

    $graph_rows[] = "['{$row['order_date']}', {$row['order_amt']}, '{$tooltip_order}', {$row['cancel_amt']}, '{$tooltip_cancel}']";

    $graph_rows_php[] = [
        'order_amt' => $row['order_amt'],
        'cancel_amt' => $row['cancel_amt']
    ];
}

?>

<style>
	* { box-sizing: border-box; }
	body { margin: 0; font-family: '맑은 고딕', sans-serif; background: #f9f9f9; }

	#admin_container {
		display: flex;
		flex-direction: column;
		height: 100%;
	}

	/* 가장 상단 날짜 및 시간 표시 */ 
	#admin_header {
		display: flex;
		justify-content: space-between;
		align-items: center;
		padding: 12px 20px;
		font-size: 18px;
		font-weight: bold;
		background: #ffffff;
		border-bottom: 1px solid #ddd;
		color: #333;
	}	

	/* 상단 4박스 영역 */
	#admin_top_section {
		display: flex;
		flex-wrap: wrap;
		justify-content: space-between;
		gap: 1%;
		padding: 10px;
		background: #f0f0f0;
		flex: 1 1 40%;
		overflow-y: auto;
	}

	.admin-box {
		width: 24%;
		padding: 10px;
		border: 1px solid #ddd;
		border-radius: 6px;
		box-shadow: 2px 2px 6px rgba(0, 0, 0, 0.1);
		background: #fff;
		display: flex;
		flex-direction: column;
		overflow-y: auto;
	}

	.admin-box-header {
		display: flex;
		align-items: center;
		justify-content: space-between;
		height: 50px;
		margin-bottom: 12px;
		padding: 0 6px;
		border-bottom: 1px solid #d3caca;
	}

	.admin-box-title {
		font-size: 16px;
		font-weight: bold;
		color: #222;
		display: flex;
		align-items: center;
		gap: 8px;
	}

	.more-link {
		font-size: 13px;
		color: #666;
		cursor: pointer;
		display: flex;
		align-items: center;
		gap: 4px;
	}

	/* 박스별 배경색 */
	.box-order   { background: #f0f8ff; }
	.box-product { background: #fff3f7; }
	.box-member  { background: #fff3e0; }
	.box-inquiry { background: #ede7f6; }

	/* 아이콘 색상 */
	.box-order   .admin-box-title i { color: #1e88e5; }
	.box-product .admin-box-title i { color: #d81b60; }
	.box-member  .admin-box-title i { color: #ef6c00; }
	.box-inquiry .admin-box-title i { color: #5e35b1; }

	/* status-wrapper 공통 */
	.status-wrapper {
		display: flex;
		flex-direction: column;
		gap: 6px;
	}

	/* 주문처리만 2열 */
	.box-order .status-wrapper {
		display: grid;
		grid-template-columns: 1fr 1fr;
		gap: 6px;
	}

	/* status 항목 스타일 */
	.status-item {
		display: flex;
		justify-content: flex-start;
		align-items: center;
		gap: 6px;
		padding: 6px 8px;
		font-size: 14px;
		color: #333;
	}

	.status-item .count {
		margin-left: auto;
		font-weight: bold;
		color: inherit;
	}

	/* dot 스타일 */
	.dot {
		font-size: 20px;
		line-height: 1;
		display: inline-block;
		vertical-align: middle;
		transform: translateY(-1px);
		color: inherit;
	}

	/* dot 색상 박스별 지정 */
	.box-order .dot   { color: #1e88e5; }
	.box-product .dot { color: #d81b60; }
	.box-member .dot  { color: #ef6c00; }
	.box-inquiry .dot { color: #5e35b1; }

	/* 상품현황 특별 구조 */
	.product-status-group {
		margin-bottom: 12px;
	}

	.product-status-pair {
		display: flex;
		align-items: center;
		gap: 6px;
		padding: 4px 0;
		font-size: 14px;
		color: #333;
	}

	.product-status-pair .label {
		flex: 1;
	}

	.product-status-pair .count {
		font-weight: bold;
		color: #d81b60;
		text-align: right;
		min-width: 50px;
	}

	.product-status-pair .dot {
		font-size: 18px;
		line-height: 1;
		transform: translateY(-1px);
		color: #d81b60;
	}

	.status-divider {
		border: none;
		border-top: 1px dashed #999;
		margin: 8px 0;
		height: 1px;
		opacity: 1;
		display: block;
	}

	/* 회원관리 */
	.member-list {
		list-style: none;
		padding: 0;
		margin: 0;
		display: flex;
		flex-direction: column;
		gap: 6px;
	}

	.member-item {
		display: flex;
		justify-content: space-between;
		padding: 8px 10px;
		border: 1px solid #ddd;
		border-radius: 4px;
		font-size: 14px;
		color: #333;
	}

	.member-item .count {
		font-weight: bold;
		color: #ef6c00;
	}

	/* 문의 */
	.inquiry-list {
		list-style: none;
		padding: 0;
		margin: 0;
	}

	.inquiry-item {
		border-bottom: 1px solid #eee;
		padding: 6px 0;
		font-size: 13px;
	}

	.inquiry-item .status {
		font-weight: bold;
		color: #e91e63;
		float: right;
	}

	.inquiry-item time {
		font-size: 11px;
		color: #999;
		display: block;
	}



	/* 하단 영역 (그래프 + 공지사항) */
	#admin_bottom_section {
		display: flex;
		gap: 10px;
		padding: 10px;
		flex: 1 1 60%;
		background: #fff;
		overflow-y: auto;
	}

	.graph-box {
		flex: 1;
		display: flex;
		flex-direction: column;
	}

	.notice-box {
		width: 40%; border: 1px solid #ddd;
		background: #fafafa; border-radius: 6px; padding: 15px;
	}

	.graph-box h3, .notice-box h3 {
		font-size: 16px; margin-bottom: 10px;
		color: #222; border-bottom: 1px solid #ddd; padding-bottom: 4px;
	}

	#chart_div {
		flex: 1; /* 차트 영역이 남은 공간 모두 차지 */
		width: 100%;
		height: 100%;
		min-height: 280px; /* 최소 높이 보장 */
	}

	.sum-box {
		display: flex; justify-content: flex-end; gap: 20px; margin-bottom: 10px;
	}

	.sum-item {
		background: #fff; padding: 10px 14px;
		border: 1px solid #ddd; border-radius: 6px;
		font-size: 13px; color: #222;
		box-shadow: 1px 1px 3px rgba(0,0,0,0.05);
	}

	.sum-item span.order { color: #1976d2; font-weight: bold; }
	.sum-item span.cancel { color: #d32f2f; font-weight: bold; }
</style>  

<script src="https://www.gstatic.com/charts/loader.js"></script>
<script>
	let chart, data, options;
	
  google.charts.load('current', {packages: ['corechart']});
  google.charts.setOnLoadCallback(initChart);

  function initChart() {
    data = google.visualization.arrayToDataTable([
      ['날짜', {label: '주문금액', type: 'number'}, {type: 'string', role: 'tooltip', p: {html: true}},
               {label: '취소금액', type: 'number'}, {type: 'string', role: 'tooltip', p: {html: true}}],
      <?= implode(",\n  ", $graph_rows) ?>

    ]);

    options = {
      title: '최근 7일 주문/취소 금액',
      legend: { position: 'top' },
      bars: 'vertical',
      colors: ['#42a5f5', '#ef5350'],
      tooltip: { isHtml: true },
      vAxis: {
        format: '#,###\u20a9',
        title: '금액(\u20a9)'
      },
      hAxis: {
        title: '날짜'
      }
    };

    chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
    drawChart();
  }

  function drawChart() {
    if (chart && data && options) {
      chart.draw(data, options);
    }
  }

  // 리사이즈 대응
  window.addEventListener('resize', drawChart);

  $(function(){
    const orders = [<?= implode(",", array_map(fn($r) => $r['order_amt'], $graph_rows_php)) ?>];
    const cancels = [<?= implode(",", array_map(fn($r) => $r['cancel_amt'], $graph_rows_php)) ?>];
    const totalOrder = orders.reduce((a,b) => a+b, 0).toLocaleString();
    const totalCancel = cancels.reduce((a,b) => a+b, 0).toLocaleString();

    $("#order_sum").html('<span class="order">' + totalOrder + '\u20a9</span>');
    $("#cancel_sum").html('<span class="cancel">' + totalCancel + '\u20a9</span>');
  });
</script>


<div style="padding:5px 5px" class="div_grid2">
	<div id="admin_container">
		<div id="admin_header">
			<span id="current_date"></span>
			<span id="current_time"></span>
		</div>

		<!-- 상단 관리자 박스 -->
		<div id="admin_top_section">
		<!-- 주문처리 -->
			<div class="admin-box box-order">
				<div class="admin-box-header">
					<div class="admin-box-title"><i class="fas fa-receipt icon-blue"></i> 주문처리</div>
					<div class="more-link">더보기 <i class="fas fa-plus"></i></div>
				</div>
				<div class="status-wrapper">
					<div class="status-item" style="justify-content: flex-start; gap: 6px;">
						<span class="dot">&middot;</span> 
						<span>입금대기</span> <span class="count" style="margin-left:auto;">2건</span>
					</div>
					<div class="status-item" style="justify-content: flex-start; gap: 6px;">
						<span class="dot">&middot;</span> 
						<span>입금완료</span> <span class="count" style="margin-left:auto;">3건</span>
					</div>
					<div class="status-item" style="justify-content: flex-start; gap: 6px;">
						<span class="dot">&middot;</span> 
						<span>발주</span> <span class="count" style="margin-left:auto;">5건</span>
					</div>
					<div class="status-item" style="justify-content: flex-start; gap: 6px;">
						<span class="dot">&middot;</span> 
						<span>발주사배송</span> <span class="count" style="margin-left:auto;">1건</span>
					</div>
					<div class="status-item" style="justify-content: flex-start; gap: 6px;">
						<span class="dot">&middot;</span> 
						<span>국내공항도착</span> <span class="count" style="margin-left:auto;">0건</span>
					</div>
					<div class="status-item" style="justify-content: flex-start; gap: 6px;">
						<span class="dot">&middot;</span> <span>배송중</span> <span class="count" style="margin-left:auto;">2건</span>
					</div>
					<div class="status-item" style="justify-content: flex-start; gap: 6px;">
						<span class="dot">&middot;</span> 
						<span>배송완료</span> <span class="count" style="margin-left:auto;">4건</span>
					</div>
					<div class="status-item" style="justify-content: flex-start; gap: 6px;">
						<span class="dot">&middot;</span> 
						<span>고객확정</span> <span class="count" style="margin-left:auto;">3건</span>
					</div>
					<div class="status-item" style="justify-content: flex-start; gap: 6px;">
						<span class="dot">&middot;</span> 
						<span>반품</span> <span class="count" style="margin-left:auto;">1건</span>
					</div>
					<div class="status-item" style="justify-content: flex-start; gap: 6px;">
						<span class="dot">&middot;</span> 
						<span>환불</span> <span class="count" style="margin-left:auto;">0건</span>
					</div>
				</div>
			</div>

			<!-- 상품현황 -->
			<div class="admin-box box-product">
				<div class="admin-box-header">
					<div class="admin-box-title"><i class="fas fa-box icon-pink"></i> 상품현황</div>
					<div class="more-link">더보기 <i class="fas fa-plus"></i></div>
				</div>

				<div class="status-wrapper product-status-wrapper" style="display: flex; flex-direction: column; gap: 12px;">

					<!-- 일반 카테고리 -->
					<div class="product-group">
						<div class="status-pair" style="display: flex; align-items: center; gap: 12px;">
							<strong style="min-width: 45px;">일반</strong>
							<div class="status-item" style="display: flex; gap: 6px; width: 100%;">
								<span class="dot">&middot;</span>
								<span>판매중</span>
								<span class="count" style="margin-left:auto;">10건</span>
							</div>
						</div>
						<div class="status-pair" style="display: flex; align-items: center; gap: 12px;">
							<span style="min-width: 45px;"></span>
							<div class="status-item" style="display: flex; gap: 6px; width: 100%;">
								<span class="dot">&middot;</span>
								<span>판매종료</span>
								<span class="count" style="margin-left:auto;">20건</span>
							</div>
						</div>
					</div>

					<!-- 구분선 -->
					<hr style="border:0;border-top:1px solid #f3cece;margin:8px auto;width:calc(100% - 20px);display:block;">

					<!-- 이벤트 카테고리 -->
					<div class="product-group">
						<div class="status-pair" style="display: flex; align-items: center; gap: 12px;">
							<strong style="min-width: 45px;">이벤트</strong>
							<div class="status-item" style="display: flex; gap: 6px; width: 100%;">
								<span class="dot">&middot;</span>
								<span>판매중</span>
								<span class="count" style="margin-left:auto;">20건</span>
							</div>
						</div>
						<div class="status-pair" style="display: flex; align-items: center; gap: 12px;">
							<span style="min-width: 45px;"></span>
							<div class="status-item" style="display: flex; gap: 6px; width: 100%;">
								<span class="dot">&middot;</span>
								<span>판매종료</span>
								<span class="count" style="margin-left:auto;">5건</span>
							</div>
						</div>
						<div class="status-pair" style="display: flex; align-items: center; gap: 12px;">
							<span style="min-width: 45px;"></span>
							<div class="status-item" style="display: flex; gap: 6px; width: 100%;">
								<span class="dot">&middot;</span>
								<span>품절</span>
								<span class="count" style="margin-left:auto;">3건</span>
							</div>
						</div>
					</div>

				</div>
			</div>

			<!-- 회원관리 -->
			<div class="admin-box box-member">
				<div class="admin-box-header">
					<div class="admin-box-title"><i class="fas fa-user icon-green"></i> 회원관리</div>
					<div class="more-link">더보기 <i class="fas fa-plus"></i></div>
				</div>
				<div class="status-wrapper">
					<div class="status-item" style="justify-content: flex-start; gap: 6px;">
						<span class="dot">&middot;</span> <span>전체가입회원</span> <span class="count" style="margin-left:auto;">102건</span>
					</div>
					<div class="status-item" style="justify-content: flex-start; gap: 6px;">
						<span class="dot">&middot;</span> <span>승인대기</span> <span class="count" style="margin-left:auto;">3건</span>
					</div>
					<div class="status-item" style="justify-content: flex-start; gap: 6px;">
						<span class="dot">&middot;</span> <span>최근한달가입</span> <span class="count" style="margin-left:auto;">8건</span>
					</div>
					<div class="status-item" style="justify-content: flex-start; gap: 6px;">
						<span class="dot">&middot;</span> <span>최근한달탈퇴</span> <span class="count" style="margin-left:auto;">2건</span>
					</div>
					<div class="status-item" style="justify-content: flex-start; gap: 6px;">
						<span class="dot">&middot;</span> <span>휴먼회원</span> <span class="count" style="margin-left:auto;">6건</span>
					</div>
				</div>
			</div>

			<!-- 상품문의 -->
			<div class="admin-box box-inquiry">
				<div class="admin-box-header">
					<div class="admin-box-title"><i class="fas fa-question-circle icon-yellow"></i> 상품문의</div>
					<div class="more-link">더보기 <i class="fas fa-plus"></i></div>
				</div>
				<div class="status-wrapper">
					<div class="status-item" style="justify-content: flex-start; gap: 6px;">
						<span class="dot">&middot;</span> <span>미답변</span> <span class="count" style="margin-left:auto;">3건</span>
					</div>
					<div class="status-item" style="justify-content: flex-start; gap: 6px;">
						<span class="dot">&middot;</span> <span>답변완료</span> <span class="count" style="margin-left:auto;">8건</span>
					</div>
				</div>
			</div>
		</div>

		<!-- 하단 그래프 + 공지사항 -->
		<div id="admin_bottom_section">
			<div class="graph-box">
				<h3>주문/취소금액(최근 일주일) </h3>
				<div class="sum-box">
				<div class="sum-item">주문 합계: <span id="order_sum" class="order">0원</span></div>
				<div class="sum-item">취소 합계: <span id="cancel_sum" class="cancel">0원</span></div>
				</div>
				<div id="chart_div"></div>
			</div>

			<div class="notice-box">
				<h3>공지사항</h3>
				<ul>
				<li>[점검] 7월 13일 오전 2시~4시 서버 점검 예정</li>
				<li>[신규기능] 배송추적 API 연동 완료 안내</li>
				</ul>
			</div>
		</div>

	</div>
</div>


<script>
// 현재 날짜와 시간을 업데이트하는 함수
function updateClock() {
  const now = new Date();
  const days = ['일', '월', '화', '수', '목', '금', '토'];

  const year = now.getFullYear();
  const month = String(now.getMonth() + 1).padStart(2, '0');
  const date = String(now.getDate()).padStart(2, '0');
  const day = days[now.getDay()];

  const hours = String(now.getHours()).padStart(2, '0');
  const minutes = String(now.getMinutes()).padStart(2, '0');

  document.getElementById('current_date').innerText = `${year}년 ${month}월 ${date}일 (${day})`;
  document.getElementById('current_time').innerText = `${hours}:${minutes}`;
}

// 최초 실행 + 1분마다 업데이트
updateClock();
setInterval(updateClock, 60000);
</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>