<?
//error_reporting(E_ALL); ini_set('display_errors', 1);
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");


$sql= "
	select tel2+'-'+tel3 tel , name
	from company
	where scode = '".$_SESSION['S_SCODE']."'
		";
$qry	= sqlsrv_query( $mscon, $sql );
extract($fet= sqlsrv_fetch_array($qry));

if($_GET['sms_type']=="sms_car_gun"){
	$gubun = "3";
}else{
	$gubun = "1";
}

// 템플릿 가져오기
$sql= "
	select scode,code,gubun,contents,attach,indate, convert(varchar,rnum)+'번 양식' rnum
	from(
		select scode,code,gubun,contents,attach,indate , row_number() over(partition by scode , gubun order by indate asc) rnum
		from template
		where scode = '".$_SESSION['S_SCODE']."' and gubun = '".$gubun."'
		) aa
	";

$qry= sqlsrv_query( $mscon, $sql );
$tempData	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
  $tempData[] = $fet;
}

$where = Decrypt_where($_GET['where'],$secret_key,$secret_iv);

sqlsrv_free_stmt($qry);
sqlsrv_close($mscon);

?>
<style>
body{background-image: none;}
</style>

<div class="tit_wrap ipgopop" style="padding-top:10px">

	<div class="tit_wrap" style="margin-top:0px">
		<span class="btn_wrap" style="padding-right:20px">
				<a href="#" class="btn_s white" style="min-width:100px;" onclick="sms_update();">알림톡전송</a>
				<a href="#" class="btn_s white" style="min-width:100px;" onclick="sms_close();">닫기</a>
		</span>
	</div>


	<form name="kakao_form" class="ajaxForm_sms" method="post" action="kakao_pop_action.php"  style="padding:0px 20px;">
		<div style="border-right:1px solid #D5D5D5; border-left:1px solid #D5D5D5; ">
			<div class="tb_type01 view">
				<input type="hidden" name="type" value="<?=$_GET['sms_type']?>">
				<input type="hidden" name="name" value="<?=$name?>">
				<input type="hidden" name="sdate1" value="<?=str_replace('-','',$_GET['sdate1'])?>">
				<input type="hidden" name="sdate2" value="<?=str_replace('-','',$_GET['sdate2'])?>">
				<input type="hidden" name="where" value="<?=$where?>">
				<input type="hidden" name="tel" value="<?=str_replace('-','',$tel)?>">
				<table id="modal_table">
					<colgroup>
						<col width="20%">
						<col width="auto">

					</colgroup>
					<tbody>
						<tr>
							<th>회사명</th>
							<td><?=$name?></td>
						</tr>
						<tr>
							<th>발신자번호</th>
							<td><?=$tel?></td>
						</tr>
						<tr>
							<th rowspan="2">알림톡양식</th>
							<td>
								<select name="template" id="template" style="width:50%"> 		
								  <option value="">선택</option>
								  <?foreach($tempData as $key => $val){?>
								  <option value="<?=$val['code']?>"><?=$val['rnum']?></option>
								  <?}?>
								</select>								
							</td>
						</tr>
						<tr>
							
							<td>
								<textarea name="bigo" id="bigo" value="<?=$bigo?>" style="width:219px;height:380px" ></textarea>
							</td>
						</tr>
					</tbody>
				</table>	
			</div>
		</div>
		<div align="right"><b style="color:#E0844F"><em class="impor">*</em>정상적인 연락처가 존재하는 건만 발송됩니다.</b></div>
	</form>

</div>

<script type="text/javascript">

// 저장
function sms_update(){
	
	var template = $("#template").val();
	var cnt = '<?=$_GET["cnt"]?>';

	var message = "";
	message = "알림톡을 "+cnt+"건 발송하시겠습니까?";

	if(isEmpty(template) == true){
		alert('알림톡양식을 선택해주세요.');
	}else{
		if(confirm(message)){
			$("form[name='kakao_form']").submit();
		}
	}
}

// 닫기
function sms_close(){	
	self.close();
}

$(document).ready(function(){


	$('#template').on('change',function(){
		var selData = this.value;

		if(isEmpty(selData) == true){
			$("#bigo").css("background-image","");
		}else{
			$("#bigo").css("background-image","url(/bin/image/"+selData+".jpg)");
		}

	});

	$('#template').val('<?=$tempData[0]["code"]?>');
	$("#bigo").css("background-image","url(/bin/image/"+'<?=$tempData[0]["code"]?>'+".jpg)");

	var options = { 
		dataType:  'json',
		beforeSubmit:  showRequest_modal_kakao,  // pre-submit callback 
		success:       processJson_modal_kakao  // post-submit callback 
	}; 

	$('.ajaxForm_sms').ajaxForm(options);

});


// pre-submit callback 
function showRequest_modal_kakao(formData, jqForm, options) { 
	var queryString = $.param(formData); 
	return true; 
} 
 
// post-submit callback 
function processJson_modal_kakao(data) { 
	console.log(data);
	if(data.message){
		alert(data.message);
	}

	if(data.result==""){
		self.close();
	}
}

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>