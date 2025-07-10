<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

// 조회수업데이트
$sql = "update gongji
		set jocnt = jocnt+1
		where scode = '".$_SESSION['S_SCODE']."' and seq = '".$_GET['seq']."'
		";
$result =  sqlsrv_query( $mscon, $sql );
if ($result == false){
	sqlsrv_query($mscon,"ROLLBACK");
}else{
	sqlsrv_query($mscon,"COMMIT");
}

if($_GET['seq']){
	$type="up";
	$sql	= "	
				select a.scode,a.seq,a.gubun,a.title,a.bigo,a.jocnt,a.topsort,a.filename,a.filepath,convert(varchar(8),a.idate,112) idate,a.iswon,a.udate,a.uswon,
						b.sname
				from GONGJI a left outer join swon b on a.scode = b.scode and a.iswon = b.skey
				where a.scode = '".$_SESSION['S_SCODE']."' and a.seq = ".$_GET['seq']." ";
	$qry	= sqlsrv_query( $mscon, $sql );
	extract($fet= sqlsrv_fetch_array($qry));
}else{
	$type="in";
}

sqlsrv_free_stmt($qry);
sqlsrv_close($mscon);

?>
<style>
body{background-image: none;}
</style>


<div class="tit_wrap ipgopop" style="padding-top:10px">
	<div class="tit_wrap" style="margin-top:0px">
		<h3 class="tit_sub" style="margin-left:20px">공지사항</h3>
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
							<td colspan=3 style="height:30px"><?=$conf['gongji_gubun'][$gubun]?></td>
						</tr>
						<tr>
							<th>제목</th>
							<td colspan=3><?=$title?></td>
						</tr>
						<tr>
							<th>내용</th>
							<td colspan=3 style="height:200px"><?=nl2br($bigo)?></td>
						</tr>

						<tr class="filetr">
							<th>첨부파일</th>
							<td colspan=3><a href="javascript:;" onclick="downFile(this)"><?=$filename?></a></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</form>
	<div class="tit_wrap" style="margin-top:10px">
		<span class="btn_wrap" style="padding-right:20px">
			<?if($iswon==$_SESSION['S_SKEY'] || $_SESSION['S_MASTER'] == 'A'){?>
			<a href="#" class="btn_s white" style="min-width:100px;" onclick="gongji_update();">수정</a>
			<?}?>
			<a href="#" class="btn_s white" style="min-width:100px;" onclick="pop_close();">닫기</a>
		</span>
	</div>
</div>

 </body>
</html>

<script type="text/javascript">

function gongji_update(){
	location.href='ga_menu7_03_write.php?seq='+'<?=$_GET["seq"]?>';
}

// 닫기
function pop_close(){
	self.close();
}

function downFile(obj){
	$(obj).attr('href' ,"/temp/gongji/"+"<?=$filename?>");
	$(obj).attr('download' , "<?=$filename?>");
}

/* 첨부파일 삭제 */
function deleteFile(num) {
    document.querySelector("#file" + num).remove();
    filesArr[num].is_delete = true;
}


$(document).ready(function(){


});



</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>