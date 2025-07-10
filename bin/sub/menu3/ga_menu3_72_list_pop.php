<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

$oridata = $_GET['oridata'];
$filename = $_GET['filename'];
$iseq = $_GET['iseq'];
 
$listData_excel = explode (".****.", $oridata);
$cnt = count($listData_excel);
//sqlsrv_free_stmt($qry);
//sqlsrv_close($mscon);


?>

<!-- html¿µ¿ª -->
<style>
body{background-image: none;}

</style>

<div class="container">
	<div class="content_wrap">
		<fieldset>
			<legend></legend>
 

			<div class="tit_wrap mt20">
				<h3 class="tit_big">¼ö¼ö·á ¿ø¼ö»ç ¿øº»DATA <?= '  ['.$filename .']  ¿¢¼¿ : '.$iseq. ' Çà'           ?> </h3>

				<span class="btn_wrap">
					<a class="btn_s white" style="min-width:100px;" onclick="kwn_close();">´Ý±â</a>
				</span>
			</div>

			<!-- //box_gray -->
			<div class="tb_type01 view">
				<table class="gridhover">
						<colgroup>
							<col width="13%">
							<col width="21%">
							<col width="13%">
							<col width="21%">
							<col width="13%">
							<col width="auto">
						</colgroup>
					<tbody class="kwndata">


						<? $sel_bit	 = 0?>
						<?for($i=0; $i< $cnt ; $i++){?>
						<?  if ( $sel_bit	 == 0 ) {?>
						<tr class="top_gubun">
						<?}?>
							<?$listData_excel_sub = explode (".***.", $listData_excel[$i])?>
						
								<?if ($sel_bit == 0) {?>
										<th><?=$listData_excel_sub[0]?></th>
										<td><?=$listData_excel_sub[1]?>
										</td>
										<?$sel_bit = 1 ?>		
										<?continue?>		
								<?}?>

								<?if ($sel_bit == 1) {?>
										<th><?=$listData_excel_sub[0]?></th>
										<td><?=$listData_excel_sub[1]?>
										</td>
										<?$sel_bit = 2 ?>		
										<?continue?>		
								<?}?>					

								<?if ($sel_bit == 2) {?>
										<th><?=$listData_excel_sub[0]?></th>
										<td><?=$listData_excel_sub[1]?>
										</td>
										<?$sel_bit = 0?>		
										<?continue?>		
								<?}?>		

						</tr>
						<?}?>

					</tbody>
					</table>

				</form>
			</div>



		</fieldset>

	</div><!-- // content_wrap -->
</div>
<!-- // container -->





<script type="text/javascript">

// ´Ý±â
function kwn_close(){	
	window.close();
	//opener.location.reload();
}
</script>
<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>