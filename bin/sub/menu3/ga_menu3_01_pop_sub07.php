<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

$inscode	= $_GET['inscode'];
$kcode		= $_GET['kcode'];


// 수납정보 리스트
if($_GET['kcode']){

	// 원수사 계약업로드 데이터
	$sql	= "
		select g.filename, a.uplseq, a.oridata
		from kwn a
			left outer join upload_history(nolock) g on a.scode = g.scode and a.upldate = g.upldate 
							and a.gubun = g.gubun and a.gubunsub = g.gubunsub and a.uplnum = g.uplnum
		where a.scode = '".$_SESSION['S_SCODE']."'
			and a.inscode = '".$_GET['inscode']."'
			and a.kcode = '".$_GET['kcode']."' ";

	$qry = sqlsrv_query( $mscon, $sql );
	$KwnInsData  = sqlsrv_fetch_array($qry);

	$kwn_filename	=	$KwnInsData['filename'];
	$kwn_uplseq		=	$KwnInsData['uplseq'];
	$kwn_oridata	=	$KwnInsData['oridata'];

	$listData_excel_kwn = explode (".****.", $kwn_oridata);
	$kwn_cnt = count($listData_excel_kwn);


	// 원수사 수납업로드 데이터
	$sql	= "
		select g.filename, a.iseq, a.oridata
		from(
			select row_number()over(partition by kcode order by ipdate desc, ncnt desc, ino desc, iseq desc) cnt,* 
			from ins_sunab(nolock)
			where scode = '".$_SESSION['S_SCODE']."'
			  and kcode = '".$_GET['kcode']."'
			) a
			left outer join upload_history(nolock) g on a.scode = g.scode and a.ipdate = g.upldate 
							and a.gubun = g.gubun and a.gubunsub = g.gubunsub and a.ino = g.uplnum
		where a.cnt = 1 ";

	$qry = sqlsrv_query( $mscon, $sql );
	$SunabInsData  = sqlsrv_fetch_array($qry);
	
	$Sunab_filename	=	$SunabInsData['filename'];
	$Sunab_uplseq		=	$SunabInsData['iseq'];
	$Sunab_oridata	=	$SunabInsData['oridata'];

	$listData_excel_Sunab = explode (".****.", $Sunab_oridata);
	$Sunab_cnt = count($listData_excel_Sunab);

	// 원수사 수수료업로드 데이터
	$sql	= "
		select g.filename, a.iseq, a.oridata
		from(
			select row_number()over(partition by kcode order by ipdate desc, ncnt desc, ino desc, iseq desc) cnt,* 
			from ins_ipmst(nolock)
			where scode = '".$_SESSION['S_SCODE']."'
			  and kcode = '".$_GET['kcode']."'
			) a
			left outer join upload_history(nolock) g on a.scode = g.scode and a.ipdate = g.upldate 
							and a.gubun = g.gubun and a.gubunsub = g.gubunsub and a.ino = g.uplnum
		where a.cnt = 1 ";

	$qry = sqlsrv_query( $mscon, $sql );
	$IpmstInsData  = sqlsrv_fetch_array($qry);

	$Ipmst_filename	=	$IpmstInsData['filename'];
	$Ipmst_uplseq		=	$IpmstInsData['iseq'];
	$Ipmst_oridata	=	$IpmstInsData['oridata'];

	$listData_excel_Ipmst = explode (".****.", $Ipmst_oridata);
	$Ipmst_cnt = count($listData_excel_Ipmst);
}



sqlsrv_free_stmt($result);
sqlsrv_close($mscon);


?>

<!-- html영역 -->
<style>
body{background-image: none;}

.tb_type01.view{
	margin-bottom:10px;
}

</style>



<div class="tit_wrap mt20">
	<p style="display:inline-block;color:blue;padding-top:10px">* 마지막 업로드 데이터를 기준으로 원수사 원본 데이터를 조회합니다.</p>
	<span class="btn_wrap">
		<a href="#" class="btn_s white" style="min-width:100px;" onclick="pop_close();">닫기</a>
	</span>
</div>


