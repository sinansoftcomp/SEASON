<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

/*
	������ ��Ʈ ���� �̼���
	A:������
*/
$sbit = $_SESSION['S_MASTER'];

$type	= ($_GET['seq']) ? "up" : "in";


if($_GET['seq']){
	$sql= "
			select  a.seq,
					a.sdate,
					a.title,
					a.bigo,
					a.gubun,
					case when a.gubun = '1' then '��ü' else '����' end gubun_nm,
					a.status,
					case when a.status = '1' then '������' else '�Ϸ�' end status_nm,
					case when a.gubun = '1' and status = '1' then '#F15F5F' 
						 when a.gubun = '1' and status = '2' then '#d5d5d5' 
						 when a.gubun = '2' and status = '1' then '#6799FF' 
						 when a.gubun = '2' and status = '2' then '#d5d5d5' 
						 else '#6799FF' end color,
					convert(varchar(30),a.idate,120) as idate
			from schd(nolock) a
			where a.scode = '".$_SESSION['S_SCODE']."'
			  and a.seq = '".$_GET['seq']."' ";

	$qry	= sqlsrv_query( $mscon, $sql );
	extract($fet= sqlsrv_fetch_array($qry));

	$update		=	$idate;
}else{
	$update		=	date("Y-m-d H:i:s");
}



sqlsrv_free_stmt($result);
sqlsrv_close($mscon);

?>

<style>
body{background-image: none;}

.tb_type01.view .lowpop th, .tb_type01.view .lowpop td {
    padding:7px 12px;
    text-align: left;
}

</style>

