<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");


$sql= "
	select tel2+'-'+tel3 tel , name
	from company
	where scode = '".$_SESSION['S_SCODE']."'
		";
$qry	= sqlsrv_query( $mscon, $sql );
extract($fet= sqlsrv_fetch_array($qry));

$macro = "";
if(substr($_GET['sms_type'],0,8) == "sms_swon"){
	$macro = "&&�����&&";
}else{
	$macro = "&&����&&";
}

$where = Decrypt_where($_GET['where'],$secret_key,$secret_iv);

sqlsrv_free_stmt($qry);
sqlsrv_close($mscon);

?>
<style>
body{background-image: none;}
</style>

<div class="tit_wrap ipgopop" style="padding-top:10px">

	<div class="tit_wrap" style="margin-top:0px">
		<span class="btn_wrap" style="padding-right:20px">
				<a href="#" class="btn_s white" style="min-width:100px;" onclick="sms_update();">SMS����</a>
				<a href="#" class="btn_s white" style="min-width:100px;" onclick="sms_close();">�ݱ�</a>
		</span>
	</div>


	<form name="sms_form" class="ajaxForm_sms" method="post" action="sms_pop_action.php"  style="padding:0px 20px;">
		<div style="border-right:1px solid #D5D5D5; border-left:1px solid #D5D5D5; ">
			<div class="tb_type01 view">
				<input type="hidden" name="type" value="<?=$_GET['sms_type']?>">
				<input type="hidden" name="name" value="<?=$name?>">
				<input type="hidden" name="totalbyte" value="">
				<input type="hidden" name="sdate1" value="<?=str_replace('-','',$_GET['sdate1'])?>">
				<input type="hidden" name="sdate2" value="<?=str_replace('-','',$_GET['sdate2'])?>">
				<input type="hidden" name="where" value="<?=$where?>">
				<input type="hidden" name="tel" value="<?=str_replace('-','',$tel)?>">
				<table id="modal_table">
					<colgroup>
						<col width="20%">
						<col width="auto">

					</colgroup>
					<tbody>
						<tr>
							<th>ȸ���</th>
							<td><?=$name?></td>
						</tr>
						<tr>
							<th>�߽��ڹ�ȣ</th>
							<td><?=$tel?></td>
						</tr>
						<tr>
							<th>SMS ����</th>
							<td>
								<textarea name="bigo" id="bigo" value="<?=$bigo?>" style="width:100%;height:150px" onkeyup="fn_chk_byte(this);" maxlength="4000"></textarea>
								<div style="margin-top:10px;">
									<span id="messagebyte">0</span><span>/ 2000 Byte</span>
								</div>
							</td>
						</tr>
						<tr>
							<th>��ũ��</th>
							<td>
								<select name="macro" id="macro" style="width:30%;"> 
									<option value="">����</option> 
									<option value="<?=$macro?>"><?=$macro?></option> 
								</select>									
							</td>
						</tr>
						<?if($_GET['sms_type']=="sms_car_gun"){?>
						<tr>
							<th>�ڵ����񱳰����� ÷��</th>
							<td>
								<input type = "checkbox" name="carbigo" id="carbigo" checked / >	
							</td>
						</tr>
						<?}?>
					</tbody>
				</table>	
			</div>
		</div>
	<div align="right" style="margin-top:10px"><b style="color:#E0844F"><em class="impor">*</em>
		<?if($_GET['sms_type']=="sms_car_gun"){?>�ڵ����񱳰������� ÷���Ұ�� MMS�� �߼۵˴ϴ�.<?}else{?>SMS������ 90Byte �̻���ʹ� MMS�� ��ȯ�Ǿ� �߼۵˴ϴ�.<?}?></b></div>
	<div align="right"><b style="color:#E0844F"><em class="impor">*</em>�������� ����ó�� �����ϴ� �Ǹ� �߼۵˴ϴ�.</b></div>
	</form>

</div>

<script type="text/javascript">

var limitByte = 2000;
var totalByte = 0;
// byteüũ
function fn_chk_byte(obj){
    totalByte = 0;
    var message = $(obj).val();

    for(var i =0; i < message.length; i++) {
            var currentByte = message.charCodeAt(i);
            if(currentByte > 128){
            	totalByte += 2;
            }else {
            	totalByte++;
            }
    }

    $("#messagebyte").text(totalByte);
}

// ����
function sms_update(){
	var bigo = $("#bigo").val();
	var cnt = '<?=$_GET["cnt"]?>';
	$("form[name='sms_form'] input[name='totalbyte']").val(totalByte);

	var message = "";
	if(totalByte<90){
		message = "SMS�� "+cnt+"�� �߼��Ͻðڽ��ϱ�?";
	}else{
		message = "MMS�� "+cnt+"�� �߼��Ͻðڽ��ϱ�?";
	}

	if(isEmpty(bigo) == true){
		alert('SMS������ �Է����ּ���.');
	}else{
		if(confirm(message)){
			$("form[name='sms_form']").submit();
		}
	}
}

// �ݱ�
function sms_close(){	
	self.close();
}

$(document).ready(function(){
	
	$('#macro').on('change',function(){
		$("#bigo").val($("#bigo").val()+this.value);

		var message = this.value;

		for(var i =0; i < message.length; i++) {
				var currentByte = message.charCodeAt(i);
				if(currentByte > 128){
					totalByte += 2;
				}else {
					totalByte++;
				}
		}

		$("#messagebyte").text(totalByte);
	});

	var options = { 
		dataType:  'json',
		beforeSubmit:  showRequest_modal_sms,  // pre-submit callback 
		success:       processJson_modal_sms  // post-submit callback 
	}; 

	$('.ajaxForm_sms').ajaxForm(options);

});


// pre-submit callback 
function showRequest_modal_sms(formData, jqForm, options) { 
	var queryString = $.param(formData); 
	return true; 
} 
 
// post-submit callback 
function processJson_modal_sms(data) { 
	console.log(data);
	if(data.message){
		alert(data.message);
	}

	if(data.result==""){
		self.close();
	}
}

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>