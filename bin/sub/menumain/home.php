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
/*
 * 세로 스크롤의 주요 원인은 전체 레이아웃의 높이 합이 100vh(뷰포트 높이)를 초과하기 때문입니다.
 * #admin_top_section이 height: 40vh, #admin_bottom_section이 height: 60vh로 합이 100vh인데,
 * 각각 padding, border, margin 등으로 실제 높이가 더 커져 스크롤이 생깁니다.
 * 
 * 해결 방법:
 * 1. 두 섹션의 height를 각각 40vh, 60vh에서 auto로 바꾸고, 필요시 min-height로 조정하세요.
 * 2. 또는 padding, border를 줄이거나 box-sizing: border-box;가 모든 요소에 적용됐는지 확인하세요.
 * 3. body에 overflow-y: hidden;을 임의로 넣지 마세요(숨겨질 수 있음).
 * 
 * 예시(추천):
 * #admin_top_section, #admin_bottom_section의 height를 auto로 변경:
 * 
 * #admin_top_section {
 *   height: auto;
 *   min-height: 40vh;
 * }
 * #admin_bottom_section {
 *   height: auto;
 *   min-height: 60vh;
 * }
 * 
 * 또는 전체 레이아웃을 flex column 구조로 감싸고, 내부 flex-grow로 비율을 맞추는 것도 방법입니다.
 */
?>

