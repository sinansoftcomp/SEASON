<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

$fdate	= str_replace('-','',$_GET['fdate']);
$md		= substr($fdate,4,4);


// ����ȸ��
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
					<span>��������(�¿���)</span>
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
							<th>����ȸ��</th>
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
								<select name="car_nm" id="car_nm" style="width:100%">				
								  <option value="">����ȸ�縦 �������ּ���</option>
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


// ����ȸ�� ���ý� ����Ʈ ������ȯ
$('#car_brand').on('change', function(){ 

	// �Һз� ���ý� ������ �������� �κж����� ����ó��
	$.post("/bin/sub/help/carsel_lv2.php",{optVal:this.value}, function(data) {
		
		//console.log(data);

		$('#car_nm').empty();		
		$('#car_nm').append('<option value="">����</option>');
		$('#car_nm').append(data);

		$('#caryear').empty();
		$('#caryear').append('<option value="">������ �������ּ���</option>');

	});				
});


// ���� ���ý� ���� ������ �ҷ�����
$('#car_nm').on('change', function(){ 
			
		$.post("/bin/sub/help/carsel_lv3.php",{optVal:this.value}, function(data) {

			$('#caryear').empty();
			$('#caryear').append(data);
		});				
});


// ���� ���ý� ���谳���� �����Ϳ� ���� ��������� �⺻����
$('#caryear').on('change', function(){ 

	var year= $("#caryear option:checked").text();
	var md	= '<?=$md?>';

	// ���谳������ �Ѿ���� ���
	if(md){
		var cardate = year+md;
		$('#cardate').val(cardate);
	}
				
});


// ������ �Է� �� �����ܰ� �̵�
function next_step(){

	var car_brand	= $("form[name='1th_carsel'] select[name='car_brand']").val();	
	var car_nm		= $("form[name='1th_carsel'] select[name='car_nm']").val();	
	var caryear		= $("form[name='1th_carsel'] select[name='caryear']").val();
	var caryeartxt	= $("#caryear option:checked").text();
	var cardate		= $("form[name='1th_carsel'] input[name='cardate']").val();

	if(!car_brand){
		alert('����ȸ�縦 �������ּ���.')
		document.getElementById('car_brand').focus();
		return;
	}else if(!car_nm){
		alert('������ �������ּ���.')
		document.getElementById('car_nm').focus();
		return;
	}else if(!caryear){
		alert('������ �������ּ���.')
		document.getElementById('caryear').focus();
		return;
	}else if(!cardate){
		alert('����������� �Է����ּ���.')
		document.getElementById('cardate').focus();
		return;
	}

	var left = Math.ceil((window.screen.width - 700)/2);
	var top = Math.ceil((window.screen.height - 500)/2);
	var popOpen	= window.open( "<?=$conf['homeDir']?>/sub/help/carsel_basic_2th.php?car_brand="+car_brand+"&car_nm="+car_nm+"&caryear="+caryear+"&caryeartxt="+caryeartxt+"&cardate="+cardate+"&fdate=<?=$fdate?>" ,"","width=700px,height=300px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
	popOpen.focus();
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