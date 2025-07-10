<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

/*
	관리자 비트 현재 미설정
	A:관리자
*/
$sbit = $_SESSION['S_MASTER'];

$type	= ($_GET['seq']) ? "up" : "in";


if($_GET['seq']){
	$sql= "
			select  a.seq,
					a.sdate,
					a.title,
					a.bigo,
					a.gubun,
					case when a.gubun = '1' then '전체' else '개인' end gubun_nm,
					a.status,
					case when a.status = '1' then '진행중' else '완료' end status_nm,
					case when a.gubun = '1' and status = '1' then '#F15F5F' 
						 when a.gubun = '1' and status = '2' then '#d5d5d5' 
						 when a.gubun = '2' and status = '1' then '#6799FF' 
						 when a.gubun = '2' and status = '2' then '#d5d5d5' 
						 else '#6799FF' end color,
					convert(varchar(30),a.idate,120) as idate
			from schd(nolock) a
			where a.scode = '".$_SESSION['S_SCODE']."'
			  and a.seq = '".$_GET['seq']."' ";

	$qry	= sqlsrv_query( $mscon, $sql );
	extract($fet= sqlsrv_fetch_array($qry));

	$update		=	$idate;
}else{
	$update		=	date("Y-m-d H:i:s");
}



sqlsrv_free_stmt($result);
sqlsrv_close($mscon);

?>

<style>
body{background-image: none;}

.tb_type01.view .lowpop th, .tb_type01.view .lowpop td {
    padding:7px 12px;
    text-align: left;
}

</style>

