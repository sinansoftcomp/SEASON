<?
//error_reporting(E_ALL); ini_set('display_errors', 1);
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");


$jik	=	$_SESSION['S_JIK'];		// 영업직위
$master	=	$_SESSION['S_MASTER'];	// 관리자여부

if(isset($_GET['pid'])){
	$type="up";
	$sql	= "	
				select a.scode,a.pid,a.gubun, a.recv,recvnm,
					   a.title,a.msg,a.jocnt,a.topsort, a.color,
					   convert(varchar(8),a.idate,112) idate,a.iswon,a.udate,a.uswon,iswon.sname,
						datediff(hour,a.idate,getdate()) ntime , row_number() over(order by topsort desc , a.idate desc) rnum
				from postlist a 			
					left outer join bonbu b on a.scode = b.scode and a.recv = b.bcode
					left outer join jisa js on a.scode = js.scode and a.recv = js.jscode
					left outer join jijum j on a.scode = j.scode and a.recv = j.jcode
					left outer join team t on a.scode = t.scode and a.recv = t.tcode
					left outer join swon s on a.scode = s.scode and a.recv = s.skey
					left outer join swon iswon on a.scode = iswon.scode and a.iswon = iswon.skey
				where a.scode = '".$_SESSION['S_SCODE']."' and a.pid = '".$_GET['pid']."' ";
	$qry	= sqlsrv_query( $mscon, $sql );
	//extract($fet= sqlsrv_fetch_array($qry));
	if ($qry === false) {
		die(print_r(sqlsrv_errors(), true)); 
	}
	$fet = sqlsrv_fetch_array($qry, SQLSRV_FETCH_ASSOC);
	if ($fet !== false) {
		extract($fet);
	}
	sqlsrv_free_stmt($qry);
	sqlsrv_close($mscon);
}else{
	$type="in";
}

?>
<style>
body{background-image: none;}
</style>


