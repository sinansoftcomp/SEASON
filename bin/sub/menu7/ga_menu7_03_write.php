<?
//error_reporting(E_ALL); ini_set('display_errors', 1);
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

$where="";
if(isset($_GET['seq'])){
	$type="up";
	$sql	= "	
				select scode,seq,gubun,title,bigo,jocnt,topsort,isnull(filename,'') filename,filepath,convert(varchar(8),idate,112) idate,iswon,udate,uswon
				from GONGJI
				where scode = '".$_SESSION['S_SCODE']."' and seq = ".$_GET['seq']." ";
	$qry	= sqlsrv_query( $mscon, $sql );
	//extract($fet= sqlsrv_fetch_array($qry));
	if ($qry === false) {
		die(print_r(sqlsrv_errors(), true)); 
	}
	$fet = sqlsrv_fetch_array($qry, SQLSRV_FETCH_ASSOC);
	if ($fet !== false) {
		extract($fet);
	}

	sqlsrv_free_stmt($qry);
	sqlsrv_close($mscon);

}else{
	$type="in";
}

?>
<style>
body{background-image: none;}
</style>


<div class="tit_wrap ipgopop" style="padding-top:10px">
	<div class="tit_wrap" style="margin-top:0px">
		<h3 class="tit_sub" style="margin-left:20px">�������� �۾���</h3>
	</div>


	<form name="gongji_form" class="ajaxForm_gongji" method="post" action="ga_menu7_03_action.php" style="padding:0px 20px;">
		<div style="border-right:1px solid #D5D5D5; border-left:1px solid #D5D5D5; ">
			<div class="tb_type01 view">
				<input type="hidden" name="type" value="<?=$type?>">
				<input type="hidden" name="seq" value="<?=$_GET['seq']?>">
				<table id="modal_table">
					<colgroup>
						<col width="20%">
						<col width="auto">

					</colgroup>
					<tbody>
						<tr>
							<th><em class="impor">*</em>����</th>
							<td style="height:30px">
							<select name="gubun" id="gubun"style="width:150px;"> 
								<option value="">����</option>
								<?foreach($conf['gongji_gubun'] as $key => $val){?>
								<option value="<?=$key?>" <?if($gubun==$key) echo "selected"?>><?=$val?></option>
								<?}?>
							</select>										
							</td>
						</tr>
						<tr>
							<th><em class="impor">*</em>����</th>
							<td><span class="input_type" style="width:100%" id="skey_input"><input type="text" name="title" id="title" value="<?=$title?>"></span></td>
						</tr>
						<tr>
							<th><em class="impor">*</em>����</th>
							<td><textarea name="bigo" id="bigo" style="width:100%;height:200px"><?=$bigo?></textarea></td>
						</tr>

						<tr class="filetr">
							<th>÷������</th>
							<td>
							
							<?if($filename<>''){?><span id='filename'><?=$filename?><a class="delete" onclick="deleteFile();" style="margin-left:6px"><i class="far fa-minus-square font_red"></i></a></span><?}?>
							
							<input type="file"
									 id="file1" name="file1"
									 accept="image/png, image/jpeg,application/pdf, image/gif, image/jpeg, image/png, image/bmp, image/tif, application/haansofthwp, application/x-hwp,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,.doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" style="display:none"></input>
							</td>
						</tr>

						<tr>
							<th>�ֻ������</th>
							<td>
								<input type = "checkbox" name="topsort" id="topsort" <?if($topsort=="Y"){?>checked<?}?> / >	
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</form>
	<div class="tit_wrap" style="margin-top:10px">
		<span class="btn_wrap" style="padding-right:20px">
			<a href="#" class="btn_s white" style="min-width:100px;" onclick="gongji_update();">����</a>
			<?if($type=='up'){?><a href="#" class="btn_s white" style="min-width:100px;" onclick="gongji_delete();">����</a><?}?>
			<a href="#" class="btn_s white" style="min-width:100px;" onclick="modal_close();">�ݱ�</a>
		</span>
	</div>
</div>

 </body>
</html>

<script type="text/javascript">

// ����
function gongji_update(){

	var gubun = $('#gubun').val();
	var title   = $("form[name='gongji_form'] input[name='title']").val();
	var bigo   = $('#bigo').val();
	
	

	if(isEmpty(gubun) == true){
		alert('�������� ������ �������ּ���.');
	}else if(isEmpty(title) == true){
		alert('������ �Է����ּ���.');
		document.getElementById('item').focus();
	}else if(isEmpty(bigo) == true){
		alert('������ �Է����ּ���.');
		document.getElementById('name').focus();
	}else{
		if(confirm("�����Ͻðڽ��ϱ�?")){
			$("form[name='gongji_form']").submit();
		}
	}

}

// ����
function gongji_delete(){
	var type   = $("form[name='gongji_form'] input[name='type']").val();

	if(type == "up"){
		if(confirm("�����Ͻðڽ��ϱ�?")){
			document.gongji_form.type.value='del';
			$("form[name='gongji_form']").submit();
			
		}
	}else{
		alert("������ ����� �����ϴ�.");
	}
}

// �ݱ�
function modal_close(){
	self.close();
}


/* ÷������ ���� */
function deleteFile() {
    document.querySelector("#filename").remove();
	$("#file1").css("display","block");
}

// ajax ȣ��
var btnAction	= true;
$(document).ready(function(){

	var type='<?=$type?>';
	var filename = '<?=$filename?>';
	if(type=='in' || filename == ''){
		$("#file1").css("display","block");
	}

	var options = { 
		dataType:  'json',
		beforeSubmit:  showRequest_modal_gongji,  // pre-submit callback 
		success:       processJson_modal_gongji  // post-submit callback 
	}; 

	$('.ajaxForm_gongji').ajaxForm(options);
});

// pre-submit callback 
function showRequest_modal_gongji(formData, jqForm, options) { 
	var queryString = $.param(formData); 
	return true; 
} 

// post-submit callback 
function processJson_modal_gongji(data) { 
	console.log(data);
	if(data.message){
		alert(data.message);
	}

	if(data.result==""){
		opener.$(".btn_search").trigger("click");
		if(data.rtype == 'in' || data.rtype == 'up'){
			location.href='ga_menu7_03_read.php?seq='+data.seq;
		}else{
			self.close();
		}
	}

}

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>