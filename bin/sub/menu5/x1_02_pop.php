<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

$type ="";


if($_GET['yymm'] and $_GET['inscode']){
	$sql= "	
			select a.scode,a.yymm,a.inscode , b.name , 
					a.dataset1 , a.dataset2 , a.dataset3 , a.dataset4 , a.dataset5 , a.dataset6 , a.dataset7 , a.dataset8 , a.dataset9 , a.dataset10 ,
					row_number()over(order by a.yymm desc , a.inscode asc) rnum
			from INSCHARGE_SET a left outer join inssetup b on a.inscode = b.inscode
			where a.SCODE =  '".$_SESSION['S_SCODE']."'  and  a.YYMM = '".$_GET['yymm']."' and a.inscode = '".$_GET['inscode']."' 
			";
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


// 보험사 가져오기
$sql= "select a.scode, a.inscode , b.name
		from inscharge_nameset a left outer join inssetup b on a.scode = b.scode and a.inscode = b.inscode
		where a.scode = '".$_SESSION['S_SCODE']."'
		group by a.scode , a.inscode , b.name";
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


<div class="tit_wrap inschargepop" style="padding-top:10px;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none" >
	<div class="tit_wrap">
		<span class="btn_wrap" style="padding-right:20px">
			<a href="#" class="btn_s white" style="min-width:100px;" onclick="inscharge_new();">신규</a>
			<a href="#" class="btn_s white" style="min-width:100px;" onclick="inscharge_update();">저장</a>
			<a href="#" class="btn_s white" style="min-width:100px;" onclick="inscharge_delete();">삭제</a>
			<a href="#" class="btn_s white" style="min-width:100px;" onclick="inscharge_close();">닫기</a>


		</span>
	</div>

	<div>
		<form name="inscharge_form" id = 'inscharge_id_form' class="ajaxForm_inscharge" method="post" action="x1_02_action.php" style="padding:0px 20px;">
		<div style="border-right:1px solid #D5D5D5; border-left:1px solid #D5D5D5; ">
			<div class="tb_type01 view">
				<input type="hidden" name="type" id="type" value="<?=$type?>">
				<input type="hidden" name="yymm" id="yymm" value="<?=$listData[0]['yymm']?>">
				<input type="hidden" name="inscode" id="inscode" value="<?=$listData[0]['inscode']?>">
				<table>
					<colgroup>
						<col width="18%">
						<col width="32%">
						<col width="18%">
						<col width="32%">
					</colgroup>
					<tbody>

					<tr>
						<th><em class="impor">*</em>마감월</th>
						<td style="height:30px">
							<span class="input_type date ml10" style="width:50%;margin-left: 0px;">
								<input type="text" class="Cal_ym" id="yymm_s" name="yymm_s" value="<?=$listData[0]['yymm']?>" readonly disabled>									
							</span>
						</td>
						<th><em class="impor">*</em>보험사</th>
						<td style="height:30px">
							<select name="inscode_s" id="inscode_s" style="width:50%;background-color:#EAEAEA" disabled> 		
							  <option value="">선택</option>
							  <?foreach($insData as $key => $val){?>
							  <option value="<?=$val['inscode']?>" <?if($listData[0]['inscode']==$val['inscode']) echo "selected"?>><?=$val['name']?></option>
							  <?}?>
							</select>										
						</td>
					</tr>

					</tbody>
					<tfoot>
						<th align="center" style="background-color: cornsilk;top:0px;color:black;">보험사별 규정명</th>
						<th align="center" style="background-color: cornsilk;top:0px;color:black;">금액 / 요율</th>
						<th align="center" style="background-color: cornsilk;top:0px;color:black;">보험사별 규정명</th>
						<th align="center" style="background-color: cornsilk;top:0px;color:black;">금액 / 요율</th>
					</tfoot>
				</table>
				<div id="inscharge_name">
					
				</div>
			</div>
		</div>
		<div align="right" style="margin-top:10px"><b style="color:#E0844F"><em class="impor">*</em>등록하려는 보험사가 없다면 보험사 수수료 기초설정을 먼저 진행해주세요.</b></div>
		</form>
		<?if($_GET['inscode'] == '00021'){?>
		<div id="inscharge_sub">
			
		</div>
		<?}?>

	</div>
</div>

<script type="text/javascript">

// 신규
function inscharge_new(){

	location.href='x1_02_pop.php';
}

// 저장
function inscharge_update(){
	var type   = $("form[name='inscharge_form'] input[name='type']").val();

	if(type == "up"){
		var yymm = document.getElementById("yymm").value;
		var inscode = document.getElementById("inscode").value;

		if(isEmpty(yymm) == true){
			alert('마감월이 존재하지않습니다.');
		}else if(isEmpty(inscode) == true){
			alert('보험사가 존재하지않습니다.');
		}else{
			if(confirm("저장하시겠습니까?")){
				$("form[name='inscharge_form']").submit();
			}
		}
	}else if(type == "in"){
		var yymm = document.getElementById("yymm_s").value;
		var inscode = document.getElementById("inscode_s").value;

		if(isEmpty(yymm) == true){
			alert('마감월이 존재하지않습니다.');
		}else if(isEmpty(inscode) == true){
			alert('보험사가 존재하지않습니다.');
		}else{
			if(confirm("신규등록 하시겠습니까?")){
				$("form[name='inscharge_form']").submit();
			}
		}
	}

}

// 삭제
function inscharge_delete(){
	var type   = $("form[name='inscharge_form'] input[name='type']").val();

	if(type == "up"){
		if(confirm("삭제하시겠습니까?")){
			document.inscharge_form.type.value='del';
			$("form[name='inscharge_form']").submit();
		}
	}else{
		alert("삭제할 대상이 없습니다.");
	}
}

// 닫기
function inscharge_close(){	
	self.close();
	//opener.$('.btn_search').trigger("click");		
}

$(document).ready(function(){
	
	if('<?=$type?>'=="in"){
		$("#yymm_s").attr("disabled",false);
		$("form[name='inscharge_form'] #yymm_s").css("backgroundColor","transparent");

		$("#inscode_s").attr("disabled",false);
		$("form[name='inscharge_form'] #inscode_s").css("backgroundColor","transparent");

	}

	$("#inscode_s").change(function(){
		var formData = $("#inscharge_id_form").serialize();	
		//alert(formData);
		ajaxLodingTarket('x1_02_pop2.php',$('#inscharge_name'),formData);
	})

	if('<?=$type?>'=="in"){		
		var id = opener.$('#id').val();
		id = id.substring(2,7);
		$("#inscode_s").val(id).trigger("change");
	}

	if('<?=$type?>'=="up"){
		var formData = $("#inscharge_id_form").serialize();	
		ajaxLodingTarket('x1_02_pop2.php',$('#inscharge_name'),formData);
	}

	if('<?=$type?>'=="up"){
		var formData = $("#inscharge_id_form").serialize();	
		ajaxLodingTarket('x1_02_sub_pop.php',$('#inscharge_sub'),formData);
	}

	var options = { 
		dataType:  'json',
		beforeSubmit:  showRequest_modal_inscharge,  // pre-submit callback 
		success:       processJson_modal_inscharge  // post-submit callback 
	}; 

	$('.ajaxForm_inscharge').ajaxForm(options);

});

// pre-submit callback 
function showRequest_modal_inscharge(formData, jqForm, options) { 
	var queryString = $.param(formData); 
	return true; 
} 
 
// post-submit callback 
function processJson_modal_inscharge(data) { 
	if(data.message){
		alert(data.message);
	}

	if(data.result==""){
		
		opener.$('.btn_search').trigger("click");	//조회버튼클릭

		if(data.rtype == 'in'){		
			location.href='x1_02_pop.php?yymm='+data.yymm+'&inscode='+data.inscode;
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