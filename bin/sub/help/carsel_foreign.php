<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

$fdate	= str_replace('-','',$_GET['fdate']);
$md		= substr($fdate,4,4);


// 모델
$sql= "select car_brand code, car_brand name from cardtb group by car_brand order by car_brand ";

$qry= sqlsrv_query( $mscon, $sql );
$typeData	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
  $typeData[] = $fet;
}



sqlsrv_free_stmt($result);
sqlsrv_close($mscon);


?>

<style>
body{background-image: none;}

</style>

<div class="container container_bk">
	<div class="content_wrap">
		<fieldset>

			<div class="menu_group_top" style="border-bottom:0px solid;">
				<div class="menu_group">
					<span>차량선택(외제차)</span>
				</div>
			</div>

		<!-- //box_gray -->
			<div class="tb_type01 view">
				<form name="1th_carsel" class="" method="post">
					<input type="hidden" name="car_code" id="car_code" value=""> 	
					<input type="hidden" name="car_brand" id="car_brand2" value=""> 
					<input type="hidden" name="car_sub" id="car_sub2" value=""> 
					<input type="hidden" name="bae_gi" id="bae_gi" value=""> 
					<input type="hidden" name="amt" id="amt" value=""> 
					<input type="hidden" name="car_part" id="car_part" value=""> 

					<input type="hidden" name="hyoung_sik" id="hyoung_sik" value=""> 
					<input type="hidden" name="car_grade" id="car_grade" value=""> 
					<input type="hidden" name="sport" id="sport" value=""> 
					<input type="hidden" name="people_num" id="people_num" value=""> 
					<input type="hidden" name="fuel" id="fuel" value=""> 
					<input type="hidden" name="hi_repair" id="hi_repair" value=""> 

					<input type="hidden" name="fracc" id="fracc" value=""> 
					<input type="hidden" name="lineout" id="lineout" value=""> 
					<input type="hidden" name="connectcar" id="connectcar" value=""> 
					<table>
						<colgroup>
							<col width="18%">
							<col width="32%">
							<col width="18%">
							<col width="32%">
						</colgroup>
					<tbody>
						<tr>
							<th>모델</th>
							<td colspan=3>
								<select name="car_brand" id="car_brand" style="width:100%">				
									<option value="">선택</option>
									<?foreach($typeData as $key => $val){?>
									<option value="<?=$val['code']?>" <?if($_GET['code']==$val['code']) echo "selected"?>><?=$val['name']?></option>
									<?}?>
								</select>
							</td>
						</tr>
						<tr>
							<th>차명</th>
							<td colspan=3>
								<select name="car_sub" id="car_sub" style="width:100%">				
								  <option value="">모델 선택해주세요</option>
								</select>
							</td>
						</tr>
						<tr>
							<th>연식</th>
							<td colspan=3>
								<select name="caryear" id="caryear" style="width:100%">				
								  <option value="">차명을 선택해주세요</option>
								</select>
							</td>
						</tr>
						<tr>
							<th>차량등록일</th>
							<td colspan=3>
								<span class="input_type" style="width:100%"><input type="text" name="cardate" id="cardate" value="<?=$cardate?>" oninput="NumberOnInput(this)" placeholder="YYYYMMDD (8자리) 입력해주세요" maxlength=8></span> 
							</td>
						</tr>
					</tbody>
					</table>
				</form>
			</div>
			<!-- // tb_type01 -->

		</fieldset>
	</div>

	<p class="mt10 font_red">
	* 오토, ABS, 에어백, 도난방지장치는 차종에 따라 다를 수 있습니다.<br>
	* 연식이 오래된 차량가액은 보험사별로 상이할 수 있습니다.<br>
	* 차량등록일은 보험개시일과 선택한 차량연식 기준으로 자동세팅합니다.<br>
	* 자동세팅되는 날짜와 다른경우 수동으로 수정 바랍니다.<br>
	* 차량등록일 기준으로 전방충돌방지,차선이탈방지 정보를 자동 세팅합니다.<br>
	</p>


	<div class="btn_wrap_center">
		<a href="#" class="btn_s white" onclick="next_step();">적용</a>
		<a href="#" class="btn_s white" onclick="btn_close();">닫기</a>
	</div>

</div>

<!-- // popup_wrap -->

 </body>
</html>

<script type="text/javascript">


// 닫기
function btn_close(){	
	window.close();
}


