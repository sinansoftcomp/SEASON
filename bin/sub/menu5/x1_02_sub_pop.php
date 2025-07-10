<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");


// ����� ��������
$sql= "select a.yymm,a.inscode,b.name,a.selfbit,a.dataset1,a.dataset2,seq
		from INSCHARGE_SET_sub a left outer join inssetup b on a.scode = b.scode and a.inscode = b.inscode
		where a.scode = '".$_SESSION['S_SCODE']."' and a.yymm='".$_GET['yymm']."' and a.inscode = '".$_GET['inscode']."'
		order by selfbit,seq";
$qry= sqlsrv_query( $mscon, $sql );
$listData_sub2	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
  $listData_sub2[] = $fet;
}

// ����� ��������
$sql= "select name
		from inssetup a
		where a.scode = '".$_SESSION['S_SCODE']."' and a.inscode = '".$_GET['inscode']."'
		";
$qry= sqlsrv_query( $mscon, $sql );
$insname	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
  $insname[] = $fet;
}

sqlsrv_free_stmt($qry);
sqlsrv_close($mscon);

?>

<div class="tit_wrap" style="margin-top:10px">
	<span class="btn_wrap" style="padding-right:20px">
		<a href="#" class="btn_s white" style="min-width:100px;" onclick="inscharge_sub_new();">�ű�</a>
		<a href="#" class="btn_s white" style="min-width:100px;" onclick="inscharge_sub_update();">����</a>
	</span>
</div>

<div>
	<form name="inscharge_sub_form" id = 'inscharge_sub_id_form' class="ajaxForm_inscharge_sub" method="post" action="x1_02_sub_action.php" style="padding:0px 20px;">
		<div style="border-right:1px solid #D5D5D5; border-left:1px solid #D5D5D5; ">
			<div class="tb_type01 view">
				<input type="hidden" name="type" id="type" value="">
				<input type="hidden" name="count" id="count" value="">
				<input type="hidden" name="yymm" id="yymm" value="<?=$_GET['yymm']?>">
				<input type="hidden" name="inscode" id="inscode" value="<?=$_GET['inscode']?>">

				<input type="hidden" name="del_selfbit" id="del_selfbit" value="">
				<input type="hidden" name="del_seq" id="del_seq" value="">
				<table id='subtable'>
					<colgroup>
						<col width="13%">
						<col width="17%">
						<col width="15%">
						<col width="27%">
						<col width="20%">
						<col width="8%">
					</colgroup>
					<thead>
						<tr>
							<th align="center">��������</th>
							<th align="center">�����</th>
							<th align="center">����</th>
							<th align="center">���л󼼸�</th>
							<th align="center">���ؽ���</th>
							<th align="center"></th>
						</tr>
					</thead>
					<tbody>
						<?if(!empty($listData_sub2)){?>
						<?foreach($listData_sub2 as $key => $val){extract($val);?>
						<tr class = 'rowData' rol-seq ='<?=$seq?>'    rol-selfbit ='<?=$selfbit?>'style="cursor:pointer;">
							<td align="center"><?=date("Y-m",strtotime($yymm."01"))?></td>
							<td align="center"><?=$name?></td>
							<td align="center">
								<select name='<?='selfbit'.(string)$key?>' style="width:60%;">
									<option value="1" <?if($listData_sub2[$key]['selfbit']=="1") echo "selected"?>>����</option>
									<option value="2" <?if($listData_sub2[$key]['selfbit']=="2") echo "selected"?>>�̰�</option>
								</select>										
							</td>

							<td>
								<span class="input_type" style="width:95%;margin-left:0px; ">
									<input type="text" name='<?='dataset1'.(string)$key?>' id='' value='<?=$dataset1?>' style="text-align:left" class="input_type_noborder_ip">
								</span>
							</td>

							<td>
								<span class="input_type" style="width:95%;margin-left:0px; ">
									<input type="text" name='<?='dataset2'.(string)$key?>' id='' value='<?=$dataset2?>' style="text-align:right" class="input_type_noborder_ip">
								</span>
							</td>
							<td align="center"><i idata1="<?=$selfbit?>" idata2="<?=$seq?>" class="w3-round yb_icon fa fa-trash-o delAction"  aria-hidden="true" style="border:0px;color:#999999;padding:0px 10px;margin-bottom:-1px;cursor:pointer;"></i></td>

						</tr>
						<?}}else{?>
						<?}?>
					</tbody>

				</table>
				<div id="inscharge_name">
					
				</div>
			</div>
		</div>
	</form>
