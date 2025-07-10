<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

$kcode	= $_GET['kcode'];
$gubun	= $_GET['gubun'];
$seq	= $_GET['seq'];

$type	= ($_GET['seq']) ? "up" : "in";


if($_GET['seq']){
	$sql	= "
				select 
						a.kcode,
						a.seq,
						a.gubun,
						a.hdate,
						a.hyymm,
						a.hyul,
						a.hamt,
						b.kdate,
						b.inscode,
						b.itemnm,
						a.hbit,
						b.kstbit,
						a.hbigo,
						case when isnull(a.uswon,'') = '' then c.sname else d.sname end tsname,
						case when isnull(a.udate,'') = '' then convert(varchar(30),a.idate,120) else convert(varchar(30),a.udate,120) end tdate,
						row_number()over(order by a.seq desc) rnum
				from hymst a
					left outer join kwn b on a.scode = b.scode and a.kcode = b.kcode 
					left outer join swon c on a.scode = c.scode and a.iswon = c.skey
					left outer join swon d on a.scode = d.scode and a.uswon = d.skey
					left outer join inssetup e on a.scode = e.scode and b.inscode = e.inscode
				where a.scode	= '".$_SESSION['S_SCODE']."' 
				  and a.kcode	='".$_GET['kcode']."' 
				  and a.seq = '".$_GET['seq']."' ";

	$qry	= sqlsrv_query( $mscon, $sql );
	extract($fet= sqlsrv_fetch_array($qry));

	$update		=	$udate;
	$upswon		=	$uswonnm;
	$upswon_txt	=	'�����������';
	$update_txt	=	'���������Ͻ�';
}else{
	$update		=	date("Y-m-d H:i:s");
	$upswon		=	$_SESSION['S_SNAME'];
	$upswon_txt	=	'��ϻ��';
	$update_txt	=	'����Ͻ�';
}

if($gubun == '1'){
	$title_txt	= '�ؾ�/����';
}else if($gubun == '2'){
	$title_txt	= 'û��öȸ';
}else if($gubun == '3'){
	$title_txt	= '�ο�����';
}else if($gubun == '4'){
	$title_txt	= 'ǰ����������';
}else if($gubun == '5'){
	$title_txt	= '�����������';
}else if($gubun == '6'){
	$title_txt	= '�ݼ�����';
}else if($gubun == '7'){
	$title_txt	= '�������';
}else if($gubun == '20'){
	$title_txt	= '��Ȱ';
}else{
	$title_txt	= 'ó��';
}

sqlsrv_free_stmt($result);
sqlsrv_close($mscon);

?>

