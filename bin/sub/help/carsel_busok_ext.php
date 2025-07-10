<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");


$extdata	= $_GET['data'];

// 특별요율 리스트
$sql	= "
			select 
					code,
					bigo,
					seq,
					seq%2 seq2
			from carext
			order by seq" ;

$result	= sqlsrv_query( $mscon, $sql );
$listData = array();
while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
	$listData[]	= $row;
}

// 전체개수 가져오기
$sql	= "Select  Count(*) cnt
			from carext  ";

$qry =  sqlsrv_query($mscon, $sql);
$totalResult =  sqlsrv_fetch_array($qry); 

$totalCnt = (int)$totalResult['cnt'];

$cnt = 0;
// 밑에 tr개수 동적으로 주려고 홀짝체크
if(count($listData) % 2 == 1 ){
	$cnt = ceil(count($listData)/2);
}else{
	$cnt = count($listData)/2;
}


//echo $cnt;

?>
<style>
body{background-image: none;}
.box_wrap{margin-bottom:0px;padding:15px 20px;}
.pop_btn{height:24px; line-height:22px;}

.input_type{border:0px solid;height:20px;}

.tb_type01 td.noborder{padding:3px 0;}
</style>

<div class="container container_bk">
	<div class="content_wrap">

		<div class="menu_group_top" style="border-bottom:0px solid;">
			<div class="menu_group">
				<span>차량옵션(특별요율)</span>
			</div>
		</div>

		
		<div class="tb_type01 tb_fix" style="height:380px;">
			<table class="gridhover">
				<colgroup>
					<col width="10%">
					<col width="40%">
					<col width="10%">
					<col width="40%">
				</colgroup>

				<tbody>
					<?for($i=0;$i<$cnt;$i++){?>
						<tr class="rowData">
							<?for($j=($i*2)+1;$j<=($i+1)*2;$j++){?>
								<td>
									<input type="checkbox" id="<?=$listData[$j-1]['code']?>">
									<input type="hidden" name = "<?=$listData[$j-1]['code']?>" value="<?=$listData[$j-1]['code']?>">
								</td>
								<!--<td align="left"><?=$listData[$j-1]['bigo']?></td>-->
								<td class="noborder" align="left"><span class="input_type"><input type="text" class = "<?=$listData[$j-1]['code']?>" value="<?=$listData[$j-1]['bigo']?>"></span></td>
							<?}?>
						</tr>
					<?}?>
				</tbody>
			</table>
		</div>
		
	</div>


	<div class="btn_wrap_center">
		<a href="#" class="btn_s white" onclick="next_step();">적용</a>
		<a href="#" class="btn_s white" onclick="btn_close();">닫기</a>
	</div>

	<p class="mt10 font_red">
	* 유상운송 : 개인용 6인승이하 승용의 경우, 여객/화물 구분 필수<br>
	</p>
	<p>
	* TPMS : 타이어공기압경고장치<br>
	* UBI할인 : [현대] 안전운전할인특약 에 해당<br>
	</p>
	<p class="mt10 font_blue">
	** 차선이탈방지,차선유지지원 설명<br>
	</p>
	<p>
	- 둘중 하나만 선택해도 차선이탈방지 특약이 적용됨<br>
	- 차선이탈경고(LDWS) 기능만 있는 경우, 차선이탈방지 선택<br>
	- 차선유지지원(LKAS) 기능만 있는 경우, 차선유지지원 선택<br>
	- 두 기능 모두 있는 경우, 둘다 선택<br>
	- 롯데의 경우만, 차선이탈경고,차선유지지원 선택에 따라 요율 상이<br>
	- 롯데를 제외한 원수사는 어떤 항목을 선택해도 요율 동일<br>
	</p>
	<p class="mt10 font_blue">
	** 전방충돌방지,자동비상제동 설명<br>
	</p>
	<p>
	- 둘중 하나만 선택해도 전방충돌방지 특약이 적용됨<br>
	- 전방충돌경고(FCW) 기능만 있는 경우, 차선이탈방지 선택<br>
	- 자동비상제동(AEB) 기능만 있는 경우, 차선유지지원 선택<br>
	- 두 기능 모두 있는 경우, 둘다 선택<br>
	- 롯데의 경우만, 전방충돌경고,자동비상제동 선택에 따라 요율 상이<br>
	- 롯데를 제외한 원수사는 어떤 항목을 선택해도 요율 동일<br>
	</p>


</div>


<script type="text/javascript">

// 닫기
function btn_close(){	
	window.close();
}


// 적용
function next_step(){

	//alert(document.getElementById('ex1').innerText);
	
	var totcnt	= '<?=$totalCnt?>';
	var id		= '';	
	var chk		= '';
	var ext_data= "";
	var dash	= "";
	var ext_text= "";
	var comma	= "";

	for(var i=1; i<=totcnt; i++){
		id = 'ex'+i.toString();

		chk = $("#"+id).is(':checked');
		txt = $("."+id).val();

		if(chk == true){
			if(ext_data.length > 0) dash="-";
			ext_data = ext_data + dash + id;

			if(ext_text.length > 0) comma=",";
			ext_text = ext_text + comma + txt;
		}
	}

	opener.setCarBusok('A', ext_data, ext_text, '', 0);
	self.close();

	//console.log(ext_data);
	//console.log(ext_text);
}

$(document).ready(function(){

	window.resizeTo("600", "900");

	// 차량선택시&데이터저장 데이터 자동적용
	var data = "<?=$extdata?>";

	const arr_data = data.split('-');

	for(var i=0; i<arr_data.length; i++){
		$("#"+arr_data[i]).prop('checked',true);
	}

	

});

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>