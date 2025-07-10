<?

include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

// ������ ���� ������Ʈ �׷��� ����(�ӽ�)
$graph_rows = [];
$graph_rows_php = [];

$sql = "SELECT order_date, order_amt, order_cnt, cancel_amt, cancel_cnt FROM order_summary_by_day";
$qry = sqlsrv_query($mscon, $sql);

while($row = sqlsrv_fetch_array($qry, SQLSRV_FETCH_ASSOC)) {
	$tooltip_order = "<div style=\"padding:5px; white-space: nowrap;\">�ֹ��ݾ�: " . number_format($row['order_amt']) . "<br>�Ǽ�: {$row['order_cnt']}��</div>";
	$tooltip_cancel = "<div style=\"padding:5px; white-space: nowrap;\">��ұݾ�: " . number_format($row['cancel_amt']) . "<br>�Ǽ�: {$row['cancel_cnt']}��</div>";

    $graph_rows[] = "['{$row['order_date']}', {$row['order_amt']}, '{$tooltip_order}', {$row['cancel_amt']}, '{$tooltip_cancel}']";

    $graph_rows_php[] = [
        'order_amt' => $row['order_amt'],
        'cancel_amt' => $row['cancel_amt']
    ];
}
/*
 * ���� ��ũ���� �ֿ� ������ ��ü ���̾ƿ��� ���� ���� 100vh(����Ʈ ����)�� �ʰ��ϱ� �����Դϴ�.
 * #admin_top_section�� height: 40vh, #admin_bottom_section�� height: 60vh�� ���� 100vh�ε�,
 * ���� padding, border, margin ������ ���� ���̰� �� Ŀ�� ��ũ���� ����ϴ�.
 * 
 * �ذ� ���:
 * 1. �� ������ height�� ���� 40vh, 60vh���� auto�� �ٲٰ�, �ʿ�� min-height�� �����ϼ���.
 * 2. �Ǵ� padding, border�� ���̰ų� box-sizing: border-box;�� ��� ��ҿ� ����ƴ��� Ȯ���ϼ���.
 * 3. body�� overflow-y: hidden;�� ���Ƿ� ���� ������(������ �� ����).
 * 
 * ����(��õ):
 * #admin_top_section, #admin_bottom_section�� height�� auto�� ����:
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
 * �Ǵ� ��ü ���̾ƿ��� flex column ������ ���ΰ�, ���� flex-grow�� ������ ���ߴ� �͵� ����Դϴ�.
 */
?>