</div>


<script type="text/javascript">

// �ű�
function inscharge_sub_new() {
	// table element ã��
	const table = document.getElementById('subtable');

	// �� ��(Row) �߰�
	const newRow = table.insertRow();

	// �� ��(Row)�� Cell �߰�
	const newCell1 = newRow.insertCell(0);
	const newCell2 = newRow.insertCell(1);
	const newCell3 = newRow.insertCell(2);
	const newCell4 = newRow.insertCell(3);
	const newCell5 = newRow.insertCell(4);

	// �� �� ���� -1
	//const tbody = table.tBodies[0].rows.length-1;
	const tbody = table.rows.length-2;
	
	var selfbit = "selfbit"+tbody;
	var dataset1name = "dataset1"+tbody;
	var dataset2name = "dataset2"+tbody;

	// Cell�� �ؽ�Ʈ �߰�
	newCell1.innerHTML = '<center><?=date("Y-m",strtotime($_GET["yymm"]."01"))?></center>';
	newCell2.innerHTML = '<center><?=$insname[0]["name"]?></center>';
	newCell3.innerHTML = '<center><select name="'+selfbit+'" style="width:60%;"><option value="1" selected>����</option><option value="2">�̰�</option></select></center>		';
	newCell4.innerHTML = '<center><span class="input_type" style="width:95%;margin-left:0px; "><input type="text" name="'+dataset1name+'" value="" style="text-align:left"						class="input_type_noborder_ip"></span></center>';
	newCell5.innerHTML = '<center><span class="input_type" style="width:95%;margin-left:0px; "><input type="text" name="'+dataset2name+'" value="" style="text-align:right"						class="input_type_noborder_ip"></span></center>';

}

// ����
function inscharge_sub_update(){
	if(confirm("�����Ͻðڽ��ϱ�??")){

		const table = document.getElementById('subtable');
		const tbody = table.rows.length-1;
		
		$("input[name='type']").val("save");
		$("input[name='count']").val(tbody);
		$("form[name='inscharge_sub_form']").submit();
	}

}

$(document).ready(function(){

	// ����ó��
	$(".delAction").click(function(){
		var idx  = $(".delAction").index($(this));
		
		var selfbit  = $(".rowData").eq(idx).attr('rol-selfbit');
		var seq  = $(".rowData").eq(idx).attr('rol-seq');

		$("input[name='del_selfbit']").val(selfbit);
		$("input[name='del_seq']").val(seq);


		if(confirm("�����Ͻðڽ��ϱ�?")){
			$("input[name='type']").val("del");
			$("form[name='inscharge_sub_form']").submit();
		}		 
	})

	var options = { 
		dataType:  'json',
		beforeSubmit:  showRequest_modal_inscharge_sub,  // pre-submit callback 
		success:       processJson_modal_inscharge_sub  // post-submit callback 
	}; 

	$('.ajaxForm_inscharge_sub').ajaxForm(options);

});




// pre-submit callback 
function showRequest_modal_inscharge_sub(formData, jqForm, options) { 
	var queryString = $.param(formData); 
	return true; 
} 
 
// post-submit callback 
function processJson_modal_inscharge_sub(data) { 
	if(data.message){
		alert(data.message);
	}

	if(data.result==""){
		
		opener.$('.btn_search').trigger("click");	//��ȸ��ưŬ��

		if(data.rtype == 'save'){		
			location.href='x1_02_pop.php?yymm='+data.yymm+'&inscode='+data.inscode;
		}else if(data.rtype == 'del'){
			location.href='x1_02_pop.php?yymm='+data.yymm+'&inscode='+data.inscode;
		}else if(data.rtype == 'up'){
		}
	}
}


</script>