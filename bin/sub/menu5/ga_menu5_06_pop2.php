<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

$sql= "
	select * from
	(
		select a.kcode,a.ksman,b.name,b.inscode
		from ins_ipmst a left outer join inssetup b on a.scode = b.scode and a.inscode = b.inscode and b.useyn = 'Y'
		where a.scode = '".$_SESSION['S_SCODE']."' and a.yymm = '".$_GET['yymm']."' and a.kcode = '".$_GET['kcode']."'
		group by a.kcode,a.ksman,b.name,b.inscode
	) aa ";

$qry	= sqlsrv_query( $mscon, $sql );
extract($fet	= sqlsrv_fetch_array($qry));

/*
echo "<pre>";
echo $sql;
echo "</pre>";
*/
?>
<style>
body{background-image: none;}
</style>

<div class="tit_wrap ipgopop" style="padding-top:10px">

	<div class="tit_wrap" style="margin-top:0px">
		<span class="btn_wrap" style="padding-right:20px">
				<a href="#" class="btn_s white" style="min-width:100px;" onclick="jojik_update();">저장</a>
				<a href="#" class="btn_s white" style="min-width:100px;" onclick="jojik_close();">닫기</a>
		</span>
	</div>


	<form name="excelupg_form" class="ajaxForm_excelupg" method="post" action="ga_menu5_06_action2.php"  style="padding:0px 20px;">
		<input type="hidden" name="yymm" id="yymm" value="<?=$_GET['yymm']?>">
		<input type="hidden" name="type" id="type" value="up">
		<div style="border-right:1px solid #D5D5D5; border-left:1px solid #D5D5D5; ">
			<div class="tb_type01 view">
				<table id="modal_table">
					<colgroup>
						<col width="20%">
						<col width="auto">

					</colgroup>
					<tbody>
						<tr>
							<th><em class="impor">*</em>증권번호</th>
							<td style="height:30px">
								<span class="input_type" style="width:100%">
									<input type="text" name="kcode" id="kcode" value="<?=$kcode?>" style="padding-left:0px" maxlength="10">
								</span> 				
							</td>
						</tr>
						<tr>
							<th><em class="impor">*</em>보험사</th>
							<td>
								<span class="input_type" style="width:100%">
									<input type="text" name="name" id="name" value="<?=$name?>" style="padding-left:0px">
								</span> 	
							</td>
						</tr>
						<tr>
							<th><em class="impor">*</em>사용인코드</th>
							<td>
								<span class="input_type" style="width:40%"><input type="text" name="ksman" id="ksman" value="<?=trim($ksman)?>" onclick="SwonSearch('<?=$inscode?>');" readonly></span>
								<a href="javascript:SwonSearch();" class="btn_s white">검색</a>
								<span class="sname" style="width:40%;margin-left:5px"></span>			
							</td>
						</tr>
					</tbody>
				</table>	
			</div>
		</div>
	</form>

</div>

<script type="text/javascript">

// 사원 팝업
function SwonSearch(inscode){
	var left = Math.ceil((window.screen.width - 800)/2);
	var top = Math.ceil((window.screen.height - 800)/2);
	var popOpen	= window.open("<?=$conf['homeDir']?>/sub/help/ga_inswon_search.php?inscode="+inscode,"swonpop","width=500px,height=400px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
	popOpen.focus();
}

function setSwonValue(row,code,name){
	$("#ksman").val(code);
	$('.sname').text(name);
}

// 저장
function jojik_update(){

	var ksman = $("form[name='excelupg_form'] input[name='ksman']").val();
	
	if(isEmpty(ksman) == true){
		alert('사용인코드를 선택해주세요.');
	}else{
		if(confirm("저장하시겠습니까?")){
			$("form[name='excelupg_form']").submit();
		}
	}
}

// 닫기
function jojik_close(){	
	self.close();
}


$(document).ready(function(){
	
/*
	if('<?=$_GET['save']?>' == 'Y'){
		opener.location.reload();
	}
*/
	$("form[name='excelupg_form'] #kcode").css("backgroundColor","#EAEAEA");
	document.getElementById('kcode').readOnly=true;
	$("form[name='excelupg_form'] #name").css("backgroundColor","#EAEAEA");
	document.getElementById('name').readOnly=true;

	var options = { 
		dataType:  'json',
		beforeSubmit:  showRequest_modal_excelupg,  // pre-submit callback 
		success:       processJson_modal_excelupg  // post-submit callback 
	}; 

	$('.ajaxForm_excelupg').ajaxForm(options);

});


// pre-submit callback 
function showRequest_modal_excelupg(formData, jqForm, options) { 
	var queryString = $.param(formData); 
	return true; 
} 
 
// post-submit callback 
function processJson_modal_excelupg(data) { 
	console.log(data);
	if(data.message){
		alert(data.message);
	}

	if(data.result==""){
		opener.$('.btn_search').trigger("click");	//조회버튼클릭
		self.close();
	}
}

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>