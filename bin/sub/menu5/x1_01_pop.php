<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

// �������. ���Ŀ� COMPANY���̺��� �����ð�.
$X = "X1";
$type ="";

for($i=1;$i<=100;$i++){
	//$select .= "jiyul".$i." ,";
	$select .= "isnull(jiyul".$i.",0.000) jiyul".$i." ,";
}

if($_GET['skey'] and $_GET['inscode'] and $_GET['insilj'] and $_GET['seq']){
	$sql= "	select ".$select." a.scode,a.skey,a.inscode,a.insilj,a.seq,a.jsyymm,a.jeyymm , b.sname,
					case when a.inscode = '00000' then '����' else c.name end insname
			from ".$X."_sjirule a left outer join swon b on a.scode = b.scode and a.skey = b.skey
								  left outer join insmaster c on a.inscode = c.code
			where a.scode = '".$_SESSION['S_SCODE']."' and  a.skey = '".$_GET['skey']."' and a.inscode = '".$_GET['inscode']."' and
				  a.insilj = '".$_GET['insilj']."' and a.seq = '".$_GET['seq']."'";
	$qry	= sqlsrv_query( $mscon, $sql );
	$listData = array();
	while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
		$listData[]	= $fet;
	}
	
	$type="up";
}else{
	$type="in";
}
/*
echo "<pre>";
echo $sql;
echo "</pre>";
*/

// ����� ��������
$sql= "select inscode, name from inssetup where scode = '".$_SESSION['S_SCODE']."' and useyn = 'Y' order by num, inscode";
$qry= sqlsrv_query( $mscon, $sql );
$insData	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
  $insData[] = $fet;
}