<div class="container container_bk">
	<div class="content_wrap">
		<fieldset>

			<div class="tit_wrap mt20">
				<span class="btn_wrap">
					<a href="#" class="btn_s white" onclick="sch_insert();" style="width:70px">신규</a>
					<a href="#" class="btn_s white"  onclick="sch_update();" style="width:70px">저장</a>
					<a href="#" class="btn_s white" onclick="sch_delete();" style="width:70px">삭제</a>
					<a href="#" class="btn_s white" onclick="btn_close();" style="width:70px">닫기</a>
				</span>
			</div>

			<!-- //box_gray -->
				<div class="tb_type01 view">
					<form name="schd_form" class="ajaxForm_schd" method="post" action="ga_menu7_04_pop_action.php">
					<input type="hidden" name="type" value="<?=$type?>">
					<input type="hidden" name="skey"  value="<?=$_SESSION['S_SKEY']?>">
					<input type="hidden" name="seq"  value="<?=$_GET['seq']?>">
						<table class="lowpop">
							<colgroup>
								<col width="18%">
								<col width="32%">
								<col width="18%">
								<col width="32%">
							</colgroup>
						<tbody>
							<tr>
								<th>구분</th>
								<td>
									<input type="radio" class="gubun updis" name="gubun" id="gubun1" value="1" <?if(trim($gubun)=='1') echo "checked";?>><label for="gubun1">전체 </label>&nbsp;&nbsp;&nbsp;
									<input type="radio" class="gubun updis" name="gubun" id="gubun2" value="2" <?if(trim($gubun)=='2') echo "checked";?>><label for="gubun2">개인</label>
								</td>
								<th><em class="impor">*</em>진행상태</th>
								<td>
									<select name="status" id="status"style="width:150px;"> 
										<?foreach($conf['sch_status'] as $key => $val){?>
										<option value="<?=$key?>" <?if($status==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>	
								</td>
							</tr>
							<tr>
								<th><em class="impor">*</em>일자</th>
								<td colspan=3>
									<span class="input_type date" style="width:100%"><input type="text" class="Calnew" name="sdate" id="sdate" value="<?if($sdate) echo date("Y-m-d",strtotime($sdate));?>" readonly></span> 
								</td>
							</tr>
							<tr>
								<th><em class="impor">*</em>제목</th>
								<td colspan=3>
									<span class="input_type" style="width:100%"><input type="text" name="title" id="title" value="<?=$title?>"></span> 
								</td>
							</tr>
							<tr>
								<th>내용</th>
								<td colspan=3>
									<span class="input_type" style="width:100%"><input type="text" name="bigo" value="<?=$bigo?>"></span> 
								</td>
							</tr>
						</tbody>
						</table>
					</form>
				</div>
				<!-- // tb_type01 -->

				<div class="tit_wrap" style="margin-top:5px">		
					<span class="btn_wrap">
						<span style="margin-left:15px" class="font_blue">등록일시 : <?=$update?></span>				
					</span>
				</div>

		</fieldset>

		<p class="mgt5 font_red font600">* 관리자 권한을 가진 사원이 전체 일정을 등록/관리 할 수 있습니다.</p>
	</div>
</div>
<!-- // popup_wrap -->

 </body>
</html>

<script type="text/javascript">


// 신규
function sch_insert(){

	var today = new Date();   

	var year = today.getFullYear();
	var month = ('0' + (today.getMonth() + 1)).slice(-2);
	var day = ('0' + today.getDate()).slice(-2);

	var dateString = year + '-' + month  + '-' + day;
	
	document.schd_form.type.value='in';
	document.schd_form.seq.value='';
	document.schd_form.sdate.value=dateString;
	document.schd_form.title.value='';
	document.schd_form.bigo.value='';
	document.schd_form.status.value='1';

	data_write();
}

// 관리자 권한 구분
function data_write(){

	var type = '<?=$type?>';
	var sbit = '<?=$sbit?>';


	if(type == 'in'){
		if(sbit != 'A'){
			$('.updis').attr('disabled', true);
			$(":radio[name$='gubun']").val([2]);
		}else{
			$(":radio[name$='gubun']").val([1]);
		}
	}else{
		if(sbit != 'A'){
			$('.updis').attr('disabled', true);
		}		
	}	
}

// 저장
function sch_update(){	

	var title	= $("form[name='schd_form'] input[name='title']").val();

	if(isEmpty(title) == true){
		alert('제목을 입력해 주세요.');
		document.getElementById('title').focus();
	}else{
		if(confirm("저장하시겠습니까?")){
			$('.updis').attr('disabled', false);
			$("form[name='schd_form']").submit();
		}
	}
}

// 삭제
function sch_delete(){
	var type   = $("form[name='schd_form'] input[name='type']").val();

	if(type == "up"){
		if(confirm("삭제하시겠습니까?")){
			document.atongha_form.type.value='del';
			$("form[name='schd_form']").submit();
		}
	}else{
		alert("삭제할 대상이 없습니다.");
	}
}

// 닫기
function btn_close(){	
	window.close();
	//opener.reset();
}



// ajax 호출
var btnAction	= true;
	
$(document).ready(function(){

	// 데이터구분 수정불가
	var type = '<?=$type?>';
	if(type == 'in'){
		sch_insert();
	}else{
		data_write();
	}


	var options = { 
		dataType:  'json',
		beforeSubmit:  showRequest_modal_sch,  // pre-submit callback 
		success:       processJson_modal_sch  // post-submit callback 
	}; 

	$('.ajaxForm_schd').ajaxForm(options);
});

// pre-submit callback 
function showRequest_modal_sch(formData, jqForm, options) { 
	var queryString = $.param(formData); 
	return true; 
} 
 
// post-submit callback 
function processJson_modal_sch(data) { 
	console.log(data);
	if(data.message){
		alert(data.message);
		//location.reload();
	}

	if(data.result==''){
		// 성공시
		if(data.rtype == 'in'){
			document.schd_form.type.value = 'up';
			document.schd_form.skey.value = data.skey;
			document.schd_form.seq.value = data.seq;
		}else if(data.rtype == 'del'){
			document.schd_form.type.value = 'in';
			sch_insert();			
		}

		data_write();
		opener.reset();	// 저장 후 오프너 화면 새로고침 선언
	}

}

</script>



 

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>