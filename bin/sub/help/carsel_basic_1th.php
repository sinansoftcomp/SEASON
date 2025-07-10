<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

$fdate	= str_replace('-','',$_GET['fdate']);
$md		= substr($fdate,4,4);


// 제조회사
$sql= "select car_brand code, car_brand name from cardta group by car_brand order by car_brand ";

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
					<span>차량선택(승용차)</span>
				</div>
			</div>

		<!-- //box_gray -->
			<div class="tb_type01 view">
				<form name="1th_carsel" class="" method="post">
					<table>
						<colgroup>
							<col width="18%">
							<col width="32%">
							<col width="18%">
							<col width="32%">
						</colgroup>
					<tbody>
						<tr>
							<th>제조회사</th>
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
								<select name="car_nm" id="car_nm" style="width:100%">				
								  <option value="">제조회사를 선택해주세요</option>
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


// 제조회사 선택시 셀렉트 동적변환
$('#car_brand').on('change', function(){ 

	// 소분류 선택시 데이터 가져오는 부분때문에 동기처리
	$.post("/bin/sub/help/carsel_lv2.php",{optVal:this.value}, function(data) {
		
		//console.log(data);

		$('#car_nm').empty();		
		$('#car_nm').append('<option value="">선택</option>');
		$('#car_nm').append(data);

		$('#caryear').empty();
		$('#caryear').append('<option value="">차명을 선택해주세요</option>');

	});				
});


// 차명 선택시 연식 데이터 불러오기
$('#car_nm').on('change', function(){ 
			
		$.post("/bin/sub/help/carsel_lv3.php",{optVal:this.value}, function(data) {

			$('#caryear').empty();
			$('#caryear').append(data);
		});				
});


// 연식 선택시 보험개시일 데이터에 따라 차량등록일 기본세팅
$('#caryear').on('change', function(){ 

	var year= $("#caryear option:checked").text();
	var md	= '<?=$md?>';

	// 보험개시일이 넘어왔을 경우
	if(md){
		var cardate = year+md;
		$('#cardate').val(cardate);
	}
				
});


// 데이터 입력 후 다음단계 이동
function next_step(){

	var car_brand	= $("form[name='1th_carsel'] select[name='car_brand']").val();	
	var car_nm		= $("form[name='1th_carsel'] select[name='car_nm']").val();	
	var caryear		= $("form[name='1th_carsel'] select[name='caryear']").val();
	var caryeartxt	= $("#caryear option:checked").text();
	var cardate		= $("form[name='1th_carsel'] input[name='cardate']").val();

	if(!car_brand){
		alert('제조회사를 선택해주세요.')
		document.getElementById('car_brand').focus();
		return;
	}else if(!car_nm){
		alert('차명을 선택해주세요.')
		document.getElementById('car_nm').focus();
		return;
	}else if(!caryear){
		alert('연식을 선택해주세요.')
		document.getElementById('caryear').focus();
		return;
	}else if(!cardate){
		alert('차량등록일을 입력해주세요.')
		document.getElementById('cardate').focus();
		return;
	}

	var left = Math.ceil((window.screen.width - 700)/2);
	var top = Math.ceil((window.screen.height - 500)/2);
	var popOpen	= window.open( "<?=$conf['homeDir']?>/sub/help/carsel_basic_2th.php?car_brand="+car_brand+"&car_nm="+car_nm+"&caryear="+caryear+"&caryeartxt="+caryeartxt+"&cardate="+cardate+"&fdate=<?=$fdate?>" ,"","width=700px,height=300px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
	popOpen.focus();
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