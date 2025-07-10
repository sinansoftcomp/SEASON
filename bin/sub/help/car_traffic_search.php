<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");


$sql	= "
				select 
						a.code,
						a.bigo
				from carlaw a
				order by a.code desc " ;

$result	= sqlsrv_query( $mscon, $sql );
$listData = array();
while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
	$listData[]	= $row;
}



?>
<style>
body{background-image: none;}
.box_wrap{margin-bottom:0px;padding:15px 20px;}
.tb_type01 th, .tb_type01 td {padding: 4px 0;}
</style>

<div class="container container_bk">
	<div class="content_wrap">
		<fieldset>

			<div class="menu_group_top" style="border-bottom:0px solid;">
				<div class="menu_group">
					<span><i class="fa-solid fa-car-side mgr5 font_topcolor"></i>법규위반</span>
				</div>
			</div>

			<div class="tit_wrap" >
				<div class="tb_type01 tb_fix">
					<table class="gridhover">
						<colgroup>
							<col width="15%">
							<col width="auto">
						</colgroup>
						<thead>
						<tr>
							<th>구분</th>
							<th>적용대상 법규위반</th>
						</tr>
						</thead>
						<tbody>
						<?if(!empty($listData)){?>
						<?foreach($listData as $key => $val){extract($val);?>
						<tr rol-data1='<?=$code?>' class="rowData"  >
							<td><?=$code?></td>
							<td align="left"><?=$bigo?></td>				
						</tr>
						<?}}?>
						</tbody>
					</table>
				</div>
			</div>

		</fieldset>
	</div>
</div>

<script type="text/javascript">


$(document).ready(function(){
	$("input[name='srchText']").focus();

	$("#SearchBtn").on("click", function(){	
		$("form[name='searchFrm']").submit();
	});

	$(".rowData").click(function(){
		var idx=$(".rowData").index($(this));
		var code	= $(".rowData").eq(idx).attr("rol-data1");

		opener.setCarlaw(code);
		self.close();

	});

});

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>