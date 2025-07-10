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

?>

<style>
	* { box-sizing: border-box; }
	body { margin: 0; font-family: '���� ���', sans-serif; background: #f9f9f9; }

	#admin_container {
		display: flex;
		flex-direction: column;
		height: 100%;
	}

	/* ���� ��� ��¥ �� �ð� ǥ�� */ 
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

	/* ��� 4�ڽ� ���� */
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
	border-radius: 6px;
	border: 1px solid #ddd;
	box-shadow: 2px 2px 6px rgba(0,0,0,0.1);
	display: flex;
	flex-direction: column;
	gap: 10px;
	}

	/* ���� */
	.bg-blue   { background-color: #e3f2fd; }
	.bg-pink   { background-color: #fdecef; }
	.bg-green  { background-color: #e0f7e9; }
	.bg-yellow { background-color: #fff8e1; }

	/* ������ ���� */
	.icon-blue   { color: #2196f3; }
	.icon-pink   { color: #e91e63; }
	.icon-green  { color: #43a047; }
	.icon-yellow { color: #f9a825; }

	/* ��� */
	.admin-box-header {
	display: flex;
	align-items: center;
	justify-content: space-between;
	height: 40px;
	border-bottom: 1px solid #ccc;
	padding: 0 6px;
	}

	.admin-box-title {
	font-size: 16px;
	font-weight: bold;
	display: flex;
	align-items: center;
	gap: 8px;
	}

	/* �׸� ��Ÿ�� */
	.status-item {
	display: flex;
	justify-content: space-between;
	padding: 6px 10px;
	font-size: 14px;
	border-radius: 4px;
	background: #fff;
	color: #333;
	}

	/* count ������ �ڽ����� ���� ��ȭ�Ӱ� */
	.bg-blue .count   { color: #1976d2; font-weight: bold; }
	.bg-pink .count   { color: #d81b60; font-weight: bold; }
	.bg-green .count  { color: #2e7d32; font-weight: bold; }
	.bg-yellow .count { color: #f57f17; font-weight: bold; }

	.more-link {
	font-size: 13px;
	color: #666;
	cursor: pointer;
	display: flex;
	align-items: center;
	gap: 4px;
	}

	/* �ϴ� ���� (�׷��� + ��������) */
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
		flex: 1; /* ��Ʈ ������ ���� ���� ��� ���� */
		width: 100%;
		height: 100%;
		min-height: 280px; /* �ּ� ���� ���� */
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
      ['��¥', {label: '�ֹ��ݾ�', type: 'number'}, {type: 'string', role: 'tooltip', p: {html: true}},
               {label: '��ұݾ�', type: 'number'}, {type: 'string', role: 'tooltip', p: {html: true}}],
      <?= implode(",\n  ", $graph_rows) ?>

    ]);

    options = {
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

    chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
    drawChart();
  }

  function drawChart() {
    if (chart && data && options) {
      chart.draw(data, options);
    }
  }

  // �������� ����
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

		<!-- ��� ������ �ڽ� -->
		<div id="admin_top_section">
		<!-- �ֹ�ó�� -->
			<div class="admin-box bg-blue">
				<div class="admin-box-header">
				<div class="admin-box-title"><i class="fas fa-receipt icon-blue"></i> �ֹ�ó��</div>
				<div class="more-link">������ <i class="fas fa-plus"></i></div>
				</div>
				<div class="status-item">�Աݴ�� <span class="count">2��</span></div>
				<div class="status-item">�ԱݿϷ� <span class="count">3��</span></div>
				<div class="status-item">���� <span class="count">5��</span></div>
				<div class="status-item">���ֻ��� <span class="count">1��</span></div>
				<div class="status-item">�������׵��� <span class="count">0��</span></div>
				<div class="status-item">����� <span class="count">2��</span></div>
				<div class="status-item">��ۿϷ� <span class="count">4��</span></div>
				<div class="status-item">��Ȯ�� <span class="count">3��</span></div>
			</div>

			<!-- ��ǰ��Ȳ -->
			<div class="admin-box bg-pink">
				<div class="admin-box-header">
				<div class="admin-box-title"><i class="fas fa-box icon-pink"></i> ��ǰ��Ȳ</div>
				<div class="more-link">������ <i class="fas fa-plus"></i></div>
				</div>
				<div class="status-item">�Ϲ��Ǹ� (�Ǹ���) <span class="count">30��</span></div>
				<div class="status-item">�Ϲ��Ǹ� (�Ǹ�����) <span class="count">12��</span></div>
				<div class="status-item">�̺�Ʈ (�Ǹ���) <span class="count">4��</span></div>
				<div class="status-item">�̺�Ʈ (ǰ��) <span class="count">2��</span></div>
				<div class="status-item">�̺�Ʈ (�Ǹ�����) <span class="count">1��</span></div>
			</div>

			<!-- ȸ������ -->
			<div class="admin-box bg-green">
				<div class="admin-box-header">
				<div class="admin-box-title"><i class="fas fa-user icon-green"></i> ȸ������</div>
				<div class="more-link">������ <i class="fas fa-plus"></i></div>
				</div>
				<div class="status-item">��ü����ȸ�� <span class="count">102��</span></div>
				<div class="status-item">���δ�� <span class="count">3��</span></div>
				<div class="status-item">�ֱ��Ѵް��� <span class="count">8��</span></div>
				<div class="status-item">�ֱ��Ѵ�Ż�� <span class="count">2��</span></div>
				<div class="status-item">�޸�ȸ�� <span class="count">6��</span></div>
			</div>

			<!-- ��ǰ���� -->
			<div class="admin-box bg-yellow">
				<div class="admin-box-header">
				<div class="admin-box-title"><i class="fas fa-question-circle icon-yellow"></i> ��ǰ����</div>
				<div class="more-link">������ <i class="fas fa-plus"></i></div>
				</div>
				<div class="status-item">�̴亯 <span class="count">3��</span></div>
				<div class="status-item">�亯�Ϸ� <span class="count">8��</span></div>
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
</div>


<script>
// ���� ��¥�� �ð��� ������Ʈ�ϴ� �Լ�
function updateClock() {
  const now = new Date();
  const days = ['��', '��', 'ȭ', '��', '��', '��', '��'];

  const year = now.getFullYear();
  const month = String(now.getMonth() + 1).padStart(2, '0');
  const date = String(now.getDate()).padStart(2, '0');
  const day = days[now.getDay()];

  const hours = String(now.getHours()).padStart(2, '0');
  const minutes = String(now.getMinutes()).padStart(2, '0');

  document.getElementById('current_date').innerText = `${year}�� ${month}�� ${date}�� (${day})`;
  document.getElementById('current_time').innerText = `${hours}:${minutes}`;
}

// ���� ���� + 1�и��� ������Ʈ
updateClock();
setInterval(updateClock, 60000);
</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>