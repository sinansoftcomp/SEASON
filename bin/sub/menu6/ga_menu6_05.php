<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");


//검색 데이터 구하기 
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

<!-- html영역 -->
<style>
body{background-image: none;}

</style>

<div class="container">
	<div class="content_wrap">
		<fieldset>
			<legend>고객관리</legend>
			

			<div id="kwngo_sort" class="tb_type01 div_grid" style="overflow-y:auto;">
				<table class="gridhover">
					<colgroup>
						<col width="200px">
					</colgroup>
					<thead>
					<tr class="rowTop">
						<th align="left">일시</th>
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
								<td style="color:#8C8C8C" colspan=12>검색된 데이터가 없습니다</td>
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

	window.parent.postMessage("자동차비교견적 > 테스트", "*");   // '*' on any domain 부모로 보내기..        


});

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>