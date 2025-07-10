<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");


// 보험사 가져오기
$sql= "select a.scode,a.gubuncode,a.gubunnm,a.gubun,a.sucode , b.suname
		from etc_set a left outer join SUNAME_SET b on a.scode = b.scode and a.sucode = b.SUCODE
		where a.scode = '".$_SESSION['S_SCODE']."'
		order by a.gubuncode";
$qry= sqlsrv_query( $mscon, $sql );
$listData	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
  $listData[] = $fet;
}

sqlsrv_free_stmt($qry);
sqlsrv_close($mscon);

?>

<style>
body{background-image: none;}
</style>

<div class="tit_wrap" style="margin-top:10px">
	<span class="btn_wrap" style="padding-right:20px">
		<a href="#" class="btn_s white" style="min-width:100px;" onclick="inscharge_sub_new();">신규</a>
		<a href="#" class="btn_s white" style="min-width:100px;" onclick="inscharge_sub_update();">저장</a>
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
						<col width="auto">
						<col width="15%">
						<col width="27%">
						<col width="8%">
					</colgroup>
					<thead>
						<tr>
							<th align="center">지급_공제명</th>
							<th align="center">구분</th>
							<th align="center">지급_공제수수료명칭</th>
							<th align="center"></th>
						</tr>
					</thead>
					<tbody>
						<?if(!empty($listData)){?>
						<?foreach($listData as $key => $val){extract($val);?>
						<tr class = 'rowData' rol-seq ='<?=$seq?>'    rol-selfbit ='<?=$selfbit?>'style="cursor:pointer;">
							<td align="left">
								<span class="input_type" style="width:95%;margin-left:0px; ">
									<input type="text" name='<?='gubunnm'.(string)$key?>' id='' value='<?=$gubunnm?>' style="text-align:left" class="input_type_noborder_ip">
								</span>
							</td>
							<td align="center">
								<select name="<?='gubun'.(string)$key?>" id="<?='gubun'.(string)$key?>"> 
									<?foreach($conf['suetc_gubun'] as $key => $val){?>
									<option value="<?=$key?>" <?if($gubun==$key) echo "selected"?>><?=$val?></option>
									<?}?> 
								</select>		
							</td>
							<td align="center"><?=$suname?></td>
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

// 신규
function inscharge_sub_new() {
	// table element 찾기
	const table = document.getElementById('subtable');

	// 새 행(Row) 추가
	const newRow = table.insertRow();

	// 새 행(Row)에 Cell 추가
	const newCell1 = newRow.insertCell(0);
	const newCell2 = newRow.insertCell(1);
	const newCell3 = newRow.insertCell(2);
	const newCell4 = newRow.insertCell(3);

	// 총 행 개수 -1
	//const tbody = table.tBodies[0].rows.length-1;
	const tbody = table.rows.length-2;
	
	var gubunnm = "gubunnm"+tbody;
	var gubun = "gubun"+tbody;
	var dataset2name = "dataset2"+tbody;

	// Cell에 텍스트 추가
	newCell1.innerHTML = '<center><span class="input_type" style="width:95%;margin-left:0px; "><input type="text" name="'+gubunnm+'" id="" value="" style="text-align:left" class="input_type_noborder_ip"></span></center>';
	newCell2.innerHTML = '<center><select name="'+gubun+'" id="'+gubun+'"><?foreach($conf["suetc_gubun"] as $key => $val){?><option value="<?=$key?>" <?if($gubun==$key) echo "selected"?>><?=$val?></option><?}?> </select></center>';


	for(var i = fcnt; i <= tbody+1; i++){
		$('#gubun'+i).on('change', function () {
			var gubun = $(this).val();
		});
	}

}

// 저장
function inscharge_sub_update(){
	if(confirm("저장하시겠습니까??")){

		const table = document.getElementById('subtable');
		const tbody = table.rows.length-1;
		
		$("input[name='type']").val("save");
		$("input[name='count']").val(tbody);
		$("form[name='inscharge_sub_form']").submit();
	}

}

var fcnt = 0;
$(document).ready(function(){

	// 삭제처리
	$(".delAction").click(function(){
		var idx  = $(".delAction").index($(this));
		
		var selfbit  = $(".rowData").eq(idx).attr('rol-selfbit');
		var seq  = $(".rowData").eq(idx).attr('rol-seq');

		$("input[name='del_selfbit']").val(selfbit);
		$("input[name='del_seq']").val(seq);


		if(confirm("삭제하시겠습니까?")){
			$("input[name='type']").val("del");
			$("form[name='inscharge_sub_form']").submit();
		}		 
	})

	// table element 찾기
	const table = document.getElementById('subtable');
	const tbody = table.rows.length-1;
	fcnt = tbody;
	for(var i = 0; i < tbody; i++){
		$('#gubun'+i).on('change', function () {
			var gubun = $(this).val();
			alert(gubun);
		});
	}

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
		
		opener.$('.btn_search').trigger("click");	//조회버튼클릭

		if(data.rtype == 'save'){		
			location.href='x1_02_pop.php?yymm='+data.yymm+'&inscode='+data.inscode;
		}else if(data.rtype == 'del'){
			location.href='x1_02_pop.php?yymm='+data.yymm+'&inscode='+data.inscode;
		}else if(data.rtype == 'up'){
		}
	}
}


</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>