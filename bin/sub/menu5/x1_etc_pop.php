<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

if($_GET['SKEY'] and $_GET['yymm'] and $_GET['seq']){
	$type="up";
	$sql= "
			select a.scode , a.yymm , a.skey ,a.seq, b.gubun , b.gubunnm , a.etcamt , a.bigo , a.gubuncode,b.sucode,
					c.sname , e.bname , f.jsname , g.jname , h.tname,ROW_NUMBER() over(order by a.yymm desc , a.skey) rnum
			from sumst_etc a left outer join etc_set b on a.scode = b.scode and a.gubuncode = b.gubuncode
							left outer join swon c on a.scode = c.scode and a.skey = c.skey
							left outer join bonbu e on c.scode = e.scode and c.bonbu = e.bcode
							left outer join jisa  f on c.scode = f.scode and c.jisa = f.jscode
							left outer join jijum g on c.scode = g.scode and c.jijum = g.jcode
							left outer join team h  on c.scode = h.scode and c.team = h.tcode
			where a.SCODE =  '".$_SESSION['S_SCODE']."' and a.skey = '".$_GET['SKEY']."' and  a.YYMM = '".$_GET['yymm']."' and a.seq = '".$_GET['seq']."'
			";
	$qry	= sqlsrv_query( $mscon, $sql );
	extract($fet= sqlsrv_fetch_array($qry));
}else{
	$type="in";
}

$sql= "select a.scode, a.gubuncode , a.gubunnm
		from etc_set a
		where a.scode = '".$_SESSION['S_SCODE']."' ";
$qry= sqlsrv_query( $mscon, $sql );
$etcData	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
  $etcData[] = $fet;
}

//��������
$sql= "select a.scode, a.gubuncode , a.gubunnm
		from etc_set a
		where a.scode = '".$_SESSION['S_SCODE']."' and gubun='1' ";
$qry= sqlsrv_query( $mscon, $sql );
$etcData1	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
  $etcData1[] = $fet;
}

//��������
$sql= "select a.scode, a.gubuncode , a.gubunnm
		from etc_set a
		where a.scode = '".$_SESSION['S_SCODE']."' and gubun='2' ";
$qry= sqlsrv_query( $mscon, $sql );
$etcData2	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
  $etcData2[] = $fet;
}

//��������
$sql= "select a.scode, a.gubuncode , a.gubunnm
		from etc_set a
		where a.scode = '".$_SESSION['S_SCODE']."' and gubun='3' ";
$qry= sqlsrv_query( $mscon, $sql );
$etcData3	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
  $etcData3[] = $fet;
}

//���İ���
$sql= "select a.scode, a.gubuncode , a.gubunnm
		from etc_set a
		where a.scode = '".$_SESSION['S_SCODE']."' and gubun='4' ";
$qry= sqlsrv_query( $mscon, $sql );
$etcData4	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
  $etcData4[] = $fet;
}

sqlsrv_free_stmt($qry);
sqlsrv_close($mscon);

?>
<style>
body{background-image: none;}
</style>


