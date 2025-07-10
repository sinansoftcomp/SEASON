<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

// 조회수업데이트
$sql = "update community
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


sqlsrv_free_stmt($qry);
sqlsrv_close($mscon);

?>
<style>
body{background-image: none;}

</style>


<div class="tit_wrap ipgopop" style="padding-top:10px">
	<div class="tit_wrap" style="margin-top:0px">
		<h3 class="tit_sub" style="margin-left:20px">커뮤니티</h3>
		<span class="btn_wrap" style="padding-right:20px">
			<?if($iswon==$_SESSION['S_SKEY'] || $_SESSION['S_MASTER'] == 'A'){?>
			<a href="#" class="btn_s white" style="min-width:100px;" onclick="commu_update();">수정</a>
			<?}?>
			<a href="#" class="btn_s white" style="min-width:100px;" onclick="pop_close();">닫기</a>
		</span>
	</div>

	<form name="gongji_form" class="ajaxForm_gongji" method="post" action="ga_menu7_07_action.php" style="padding:0px 20px;">
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
							<th>소속</th>
							<td><?=$sosok?></td>
							<th>직위</th>
							<td><?=$posnm?></td>
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

	<!-- 댓글은 알림장과 구분하여 사용하지 않고 공통 사용 -->
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

function commu_update(){
	location.href='ga_menu7_07_write.php?pid='+'<?=$_GET["pid"]?>';
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

	// 알림장 댓글과 공통으로 사용
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