<style>
	* { box-sizing: border-box; }
	body { margin: 0; font-family: '���� ���', sans-serif; background: #f9f9f9; }

	/* admin-box ���� ���� (CSS flex��) */
	#admin_top_section {
		display: flex;
		flex-wrap: wrap;
		justify-content: space-between;
		align-items: stretch; /* �߰�: ��� .admin-box ���� ���� */
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
		/* height: calc(100% - 32px);  ���� */
		overflow-y: auto;
		border-radius: 6px;
		position: relative;
		display: flex;           /* �߰� */
		flex-direction: column;  /* �߰� */
	}

	.admin-box-header {
		display: flex; align-items: center; justify-content: space-between;
		height: 50px; margin-bottom: 12px; padding: 0 6px; border-bottom: 1px solid #eee;
	}

	.admin-box-title {
	font-size: 16px; /* �� ���� */
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
	['��¥', {label: '�ֹ��ݾ�', type: 'number'}, {type: 'string', role: 'tooltip', p: {html: true}},
			{label: '��ұݾ�', type: 'number'}, {type: 'string', role: 'tooltip', p: {html: true}}],
	<?= implode(",\n  ", $graph_rows) ?>
	]);

    const options = {
      title: '�ֱ� 7�� �ֹ�/��� �ݾ�',
      legend: { position: 'top' },
      bars: 'vertical',
      colors: ['#42a5f5', '#ef5350'],
      tooltip: { isHtml: true },
      vAxis: {
        format: '#,###\u20a9',
        title: '�ݾ�(\u20a9)'
      },
      hAxis: {
        title: '��¥'
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
	<!-- ��� ������ �ڽ� -->
	<div id="admin_top_section">
	<div class="admin-box">
		<div class="admin-box-header">
		<div class="admin-box-title"><i class="fas fa-truck"></i> �ֹ�ó��</div>
		<div class="more-link"><i class="fas fa-plus"></i> ������</div>
		</div>
		<div class="order-grid">
		<div class="order-item">�Աݴ�� <span class="count">2��</span></div>
		<div class="order-item">�ԱݿϷ� <span class="count">1��</span></div>
		<div class="order-item">���� <span class="count">4��</span></div>
		<div class="order-item">���ֻ��� <span class="count">2��</span></div>
		<div class="order-item">�������׵��� <span class="count">0��</span></div>
		<div class="order-item">����� <span class="count">3��</span></div>
		<div class="order-item">��ۿϷ� <span class="count">6��</span></div>
		<div class="order-item">��Ȯ�� <span class="count">7��</span></div>
		<div class="order-item">��� <span class="count">0��</span></div>
		<div class="order-item">��ǰ <span class="count">1��</span></div>
		</div>
	</div>

	<div class="admin-box">
		<div class="admin-box-header">
		<div class="admin-box-title"><i class="fas fa-box"></i> ��ǰ��Ȳ</div>
		<div class="more-link"><i class="fas fa-plus"></i> ������</div>
		</div>
		<div class="product-grid">
		<div class="product-box bg-blue">
			<div class="product-title">�Ϲ��Ǹ�</div>
			<div class="product-status-list">
			<div class="product-item"><span>�Ǹ���</span> <span>63��</span></div>
			<div class="product-item"><span>�Ǹ�����</span> <span>12��</span></div>
			</div>
		</div>
		<div class="product-box bg-pink">
			<div class="product-title">�̺�Ʈ�Ǹ�</div>
			<div class="product-status-list">
			<div class="product-item"><span>�Ǹ���</span> <span>5��</span></div>
			<div class="product-item"><span>ǰ��</span> <span>3��</span></div>
			<div class="product-item"><span>�Ǹ�����</span> <span>7��</span></div>
			</div>
		</div>
		</div>
	</div>

	<div class="admin-box">
		<div class="admin-box-header">
		<div class="admin-box-title"><i class="fas fa-user-headset"></i> 1:1����</div>
		<div class="more-link"><i class="fas fa-plus"></i> ������</div>
		</div>
		<ul class="inquiry-list">
		<li class="inquiry-item">��� ���� ������?<span class="status">�̴亯</span><time>07:30</time></li>
		<li class="inquiry-item">��ǰ �ҷ� �����ϴ�<span class="status">�̴亯</span><time>06:20</time></li>
		<li class="inquiry-item">��ȯ ��û�մϴ�<span class="status">�亯�Ϸ�</span><time>06:10</time></li>
		</ul>
	</div>

	<div class="admin-box">
		<div class="admin-box-header">
		<div class="admin-box-title"><i class="fas fa-comment-dots"></i> ��ǰ����</div>
		<div class="more-link"><i class="fas fa-plus"></i> ������</div>
		</div>
		<ul class="inquiry-list">
		<li class="inquiry-item">����� ��� �ǳ���?<span class="status">�̴亯</span><time>07:00</time></li>
		<li class="inquiry-item">���԰� ���� �ǳ���?<span class="status">�亯�Ϸ�</span><time>06:45</time></li>
		<li class="inquiry-item">���� ���� �����Ѱ���?<span class="status">�̴亯</span><time>05:55</time></li>
		</ul>
	</div>
	</div>

	<!-- �ϴ� �׷��� + �������� -->
	<div id="admin_bottom_section">
	<div class="graph-box">
		<h3>�ֹ�/��ұݾ� �׷���</h3>
		<div class="sum-box">
		<div class="sum-item">�ֹ� �հ�: <span id="order_sum" class="order">0��</span></div>
		<div class="sum-item">��� �հ�: <span id="cancel_sum" class="cancel">0��</span></div>
		</div>
		<div id="chart_div"></div>
	</div>

	<div class="notice-box">
		<h3>��������</h3>
		<ul>
		<li>[����] 7�� 13�� ���� 2��~4�� ���� ���� ����</li>
		<li>[�űԱ��] ������� API ���� �Ϸ� �ȳ�</li>
		</ul>
	</div>
	</div>
</div>


<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>