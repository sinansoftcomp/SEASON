<?
//error_reporting(E_ALL); ini_set('display_errors', 1);
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");


$jik	=	$_SESSION['S_JIK'];		// ��������
$master	=	$_SESSION['S_MASTER'];	// �����ڿ���

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
		<h3 class="tit_sub" style="margin-left:20px">�˸��� �ۼ�</h3>
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
							<th><em class="impor">*</em>����</th>
							<td colspan=3 style="height:30px">
								<input type="checkbox" class="gubun" name="gubun" id="gubun1" value="1" <?if(trim($gubun)=='1') echo "checked";?> onclick="checkOnlyOne(this)"><label for="gubun1">��ü </label>&nbsp;&nbsp;&nbsp;
								<input type="checkbox" class="gubun" name="gubun" id="gubun2" value="2" <?if(trim($gubun)=='2') echo "checked";?> onclick="checkOnlyOne(this)"><label for="gubun2">���� </label>&nbsp;&nbsp;&nbsp;
								<input type="checkbox" class="gubun" name="gubun" id="gubun3" value="3" <?if(trim($gubun)=='3') echo "checked";?> onclick="checkOnlyOne(this)"><label for="gubun3">���� </label>&nbsp;&nbsp;&nbsp;
								<input type="checkbox" class="gubun" name="gubun" id="gubun4" value="4" <?if(trim($gubun)=='4') echo "checked";?> onclick="checkOnlyOne(this)"><label for="gubun4">����</label>&nbsp;&nbsp;&nbsp;			
								<input type="checkbox" class="gubun" name="gubun" id="gubun5" value="5" <?if(trim($gubun)=='5') echo "checked";?> onclick="checkOnlyOne(this)"><label for="gubun5">��</label>&nbsp;&nbsp;&nbsp;	
								<input type="checkbox" class="gubun" name="gubun" id="gubun6" value="6" <?if(trim($gubun)=='6') echo "checked";?> onclick="checkOnlyOne(this)"><label for="gubun6">����</label>
							</td>
						</tr>
						<tr>
							<th><em class="impor">*</em>���</th>
							<td colspan=3 >
								<a class="btn_s white ser_recv" onclick="recv_ser();" style="min-width:80px;display:none" ><i class="fa-solid fa-magnifying-glass fa-lg mgr3"></i>ã��</a>
								<input type="hidden" name="recv" id="recv" value="<?=$recv?>">
								<input type="hidden" name="recvnm" id="recvnm" value="<?=$recvnm?>">
								<span style="width:40%;margin-left:5px" id="recvnm2"><?=trim($recvnm)?></span>
							</td>
						</tr>
						<tr>
							<th><em class="impor">*</em>����</th>
							<td colspan=3 ><span class="input_type" style="width:100%" id="skey_input"><input type="text" name="title" id="title" value="<?=$title?>"></span></td>
						</tr>
						<tr>
							<th><em class="impor">*</em>����</th>
							<td colspan=3 ><textarea name="msg" id="msg" style="width:100%;height:200px"><?=$msg?></textarea></td>
						</tr>
						<tr>
							<th>�������</th>
							<td colspan=3 >
								<input type="radio" class="color" name="color" id="color1" value="1" <?if(trim($color)=='1') echo "checked";?>><label for="color1">���� </label>&nbsp;&nbsp;&nbsp;
								<input type="radio" class="color" name="color" id="color2" value="2" <?if(trim($color)=='2') echo "checked";?>><label for="color2" style="color:#f9650e">���� </label>&nbsp;&nbsp;&nbsp;
								<input type="radio" class="color" name="color" id="color3" value="3" <?if(trim($color)=='3') echo "checked";?>><label for="color3" style="color:#1266FF">�Ķ� </label>&nbsp;&nbsp;&nbsp;
								<input type="radio" class="color" name="color" id="color4" value="4" <?if(trim($color)=='4') echo "checked";?>><label for="color4" style="color:#f9b300">��Ȳ</label>&nbsp;&nbsp;&nbsp;			
								<input type="radio" class="color" name="color" id="color5" value="5" <?if(trim($color)=='5') echo "checked";?>><label for="color5" style="color:#8041D9">����</label>&nbsp;&nbsp;&nbsp;	
								<input type="radio" class="color" name="color" id="color6" value="6" <?if(trim($color)=='6') echo "checked";?>><label for="color6" style="color:#2F9D27">�ʷ�</label>	
							</td>
						</tr>
						<tr>
							<th>�ֻ������</th>
							<td <?if($type=="in"){?>colspan=3<?}?>>
								<input type = "checkbox" name="topsort" id="topsort" <?if($topsort=="Y"){?>checked<?}?> / >	
							</td>
							<th id="pushsendh" style="display:none">Ǫ������</th>
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
			<a href="#" class="btn_s white" style="min-width:100px;" onclick="post_update();">����</a>
			<?if($type=='up'){?><a href="#" class="btn_s white" style="min-width:100px;" onclick="post_delete();">����</a><?}?>
			<a href="#" class="btn_s white" style="min-width:100px;" onclick="btn_close();">�ݱ�</a>
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

// ����
function post_update(){

	var gubun	= $('#gubun').val();
	var title   = $("form[name='post_form'] input[name='title']").val();
	var msg		= $('#msg').val();
	var chk		= $("input:checkbox[name=gubun]:checked").val();
	var recv	= $('#recv').val();
	

	if(chk != '1' && isEmpty(recv) == true){
		alert('�˸��� ���� �� ����� �������ּ���.');
	}else if(isEmpty(title) == true){
		alert('������ �Է����ּ���.');
		recv_ser();
	}else if(isEmpty(msg) == true){
		alert('������ �Է����ּ���.');
		document.getElementById('msg').focus();
	}else{
		if(confirm("�����Ͻðڽ��ϱ�?")){
			$("form[name='post_form']").submit();
		}
	}

}

// ����
function post_delete(){
	var type   = $("form[name='post_form'] input[name='type']").val();

	if(type == "up"){
		if(confirm("�����Ͻðڽ��ϱ�?")){
			document.post_form.type.value='del';
			$("form[name='post_form']").submit();
			
		}
	}else{
		alert("������ ����� �����ϴ�.");
	}
}

// �ݱ�
function btn_close(){
	self.close();
}


// ������ �� ���޿� ���� �⺻�� ����
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


// ���п� ���� ����� �˾�
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

// ���� ���� ��
function setrecvValue(code, name, level){
	$("#recv").val(code);
	$("#recvnm").val(name);
	$("#recvnm2").text(name);	
}

// ����(���) ���� ��
function setSwonValue(code,name){
	$("#recv").val(code);
	$("#recvnm").val(name);
	$("#recvnm2").text(name);	
}


// ajax ȣ��
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