<!-- //box_gray -->
<div class="tb_type01" style="height:780px;overflow-y:auto;">

	<div id="data01"><!-- 계약 원본  -->
		<div class="tit_wrap mt20" style="margin:10px 0 0 0;color:#0000B7">
			<h4>계약 원수사 원본DATA <?= '  ['.$kwn_filename .']  엑셀 : '.$kwn_uplseq. ' 행'           ?> </h4>
		</div>

		<!-- //box_gray -->
		<div class="tb_type01 view" style="border:1px solid #e5e5e5">
			<table class="">
					<colgroup>
						<col width="13%">
						<col width="21%">
						<col width="13%">
						<col width="21%">
						<col width="13%">
						<col width="auto">
					</colgroup>
				<tbody>

					<?if($kwn_cnt > 1){?>
						<? $sel_bit1	 = 0?>
						<?for($i=0; $i< $kwn_cnt ; $i++){?>
						<?  if ( $sel_bit1	 == 0 ) {?>
						<tr class="top_gubun">
						<?}?>
							<?$listData_excel_kwn_sub = explode (".***.", $listData_excel_kwn[$i])?>
						
								<?if ($sel_bit1 == 0) {?>
										<th><?=$listData_excel_kwn_sub[0]?></th>
										<td><?=$listData_excel_kwn_sub[1]?>
										</td>
										<?$sel_bit1 = 1 ?>		
										<?continue?>		
								<?}?>

								<?if ($sel_bit1 == 1) {?>
										<th><?=$listData_excel_kwn_sub[0]?></th>
										<td><?=$listData_excel_kwn_sub[1]?>
										</td>
										<?$sel_bit1 = 2 ?>		
										<?continue?>		
								<?}?>					

								<?if ($sel_bit1 == 2) {?>
										<th><?=$listData_excel_kwn_sub[0]?></th>
										<td><?=$listData_excel_kwn_sub[1]?>
										</td>
										<?$sel_bit1 = 0?>		
										<?continue?>		
								<?}?>		

						</tr>
						<?}?>
					<?}else{?>
						<tr>
							<td style="color:#8C8C8C" colspan=5>데이터가 없습니다</td>
						</tr>
					<?}?>

				</tbody>
				</table>

			</form>
		</div>
	</div><!-- End 계약 원본  -->



	<div id="data02"><!-- 수납 원본  -->
		<div class="tit_wrap mt20" style="margin-bottom:0px;color:#0000B7">
			<h4>수납 원수사 원본DATA <?= '  ['.$Sunab_filename .']  엑셀 : '.$Sunab_uplseq. ' 행'           ?> </h4>
		</div>

		<!-- //box_gray -->
		<div class="tb_type01 view" style="border:1px solid #e5e5e5">
			<table class="">
					<colgroup>
						<col width="13%">
						<col width="21%">
						<col width="13%">
						<col width="21%">
						<col width="13%">
						<col width="auto">
					</colgroup>
				<tbody>

					<?if($Sunab_cnt > 1){?>

						<? $sel_bit3	 = 0?>
						<?for($k=0; $k< $Sunab_cnt ; $k++){?>
						<?  if ( $sel_bit3	 == 0 ) {?>
						<tr class="top_gubun">
						<?}?>
							<?$listData_excel_sunab_sub = explode (".***.", $listData_excel_Sunab[$k])?>
						
								<?if ($sel_bit3 == 0) {?>
										<th><?=$listData_excel_sunab_sub[0]?></th>
										<td><?=$listData_excel_sunab_sub[1]?>
										</td>
										<?$sel_bit3 = 1 ?>		
										<?continue?>		
								<?}?>

								<?if ($sel_bit3 == 1) {?>
										<th><?=$listData_excel_sunab_sub[0]?></th>
										<td><?=$listData_excel_sunab_sub[1]?>
										</td>
										<?$sel_bit3 = 2 ?>		
										<?continue?>		
								<?}?>					

								<?if ($sel_bit3 == 2) {?>
										<th><?=$listData_excel_sunab_sub[0]?></th>
										<td><?=$listData_excel_sunab_sub[1]?>
										</td>
										<?$sel_bit3 = 0?>		
										<?continue?>		
								<?}?>		
						</tr>
						<?}?>
					<?}else{?>
						<tr>
							<td style="color:#8C8C8C" colspan=5>데이터가 없습니다</td>
						</tr>
					<?}?>

				</tbody>
				</table>

			</form>
		</div>
	</div><!-- End 수납 원본  -->



	<div id="data03"><!-- 수수료 데이터 원본 -->
		<div class="tit_wrap mt20" style="margin-bottom:0px;color:#0000B7">
			<h4>수수료 원수사 원본DATA <?= '  ['.$Ipmst_filename .']  엑셀 : '.$Ipmst_uplseq. ' 행'           ?> </h4>
		</div>

		<!-- //box_gray -->
		<div class="tb_type01 view" style="border:1px solid #e5e5e5">
			<table class="">
					<colgroup>
						<col width="13%">
						<col width="21%">
						<col width="13%">
						<col width="21%">
						<col width="13%">
						<col width="auto">
					</colgroup>
				<tbody>

					<?if($Ipmst_cnt > 1){?>
						<? $sel_bit2	 = 0?>
						<?for($j=0; $j< $Ipmst_cnt ; $j++){?>
						<?  if ( $sel_bit2	 == 0 ) {?>
						<tr class="top_gubun">
						<?}?>
							<?$listData_excel_ipmst_sub = explode (".***.", $listData_excel_Ipmst[$j])?>
						
								<?if ($sel_bit2 == 0) {?>
										<th><?=$listData_excel_ipmst_sub[0]?></th>
										<td><?=$listData_excel_ipmst_sub[1]?>
										</td>
										<?$sel_bit2 = 1 ?>		
										<?continue?>		
								<?}?>

								<?if ($sel_bit2 == 1) {?>
										<th><?=$listData_excel_ipmst_sub[0]?></th>
										<td><?=$listData_excel_ipmst_sub[1]?>
										</td>
										<?$sel_bit2 = 2 ?>		
										<?continue?>		
								<?}?>					

								<?if ($sel_bit2 == 2) {?>
										<th><?=$listData_excel_ipmst_sub[0]?></th>
										<td><?=$listData_excel_ipmst_sub[1]?>
										</td>
										<?$sel_bit2 = 0?>		
										<?continue?>		
								<?}?>		

						</tr>
						<?}?>

					<?}else{?>
						<tr>
							<td style="color:#8C8C8C" colspan=5>데이터가 없습니다</td>
						</tr>
					<?}?>

				</tbody>
				</table>

			</form>
		</div>
	</div><!-- End 수수료 데이터 원본  -->


	<div id="data04" style="display:none"><!-- 노데이터 안내  -->
		<div class="tit_wrap mt20" >
			<h4>원수사 원본DATA</h4>
		</div>
		<!-- //box_gray -->
		<div class="tb_type01 view" style="border-top: 0px solid #47474a;">
			<table class="">
					<colgroup>
						<col width="100%">
					</colgroup>
				<tbody>
					<tr>
						<td style="color:#8C8C8C">원본 데이터가 없습니다</td>
					</tr>
				</tbody>
				</table>

			</form>
		</div>
	</div><!-- End 노데이터  -->

</div><!-- // tb_type01 -->



<script type="text/javascript">

// 닫기
function pop_close(){	
	window.close();
}




$(document).ready(function(){

	var kwn_cnt		= '<?=$kwn_cnt?>';
	var Sunab_cnt	= '<?=$Sunab_cnt?>';
	var Ipmst_cnt	= '<?=$Ipmst_cnt?>';

	// 계약데이터 미존재시 hidden
	if(parseInt(kwn_cnt) > 1){
		$("#data01").css("display","");
	}else{
		$("#data01").css("display","none");
	}

	// 수납데이터 미존재시 hidden
	if(parseInt(Sunab_cnt) > 1){
		$("#data02").css("display","");
	}else{
		$("#data02").css("display","none");
	}

	// 수수료데이터 미존재시 hidden
	if(parseInt(Ipmst_cnt) > 1){
		$("#data03").css("display","");
	}else{
		$("#data03").css("display","none");
	}


	if(parseInt(kwn_cnt) < 2 && parseInt(Sunab_cnt) < 2 && parseInt(Ipmst_cnt) < 2){
		$("#data04").css("display","");
	}else{
		$("#data04").css("display","none");
	}

});


</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>