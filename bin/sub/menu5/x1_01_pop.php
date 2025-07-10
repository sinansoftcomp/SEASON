<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

// 수당규정. 추후에 COMPANY테이블에서 가져올것.
$X = "X1";
$type ="";

for($i=1;$i<=100;$i++){
	//$select .= "jiyul".$i." ,";
	$select .= "isnull(jiyul".$i.",0.000) jiyul".$i." ,";
}

if($_GET['skey'] and $_GET['inscode'] and $_GET['insilj'] and $_GET['seq']){
	$sql= "	select ".$select." a.scode,a.skey,a.inscode,a.insilj,a.seq,a.jsyymm,a.jeyymm , b.sname,
					case when a.inscode = '00000' then '통합' else c.name end insname
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

// 보험사 가져오기
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
			<a class="btn_s white" style="min-width:100px;" onclick="sjirule_new();">신규</a>
			<a class="btn_s white" style="min-width:100px;" onclick="sjirule_update();">저장</a>
			<a class="btn_s white" style="min-width:100px;" onclick="sjirule_delete();">삭제</a>
			<a class="btn_s white" style="min-width:100px;" onclick="sjirule_close();">닫기</a>

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
						<th><em class="impor">*</em>사원코드</th>
						<td style="height:30px">
							<span class="input_type" style="width:87%">
								<input type="text" name="skey_s" id="skey_s" value="<?=$listData[0]['skey']?>" onclick="swonSearch();" readonly disabled>			
							</span> 	
							<a href="#" class="btn_s white" onclick="swonSearch();" name="skeys" id="skeys" style="display:none">검색</a>
						</td>
						<th><em class="impor">*</em>사원명</th>
						<td><b style="width:100%" name="sname" id="sname"><?=$listData[0]['sname']?></b></td>
					</tr>
					<tr>
						<th><em class="impor">*</em>보험사</th>
						<td style="height:30px">
							<select name="inscode_s" id="inscode_s" style="width:50%;background-color:#EAEAEA" disabled> 		
							  <option value="">선택</option>
							  <option value="00000" <?if($listData[0]['inscode']=="00000") echo "selected"?>>통합</option>
							  <?foreach($insData as $key => $val){?>
							  <option value="<?=$val['inscode']?>" <?if($listData[0]['inscode']==$val['inscode']) echo "selected"?>><?=$val['name']?></option>
							  <?}?>
							</select>										
						</td>
						<th><em class="impor">*</em>상품보종</th>
						<td style="height:30px">
							<select name="insilj_s" id="insilj_s" style="width:50%;background-color:#EAEAEA" disabled> 		
							  <option value="">선택</option>
							  <option value="1" <?if($listData[0]['insilj']=="1") echo "selected"?>>일반</option>
							  <option value="2" <?if($listData[0]['insilj']=="2") echo "selected"?>>생손보 장기</option>
							  <option value="3" <?if($listData[0]['insilj']=="3") echo "selected"?>>자동차</option>
							</select>										
						</td>
					</tr>
					<tr>
						<th><em class="impor">*</em>지급시작일자</th>
						<td>
							<span class="input_type date ml10" style="width:100%;margin-left:0px">
								<input type="text" class="Calnew" name="jsyymm" id="jsyymm" value="<? if(trim($listData[0]['jsyymm'])) echo  date("Y-m-d",strtotime($listData[0]['jsyymm']));?> "  readonly>	
							</span> 	
						</td>
						<th><em class="impor">*</em>지급종료일자 <?if($type=='in'){?><span style="color:#E0844F">( 미입력시 자동 9999-12-31 )</span><?}?></th>
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
						<th align="center" style="background-color: cornsilk;top:0px;">회차</th>
						<th align="center" style="background-color: cornsilk;top:0px;">지급율</th>
						<th align="center" style="background-color: cornsilk;top:0px;">회차</th>
						<th align="center" style="background-color: cornsilk;top:0px;">지급율</th>
						<th align="center" style="background-color: cornsilk;top:0px;">회차</th>
						<th align="center" style="background-color: cornsilk;top:0px;">지급율</th>
						<th align="center" style="background-color: cornsilk;top:0px;">회차</th>
						<th align="center" style="background-color: cornsilk;top:0px;">지급율</th>
						<th align="center" style="background-color: cornsilk;top:0px;">회차</th>
						<th align="center" style="background-color: cornsilk;top:0px;">지급율</th>
						<th align="center" style="background-color: cornsilk;top:0px;">회차</th>
						<th align="center" style="background-color: cornsilk;top:0px;">지급율</th>
						<th align="center" style="background-color: cornsilk;top:0px;">회차</th>
						<th align="center" style="background-color: cornsilk;top:0px;">지급율</th>
						<th align="center" style="background-color: cornsilk;top:0px;">회차</th>
						<th align="center" style="background-color: cornsilk;top:0px;">지급율</th>
						<th align="center" style="background-color: cornsilk;top:0px;">회차</th>
						<th align="center" style="background-color: cornsilk;top:0px;">지급율</th>
						<th align="center" style="background-color: cornsilk;top:0px;">회차</th>
						<th align="center" style="background-color: cornsilk;top:0px;">지급율</th>
					</thead>
					<tbody>
						<?for($i=0;$i<10;$i++){?>
						<tr>
							<?for($j=($i*10)+1;$j<=($i+1)*10;$j++){?>
							<th align="center" <?if($j==100){?> style="color:hotpink;" <?}?> ><?=$j?>회차</th>
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
		<div align="right" style="margin-top:10px"><b style="color:#E0844F"><em class="impor">*</em>100회차 이후 모든 지급율은 100회차에 입력된 지급율만큼 지급됩니다.</b></div>
		</form>
	</div>
</div>

<script type="text/javascript">

// 신규
function sjirule_new(){

	location.href='x1_01_pop.php';
}

// 저장
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
			alert('사원코드가 존재하지않습니다.');
		}else if(isEmpty(inscode) == true){
			alert('보험사가 존재하지않습니다.');
		}else if(isEmpty(insilj) == true){
			alert('상품보종이 존재하지않습니다.');
		}else if(isEmpty(seq) == true){
			alert('순번이 존재하지않습니다.');
		}else if(isEmpty(jsyymm) == true){
			alert('지급시작일자가 존재하지않습니다.');
		}else if(isEmpty(jeyymm) == true){
			alert('지급종료일자가 존재하지않습니다.');
		}else{
			if(confirm("저장하시겠습니까?")){
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
			alert('사원코드가 존재하지않습니다.');
		}else if(isEmpty(inscode) == true){
			alert('보험사가 존재하지않습니다.');
		}else if(isEmpty(insilj) == true){
			alert('상품보종이 존재하지않습니다.');
		}else if(isEmpty(jsyymm) == true){
			alert('지급시작일자가 존재하지않습니다.');
		}else if(isEmpty(jeyymm) == true){
			alert('지급종료일자가 존재하지않습니다.');
		}else{
			if(confirm("신규등록 하시겠습니까?")){
				$("form[name='sjirule_form']").submit();
			}
		}
	}

}

// 삭제
function sjirule_delete(){
	var type   = $("form[name='sjirule_form'] input[name='type']").val();

	if(type == "up"){
		if(confirm("삭제하시겠습니까?")){
			document.sjirule_form.type.value='del';
			$("form[name='sjirule_form']").submit();
		}
	}else{
		alert("삭제할 대상이 없습니다.");
	}
}

// 닫기
function sjirule_close(){	
	self.close();
	//opener.$('.btn_search').trigger("click");		
}

// 사원리스트 팝업
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
		
		opener.$('.btn_search').trigger("click");	//조회버튼클릭

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