<div class="container container_bk">
	<div class="content_wrap">
		<fieldset>

			<div class="tit_wrap mt20">
				<span class="btn_wrap">
					<a href="#" class="btn_s white" onclick="sch_insert();" style="width:70px">�ű�</a>
					<a href="#" class="btn_s white"  onclick="sch_update();" style="width:70px">����</a>
					<a href="#" class="btn_s white" onclick="sch_delete();" style="width:70px">����</a>
					<a href="#" class="btn_s white" onclick="btn_close();" style="width:70px">�ݱ�</a>
				</span>
			</div>

			<!-- //box_gray -->
				<div class="tb_type01 view">
					<form name="schd_form" class="ajaxForm_schd" method="post" action="ga_menu7_04_pop_action.php">
					<input type="hidden" name="type" value="<?=$type?>">
					<input type="hidden" name="skey"  value="<?=$_SESSION['S_SKEY']?>">
					<input type="hidden" name="seq"  value="<?=$_GET['seq']?>">
						<table class="lowpop">
							<colgroup>
								<col width="18%">
								<col width="32%">
								<col width="18%">
								<col width="32%">
							</colgroup>
						<tbody>
							<tr>
								<th>����</th>
								<td>
									<input type="radio" class="gubun updis" name="gubun" id="gubun1" value="1" <?if(trim($gubun)=='1') echo "checked";?>><label for="gubun1">��ü </label>&nbsp;&nbsp;&nbsp;
									<input type="radio" class="gubun updis" name="gubun" id="gubun2" value="2" <?if(trim($gubun)=='2') echo "checked";?>><label for="gubun2">����</label>
								</td>
								<th><em class="impor">*</em>�������</th>
								<td>
									<select name="status" id="status"style="width:150px;"> 
										<?foreach($conf['sch_status'] as $key => $val){?>
										<option value="<?=$key?>" <?if($status==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>	
								</td>
							</tr>
							<tr>
								<th><em class="impor">*</em>����</th>
								<td colspan=3>
									<span class="input_type date" style="width:100%"><input type="text" class="Calnew" name="sdate" id="sdate" value="<?if($sdate) echo date("Y-m-d",strtotime($sdate));?>" readonly></span> 
								</td>
							</tr>
							<tr>
								<th><em class="impor">*</em>����</th>
								<td colspan=3>
									<span class="input_type" style="width:100%"><input type="text" name="title" id="title" value="<?=$title?>"></span> 
								</td>
							</tr>
							<tr>
								<th>����</th>
								<td colspan=3>
									<span class="input_type" style="width:100%"><input type="text" name="bigo" value="<?=$bigo?>"></span> 
								</td>
							</tr>
						</tbody>
						</table>
					</form>
				</div>
				<!-- // tb_type01 -->

				<div class="tit_wrap" style="margin-top:5px">		
					<span class="btn_wrap">
						<span style="margin-left:15px" class="font_blue">����Ͻ� : <?=$update?></span>				
					</span>
				</div>

		</fieldset>

		<p class="mgt5 font_red font600">* ������ ������ ���� ����� ��ü ������ ���/���� �� �� �ֽ��ϴ�.</p>
	</div>
</div>
<!-- // popup_wrap -->

 </body>
</html>

<script type="text/javascript">


// �ű�
function sch_insert(){

	var today = new Date();   

	var year = today.getFullYear();
	var month = ('0' + (today.getMonth() + 1)).slice(-2);
	var day = ('0' + today.getDate()).slice(-2);

	var dateString = year + '-' + month  + '-' + day;
	
	document.schd_form.type.value='in';
	document.schd_form.seq.value='';
	document.schd_form.sdate.value=dateString;
	document.schd_form.title.value='';
	document.schd_form.bigo.value='';
	document.schd_form.status.value='1';

	data_write();
}

// ������ ���� ����
function data_write(){

	var type = '<?=$type?>';
	var sbit = '<?=$sbit?>';


	if(type == 'in'){
		if(sbit != 'A'){
			$('.updis').attr('disabled', true);
			$(":radio[name$='gubun']").val([2]);
		}else{
			$(":radio[name$='gubun']").val([1]);
		}
	}else{
		if(sbit != 'A'){
			$('.updis').attr('disabled', true);
		}		
	}	
}

// ����
function sch_update(){	

	var title	= $("form[name='schd_form'] input[name='title']").val();

	if(isEmpty(title) == true){
		alert('������ �Է��� �ּ���.');
		document.getElementById('title').focus();
	}else{
		if(confirm("�����Ͻðڽ��ϱ�?")){
			$('.updis').attr('disabled', false);
			$("form[name='schd_form']").submit();
		}
	}
}

// ����
function sch_delete(){
	var type   = $("form[name='schd_form'] input[name='type']").val();

	if(type == "up"){
		if(confirm("�����Ͻðڽ��ϱ�?")){
			document.atongha_form.type.value='del';
			$("form[name='schd_form']").submit();
		}
	}else{
		alert("������ ����� �����ϴ�.");
	}
}

// �ݱ�
function btn_close(){	
	window.close();
	//opener.reset();
}



// ajax ȣ��
var btnAction	= true;
	
$(document).ready(function(){

	// �����ͱ��� �����Ұ�
	var type = '<?=$type?>';
	if(type == 'in'){
		sch_insert();
	}else{
		data_write();
	}


	var options = { 
		dataType:  'json',
		beforeSubmit:  showRequest_modal_sch,  // pre-submit callback 
		success:       processJson_modal_sch  // post-submit callback 
	}; 

	$('.ajaxForm_schd').ajaxForm(options);
});

// pre-submit callback 
function showRequest_modal_sch(formData, jqForm, options) { 
	var queryString = $.param(formData); 
	return true; 
} 
 
// post-submit callback 
function processJson_modal_sch(data) { 
	console.log(data);
	if(data.message){
		alert(data.message);
		//location.reload();
	}

	if(data.result==''){
		// ������
		if(data.rtype == 'in'){
			document.schd_form.type.value = 'up';
			document.schd_form.skey.value = data.skey;
			document.schd_form.seq.value = data.seq;
		}else if(data.rtype == 'del'){
			document.schd_form.type.value = 'in';
			sch_insert();			
		}

		data_write();
		opener.reset();	// ���� �� ������ ȭ�� ���ΰ�ħ ����
	}

}

</script>



 

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>