<div class="tit_wrap ipgopop" style="padding-top:10px">
	<div class="tit_wrap" style="margin-top:0px">
		<span class="btn_wrap" style="padding-right:20px">
			<a href="#" class="btn_s white" style="min-width:100px;" onclick="suetc_insert();">�ű�</a>
			<a href="#" class="btn_s white" style="min-width:100px;" onclick="swon_update();">����</a>
			<a href="#" class="btn_s white" style="min-width:100px;" onclick="swon_delete();">����</a>
			<a href="#" class="btn_s white" style="min-width:100px;" onclick="modal_close();">�ݱ�</a>
		</span>
	</div>


	<form name="suetc_form" class="ajaxForm_suetc" method="post" action="x1_etc_action.php" style="padding:0px 20px;">
		<div style="border-right:1px solid #D5D5D5; border-left:1px solid #D5D5D5; ">
			<div class="tb_type01 view">
				<input type="hidden" name="type" value="<?=$type?>">
				<input type="hidden" name="ori_yymm" value="<?=$_GET['yymm']?>">
				<input type="hidden" name="ori_skey" value="<?=$_GET['SKEY']?>">
				<input type="hidden" name="ori_seq" value="<?=$_GET['seq']?>">
				<table id="modal_table">
					<colgroup>
						<col width="20%">
						<col width="auto">

					</colgroup>
				<tbody>
					<tr>
						<th><em class="impor">*</em>������</th>
						<td>
							<span class="input_type date ml10" style="width:50%;margin-left: 0px;">
								<input type="text" class="Cal_ym" id="yymm" name="yymm" value="<?=$yymm?>" readonly disabled>									
							</span>
						</td>
					</tr>		
					<tr>
						<th><em class="impor">*</em>����ڵ�</th>
						<td><span class="input_type" style="width:100%"><input type="text" name="skey" id="skey" value="<?=$skey?>" onclick="SwonSearch();" readonly disabled></span></td>
					</tr>
					<tr>
						<th><em class="impor">*</em>�����</th>
						<td><span class="input_type" style="width:100%"><input type="text" name="sname" id="sname" value="<?=$sname?>" readonly></span></td>
					</tr>
					<tr>
						<th><em class="impor">*</em>����</th>
						<td>
							<select name="gubun" id="gubun" style="width:30%"> 
								<option value="">����</option>						
								<option value="1" <?if($gubun=="1") echo "selected"?>><?="��������"?></option>
								<option value="2" <?if($gubun=="2") echo "selected"?>><?="��������"?></option>
								<option value="3" <?if($gubun=="3") echo "selected"?>><?="��������"?></option>
								<option value="4" <?if($gubun=="4") echo "selected"?>><?="���İ���"?></option>
							</select>	
						</td>
					</tr>

					<tr>
						<th><em class="impor">*</em>����_�����׸�</th>
						<td id="gubun1" style="height:30px;display:none">
							<select name="gubuncode1" id="gubuncode1" style="width:50%;"> 		
							  <option value="">����</option>
							  <?foreach($etcData1 as $key => $val){?>
							  <option value="<?=$val['gubuncode']?>" <?if($gubuncode==$val['gubuncode']) echo "selected"?>><?=$val['gubunnm']?></option>
							  <?}?>
							</select>										
						</td>
						<td id="gubun2" style="height:30px">
							<select name="gubuncode2" id="gubuncode2" style="width:50%;"> 		
							  <option value="">����</option>
							  <?foreach($etcData2 as $key => $val){?>
							  <option value="<?=$val['gubuncode']?>" <?if($gubuncode==$val['gubuncode']) echo "selected"?>><?=$val['gubunnm']?></option>
							  <?}?>
							</select>										
						</td>
						<td id="gubun3" style="height:30px">
							<select name="gubuncode3" id="gubuncode3" style="width:50%;"> 		
							  <option value="">����</option>
							  <?foreach($etcData3 as $key => $val){?>
							  <option value="<?=$val['gubuncode']?>" <?if($gubuncode==$val['gubuncode']) echo "selected"?>><?=$val['gubunnm']?></option>
							  <?}?>
							</select>										
						</td>
						<td id="gubun4" style="height:30px">
							<select name="gubuncode4" id="gubuncode4" style="width:50%;"> 		
							  <option value="">����</option>
							  <?foreach($etcData4 as $key => $val){?>
							  <option value="<?=$val['gubuncode']?>" <?if($gubuncode==$val['gubuncode']) echo "selected"?>><?=$val['gubunnm']?></option>
							  <?}?>
							</select>										
						</td>
					</tr>
					<tr>
						<th><em class="impor">*</em>����_������</th>
						<td><span class="input_type" style="width:100%"><input type="text" name="etcamt" id="etcamt" value="<?=number_format($etcamt)?>"></span></td>
					</tr>
					<tr>
						<th>���</th>
						<td><span class="input_type" style="width:100%"><input type="text" name="bigo" id="bigo" value="<?=$bigo?>"></span></td>
					</tr>
				</tbody>
				</table>
			</div>
		</div>
	</form>
</div>

 </body>
</html>

<script type="text/javascript">