// 모델 선택시 셀렉트 동적변환
$('#car_brand').on('change', function(){ 

	// 소분류 선택시 데이터 가져오는 부분때문에 동기처리
	//$.ajaxSetup({ async:false });	
	$.post("/bin/sub/help/carsel_foreign_lv2.php",{optVal:this.value}, function(data) {

		$('#car_sub').empty();		
		//$('#car_sub').append('<option value="">선택</option>');
		$('#car_sub').append(data);

		$('#caryear').empty();
		$('#caryear').append('<option value="">차명을 선택해주세요</option>');

	});				
});


// 차명 선택시 연식 데이터 불러오기
$('#car_sub').on('change', function(){ 

			
		$.post("/bin/sub/help/carsel_foreign_lv3.php",{optVal:this.value}, function(data) {

			$('#caryear').empty();		
			$('#caryear').append('<option value="">선택</option>');
			$('#caryear').append(data);
			
		});	
		
});


// 연식 선택시 보험개시일 데이터에 따라 차량등록일 기본세팅 & 차량데이터 가져오기
$('#caryear').on('change', function(){ 

	var year	= $("#caryear option:checked").text();
	var carsub	= $("form[name='1th_carsel'] select[name='car_sub']").val();	
	var md		= '<?=$md?>';

	$.ajaxSetup({ cache : false, dataType : "json", data: { ajaxType : true}, contentType:'application/x-www-form-urlencoded; charset=euc-kr'});	
	$.post("/bin/sub/help/carsel_foreign_cardata.php",{optVal:this.value, carsub:carsub}, function(data) {

		console.log(data);

		$('#car_code').val(data.car_code);
		$('#car_brand2').val(data.car_brand);
		$('#car_sub2').val(data.car_sub);
		$('#bae_gi').val(data.bae_gi);
		$('#amt').val(data.amt);
		$('#car_part').val(data.car_part);

		$('#hyoung_sik').val(data.hyoung_sik);
		$('#car_grade').val(data.car_grade);
		$('#sport').val(data.sport);
		$('#people_num').val(data.people_num);
		$('#fuel').val(data.fuel);
		$('#hi_repair').val(data.hi_repair);

		$('#fracc').val(data.fracc);
		$('#lineout').val(data.lineout);
		$('#connectcar').val(data.connectcar);
		
	});	

	// 보험개시일이 넘어왔을 경우
	if(md){
		var cardate = year+md;
		$('#cardate').val(cardate);
	}
				
});


// 데이터 적용 후 팝업닫기
function next_step(){


	var car_code	= $("form[name='1th_carsel'] input[name='car_code']").val();
	var car_grade	= $("form[name='1th_carsel'] input[name='car_grade']").val();
	var bae_gi		= $("form[name='1th_carsel'] input[name='bae_gi']").val();
	var caryear		= $("form[name='1th_carsel'] select[name='caryear']").val();	
	var cardate		= $("form[name='1th_carsel'] input[name='cardate']").val();
	var caryeartxt	= $("#caryear option:checked").text();

	var hyoung_sik	= $("form[name='1th_carsel'] input[name='hyoung_sik']").val();
	var car_sub		= $("form[name='1th_carsel'] input[name='car_sub']").val();
	var amt			= $("form[name='1th_carsel'] input[name='amt']").val();
	var fuel		= $("form[name='1th_carsel'] input[name='fuel']").val();
	var hi_repair	= $("form[name='1th_carsel'] input[name='hi_repair']").val();
	var sport		= $("form[name='1th_carsel'] input[name='sport']").val();

	var car_part	= $("form[name='1th_carsel'] input[name='car_part']").val();
	var fraccyn		= $("form[name='1th_carsel'] input[name='fracc']").val();
	var lineoutyn	= $("form[name='1th_carsel'] input[name='lineout']").val();
	var connectcaryn= $("form[name='1th_carsel'] input[name='connectcar']").val();
	var people_num	= $("form[name='1th_carsel'] input[name='people_num']").val();

	if(fraccyn == 'Y'){
		car_part = car_part + ',전방충돌방지';
	}

	if(lineoutyn == 'Y'){
		car_part = car_part + ',차선이탈방지';
	}

	if(connectcaryn == 'Y'){
		car_part = car_part + ',커넥티드카';
	}
	
	opener.setCarValue('C', car_code, car_grade, bae_gi, caryeartxt, cardate, hyoung_sik, car_sub, amt, fuel, hi_repair, car_part, people_num, sport);
	self.close();
	
}


//  숫자만 입력가능
function NumberOnInput(e)  {
  e.value = e.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1')
}

	
$(document).ready(function(){
	
	// 팝업크기 resize
	window.resizeTo("700", "400"); 
});


</script>



 

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>