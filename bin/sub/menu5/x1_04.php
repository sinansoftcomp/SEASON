<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

/*
	���Ѱ���
	bin/include/source/auch_chk.php
*/
$pageTemp	= explode("/",$_SERVER['PHP_SELF']);
$auth = auth_Ser($_SESSION['S_MASTER'], $pageTemp[count($pageTemp)-1], $_SESSION['S_SKEY'], $mscon);
if($auth != "Y"){
	sqlsrv_close($mscon);
	echo "<script> alert('�ش� �޴��� ���� ������ �����ϴ�. �����ڿ��� ���� �ٶ��ϴ�.'); self.close(); </script>";
}

 
$sdate1 =  date("Y-m-01");
$lastday = DATE('t', strtotime($sdate1));
$sdate2 =  date("Y-m-".$lastday);
/* ------------------------------------------------------------
	End Date �ʱⰪ ����
------------------------------------------------------------ */

$sql= "	
		select scode,sucode,suname,useyn
		from SUNAME_SET
		where scode = '".$_SESSION['S_SCODE']."' and substring(sucode,1,1) = 'K'
		ORDER BY substring(sucode,1,1) desc , convert(int,substring(sucode,5,2)) asc
		";
$qry	= sqlsrv_query( $mscon, $sql );
$listData_name = array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listData_kname[]	= $fet;
}

$sql= "	
		select scode,sucode,suname,useyn
		from SUNAME_SET
		where scode = '".$_SESSION['S_SCODE']."' and substring(sucode,1,1) = 'G'
		ORDER BY substring(sucode,1,1) desc , convert(int,substring(sucode,5,2)) asc
		";
$qry	= sqlsrv_query( $mscon, $sql );
$listData_name = array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listData_gname[]	= $fet;
}

sqlsrv_free_stmt($result);
sqlsrv_close($mscon);

?>

<!-- html���� -->
<style>
body{background-image: none;}
</style>

<div class="container">
	<div class="content_wrap">


		<div class="tit_wrap">
			<h3 class="tit_sub">������ ��Ī����</h3>
			<!--<span class="btn_wrap" style="padding-right:20px">-->
			<span style = "margin-left:170px;">
				<a class="btn_s white hover_btn btn_search btn_off" style="width:100px;margin:0px; " onclick="suname_update('save');">����</a>
				<a class="btn_s white btn_off" style="width:100px;margin-left:-4px" onclick="suname_update('reset');">�ʱ�ȭ</a>
				<a class="btn_s white btn_off" style="width: 100px;margin-left:-4px" onclick="self.close();">�ݱ�</a>
			</span>
		</div>

		<div style="padding: 0px;overflow-x:auto;"> 
			<div class="tb_type01 kwndatalist" style="overflow-y:auto;width:600px; height:590px">	
				<form name="suname_form" id = 'inscharge_id_form' class="ajaxForm_suname" method="post" action="x1_04_action.php">
				<input type="hidden" name="inscode" id="inscode" value="<?=$inscode?>">
				<input type="hidden" name="type" id="type" value="">
					<table  class="gridhover">
						<colgroup>
							<col width="70px">
							<col width="300px">						
							<col width="150px">											
						</colgroup>
						<thead>
						<tr class="rowTop">
							<th align="center">�������ڵ�</th>
							<th align="center">�������Ī</th>						
							<th align="center">��뱸��</th>					
						</tr>
						</thead>
						<tbody>
							<?for($i=1; $i<=20; $i++){?>
							<tr>
								<td>
									<span class="input_type" style="width:100%">
										<input type="text" name='<?="kamt".$i?>' id='<?="kamt".$i?>' value='<?=$listData_kname[$i-1]['sucode']?>' style="text-align:center;background-Color:#EAEAEA;padding-left:0px" readonly>
									</span> 				
								</td>
								<td>
									<span class="input_type" style="width:95%;margin-left:0px; ">
										<input type="text" name='<?="ksuname".$i?>' id='<?="ksuname".$i?>' value='<?=$listData_kname[$i-1]['suname']?>' style="text-align:left" class="input_type_noborder_ip">
									</span>						
								</td>
								<td>
									<select name='<?="kuseyn".$i?>' id='<?="kuseyn".$i?>' style="width:50%;text-align:center"> 		
									  <option value="Y" <?if($listData_kname[$i-1]['useyn']=="Y") echo "selected"?>>���</option>
									  <option value="N" <?if($listData_kname[$i-1]['useyn']!="Y") echo "selected"?>>�̻��</option>
									</select>
								</td>
							</tr>
							<?}?>

						<thead>
						<tr class="rowTop">
							<th align="center">���ļ���</th>
							<th align="center">������Ī</th>						
							<th align="center">��뱸��</th>					
						</tr>
						</thead>
						<tbody>
							<?for($i=1; $i<=15; $i++){?>
							<tr>
								<td>
									<span class="input_type" style="width:100%">
										<input type="text" name='<?="gamt".$i?>' id='<?="gamt".$i?>' value='<?=$listData_gname[$i-1]['sucode']?>' style="text-align:center;background-Color:#EAEAEA;padding-left:0px" readonly>
									</span> 				
								</td>
								<td>
									<span class="input_type" style="width:95%;margin-left:0px; ">
										<input type="text" name='<?="gsuname".$i?>' id='<?="gsuname".$i?>' value='<?=$listData_gname[$i-1]['suname']?>' style="text-align:left" class="input_type_noborder_ip">
									</span>						
								</td>
								<td>
									<select name='<?="guseyn".$i?>' id='<?="guseyn".$i?>' style="width:50%;text-align:center"> 		
									  <option value="Y" <?if($listData_gname[$i-1]['useyn']=="Y") echo "selected"?>>���</option>
									  <option value="N" <?if($listData_gname[$i-1]['useyn']!="Y") echo "selected"?>>�̻��</option>
									</select>
								</td>
							</tr>
							<?}?>
						</tbody>
					</table>



				</form>

			</div>			
		</div>  

	</div>
</div>

<script type="text/javascript">

// ���� or �ʱ�ȭ
function suname_update(bit){
	$("form[name='suname_form'] input[name='type']").val(bit);
	var conm = '�����Ͻðڽ��ϱ�?';
	if(bit=='reset'){
		conm = '�ʱ�ȭ�Ͻðڽ��ϱ�?';
	}
	if(confirm(conm)){
		$("form[name='suname_form']").submit();
	}
}

$(document).ready(function(){	
	
	var options = { 
		dataType:  'json',
		beforeSubmit:  showRequest_modal_suname,  // pre-submit callback 
		success:       processJson_modal_suname  // post-submit callback 
	}; 

	$('.ajaxForm_suname').ajaxForm(options);
	
});

// pre-submit callback 
function showRequest_modal_suname(formData, jqForm, options) { 
	var queryString = $.param(formData); 
	return true; 
} 
 
// post-submit callback 
function processJson_modal_suname(data) { 
	if(data.message){
		alert(data.message);
	}
	if(data.rtype=='save' || data.rtype=='reset'){
		$('#tree-container').jstree($("#N2"+data.inscode+"_anchor").trigger("click"));
	}

}

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>