<div class="popup_wrap" style="display:block"><!-- popup ���½� html,body�� overflow:hidden --> 
	  <div class="popup_con" style="width:550px">
	  		<h1 class="pop_tit"><?=$title_txt?></h1>
			<a href="#" class="btn_close" onclick="modal_close();"><span class="blind">�ݱ�</span></a>
			<div class="popup_con_in">
			<div class="tit_wrap" style="margin-top:0px">
				<h2 class="tit_sub"><?=$txtname?></h2>
				<span class="btn_wrap">
					<a href="#" class="btn_s white" onclick="at_insert();" style="width:55px">�ű�</a>
					<a href="#" class="btn_s navy"  onclick="at_update();" style="width:55px">����</a>
					<a href="#" class="btn_s white" onclick="at_delete();" style="width:55px">����</a>
					<a href="#" class="btn_s white" onclick="modal_close();" style="width:55px">�ݱ�</a>
				</span>
			</div>

			<!-- //box_gray -->
				<div class="tb_type01 view">
					<form name="hymst_form" class="ajaxForm_hymst" method="post" action="/bin/sub/menu3/ga_menu3_01_tab3_action.php">
					<input type="hidden" name="type" value="<?=$type?>">
					<input type="hidden" name="kcode"  value="<?=$kcode?>">
					<input type="hidden" name="num"  value="<?=$num?>">
						<table>
							<colgroup>
								<col width="18%">
								<col width="32%">
							</colgroup>
						<tbody>
							<tr>
								<th><em class="impor">*</em>����</th>
								<td>
									<select name="gubun" id="gubun" style="width:100%"> 
										<option value="">����</option>
										<?foreach($conf['hymst_gubun'] as $key => $val){?>
										<option value="<?=$key?>" <?if($gubun==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>	
								</td>
							</tr>
							<tr>
								<th><em class="impor">*</em><?=$title_txt?>����</th>
								<td>
									<span class="input_type date" style="width:100%"><input type="text" class="Calnew" name="hdate" id="hdate" value="<?if($hdate) echo date("Y-m-d",strtotime($hdate)); elseif(!$hdate) echo '';?>" readonly></span> 
								</td>								
							</tr>
							<tr>
								<th><em class="impor">*</em>ȯ�������</th>
								<td>
									<span class="input_type date" style="width:100%"><input type="text" class="Cal_ym" name="hyymm" id="hyymm" value="<?if($hyymm) echo date("Y-m",strtotime($hyymm)); elseif(!$hyymm) echo '';?>" readonly></span> 
								</td>								
							</tr>
							<tr>
								<th>ȯ������</th>
								<td>
									<span class="input_type_number" style="width:90%"><input type="text" name="hyul" class="numberInput yb_right" value="<?=number_format($hyul)?>" style="padding-left:0px" ></span> 
									<span style="margin-left:5px;display: inline-block;"> %</span> 

								</td>								
							</tr>
							<tr>
								<th>ȯ���ݾ�</th>
								<td>
									<span class="input_type_number" style="width:90%"><input type="text" name="hamt" class="numberInput yb_right" value="<?=number_format($hamt)?>" style="padding-left:0px" ></span>
									<span style="margin-left:5px;display: inline-block;"> ��</span>	

								</td>								
							</tr>
							<tr>
								<th>ó������</th>
								<td><textarea type="text" name="hbigo" id="hbigo" style="width:100%;height:80px;border: 1px solid #b7b7b7;"><?=$hbigo?></textarea></td>
							</tr>
						</tbody>
						</table>
					</form>
				</div>
				<!-- // tb_type01 -->

				<div class="tit_wrap" style="margin-top:5px">		
					<span class="btn_wrap">
						<span style="margin-left:15px" class="font_blue"><?=$upswon_txt?> : <?=$upswon?></span>
						<span style="margin-left:15px" class="font_blue"><?=$update_txt?> : <?=$update?></span>				
					</span>
				</div>


			</div>
	  </div>
</div>
<!-- // popup_wrap -->

 </body>
</html>

<script type="text/javascript">


// �ű�
function at_insert(){

	var today = new Date();   

	var year = today.getFullYear();
	var month = ('0' + (today.getMonth() + 1)).slice(-2);
	var day = ('0' + today.getDate()).slice(-2);
	var hours = ('0' + today.getHours()).slice(-2); 
	var minutes = ('0' + today.getMinutes()).slice(-2);

	var dateString = year + '-' + month  + '-' + day;
	var timeString = hours + ':' + minutes;

	var skey	= '<?=$_SESSION['S_SKEY']?>'
	var sname	= '<?=$_SESSION['S_SNAME']?>'
	var bit		= '<?=$bit?>'
	
	document.atongha_form.type.value='in';
	document.atongha_form.bit.value=bit;
	document.atongha_form.gubun.value='';
	document.atongha_form.skey.value=skey;
	$('.sname').text(sname);
	document.atongha_form.tondat.value=dateString;
	document.atongha_form.tontim.value=timeString;
	document.atongha_form.tontxt.value='';
}

// ����
function at_update(){	

	var tondat	= $("form[name='atongha_form'] input[name='tondat']").val();
	var skey	= $("form[name='atongha_form'] input[name='skey']").val();
	var tontxt	= $("form[name='atongha_form'] input[name='tontxt']").val();

	if(!$('#gubun > option:selected').val()) {
		alert('��㱸���� ������ �ּ���.');
		document.getElementById('gubun').focus();
	}else if(isEmpty(skey) == true){
		alert('������� �Է��� �ּ���.');
		document.getElementById('skey').focus();
	}else if(isEmpty(tondat) == true){
		alert('������ڸ� �Է��� �ּ���.');
		document.getElementById('tondat').focus();
	}else if(document.getElementById("tontxt").value.length == 0){
		alert('��㳻���� �Է��� �ּ���.');
		document.getElementById('tontxt').focus();
	}else{
		if(confirm("�����Ͻðڽ��ϱ�?")){
			$('.updis').attr('disabled', false);
			$("form[name='atongha_form']").submit();
		}
	}
}

// ����
function itemd_delete(){
	var type   = $("form[name='atongha_form'] input[name='type']").val();

	if(type == "up"){
		if(confirm("�����Ͻðڽ��ϱ�?")){
			document.atongha_form.type.value='del';
			$("form[name='atongha_form']").submit();
		}
	}else{
		alert("������ ����� �����ϴ�.");
	}
}

// �ݱ�
function modal_close(){
	$("#modal3").hide();  
}



// ajax ȣ��
var btnAction	= true;
	
$(document).ready(function(){

	// �����ͱ��� �����Ұ�
	$('.updis').attr('disabled', true);
	$("#input_tontim").css("backgroundColor","#EAEAEA");

	// �θ�â���� �űԵ�� ��ư Ŭ�� ��
	if('<?=$type?>' == 'in'){
		at_insert();
	}

	var options = { 
		dataType:  'json',
		beforeSubmit:  showRequest_modal_hymst,  // pre-submit callback 
		success:       processJson_modal_hymst  // post-submit callback 
	}; 

	$('.ajaxForm_hymst').ajaxForm(options);
});

// pre-submit callback 
function showRequest_modal_atongha(formData, jqForm, options) { 
	var queryString = $.param(formData); 
	return true; 
} 
 
// post-submit callback 
function processJson_modal_hymst(data) { 
	console.log(data);
	if(data.message){
		alert(data.message);
		location.reload();
	}

	if(data.result==''){
		// ������
		document.atongha.type.value = data.rtype;
		if(data.rtype == 'in'){
			document.atongha.type.value = 'up';
			//document.atongha.dcode.value = data.dcode;
		}else if(data.rtype == 'del'){
			itemd_insert();
		}
	}

}

</script>



 