<style>
	* { box-sizing: border-box; }
	body { margin: 0; font-family: '맑은 고딕', sans-serif; background: #f9f9f9; }

	/* admin-box 높이 통일 (CSS flex로) */
	#admin_top_section {
		display: flex;
		flex-wrap: wrap;
		justify-content: space-between;
		align-items: stretch; /* 추가: 모든 .admin-box 높이 맞춤 */
		padding: 10px;
		height: auto;
		min-height: 40vh;
		background: #f0f0f0;
		border-bottom: 2px solid #ccc;
	}
	.admin-box {
		flex: 0 0 calc(25% - 12px);
		margin: 6px;
		padding: 10px;
		background: #fff;
		border: 1px solid #ddd;
		box-shadow: 2px 2px 6px rgba(0,0,0,0.1);
		/* height: calc(100% - 32px);  삭제 */
		overflow-y: auto;
		border-radius: 6px;
		position: relative;
		display: flex;           /* 추가 */
		flex-direction: column;  /* 추가 */
	}

	.admin-box-header {
		display: flex; align-items: center; justify-content: space-between;
		height: 50px; margin-bottom: 12px; padding: 0 6px; border-bottom: 1px solid #eee;
	}

	.admin-box-title {
	font-size: 16px; /* ↓ 줄임 */
	font-weight: bold;
	color: #222;
	display: flex;
	align-items: center;
	gap: 8px;
	}

	.admin-box-title i { font-size: 22px; color: #2196f3; }

	.more-link {
		font-size: 13px; color: #666; cursor: pointer;
		display: flex; align-items: center; gap: 4px;
	}

	.order-grid, .product-grid {
		display: grid; grid-template-columns: 1fr 1fr; gap: 6px;
	}

	.order-item, .product-item {
		display: flex; justify-content: space-between;
		padding: 6px 8px; background: #f7f7f7;
		border: 1px solid #ddd; border-radius: 4px;
		font-size: 14px; color: #333;
	}


	.order-item span.count {
		font-weight: bold;
		color: #2196f3;
	}	

	.product-box {
		border: 1px solid #ddd; border-radius: 6px;
		padding: 10px; height: 100%;
	}

	.product-title {
		font-size: 14px; margin-bottom: 8px; padding-bottom: 4px;
		font-weight: bold; border-bottom: 1px solid #ddd;
	}

	.product-status-list {
		display: flex; flex-direction: column; gap: 6px;
	}

	.bg-blue { background-color: #e3f2fd; }
	.bg-pink { background-color: #fdecef; }

	.inquiry-list {
		list-style: none; padding: 0; margin: 0;
	}

	.inquiry-item {
		border-bottom: 1px solid #eee;
		padding: 6px 0; font-size: 13px;
	}

	.inquiry-item .status {
		font-weight: bold; color: #e91e63; float: right;
	}

	.inquiry-item time {
		font-size: 11px; color: #999; display: block;
	}

	#admin_bottom_section {
		display: flex; 
		padding: 20px;
	    height: auto;
	    min-height: 60vh;
		background: #fff; gap: 20px;
	}

	.graph-box {
		width: 60%; border: 1px solid #ddd;
		background: #fafafa; border-radius: 6px; padding: 15px;
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
		width: 100%; height: 280px;
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
    google.charts.load('current', {packages: ['corechart']});
    google.charts.setOnLoadCallback(drawChart);

  function drawChart() {
	const data = google.visualization.arrayToDataTable([
	['날짜', {label: '주문금액', type: 'number'}, {type: 'string', role: 'tooltip', p: {html: true}},
			{label: '취소금액', type: 'number'}, {type: 'string', role: 'tooltip', p: {html: true}}],
	<?= implode(",\n  ", $graph_rows) ?>
	]);

    const options = {
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

    const chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
    chart.draw(data, options);
  }

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
	<!-- 상단 관리자 박스 -->
	<div id="admin_top_section">
	<div class="admin-box">
		<div class="admin-box-header">
		<div class="admin-box-title"><i class="fas fa-truck"></i> 주문처리</div>
		<div class="more-link"><i class="fas fa-plus"></i> 더보기</div>
		</div>
		<div class="order-grid">
		<div class="order-item">입금대기 <span class="count">2건</span></div>
		<div class="order-item">입금완료 <span class="count">1건</span></div>
		<div class="order-item">발주 <span class="count">4건</span></div>
		<div class="order-item">발주사배송 <span class="count">2건</span></div>
		<div class="order-item">국내공항도착 <span class="count">0건</span></div>
		<div class="order-item">배송중 <span class="count">3건</span></div>
		<div class="order-item">배송완료 <span class="count">6건</span></div>
		<div class="order-item">고객확정 <span class="count">7건</span></div>
		<div class="order-item">취소 <span class="count">0건</span></div>
		<div class="order-item">반품 <span class="count">1건</span></div>
		</div>
	</div>

	<div class="admin-box">
		<div class="admin-box-header">
		<div class="admin-box-title"><i class="fas fa-box"></i> 상품현황</div>
		<div class="more-link"><i class="fas fa-plus"></i> 더보기</div>
		</div>
		<div class="product-grid">
		<div class="product-box bg-blue">
			<div class="product-title">일반판매</div>
			<div class="product-status-list">
			<div class="product-item"><span>판매중</span> <span>63개</span></div>
			<div class="product-item"><span>판매종료</span> <span>12개</span></div>
			</div>
		</div>
		<div class="product-box bg-pink">
			<div class="product-title">이벤트판매</div>
			<div class="product-status-list">
			<div class="product-item"><span>판매중</span> <span>5개</span></div>
			<div class="product-item"><span>품절</span> <span>3개</span></div>
			<div class="product-item"><span>판매종료</span> <span>7개</span></div>
			</div>
		</div>
		</div>
	</div>

	<div class="admin-box">
		<div class="admin-box-header">
		<div class="admin-box-title"><i class="fas fa-user-headset"></i> 1:1문의</div>
		<div class="more-link"><i class="fas fa-plus"></i> 더보기</div>
		</div>
		<ul class="inquiry-list">
		<li class="inquiry-item">배송 언제 오나요?<span class="status">미답변</span><time>07:30</time></li>
		<li class="inquiry-item">제품 불량 같습니다<span class="status">미답변</span><time>06:20</time></li>
		<li class="inquiry-item">교환 요청합니다<span class="status">답변완료</span><time>06:10</time></li>
		</ul>
	</div>

	<div class="admin-box">
		<div class="admin-box-header">
		<div class="admin-box-title"><i class="fas fa-comment-dots"></i> 상품문의</div>
		<div class="more-link"><i class="fas fa-plus"></i> 더보기</div>
		</div>
		<ul class="inquiry-list">
		<li class="inquiry-item">사이즈가 어떻게 되나요?<span class="status">미답변</span><time>07:00</time></li>
		<li class="inquiry-item">재입고 언제 되나요?<span class="status">답변완료</span><time>06:45</time></li>
		<li class="inquiry-item">가격 조정 가능한가요?<span class="status">미답변</span><time>05:55</time></li>
		</ul>
	</div>
	</div>

	<!-- 하단 그래프 + 공지사항 -->
	<div id="admin_bottom_section">
	<div class="graph-box">
		<h3>주문/취소금액 그래프</h3>
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


<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>