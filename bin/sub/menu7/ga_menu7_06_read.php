<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

// 조회수업데이트
$sql = "update postlist
		set jocnt = jocnt+1
		where scode = '".$_SESSION['S_SCODE']."' and pid = '".$_GET['pid']."'
		";
$result =  sqlsrv_query( $mscon, $sql );
if ($result == false){
	sqlsrv_query($mscon,"ROLLBACK");
}else{
	sqlsrv_query($mscon,"COMMIT");
}

if($_GET['pid']){
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
	extract($fet= sqlsrv_fetch_array($qry));
}else{
	$type="in";
}



if($color == '2'){
	$style_color = 'color:#f9650e;';
}else if($color == '3'){
	$style_color = 'color:#1266FF;';
}else if($color == '4'){
	$style_color = 'color:#f9b300;';
}else if($color == '5'){
	$style_color = 'color:#8041D9;';
}else if($color == '6'){
	$style_color = 'color:#2F9D27;';
}else{
	$style_color = 'color:#333;';
}


if($topsort == 'Y'){
	$style_color .= 'font-weight:700;';
}

sqlsrv_free_stmt($qry);
sqlsrv_close($mscon);

?>
<style>
body{background-image: none;}

</style>


<div class="tit_wrap ipgopop" style="padding-top:10px">
	<div class="tit_wrap" style="margin-top:0px">
		<h3 class="tit_sub" style="margin-left:20px">알림장</h3>
		<span class="btn_wrap" style="padding-right:20px">
			<?if($iswon==$_SESSION['S_SKEY'] || $_SESSION['S_MASTER'] == 'A'){?>
			<a href="#" class="btn_s white" style="min-width:100px;" onclick="post_update();">수정</a>
			<?}?>
			<a href="#" class="btn_s white" style="min-width:100px;" onclick="pop_close();">닫기</a>
		</span>
	</div>

	<form name="gongji_form" class="ajaxForm_gongji" method="post" action="ga_menu7_03_action.php" style="padding:0px 20px;">
		<div style="border-right:1px solid #D5D5D5; border-left:1px solid #D5D5D5; ">
			<div class="tb_type01 view">
				<input type="hidden" name="type" value="<?=$type?>">
				<table id="modal_table">
					<colgroup>
						<col width="20%">
						<col width="30%">
						<col width="20%">
						<col width="auto">
					</colgroup>
					<tbody>
						<tr>
							<th>작성자</th>
							<td><?=$sname?> (<?=$iswon?>)</td>
							<th>작성일자</th>
							<td><?=date("Y-m-d",strtotime($idate))?></td>
						</tr>
						<tr>
							<th>구분</th>
							<td colspan=3>
								<input type="checkbox" class="gubun" name="gubun" id="gubun1" value="1" <?if(trim($gubun)=='1') echo "checked";?> onclick="checkOnlyOne(this)"><label for="gubun1">전체 </label>&nbsp;&nbsp;&nbsp;
								<input type="checkbox" class="gubun" name="gubun" id="gubun2" value="2" <?if(trim($gubun)=='2') echo "checked";?> onclick="checkOnlyOne(this)"><label for="gubun2">본부 </label>&nbsp;&nbsp;&nbsp;
								<input type="checkbox" class="gubun" name="gubun" id="gubun3" value="3" <?if(trim($gubun)=='3') echo "checked";?> onclick="checkOnlyOne(this)"><label for="gubun3">지사 </label>&nbsp;&nbsp;&nbsp;
								<input type="checkbox" class="gubun" name="gubun" id="gubun4" value="4" <?if(trim($gubun)=='4') echo "checked";?> onclick="checkOnlyOne(this)"><label for="gubun4">지점</label>&nbsp;&nbsp;&nbsp;			
								<input type="checkbox" class="gubun" name="gubun" id="gubun5" value="5" <?if(trim($gubun)=='5') echo "checked";?> onclick="checkOnlyOne(this)"><label for="gubun5">팀</label>&nbsp;&nbsp;&nbsp;	
								<input type="checkbox" class="gubun" name="gubun" id="gubun6" value="6" <?if(trim($gubun)=='6') echo "checked";?> onclick="checkOnlyOne(this)"><label for="gubun6">개인</label>
							</td>
						</tr>
						<tr>
							<th>대상</th>
							<td colspan=3>
								<input type="hidden" name="recv" id="recv" value="<?=$recv?>">
								<?=trim($recvnm)?>
							</td>
						</tr>
						<tr>
							<th>제목</th>
							<td colspan=3 style="<?=$style_color?>"><?=$title?></td>
						</tr>
						<tr>
							<th>내용</th>
							<td colspan=3 style="height:200px"><?=nl2br($msg)?></td>
						</tr>

					</tbody>
				</table>
			</div>
		</div>
	</form>

	<div style="margin-top:20px;padding:0px 20px;">
		<form name="comment_form" class="ajaxForm_comment" method="post" action="ga_menu7_06_comment_action.php">
			<input type="hidden" name="pid" value="<?=$_GET['pid']?>">
			<input type="hidden" name="type" value="">
			<div style="display:inline-block;width:620px">
				<textarea class="text_input" placeholder="댓글을 입력해주세요." name="ctext" id="commenttext" style="height: 20px"></textarea>
			</div>
			<div style="float:right;">
				<a class="btn_s white" style="min-width:100px;height:30px;line-height:28px;cursor:pointer" onclick="comment_ins();">댓글등록</a>
			</div>
		</form>
	</div>


	<div class="comment">
		<!-- 댓글 리스트 -->
	</div>


</div>

 </body>
</html>

<script type="text/javascript">

function post_update(){
	location.href='ga_menu7_06_write.php?pid='+'<?=$_GET["pid"]?>';
}

// 닫기
function pop_close(){
	self.close();
}



// 댓글등록
function comment_ins(){

	var ctext	= $('#commenttext').val();
	document.comment_form.type.value='in';

	if(isEmpty(ctext) == true){
		alert('댓글을 입력해주세요.');
		document.getElementById('commenttext').focus();
	}else{
		if(confirm("저장하시겠습니까?")){
			$("form[name='comment_form']").submit();
		}
	}

}


$(document).ready(function(){

	$('.gubun').attr('disabled',true);

	ajaxLodingTarket('ga_menu7_06_comment.php',$('.comment'),'&pid=<?=$_GET["pid"]?>');


	var options = { 
		dataType:  'json',
		beforeSubmit:  showRequest_modal_comment,  // pre-submit callback 
		success:       processJson_modal_comment  // post-submit callback 
	}; 

	$('.ajaxForm_comment').ajaxForm(options);

});


// pre-submit callback 
function showRequest_modal_comment(formData, jqForm, options) { 
	var queryString = $.param(formData); 
	return true; 
} 

// post-submit callback 
function processJson_modal_comment(data) { 
	console.log(data);
	if(data.message){
		//alert(data.message);
		// 일일히 댓글마다 알림창 불필요 판단
	}

	if(data.result==""){
		document.getElementById('commenttext').value = "";
		ajaxLodingTarket('ga_menu7_06_comment.php',$('.comment'),'&pid='+data.pid);
	}

}


</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>