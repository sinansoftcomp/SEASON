<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

$skey = $_GET['skey'];

if($_GET['inscode']){
	$sql	= "select skey,inscode,bscode,ydate,hdate,convert(varchar,idate,21) idate , iswon , sgubun
				from inswon a
				where a.scode = '".$_SESSION['S_SCODE']."'
				  and a.skey = '".$_GET['skey']."' and a.inscode = '".$_GET['inscode']."' and a.bscode = '".$_GET['bscode']."' ";
	$qry	= sqlsrv_query( $mscon, $sql );
	extract($fet	= sqlsrv_fetch_array($qry));
}
/*
echo "<pre>";
echo $sql;
echo "</pre>";
*/
if($_GET['skey'] and $_GET['inscode']){
	$type = "up";
}else{
	$type = "in";
}

if(!$sgubun){
	$sgubun = '1';
}

// ����� ��������
$sql= "select inscode, name from inssetup where scode = '".$_SESSION['S_SCODE']."' and useyn = 'Y' order by num, inscode";
$qry= sqlsrv_query( $mscon, $sql );
$insData	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
  $insData[] = $fet;
}

?>
<style>
body{background-image: none;}
.box_wrap{margin-bottom:0px}
.tb_type01 th, .tb_type01 td {padding: 4px 0;}
</style>

<div class="tit_wrap ipgopop" >
	<div class="tit_wrap">
		<span class="btn_wrap" style="padding-right:20px;margin-top:10px">
			<a class="btn_s white" style="min-width:100px;" onclick="insData_new();">�ű�</a>
			<a class="btn_s white" style="min-width:100px;" onclick="insData_update();">����</a>
			<a class="btn_s white" style="min-width:100px;" onclick="insData_delete();">����</a>
			<a class="btn_s white" style="min-width:100px;" onclick="insData_close();">�ݱ�</a>
		</span>
	</div>

	<div>
	<form name="Insswon_form" class="ajaxForm_swonins" method="post" action="ga_swonins_action.php" style="padding:0px 20px;">
	<div style="border-right:1px solid #D5D5D5; border-left:1px solid #D5D5D5">
	<div class="tb_type01 view">
		<input type="hidden" name="type" value="<?=$type?>">
		<input type="hidden" name="skey" value="<?=$skey?>">
		<input type="hidden" name="bscode_f" value="<?=$bscode?>">
		<table>
			<colgroup>
				<col width="30%">
				<col width="70%">
			</colgroup>
			<tbody>
			<tr>
				<th><em class="impor">*</em>�����</th>
				<td>
					<select name="inscode" id="inscode" style="width:100%"
					<?if($type=='up'){?>class="readonly" onFocus="this.initialSelect = this.selectedIndex;" onChange="this.selectedIndex = this.initialSelect;"<?}?>> 		
					  <option value="">����</option>
					  <?foreach($insData as $key => $val){?>
					  <option value="<?=$val['inscode']?>" <?if($inscode==$val['inscode']) echo "selected"?>><?=$val['name']?></option>
					  <?}?>
					</select>				
				</td>
			</tr>
			<tr>
				<th><em class="impor">*</em>������ �����ȣ</th>
				<td>
					<span class="input_type" style="width:100%"><input type="text" name="bscode" id="bscode" value="<?=trim($bscode)?>"></span>
				</td>
			</tr>
			<tr>
				<th><em class="impor">*</em>��/�� �ڵ屸��</th>
				<td>
					<input type="radio" class="sgubun" name="sgubun" id="sgubun1" value="1" <?if(trim($sgubun)=='1') echo "checked";?>><label for="sgubun1">�� </label>&nbsp;&nbsp;&nbsp;
					<input type="radio" class="sgubun" name="sgubun" id="sgubun2" value="2" <?if(trim($sgubun)=='2') echo "checked";?>><label for="sgubun2">��</label>		
				</td>
			</tr>
			<tr>
				<th><em class="impor">*</em>��������</th>
				<td>
					<span class="input_type" style="width:100%"><input type="text" class="Calnew" name="ydate" value="<?if($ydate) echo date("Y-m-d",strtotime($ydate));?>" readonly></span> 	
				</td>
			</tr>
			<tr>
				<th><em class="impor">*</em>��������</th>
				<td>
					<span class="input_type" style="width:100%"><input type="text" class="Calnew" name="hdate" value="<?if($hdate) echo date("Y-m-d",strtotime($hdate));?>" readonly></span> 	
				</td>
			</tr>
			</tbody>
		</table>		
	</div>

	<div  style="margin-top:5px">		
		<span class="btn_wrap">
			<span style="margin-left:15px" class="font_blue">��ϻ�� : <?=$_SESSION['S_SNAME']?></span> 
			<span style="margin-left:15px" class="font_blue">����Ͻ� : <?=date("Y-m-d H:i:s")?></span>			
		</span>
	</div>


	</div>
	</form>
</div>

<script type="text/javascript">

//window.resizeTo("500", "320");                             // ������ ��������

// ����
function insData_update(){

	if(confirm("�����Ͻðڽ��ϱ�?")){
		$("form[name='Insswon_form']").submit();
	}

}

// �ű�
function insData_new(){
	var skey = "<?=$_GET['skey']?>";
	location.href='ga_swonins_in.php?skey='+skey;
}

// �ݱ�
function insData_close(){	
	self.close();
	//opener.location.reload();
}

// ����
function insData_delete(){
	var type   = $("form[name='Insswon_form'] input[name='type']").val();

	if(type == "up"){
		if(confirm("�����Ͻðڽ��ϱ�?")){
			document.Insswon_form.type.value='del';
			$("form[name='Insswon_form']").submit();
		}
	}else{
		alert("������ ����� �����ϴ�.");
	}
}

$(document).ready(function(){
	
	// ���� �� ����� ����ȭ�� ���ε�
	if('<?=$_GET['save']?>' == 'Y'){
		opener.location.reload();
	}

	var options = { 
		dataType:  'json',
		beforeSubmit:  showRequest_modal_ipgo,  // pre-submit callback 
		success:       processJson_modal_ipgo  // post-submit callback 
	}; 

	$('.ajaxForm_swonins').ajaxForm(options);

});


// pre-submit callback 
function showRequest_modal_ipgo(formData, jqForm, options) { 
	var queryString = $.param(formData); 
	return true; 
} 
 
// post-submit callback 
function processJson_modal_ipgo(data) { 
	console.log(data);
	if(data.message){
		alert(data.message);
	}

	var inscode = data.inscode;
	if(data.result==''){
		opener.$(".btn_search").trigger("click");
		if(data.rtype=="del"){
			self.close();
		}else if(data.rtype=="in"){
			location.href='ga_swonins_in.php?skey='+data.skey+'&inscode='+data.inscode+'&bscode='+data.bscode;
		}else if(data.rtype=="up"){
			location.href='ga_swonins_in.php?skey='+data.skey+'&inscode='+data.inscode+'&bscode='+data.bscode;
		}
	}
/*
	if(data.result==''){
		// ������
		$("#modal").hide();
		ajaxLodingTarget("/bin/sub/menu6/ga_swonins_in.php",'&inscode='+inscode+'&save=Y',event,$('.ipgopop'));    
	}
*/

}

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>