<div class="tit_wrap" style="padding-top:10px">
	<div class="tit_wrap" style="margin-top:0px">
		<h3 class="tit_sub" style="margin-left:20px">알림장 작성</h3>
	</div>


	<form name="post_form" class="ajaxForm_post" method="post" action="ga_menu7_06_action.php" style="padding:0px 20px;">
		<div style="border-right:1px solid #D5D5D5; border-left:1px solid #D5D5D5; ">
			<div class="tb_type01 view">
				<input type="hidden" name="type" value="<?=$type?>">
				<input type="hidden" name="pid" value="<?=$_GET['pid']?>">
				<input type="hidden" name="level" id="level" value="">
				<table id="modal_table">
					<colgroup>
						<col width="20%">
						<col width="30%">
						<col width="20%">
						<col width="30%">

					</colgroup>
					<tbody>
						<tr>
							<th><em class="impor">*</em>구분</th>
							<td colspan=3 style="height:30px">
								<input type="checkbox" class="gubun" name="gubun" id="gubun1" value="1" <?if(trim($gubun)=='1') echo "checked";?> onclick="checkOnlyOne(this)"><label for="gubun1">전체 </label>&nbsp;&nbsp;&nbsp;
								<input type="checkbox" class="gubun" name="gubun" id="gubun2" value="2" <?if(trim($gubun)=='2') echo "checked";?> onclick="checkOnlyOne(this)"><label for="gubun2">본부 </label>&nbsp;&nbsp;&nbsp;
								<input type="checkbox" class="gubun" name="gubun" id="gubun3" value="3" <?if(trim($gubun)=='3') echo "checked";?> onclick="checkOnlyOne(this)"><label for="gubun3">지사 </label>&nbsp;&nbsp;&nbsp;
								<input type="checkbox" class="gubun" name="gubun" id="gubun4" value="4" <?if(trim($gubun)=='4') echo "checked";?> onclick="checkOnlyOne(this)"><label for="gubun4">지점</label>&nbsp;&nbsp;&nbsp;			
								<input type="checkbox" class="gubun" name="gubun" id="gubun5" value="5" <?if(trim($gubun)=='5') echo "checked";?> onclick="checkOnlyOne(this)"><label for="gubun5">팀</label>&nbsp;&nbsp;&nbsp;	
								<input type="checkbox" class="gubun" name="gubun" id="gubun6" value="6" <?if(trim($gubun)=='6') echo "checked";?> onclick="checkOnlyOne(this)"><label for="gubun6">개인</label>
							</td>
						</tr>
						<tr>
							<th><em class="impor">*</em>대상</th>
							<td colspan=3 >
								<a class="btn_s white ser_recv" onclick="recv_ser();" style="min-width:80px;display:none" ><i class="fa-solid fa-magnifying-glass fa-lg mgr3"></i>찾기</a>
								<input type="hidden" name="recv" id="recv" value="<?=$recv?>">
								<input type="hidden" name="recvnm" id="recvnm" value="<?=$recvnm?>">
								<span style="width:40%;margin-left:5px" id="recvnm2"><?=trim($recvnm)?></span>
							</td>
						</tr>
						<tr>
							<th><em class="impor">*</em>제목</th>
							<td colspan=3 ><span class="input_type" style="width:100%" id="skey_input"><input type="text" name="title" id="title" value="<?=$title?>"></span></td>
						</tr>
						<tr>
							<th><em class="impor">*</em>내용</th>
							<td colspan=3 ><textarea name="msg" id="msg" style="width:100%;height:200px"><?=$msg?></textarea></td>
						</tr>
						<tr>
							<th>제목색상</th>
							<td colspan=3 >
								<input type="radio" class="color" name="color" id="color1" value="1" <?if(trim($color)=='1') echo "checked";?>><label for="color1">검정 </label>&nbsp;&nbsp;&nbsp;
								<input type="radio" class="color" name="color" id="color2" value="2" <?if(trim($color)=='2') echo "checked";?>><label for="color2" style="color:#f9650e">빨강 </label>&nbsp;&nbsp;&nbsp;
								<input type="radio" class="color" name="color" id="color3" value="3" <?if(trim($color)=='3') echo "checked";?>><label for="color3" style="color:#1266FF">파랑 </label>&nbsp;&nbsp;&nbsp;
								<input type="radio" class="color" name="color" id="color4" value="4" <?if(trim($color)=='4') echo "checked";?>><label for="color4" style="color:#f9b300">주황</label>&nbsp;&nbsp;&nbsp;			
								<input type="radio" class="color" name="color" id="color5" value="5" <?if(trim($color)=='5') echo "checked";?>><label for="color5" style="color:#8041D9">보라</label>&nbsp;&nbsp;&nbsp;	
								<input type="radio" class="color" name="color" id="color6" value="6" <?if(trim($color)=='6') echo "checked";?>><label for="color6" style="color:#2F9D27">초록</label>	
							</td>
						</tr>
						<tr>
							<th>최상단정렬</th>
							<td <?if($type=="in"){?>colspan=3<?}?>>
								<input type = "checkbox" name="topsort" id="topsort" <?if($topsort=="Y"){?>checked<?}?> / >	
							</td>
							<th id="pushsendh" style="display:none">푸시전송</th>
							<td>
								<input type = "checkbox" name="pushsend" id="pushsend" style="display:none" checked / >	
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</form>
	<div class="tit_wrap" style="margin-top:10px">
		<span class="btn_wrap" style="padding-right:20px">
			<a href="#" class="btn_s white" style="min-width:100px;" onclick="post_update();">저장</a>
			<?if($type=='up'){?><a href="#" class="btn_s white" style="min-width:100px;" onclick="post_delete();">삭제</a><?}?>
			<a href="#" class="btn_s white" style="min-width:100px;" onclick="btn_close();">닫기</a>
		</span>
	</div>
</div>

 </body>
</html>

<script type="text/javascript">

function checkOnlyOne(element) {

	if(element.value == '1'){
		$('.ser_recv').css("display","none");
	}else{
		$('.ser_recv').css("display","");
	}
  
	const checkboxes = document.getElementsByName("gubun");

	checkboxes.forEach((cb) => {
		cb.checked = false;
	})

	element.checked = true;
}

// 저장
function post_update(){

	var gubun	= $('#gubun').val();
	var title   = $("form[name='post_form'] input[name='title']").val();
	var msg		= $('#msg').val();
	var chk		= $("input:checkbox[name=gubun]:checked").val();
	var recv	= $('#recv').val();
	

	if(chk != '1' && isEmpty(recv) == true){
		alert('알림장 구분 및 대상을 선택해주세요.');
	}else if(isEmpty(title) == true){
		alert('제목을 입력해주세요.');
		recv_ser();
	}else if(isEmpty(msg) == true){
		alert('내용을 입력해주세요.');
		document.getElementById('msg').focus();
	}else{
		if(confirm("저장하시겠습니까?")){
			$("form[name='post_form']").submit();
		}
	}

}

