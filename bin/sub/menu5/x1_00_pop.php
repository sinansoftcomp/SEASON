<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

// �������. ���Ŀ� COMPANY���̺��� �����ð�.
$X = "X1";
$type ="";

for($i=1;$i<=100;$i++){
	//$select .= "jiyul".$i." ,";
	$select .= "isnull(jiyul".$i.",0.000) jiyul".$i." ,";
}


if($_GET['jik'] and $_GET['inscode'] and $_GET['insilj'] and $_GET['seq']){
	$sql= "	select ".$select." a.scode,a.jik,a.inscode,a.insilj,a.seq,a.jsyymm,a.jeyymm , h.subnm,
					case when a.inscode = '00000' then '����' else g.name end insname
			from x1_jirule(nolock) a left outer join common(nolock) h  on a.scode = h.scode and h.CODE = 'COM006' and  a.jik = h.CODESUB	
							              	  left outer join INSSETUP(nolock) g on  a.scode = g.scode and a.inscode = g.inscode
			where a.scode = '".$_SESSION['S_SCODE']."' and  a.jik = '".$_GET['jik']."' and a.inscode = '".$_GET['inscode']."' and
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
// ����� ��������
$sql= "select inscode, name from inssetup(nolock) where scode = '".$_SESSION['S_SCODE']."' and useyn = 'Y' order by num, inscode";
$qry= sqlsrv_query( $mscon, $sql );
$insData	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
  $insData[] = $fet;
}

// ���޺ҷ����� 
$sql= "	select codesub, subnm from  common(nolock) where  scode = '".$_SESSION['S_SCODE']."' and  CODE = 'COM006'  order by num ";
$qry= sqlsrv_query( $mscon, $sql );
$jikData	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
  $jikData[] = $fet;
}
/*
echo "<pre>";
echo ($sql);
echo "</pre>";
*/ 
 
sqlsrv_free_stmt($qry);
sqlsrv_close($mscon);

?>
<style>
body{background-image: none;}
</style>


<div class="tit_wrap ipgopop" style="padding-top:10px">
	<div class="tit_wrap">
		<span class="btn_wrap" style="padding-right:20px">
			<a class="btn_s white" style="min-width:100px;" onclick="sjirule_new();">�ű�</a>
			<a class="btn_s white" style="min-width:100px;" onclick="sjirule_update();">����</a>
			<a class="btn_s white" style="min-width:100px;" onclick="sjirule_delete();">����</a>
			<a class="btn_s white" style="min-width:100px;" onclick="sjirule_close();">�ݱ�</a>
		</span>
	</div>

	<div>
		<form name="sjirule_form" class="ajaxForm_sjirule" method="post" action="x1_00_action.php" style="padding:0px 20px;">
		<div style="border-right:1px solid #D5D5D5; border-left:1px solid #D5D5D5; ">
			<div class="tb_type01 view">
				<input type="hidden" name="type" id="type" value="<?=$type?>">
				<input type="hidden" name="jik" id="jik" value="<?=$listData[0]['jik']?>">
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

						<th><em class="impor">*</em>����</th>
							<td style="height:30px">
								<select name="jik_s" id="jik_s" class="srch_css" style="margin-left:0" disabled>				
								  <option value="">����</option>
								  <?foreach($jikData as $key => $val){?>						 
									  <option value="<?=$val['codesub']?>" <?if($jikData[$key]['codesub']==$val['codesub']) echo "selected"?>><?=$val['subnm']?></option>
								  <?}?>
								</select>								
							</td>
						<th><em class="impor"> </th>
						<td></td>

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

	location.href='x1_00_pop.php';
}

// ����
function sjirule_update(){
	var type   = $("form[name='sjirule_form'] input[name='type']").val();

	if(type == "up"){
		var jik = document.getElementById("jik").value;
		var inscode = document.getElementById("inscode").value;
		var insilj = document.getElementById("insilj").value;
		var seq = document.getElementById("seq").value;
		var jsyymm = document.getElementById("jsyymm").value;
		var jeyymm = document.getElementById("jeyymm").value;
		if(isEmpty(jik) == true){
			alert('�����ڵ尡 ���������ʽ��ϴ�.');
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
		var jik = document.getElementById("jik_s").value;
		var inscode = document.getElementById("inscode_s").value;
		var insilj = document.getElementById("insilj_s").value;

		if(isEmpty(document.getElementById("jeyymm").value)){
			document.getElementById("jeyymm").value = "9999-12-31";
		}

		var jsyymm = document.getElementById("jsyymm").value;
		var jeyymm = document.getElementById("jeyymm").value;
		if(isEmpty(jik) == true){
			alert('�����ڵ尡 ���������ʽ��ϴ�.');
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
	opener.$('.btn_search').trigger("click");
	self.close();
}

  
$(document).ready(function(){
	
	if('<?=$type?>'=="in"){
		$("#jik_s").attr("disabled",false);
		$("form[name='sjirule_form'] #jik_s").css("backgroundColor","transparent");

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
		if(data.rtype == 'in'){		
			location.href='x1_00_pop.php?jik='+data.jik+'&inscode='+data.inscode+'&insilj='+data.insilj+'&seq='+data.seq;
			opener.$('.btn_search').trigger("click");
		}else if(data.rtype == 'del'){
			self.close();
			opener.$('.btn_search').trigger("click");
		}else if(data.rtype == 'up'){
			opener.$('.btn_search').trigger("click");
		}
	}

}

</script>


<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>