<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

$carseq = $_GET['carseq'];

if($_GET['carseq']){
	$sql= "select 
				b.resdt,
				b.hd_tot,b.hd_man1,b.hd_man2,b.hd_mul,b.hd_sin,b.hd_mu,b.hd_car,b.hd_goout,b.hd_msg,b.hd_text,b.hd_txt,
				b.ss_tot,b.ss_man1,b.ss_man2,b.ss_mul,b.ss_sin,b.ss_mu,b.ss_car,b.ss_goout,b.ss_msg,b.ss_text,b.ss_txt,
				b.db_tot,b.db_man1,b.db_man2,b.db_mul,b.db_sin,b.db_mu,b.db_car,b.db_goout,b.db_msg,b.db_text,b.db_txt,
				b.lg_tot,b.lg_man1,b.lg_man2,b.lg_mul,b.lg_sin,b.lg_mu,b.lg_car,b.lg_goout,b.lg_msg,b.lg_text,b.lg_txt,
				b.dy_tot,b.dy_man1,b.dy_man2,b.dy_mul,b.dy_sin,b.dy_mu,b.dy_car,b.dy_goout,b.dy_msg,b.dy_text,b.dy_txt,
				b.sy_tot,b.sy_man1,b.sy_man2,b.sy_mul,b.sy_sin,b.sy_mu,b.sy_car,b.sy_goout,b.sy_msg,b.sy_text,b.sy_txt,
				b.sd_tot,b.sd_man1,b.sd_man2,b.sd_mul,b.sd_sin,b.sd_mu,b.sd_car,b.sd_goout,b.sd_msg,b.sd_text,b.sd_txt,
				b.dh_tot,b.dh_man1,b.dh_man2,b.dh_mul,b.dh_sin,b.dh_mu,b.dh_car,b.dh_goout,b.dh_msg,b.dh_text,b.dh_txt,
				b.gr_tot,b.gr_man1,b.gr_man2,b.gr_mul,b.gr_sin,b.gr_mu,b.gr_car,b.gr_goout,b.gr_msg,b.gr_text,b.gr_txt
	from carestamt b 
	where b.scode = '".$_SESSION['S_SCODE']."' and b.carseq = '".$_GET['carseq']."' ";

	$qry	= sqlsrv_query( $mscon, $sql );
	extract($fet	= sqlsrv_fetch_array($qry));
}

?>