// 삭제
function post_delete(){
	var type   = $("form[name='post_form'] input[name='type']").val();

	if(type == "up"){
		if(confirm("삭제하시겠습니까?")){
			document.post_form.type.value='del';
			$("form[name='post_form']").submit();
			
		}
	}else{
		alert("삭제할 대상이 없습니다.");
	}
}

// 닫기
function btn_close(){
	self.close();
}


// 관리자 및 직급에 따른 기본값 설정
function radio_chk(){
	var jik	=	'<?=$_SESSION['S_JIK']?>'		
	var mas	=	'<?=$_SESSION['S_MASTER']?>'	
	
	if(mas == 'A'){
		$('#gubun1').prop('checked',true);	
		$('.ser_recv').css("display","none");
	}else{
		if(jik == '5001'){
			$('#gubun2').prop('checked',true);
			
			$("#gubun1").prop("disabled",true);
		}else if(jik == '4001'){
			$('#gubun3').prop('checked',true);	
			
			$("#gubun1").prop("disabled",true);
			$("#gubun2").prop("disabled",true);
		}else if(jik == '3001'){
			$('#gubun4').prop('checked',true);	

			$("#gubun1").prop("disabled",true);
			$("#gubun2").prop("disabled",true);		
			$("#gubun3").prop("disabled",true);		
		}else if(jik == '2001'){
			$('#gubun5').prop('checked',true);	

			$("#gubun1").prop("disabled",true);
			$("#gubun2").prop("disabled",true);		
			$("#gubun3").prop("disabled",true);	
			$("#gubun4").prop("disabled",true);		
		}
		$('.ser_recv').css("display","");
	}


	$(":radio[name$='color']").val([1]);		

}


// 구분에 따른 대상자 팝업
function recv_ser(){

	var chk = $("input:checkbox[name=gubun]:checked").val();
	var url = '';

	if(chk == '2'){
		url = "/bin/sub/menu7/ga_menu7_06_jojik_search.php?level=1&post=Y";
	}else if(chk == '3'){
		url = "/bin/sub/menu7/ga_menu7_06_jojik_search.php?level=2&post=Y";
	}else if(chk == '4'){
		url = "/bin/sub/menu7/ga_menu7_06_jojik_search.php?level=3&post=Y";
	}else if(chk == '5'){
		url = "/bin/sub/menu7/ga_menu7_06_jojik_search.php?level=4&post=Y";
	}else if(chk == '6'){
		url = "/bin/sub/menu7/ga_menu7_06_swon_search.php?post=Y";
	}

	var left = Math.ceil((window.screen.width - 600)/2);
	var top = Math.ceil((window.screen.height - 800)/2);
	var popOpen	= window.open(url,"jojik","width=600px,height=705px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
	popOpen.focus();
}

// 조직 선택 후
function setrecvValue(code, name, level){
	$("#recv").val(code);
	$("#recvnm").val(name);
	$("#recvnm2").text(name);	
}

// 개인(사원) 선택 후
function setSwonValue(code,name){
	$("#recv").val(code);
	$("#recvnm").val(name);
	$("#recvnm2").text(name);	
}


// ajax 호출
var btnAction	= true;
$(document).ready(function(){

	if('<?=$type?>' == 'in'){
		radio_chk();	
	}
	if('<?=$type?>' == 'up'){
		 $('#pushsendh').css('display', 'block');
		 $('#pushsend').css('display', 'block');
	}
	
	var options = { 
		dataType:  'json',
		beforeSubmit:  showRequest_modal_post,  // pre-submit callback 
		success:       processJson_modal_post  // post-submit callback 
	}; 

	$('.ajaxForm_post').ajaxForm(options);
});

// pre-submit callback 
function showRequest_modal_post(formData, jqForm, options) { 
	var queryString = $.param(formData); 
	return true; 
} 

// post-submit callback 
function processJson_modal_post(data) { 
	console.log(data);
	if(data.message){
		alert(data.message);
	}

	if(data.result==""){
		opener.$(".btn_search").trigger("click");
		if(data.rtype == 'in' || data.rtype == 'up'){
			location.href='ga_menu7_06_read.php?pid='+data.pid;
		}else{
			self.close();
		}
	}

}

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>