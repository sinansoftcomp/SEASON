<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");


//�˻� ������ ���ϱ� 
$sql= "
	select convert(varchar,date,120) ildate
		from d_test 
		order by date desc ";

$qry	= sqlsrv_query( $mscon, $sql );
$listData = array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listData[]	= $fet;
}


sqlsrv_free_stmt($result);
sqlsrv_close($mscon);

?>

<!-- html���� -->
<style>
body{background-image: none;}

</style>

<div class="container">
	<div class="content_wrap">
		<fieldset>
			<legend>������</legend>
			

			<div id="kwngo_sort" class="tb_type01 div_grid" style="overflow-y:auto;">
				<table class="gridhover">
					<colgroup>
						<col width="200px">
					</colgroup>
					<thead>
					<tr class="rowTop">
						<th align="left">�Ͻ�</th>
					</tr>
					</thead>
					<tbody>
						<?if(!empty($listData)){?>
						<?foreach($listData as $key => $val){extract($val);?>
						<tr class="rowData" >
							<td align="left"><?=$ildate?></td>
						</tr>
						<?}}else{?>
							<tr>
								<td style="color:#8C8C8C" colspan=12>�˻��� �����Ͱ� �����ϴ�</td>
							</tr>
						<?}?>

					</tbody>
				</table>
			</div><!-- // tb_type01 -->



		</fieldset>
	</div><!-- // content_wrap -->
</div>
<!-- // container -->
<!-- // wrap -->
<script type="text/javascript">


$(document).ready(function(){

	window.parent.postMessage("�ڵ����񱳰��� > �׽�Ʈ", "*");   // '*' on any domain �θ�� ������..        


});

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>