sqlsrv_free_stmt($qry);
sqlsrv_close($mscon);
?>
<style>
body{background: #FFFFFF;}
.box_wrap{margin-bottom:0px}
.tb_type01 th, .tb_type01 td {padding: 6px 0;}

table.gridhover2 tr td:hover { background-color: #EAEAEA;} 
table.gridhover2 tr td:hover input{ background-color: #EAEAEA;} 

</style>


<div class="tit_wrap ipgopop" style="padding-top:10px;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none" >
	<div class="tit_wrap">
		<span class="btn_wrap" style="padding-right:20px">
			<a class="btn_s white" style="min-width:100px;" onclick="sjirule_new();">�ű�</a>
			<a class="btn_s white" style="min-width:100px;" onclick="sjirule_update();">����</a>
			<a class="btn_s white" style="min-width:100px;" onclick="sjirule_delete();">����</a>
			<a class="btn_s white" style="min-width:100px;" onclick="sjirule_close();">�ݱ�</a>

		</span>
	</div>

	<div>
		<form name="sjirule_form" class="ajaxForm_sjirule" method="post" action="x1_01_action.php" style="padding:0px 20px;">
		<div style="border-right:1px solid #D5D5D5; border-left:1px solid #D5D5D5; ">
			<div class="tb_type01 view">
				<input type="hidden" name="type" id="type" value="<?=$type?>">
				<input type="hidden" name="skey" id="skey" value="<?=$listData[0]['skey']?>">
				<input type="hidden" name="inscode" id="inscode" value="<?=$listData[0]['inscode']?>">
				<input type="hidden" name="insilj" id="insilj" value="<?=$listData[0]['insilj']?>">
				<input type="hidden" name="seq" id="seq" value="<?=$listData[0]['seq']?>">
				<table>
					<colgroup>
						<col width="18%">
						<col width="32%">
						<col width="18%">
						<col width="32%">
					</colgroup>
					<tbody>
					<tr>
						<th><em class="impor">*</em>����ڵ�</th>
						<td style="height:30px">
							<span class="input_type" style="width:87%">
								<input type="text" name="skey_s" id="skey_s" value="<?=$listData[0]['skey']?>" onclick="swonSearch();" readonly disabled>			
							</span> 	
							<a href="#" class="btn_s white" onclick="swonSearch();" name="skeys" id="skeys" style="display:none">�˻�</a>
						</td>
						<th><em class="impor">*</em>�����</th>
						<td><b style="width:100%" name="sname" id="sname"><?=$listData[0]['sname']?></b></td>
					</tr>
					<tr>
						<th><em class="impor">*</em>�����</th>
						<td style="height:30px">
							<select name="inscode_s" id="inscode_s" style="width:50%;background-color:#EAEAEA" disabled> 		
							  <option value="">����</option>
							  <option value="00000" <?if($listData[0]['inscode']=="00000") echo "selected"?>>����</option>
							  <?foreach($insData as $key => $val){?>
							  <option value="<?=$val['inscode']?>" <?if($listData[0]['inscode']==$val['inscode']) echo "selected"?>><?=$val['name']?></option>
							  <?}?>
							</select>										
						</td>
						<th><em class="impor">*</em>��ǰ����</th>
						<td style="height:30px">
							<select name="insilj_s" id="insilj_s" style="width:50%;background-color:#EAEAEA" disabled> 		
							  <option value="">����</option>
							  <option value="1" <?if($listData[0]['insilj']=="1") echo "selected"?>>�Ϲ�</option>
							  <option value="2" <?if($listData[0]['insilj']=="2") echo "selected"?>>���պ� ���</option>
							  <option value="3" <?if($listData[0]['insilj']=="3") echo "selected"?>>�ڵ���</option>
							</select>										
						</td>
					</tr>
					<tr>
						<th><em class="impor">*</em>���޽�������</th>
						<td>
							<span class="input_type date ml10" style="width:100%;margin-left:0px">
								<input type="text" class="Calnew" name="jsyymm" id="jsyymm" value="<? if(trim($listData[0]['jsyymm'])) echo  date("Y-m-d",strtotime($listData[0]['jsyymm']));?> "  readonly>	
							</span> 	
						</td>
						<th><em class="impor">*</em>������������ <?if($type=='in'){?><span style="color:#E0844F">( ���Է½� �ڵ� 9999-12-31 )</span><?}?></th>
						<td>
							<span class="input_type date ml10" style="width:100%;margin-left:0px">
								<input type="text" class="Calnew" name="jeyymm" id="jeyymm" value="<? if(trim($listData[0]['jeyymm'])) echo  date("Y-m-d",strtotime($listData[0]['jeyymm']));?> "  readonly>
							</span> 	
						</td>
					</tr>
					</tbody>
				</table>
				<table class="gridhover2" >
					<colgroup>
						<col width="100px">
						<col width="100px">
						<col width="100px">
						<col width="100px">
						
						<col width="100px">
						<col width="100px">
						<col width="100px">
						<col width="100px">

						<col width="100px">
						<col width="100px">
						<col width="100px">
						<col width="100px">
						<col width="100px">
						<col width="100px">
						<col width="100px">
						<col width="100px">
						<col width="100px">
						<col width="100px">
						<col width="100px">
						<col width="100px">
					</colgroup>
					<thead>
						<th align="center" style="background-color: cornsilk;top:0px;">ȸ��</th>
						<th align="center" style="background-color: cornsilk;top:0px;">������</th>
						<th align="center" style="background-color: cornsilk;top:0px;">ȸ��</th>
						<th align="center" style="background-color: cornsilk;top:0px;">������</th>
						<th align="center" style="background-color: cornsilk;top:0px;">ȸ��</th>
						<th align="center" style="background-color: cornsilk;top:0px;">������</th>
						<th align="center" style="background-color: cornsilk;top:0px;">ȸ��</th>
						<th align="center" style="background-color: cornsilk;top:0px;">������</th>
						<th align="center" style="background-color: cornsilk;top:0px;">ȸ��</th>
						<th align="center" style="background-color: cornsilk;top:0px;">������</th>
						<th align="center" style="background-color: cornsilk;top:0px;">ȸ��</th>
						<th align="center" style="background-color: cornsilk;top:0px;">������</th>
						<th align="center" style="background-color: cornsilk;top:0px;">ȸ��</th>
						<th align="center" style="background-color: cornsilk;top:0px;">������</th>
						<th align="center" style="background-color: cornsilk;top:0px;">ȸ��</th>
						<th align="center" style="background-color: cornsilk;top:0px;">������</th>
						<th align="center" style="background-color: cornsilk;top:0px;">ȸ��</th>
						<th align="center" style="background-color: cornsilk;top:0px;">������</th>
						<th align="center" style="background-color: cornsilk;top:0px;">ȸ��</th>
						<th align="center" style="background-color: cornsilk;top:0px;">������</th>
					</thead>
					<tbody>
						<?for($i=0;$i<10;$i++){?>
						<tr>
							<?for($j=($i*10)+1;$j<=($i+1)*10;$j++){?>
							<th align="center" <?if($j==100){?> style="color:hotpink;" <?}?> ><?=$j?>ȸ��</th>
							<td>
								<span class="input_type" style="width:95%;margin-left:0px; ">
									<input type="text" name='<?="jiyul".$j?>' id='<?="jiyul".$j?>' value='<?=$listData[0]['jiyul'.$j]?>' style="text-align:right" class="input_type_noborder_ip">
								</span>
							</td>
							<?}?>
						</tr>
						<?}?>
					</tbody>
				</table>
			</div>
		</div>
		<div align="right" style="margin-top:10px"><b style="color:#E0844F"><em class="impor">*</em>100ȸ�� ���� ��� �������� 100ȸ���� �Էµ� ��������ŭ ���޵˴ϴ�.</b></div>
		</form>
	</div>
</div>

<script type="text/javascript">

// �ű�
function sjirule_new(){

	location.href='x1_01_pop.php';
}

// ����
function sjirule_update(){
	var type   = $("form[name='sjirule_form'] input[name='type']").val();

	if(type == "up"){
		var skey = document.getElementById("skey").value;
		var inscode = document.getElementById("inscode").value;
		var insilj = document.getElementById("insilj").value;
		var seq = document.getElementById("seq").value;
		var jsyymm = document.getElementById("jsyymm").value;
		var jeyymm = document.getElementById("jeyymm").value;
		if(isEmpty(skey) == true){
			alert('����ڵ尡 ���������ʽ��ϴ�.');
		}else if(isEmpty(inscode) == true){
			alert('����簡 ���������ʽ��ϴ�.');
		}else if(isEmpty(insilj) == true){
			alert('��ǰ������ ���������ʽ��ϴ�.');
		}else if(isEmpty(seq) == true){
			alert('������ ���������ʽ��ϴ�.');
		}else if(isEmpty(jsyymm) == true){
			alert('���޽������ڰ� ���������ʽ��ϴ�.');
		}else if(isEmpty(jeyymm) == true){
			alert('�����������ڰ� ���������ʽ��ϴ�.');
		}else{
			if(confirm("�����Ͻðڽ��ϱ�?")){
				$("form[name='sjirule_form']").submit();
			}
		}
	}else if(type == "in"){
		var skey = document.getElementById("skey_s").value;
		var inscode = document.getElementById("inscode_s").value;
		var insilj = document.getElementById("insilj_s").value;

		if(isEmpty(document.getElementById("jeyymm").value)){
			document.getElementById("jeyymm").value = "9999-12-31";
		}

		var jsyymm = document.getElementById("jsyymm").value;
		var jeyymm = document.getElementById("jeyymm").value;
		if(isEmpty(skey) == true){
			alert('����ڵ尡 ���������ʽ��ϴ�.');
		}else if(isEmpty(inscode) == true){
			alert('����簡 ���������ʽ��ϴ�.');
		}else if(isEmpty(insilj) == true){
			alert('��ǰ������ ���������ʽ��ϴ�.');
		}else if(isEmpty(jsyymm) == true){
			alert('���޽������ڰ� ���������ʽ��ϴ�.');
		}else if(isEmpty(jeyymm) == true){
			alert('�����������ڰ� ���������ʽ��ϴ�.');
		}else{
			if(confirm("�űԵ�� �Ͻðڽ��ϱ�?")){
				$("form[name='sjirule_form']").submit();
			}
		}
	}

}

// ����
function sjirule_delete(){
	var type   = $("form[name='sjirule_form'] input[name='type']").val();

	if(type == "up"){
		if(confirm("�����Ͻðڽ��ϱ�?")){
			document.sjirule_form.type.value='del';
			$("form[name='sjirule_form']").submit();
		}
	}else{
		alert("������ ����� �����ϴ�.");
	}
}

// �ݱ�
function sjirule_close(){	
	self.close();
	//opener.$('.btn_search').trigger("click");		
}

// �������Ʈ �˾�
function swonSearch(){

	var left = Math.ceil((window.screen.width - 400)/2);
	var top = Math.ceil((window.screen.height - 740)/2);
	var popOpen	= window.open("<?=$conf['homeDir']?>/sub/help/ga_swon_search.php","swon","width=600px,height=705px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
	popOpen.focus();
}

function setSwonValue(row,code,name){
	
	$("#skey_s").val(code);
	$("#sname").text(name);
}

$(document).ready(function(){
	
	if('<?=$type?>'=="in"){
		$("#skey_s").attr("disabled",false);
		$("form[name='sjirule_form'] #skey_s").css("backgroundColor","transparent");
		document.getElementById("skeys").style.display = "";

		$("#inscode_s").attr("disabled",false);
		$("form[name='sjirule_form'] #inscode_s").css("backgroundColor","transparent");
		$("#insilj_s").attr("disabled",false);
		$("form[name='sjirule_form'] #insilj_s").css("backgroundColor","transparent");
	}

	var options = { 
		dataType:  'json',
		beforeSubmit:  showRequest_modal_sjirule,  // pre-submit callback 
		success:       processJson_modal_sjirule  // post-submit callback 
	}; 

	$('.ajaxForm_sjirule').ajaxForm(options);

});


// pre-submit callback 
function showRequest_modal_sjirule(formData, jqForm, options) { 
	var queryString = $.param(formData); 
	return true; 
} 
 
// post-submit callback 
function processJson_modal_sjirule(data) { 
	if(data.message){
		alert(data.message);
	}

	if(data.result==""){
		
		opener.$('.btn_search').trigger("click");	//��ȸ��ưŬ��

		if(data.rtype == 'in'){		
			location.href='x1_01_pop.php?skey='+data.skey+'&inscode='+data.inscode+'&insilj='+data.insilj+'&seq='+data.seq;
		}else if(data.rtype == 'del'){
			self.close();
		}else if(data.rtype == 'up'){
		}
	}

}

</script>


<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>