// ��ũ���� ��� �˾�
function SwonSearch(){
	var left = Math.ceil((window.screen.width - 800)/2);
	var top = Math.ceil((window.screen.height - 800)/2);
	var popOpen	= window.open("<?=$conf['homeDir']?>/sub/help/ga_swon_search.php","swonpop","width=500px,height=400px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
	popOpen.focus();
}

function setSwonValue(row,code,name){
	$("#skey").val(code);
	$('#sname').val(name);
}

// �ű�
function suetc_insert(){
	document.suetc_form.type.value='in';
	document.suetc_form.yymm.value='';
	document.suetc_form.skey.value='';
	document.suetc_form.sname.value='';
	document.suetc_form.gubun.value='';
	document.suetc_form.gubuncode1.value='';
	document.suetc_form.gubuncode2.value='';
	document.suetc_form.gubuncode3.value='';
	document.suetc_form.gubuncode4.value='';
	document.suetc_form.etcamt.value=0;
	document.suetc_form.bigo.value='';

	//document.getElementById('item').readOnly=false;

	$("#yymm").attr("disabled",false);
	$("form[name='suetc_form'] #yymm").css("backgroundColor","transparent");

	$("#skey").attr("disabled",false);
	$("form[name='suetc_form'] #skey").css("backgroundColor","transparent");

	$("form[name='suetc_form'] input[name='type']").val('in');
}

// ����
function swon_update(){
	var yymm = $("form[name='suetc_form'] input[name='yymm']").val();
	var skey = $("form[name='suetc_form'] input[name='skey']").val();
	var gubun = $('#gubun').val();

	if(gubun == "1"){
		var gubuncode = $('#gubuncode1').val();
	}else if(gubun == "2"){
		var gubuncode = $('#gubuncode2').val();
	}else if(gubun == "3"){
		var gubuncode = $('#gubuncode3').val();
	}else if(gubun == "4"){
		var gubuncode = $('#gubuncode4').val();
	}

	var etcamt = uncomma($("form[name='suetc_form'] input[name='etcamt']").val());
	
	if(etcamt <= 0){
		alert("���ް������� 0�� �̻��̾�� �մϴ�.");
		return false;
	}

	if(isEmpty(yymm) == true){
		alert('�������� �������ּ���.');
	}else if(isEmpty(skey) == true){
		alert('����ڵ带 �������ּ���.');
	}else if(isEmpty(gubun) == true){
		alert('���а��� �������ּ���.');
	}else if(isEmpty(gubuncode) == true){
		alert('����_�����׸��� �������ּ���.');
	}else if(isEmpty(etcamt) == true){
		alert('����_�������� �Է����ּ���.');
	}else{
		if(confirm("�����Ͻðڽ��ϱ�?")){
			$("form[name='suetc_form']").submit();
		}
	}
}

// ����
function swon_delete(){
	var type   = $("form[name='suetc_form'] input[name='type']").val();
	if(type == "up"){
		if(confirm("�����Ͻðڽ��ϱ�?")){
			document.suetc_form.type.value='del';
			$("form[name='suetc_form']").submit();
		}
	}else{
		alert("������ ����� �����ϴ�.");
	}
}

// �ݱ�
function modal_close(){
	self.close();
}


// ajax ȣ��
var btnAction	= true;
	
$(document).ready(function(){

	// �θ�â���� �űԵ�� ��ư Ŭ�� ��
	if('<?=$type?>' == 'in'){
		suetc_insert();
		$("#gubun1").css("display","");
		$("#gubun1").attr("disabled",false);
		$("#gubun2").css("display","none");
		$("#gubun2").attr("disabled",true);
		$("#gubun3").css("display","none");
		$("#gubun3").attr("disabled",true);
		$("#gubun4").css("display","none");
		$("#gubun4").attr("disabled",true);
	}else{
		if("<?=$gubun?>"=="1"){
			$("#gubun1").css("display","");
			$("#gubun1").attr("disabled",false);
			$("#gubun2").css("display","none");
			$("#gubun2").attr("disabled",true);
			$("#gubun3").css("display","none");
			$("#gubun3").attr("disabled",true);
			$("#gubun4").css("display","none");
			$("#gubun4").attr("disabled",true);
		}else if("<?=$gubun?>" =="2"){
			$("#gubun2").css("display","");
			$("#gubun2").attr("disabled",false);
			$("#gubun1").css("display","none");
			$("#gubun1").attr("disabled",true);
			$("#gubun3").css("display","none");
			$("#gubun3").attr("disabled",true);
			$("#gubun4").css("display","none");
			$("#gubun4").attr("disabled",true);
		}else if("<?=$gubun?>" =="3"){
			$("#gubun3").css("display","");
			$("#gubun3").attr("disabled",false);
			$("#gubun1").css("display","none");
			$("#gubun1").attr("disabled",true);
			$("#gubun2").css("display","none");
			$("#gubun2").attr("disabled",true);
			$("#gubun4").css("display","none");
			$("#gubun4").attr("disabled",true);
		}else if("<?=$gubun?>" =="4"){
			$("#gubun4").css("display","");
			$("#gubun4").attr("disabled",false);
			$("#gubun1").css("display","none");
			$("#gubun1").attr("disabled",true);
			$("#gubun3").css("display","none");
			$("#gubun3").attr("disabled",true);
			$("#gubun2").css("display","none");
			$("#gubun2").attr("disabled",true);
		}
	}

	$('#gubun').on('change', function () {
		var gubun = $(this).val();
		
		if(gubun == "1"){
			$("#gubun1").css("display","");
			$("#gubun1").attr("disabled",false);
			$("#gubun2").css("display","none");
			$("#gubun2").attr("disabled",true);
			$("#gubun3").css("display","none");
			$("#gubun3").attr("disabled",true);
			$("#gubun4").css("display","none");
			$("#gubun4").attr("disabled",true);
		}else if(gubun =="2"){
			$("#gubun2").css("display","");
			$("#gubun2").attr("disabled",false);
			$("#gubun1").css("display","none");	
			$("#gubun1").attr("disabled",true);
			$("#gubun3").css("display","none");
			$("#gubun3").attr("disabled",true);
			$("#gubun4").css("display","none");
			$("#gubun4").attr("disabled",true);
		}else if(gubun =="3"){
			$("#gubun3").css("display","");
			$("#gubun3").attr("disabled",false);
			$("#gubun1").css("display","none");
			$("#gubun1").attr("disabled",true);
			$("#gubun2").css("display","none");
			$("#gubun2").attr("disabled",true);
			$("#gubun4").css("display","none");
			$("#gubun4").attr("disabled",true);
		}else if(gubun =="4"){
			$("#gubun4").css("display","");
			$("#gubun4").attr("disabled",false);
			$("#gubun1").css("display","none");
			$("#gubun1").attr("disabled",true);
			$("#gubun3").css("display","none");
			$("#gubun3").attr("disabled",true);
			$("#gubun2").css("display","none");
			$("#gubun2").attr("disabled",true);
		}

	});

	var options = { 
		dataType:  'json',
		beforeSubmit:  showRequest_modal_suetc,  // pre-submit callback 
		success:       processJson_modal_suetc  // post-submit callback 
	}; 

	$('.ajaxForm_suetc').ajaxForm(options);
});

// pre-submit callback 
function showRequest_modal_suetc(formData, jqForm, options) { 
	var queryString = $.param(formData); 
	return true; 
} 

// post-submit callback 
function processJson_modal_suetc(data) { 
	console.log(data);
	if(data.message){
		alert(data.message);
		//opener.location.reload();
	}
	//document.suetc_form.type.value = data.rtype;

	if(data.result==""){

		opener.$('.btn_search').trigger("click");	//��ȸ��ưŬ��

		if(data.rtype == 'in'){
			location.href='x1_etc_pop.php?SKEY='+data.skey+'&yymm='+data.yymm+'&seq='+data.seq;
		}else if(data.rtype == 'del'){
			self.close();
		}
	}

	if(data.result==''){
		// ������
		//opener.location.reload();
	}
}

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>