<div class="tb_type01 view" style="margin-bottom:20px;">
	<table>
		<colgroup>
			<col width="20px">
			<col width="80px">
			<col width="80px">
			<col width="80px">
			<col width="80px">
			<col width="80px">
			<col width="80px">
			<col width="80px">
			<col width="80px">
			<col width="80px">
			<col width="80px">
			<col width="80px">
			<col width="auto">
		</colgroup>

		<tbody id="caramt" style="display:none">
			<tr>
				<th><input type="checkbox" id="top_check" onclick="check_sel();"></th>
				<th>보험사</th>
				<th>총보험료</th>
				<th>대인I</th>
				<th>대인II</th>
				<th>대물배상</th>
				<th>신체상해</th>
				<th>무보험차</th>
				<th>자차손해</th>
				<th>긴급출동</th>
				<th>연령한정</th>
				<th>운전자한정</th>
				<th>메시지</th>
			</tr>
			<tr class="">
				<td><input type="checkbox" class="inscheck" value="DB"></td>
				<td align="center">DB</td>
				<td align="right"><?=number_format($db_tot)?></td>
				<td align="right"><?=number_format($db_man1)?></td>
				<td align="right"><?=number_format($db_man2)?></td>
				<td align="right"><?=number_format($db_mul)?></td>
				<td align="right"<?=number_format($db_sin)?></td>
				<td align="right"><?=number_format($db_mu)?></td>
				<td align="right"><?=number_format($db_car)?></td>
				<td align="right"><?=number_format($db_goout)?></td>									
				<td align="left"><?=$db_text?></td>
				<td align="left"><?=$db_txt?></td>
				<td align="left"><?=$db_msg?></td>
			</tr>
			<tr class="">
				<td><input type="checkbox" class="inscheck" value="LG"></td>
				<td align="center">KB</td>
				<td align="right"><?=number_format($lg_tot)?></td>
				<td align="right"><?=number_format($lg_man1)?></td>
				<td align="right"><?=number_format($lg_man2)?></td>
				<td align="right"><?=number_format($lg_mul)?></td>
				<td align="right"<?=number_format($lg_sin)?></td>
				<td align="right"><?=number_format($lg_mu)?></td>
				<td align="right"><?=number_format($lg_car)?></td>
				<td align="right"><?=number_format($lg_goout)?></td>									
				<td align="left"><?=$lg_text?></td>
				<td align="left"><?=$lg_txt?></td>
				<td align="left"><?=$lg_msg?></td>
			</tr>
			<tr class="">
				<td><input type="checkbox" class="inscheck" value="GR"></td>
				<td align="center">MG</td>
				<td align="right"><?=number_format($gr_tot)?></td>
				<td align="right"><?=number_format($gr_man1)?></td>
				<td align="right"><?=number_format($gr_man2)?></td>
				<td align="right"><?=number_format($gr_mul)?></td>
				<td align="right"<?=number_format($gr_sin)?></td>
				<td align="right"><?=number_format($gr_mu)?></td>
				<td align="right"><?=number_format($gr_car)?></td>
				<td align="right"><?=number_format($gr_goout)?></td>									
				<td align="left"><?=$gr_text?></td>
				<td align="left"><?=$gr_txt?></td>
				<td align="left"><?=$gr_msg?></td>
			</tr>
			<tr class="">
				<td><input type="checkbox" class="inscheck" value="DH"></td>
				<td align="center">롯데</td>
				<td align="right"><?=number_format($dh_tot)?></td>
				<td align="right"><?=number_format($dh_man1)?></td>
				<td align="right"><?=number_format($dh_man2)?></td>
				<td align="right"><?=number_format($dh_mul)?></td>
				<td align="right"<?=number_format($dh_sin)?></td>
				<td align="right"><?=number_format($dh_mu)?></td>
				<td align="right"><?=number_format($dh_car)?></td>
				<td align="right"><?=number_format($dh_goout)?></td>									
				<td align="left"><?=$dh_text?></td>
				<td align="left"><?=$dh_txt?></td>
				<td align="left"><?=$dh_msg?></td>
			</tr>
			<tr class="">
				<td><input type="checkbox" class="inscheck" value="DY"></td>
				<td align="center">메리츠</td>
				<td align="right"><?=number_format($dy_tot)?></td>
				<td align="right"><?=number_format($dy_man1)?></td>
				<td align="right"><?=number_format($dy_man2)?></td>
				<td align="right"><?=number_format($dy_mul)?></td>
				<td align="right"<?=number_format($dy_sin)?></td>
				<td align="right"><?=number_format($dy_mu)?></td>
				<td align="right"><?=number_format($dy_car)?></td>
				<td align="right"><?=number_format($dy_goout)?></td>									
				<td align="left"><?=$dy_text?></td>
				<td align="left"><?=$dy_txt?></td>
				<td align="left"><?=$dy_msg?></td>
			</tr>
			<tr class="">
				<td><input type="checkbox" class="inscheck" value="SS"></td>
				<td align="center">삼성</td>
				<td align="right"><?=number_format($ss_tot)?></td>
				<td align="right"><?=number_format($ss_man1)?></td>
				<td align="right"><?=number_format($ss_man2)?></td>
				<td align="right"><?=number_format($ss_mul)?></td>
				<td align="right"<?=number_format($ss_sin)?></td>
				<td align="right"><?=number_format($ss_mu)?></td>
				<td align="right"><?=number_format($ss_car)?></td>
				<td align="right"><?=number_format($ss_goout)?></td>									
				<td align="left"><?=$ss_text?></td>
				<td align="left"><?=$ss_txt?></td>
				<td align="left"><?=$ss_msg?></td>
			</tr>
			<tr class="">
				<td><input type="checkbox" class="inscheck" value="SD"></td>
				<td align="center">한화</td>
				<td align="right"><?=number_format($sd_tot)?></td>
				<td align="right"><?=number_format($sd_man1)?></td>
				<td align="right"><?=number_format($sd_man2)?></td>
				<td align="right"><?=number_format($sd_mul)?></td>
				<td align="right"<?=number_format($sd_sin)?></td>
				<td align="right"><?=number_format($sd_mu)?></td>
				<td align="right"><?=number_format($sd_car)?></td>
				<td align="right"><?=number_format($sd_goout)?></td>									
				<td align="left"><?=$sd_text?></td>
				<td align="left"><?=$sd_txt?></td>
				<td align="left"><?=$sd_msg?></td>
			</tr>
			<tr class="">
				<td><input type="checkbox" class="inscheck" value="HD"></td>
				<td align="center">현대</td>
				<td align="right"><?=number_format($hd_tot)?></td>
				<td align="right"><?=number_format($hd_man1)?></td>
				<td align="right"><?=number_format($hd_man2)?></td>
				<td align="right"><?=number_format($hd_mul)?></td>
				<td align="right"<?=number_format($hd_sin)?></td>
				<td align="right"><?=number_format($hd_mu)?></td>
				<td align="right"><?=number_format($hd_car)?></td>
				<td align="right"><?=number_format($hd_goout)?></td>									
				<td align="left"><?=$hd_text?></td>
				<td align="left"><?=$hd_txt?></td>
				<td align="left"><?=$hd_msg?></td>
			</tr>
			<tr class="">
				<td><input type="checkbox" class="inscheck" value="SY"></td>
				<td align="center">흥국</td>
				<td align="right"><?=number_format($sy_tot)?></td>
				<td align="right"><?=number_format($sy_man1)?></td>
				<td align="right"><?=number_format($sy_man2)?></td>
				<td align="right"><?=number_format($sy_mul)?></td>
				<td align="right"<?=number_format($sy_sin)?></td>
				<td align="right"><?=number_format($sy_mu)?></td>
				<td align="right"><?=number_format($sy_car)?></td>
				<td align="right"><?=number_format($sy_goout)?></td>									
				<td align="left"><?=$sy_text?></td>
				<td align="left"><?=$sy_txt?></td>
				<td align="left"><?=$sy_msg?></td>
			</tr>
		</tbody>
	</table>
</div>


<script type="text/javascript">

// 체크박스 전체 선택/해제
function check_sel(){
	if($('input:checkbox[id="top_check"]').is(":checked") == true){
		$(".inscheck").prop('checked',true);
	}else{
		$(".inscheck").prop('checked',false);
	}
}


$(document).ready(function(){

	$("#top_check").prop('checked',true);
	$(".inscheck").prop('checked',true);

	// 조회 시 보험료결과 display 오픈
	var carseq = '<?=$carseq?>';

	if(carseq){
		$("#caramt").css("display","");
	}

});

</script>