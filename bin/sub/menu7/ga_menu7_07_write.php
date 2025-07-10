<?
//error_reporting(E_ALL); ini_set('display_errors', 1);
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");


$jik	=	$_SESSION['S_JIK'];		// 영업직위
$master	=	$_SESSION['S_MASTER'];	// 관리자여부

if($_GET['pid']){
	$type="up";
	$sql	= "	
				select a.scode,a.pid,
					   a.title,a.msg,a.jocnt,a.color, s.pos, co.subnm posnm,
						case when isnull(s.bonbu,'') != '' then substring(b.bname,1,2) else '' end +
						case when isnull(s.bonbu,'') != '' and (isnull(s.jisa,'') != '' or isnull(s.team,'') != '')  then ' > ' else '' end +
						case when isnull(s.jisa,'') != '' then substring(js.jsname,1,2) else '' end +
						case when isnull(s.jisa,'') != '' and isnull(s.jijum,'') != '' then ' > ' else '' end +
						case when isnull(s.jijum,'') != '' then substring(j.jname,1,4) else '' end +
						case when isnull(s.jijum,'') != '' and isnull(s.team,'') != '' then ' > ' else '' end +
						case when isnull(s.team,'') != '' then t.tname else '' end as sosok,
					   convert(varchar(8),a.idate,112) idate,a.iswon,a.udate,a.uswon,s.sname,
					   case when datediff(day, a.idate, convert(varchar,getdate(),112)) <= 5 then 'Y' else 'N' end as newbit,
						datediff(hour,a.idate,getdate()) ntime , row_number() over(order by a.idate desc) rnum
				from community a 	
					left outer join swon s on a.scode = s.scode and a.iswon = s.skey
					left outer join bonbu b on a.scode = b.scode and b.bcode = s.bonbu
					left outer join jisa js on a.scode = js.scode and js.jscode = s.jisa
					left outer join jijum j on a.scode = j.scode and j.jcode = s.jijum
					left outer join team t on a.scode = t.scode and t.tcode = s.team
					left outer join common co on a.scode = co.scode and co.code = 'COM006' and s.pos = co.codesub
				where a.scode = '".$_SESSION['S_SCODE']."' and a.pid = '".$_GET['pid']."' ";
	$qry	= sqlsrv_query( $mscon, $sql );
	extract($fet= sqlsrv_fetch_array($qry));

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
		<h3 class="tit_sub" style="margin-left:20px">커뮤니티 작성</h3>
	</div>


	<form name="commu_form" class="ajaxForm_commu" method="post" action="ga_menu7_07_action.php" style="padding:0px 20px;">
		<div style="border-right:1px solid #D5D5D5; border-left:1px solid #D5D5D5; ">
			<div class="tb_type01 view">
				<input type="hidden" name="type" value="<?=$type?>">
				<input type="hidden" name="pid" value="<?=$_GET['pid']?>">
				<table id="modal_table">
					<colgroup>
						<col width="20%">
						<col width="30%">
						<col width="20%">
						<col width="30%">

					</colgroup>
					<tbody>
						<tr>
							<th>작성자</th>
							<?if($type == 'in'){?>
							<td style="height:30px"><?=$_SESSION['S_SNAME'].'('.$_SESSION['S_SKEY'].')'?></td>
							<?}else{?>
							<td style="height:30px"><?=$sname.'('.$iswon.')'?></td>
							<?}?>
							<th>소속</th>
							<td style="height:30px"><?=$sosok?></td>
						</tr>
						<tr>
							<th><em class="impor">*</em>제목</th>
							<td colspan=3><span class="input_type" style="width:100%" id="skey_input"><input type="text" name="title" id="title" value="<?=$title?>"></span></td>
						</tr>
						<tr>
							<th><em class="impor">*</em>내용</th>
							<td colspan=3><textarea name="msg" id="msg" style="width:100%;height:200px"><?=$msg?></textarea></td>
						</tr>
						<tr>
							<th>제목색상</th>
							<td colspan=3>
								<input type="radio" class="color" name="color" id="color1" value="1" <?if(trim($color)=='1') echo "checked";?>><label for="color1">검정 </label>&nbsp;&nbsp;&nbsp;
								<input type="radio" class="color" name="color" id="color2" value="2" <?if(trim($color)=='2') echo "checked";?>><label for="color2" style="color:#f9650e">빨강 </label>&nbsp;&nbsp;&nbsp;
								<input type="radio" class="color" name="color" id="color3" value="3" <?if(trim($color)=='3') echo "checked";?>><label for="color3" style="color:#1266FF">파랑 </label>&nbsp;&nbsp;&nbsp;
								<input type="radio" class="color" name="color" id="color4" value="4" <?if(trim($color)=='4') echo "checked";?>><label for="color4" style="color:#f9b300">주황</label>&nbsp;&nbsp;&nbsp;			
								<input type="radio" class="color" name="color" id="color5" value="5" <?if(trim($color)=='5') echo "checked";?>><label for="color5" style="color:#8041D9">보라</label>&nbsp;&nbsp;&nbsp;	
								<input type="radio" class="color" name="color" id="color6" value="6" <?if(trim($color)=='6') echo "checked";?>><label for="color6" style="color:#2F9D27">초록</label>	
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</form>
	<div class="tit_wrap" style="margin-top:10px">
		<span class="btn_wrap" style="padding-right:20px">
			<a href="#" class="btn_s white" style="min-width:100px;" onclick="commu_update();">저장</a>
			<?if($type=='up'){?><a href="#" class="btn_s white" style="min-width:100px;" onclick="commu_delete();">삭제</a><?}?>
			<a href="#" class="btn_s white" style="min-width:100px;" onclick="btn_close();">닫기</a>
		</span>
	</div>
</div>

 </body>
</html>

<script type="text/javascript">


// 저장
function commu_update(){

	var title   = $("form[name='commu_form'] input[name='title']").val();
	var msg		= $('#msg').val();
	

	if(isEmpty(title) == true){
		alert('제목을 입력해주세요.');
		document.getElementById('title').focus();
	}else if(isEmpty(msg) == true){
		alert('내용을 입력해주세요.');
		document.getElementById('msg').focus();
	}else{
		if(confirm("저장하시겠습니까?")){
			$("form[name='commu_form']").submit();
		}
	}

}

// 삭제
function commu_delete(){
	var type   = $("form[name='commu_form'] input[name='type']").val();

	if(type == "up"){
		if(confirm("삭제하시겠습니까?")){
			document.commu_form.type.value='del';
			$("form[name='commu_form']").submit();
			
		}
	}else{
		alert("삭제할 대상이 없습니다.");
	}
}

// 닫기
function btn_close(){
	self.close();
}


// ajax 호출
var btnAction	= true;
$(document).ready(function(){

	if('<?=$type?>' == 'in'){
		$(":radio[name$='color']").val([1]);		
	}

	var options = { 
		dataType:  'json',
		beforeSubmit:  showRequest_modal_commu,  // pre-submit callback 
		success:       processJson_modal_commu  // post-submit callback 
	}; 

	$('.ajaxForm_commu').ajaxForm(options);
});

// pre-submit callback 
function showRequest_modal_commu(formData, jqForm, options) { 
	var queryString = $.param(formData); 
	return true; 
} 

// post-submit callback 
function processJson_modal_commu(data) { 
	console.log(data);
	if(data.message){
		alert(data.message);
	}

	if(data.result==""){
		opener.$(".btn_search").trigger("click");
		if(data.rtype == 'in' || data.rtype == 'up'){
			location.href='ga_menu7_07_read.php?pid='+data.pid;
		}else{
			self.close();
		}
	}

}

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>