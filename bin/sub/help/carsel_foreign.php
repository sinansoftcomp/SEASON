<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

$fdate	= str_replace('-','',$_GET['fdate']);
$md		= substr($fdate,4,4);


// ��
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
					<span>��������(������)</span>
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
							<th>��</th>
							<td colspan=3>
								<select name="car_brand" id="car_brand" style="width:100%">				
									<option value="">����</option>
									<?foreach($typeData as $key => $val){?>
									<option value="<?=$val['code']?>" <?if($_GET['code']==$val['code']) echo "selected"?>><?=$val['name']?></option>
									<?}?>
								</select>
							</td>
						</tr>
						<tr>
							<th>����</th>
							<td colspan=3>
								<select name="car_sub" id="car_sub" style="width:100%">				
								  <option value="">�� �������ּ���</option>
								</select>
							</td>
						</tr>
						<tr>
							<th>����</th>
							<td colspan=3>
								<select name="caryear" id="caryear" style="width:100%">				
								  <option value="">������ �������ּ���</option>
								</select>
							</td>
						</tr>
						<tr>
							<th>���������</th>
							<td colspan=3>
								<span class="input_type" style="width:100%"><input type="text" name="cardate" id="cardate" value="<?=$cardate?>" oninput="NumberOnInput(this)" placeholder="YYYYMMDD (8�ڸ�) �Է����ּ���" maxlength=8></span> 
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
	* ����, ABS, �����, ����������ġ�� ������ ���� �ٸ� �� �ֽ��ϴ�.<br>
	* ������ ������ ���������� ����纰�� ������ �� �ֽ��ϴ�.<br>
	* ����������� ���谳���ϰ� ������ �������� �������� �ڵ������մϴ�.<br>
	* �ڵ����õǴ� ��¥�� �ٸ���� �������� ���� �ٶ��ϴ�.<br>
	* ��������� �������� �����浹����,������Ż���� ������ �ڵ� �����մϴ�.<br>
	</p>


	<div class="btn_wrap_center">
		<a href="#" class="btn_s white" onclick="next_step();">����</a>
		<a href="#" class="btn_s white" onclick="btn_close();">�ݱ�</a>
	</div>

</div>

<!-- // popup_wrap -->

 </body>
</html>

<script type="text/javascript">


// �ݱ�
function btn_close(){	
	window.close();
}


// �� ���ý� ����Ʈ ������ȯ
$('#car_brand').on('change', function(){ 

	// �Һз� ���ý� ������ �������� �κж����� ����ó��
	//$.ajaxSetup({ async:false });	
	$.post("/bin/sub/help/carsel_foreign_lv2.php",{optVal:this.value}, function(data) {

		$('#car_sub').empty();		
		//$('#car_sub').append('<option value="">����</option>');
		$('#car_sub').append(data);

		$('#caryear').empty();
		$('#caryear').append('<option value="">������ �������ּ���</option>');

	});				
});


// ���� ���ý� ���� ������ �ҷ�����
$('#car_sub').on('change', function(){ 

			
		$.post("/bin/sub/help/carsel_foreign_lv3.php",{optVal:this.value}, function(data) {

			$('#caryear').empty();		
			$('#caryear').append('<option value="">����</option>');
			$('#caryear').append(data);
			
		});	
		
});


// ���� ���ý� ���谳���� �����Ϳ� ���� ��������� �⺻���� & ���������� ��������
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

	// ���谳������ �Ѿ���� ���
	if(md){
		var cardate = year+md;
		$('#cardate').val(cardate);
	}
				
});


// ������ ���� �� �˾��ݱ�
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
		car_part = car_part + ',�����浹����';
	}

	if(lineoutyn == 'Y'){
		car_part = car_part + ',������Ż����';
	}

	if(connectcaryn == 'Y'){
		car_part = car_part + ',Ŀ��Ƽ��ī';
	}
	
	opener.setCarValue('C', car_code, car_grade, bae_gi, caryeartxt, cardate, hyoung_sik, car_sub, amt, fuel, hi_repair, car_part, people_num, sport);
	self.close();
	
}


//  ���ڸ� �Է°���
function NumberOnInput(e)  {
  e.value = e.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1')
}

	
$(document).ready(function(){
	
	// �˾�ũ�� resize
	window.resizeTo("700", "400"); 
});


</script>



 

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>