<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

// ��� ����Ʈ
if(isset($_GET['pid'])){

	//�˻� ������ ���ϱ� 
	$sql = "
		select a.cid,
			   a.pid,
			   a.ctext,
			   a.iswon,
			   b.sname+'('+isnull(c.subnm,'FC')+')' as iswon_nm,
			   convert(varchar(20), a.idate, 120) idate
		from comment a 
			left outer join swon b on a.scode = b.scode and a.iswon = b.skey
			left outer join common c on a.scode = c.scode and c.code = 'COM006' and b.pos = c.codesub
		where a.scode = '".$_SESSION['S_SCODE']."'
		  and a.pid = '".$_GET['pid']."'
		order by a.idate desc";	

	$qry	= sqlsrv_query( $mscon, $sql );
	$listData = array();
	while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
		$listData[]	= $fet;
	}


	//�˻� ������ ���ϱ� 
	$sql = "
		select count(*) CNT
		from comment a 
			left outer join swon b on a.scode = b.scode and a.iswon = b.skey
			left outer join common c on a.scode = c.scode and c.code = 'COM006' and b.pos = c.codesub
		where a.scode = '".$_SESSION['S_SCODE']."'
		  and a.pid = '".$_GET['pid']."' ";	

	$qry =  sqlsrv_query($mscon, $sql);
	$totalResult =  sqlsrv_fetch_array($qry); 
	$totalCnt = $totalResult['CNT'];

}


sqlsrv_free_stmt($result);
sqlsrv_close($mscon);


?>

<!-- html���� -->
<style>
body{background-image: none;}

.comment_btn a{cursor:pointer;}
.inscomment{margin-top:10px;}
</style>


<?if(!empty($listData)){?>
<p style="font-size:15px;font-weight:600;">��� <span style="color:#f9650e;"><?=$totalCnt?></span></p>
<div class="comment_list">
	<?foreach($listData as $key => $val){extract($val);
		// �ٹٲ� ���͸�
		$ctext	=	nl2br($ctext); 
	?>
		<li>
			<p class="comment_name"><?=$iswon_nm?>
				<span class="datewr"><?=$idate?></span>
			</p>

			<p class="con" id="read_<?=$cid?>"><?=$ctext?></p>
			<span class="input_type inscomment" id="inp_<?=$cid?>" style="width:100%;display:none;" id="skey_input"><input type="text" name="ctext" id="ctext_<?=$cid?>" value="<?=$title?>"></span>

			<!-- ������ �� �ۼ��ڸ��� ���� ���� -->
			<?if($iswon==$_SESSION['S_SKEY'] || $_SESSION['S_MASTER'] == 'A'){?>
			<p class="btn_data comment_btn" id="btn_<?=$cid?>">
				<a onclick="comment_edit('<?=$cid?>');">����</a>
				<a onclick="comment_del('<?=$cid?>');">����</a>
			</p>
			<?}?>

			<p class="btn_data comment_btn" id="btnnew_<?=$cid?>" style="display:none;">
				<a onclick="comment_up('<?=$cid?>');">��ۼ���</a>
				<a onclick="comment_cncl('<?=$cid?>');">���</a>
			</p>
		</li>
	<?}?>
</div>
<?}?>


<form name="commentlist_form" class="ajaxForm_commentlist" method="post" action="ga_menu7_06_comment_action.php">
	<input type="hidden" name="pid" value="<?=$_GET['pid']?>">
	<input type="hidden" name="cid" value="">
	<input type="hidden" name="type" value="">	
	<input type="hidden" name="ctext" value="">
</form>


<script type="text/javascript">

// ��� ������ȯ
function comment_edit(cid){

	// ��� ���� ����� ��ǲ�ڽ� ����
	$('#read_'+cid).css("display","none");
	$('#inp_'+cid).css("display","");	

	// ����&���� ��ư ����� ��ۼ��� ��ư ����
	$('#btn_'+cid).css("display","none");
	$('#btnnew_'+cid).css("display","");

	$('#ctext_'+cid).val($('#read_'+cid).text());
}


// ��� �������
function comment_cncl(cid){

	// ��� ���� ����� ��ǲ�ڽ� ����
	$('#read_'+cid).css("display","");
	$('#inp_'+cid).css("display","none");	

	// ����&���� ��ư ����� ��ۼ��� ��ư ����
	$('#btn_'+cid).css("display","");
	$('#btnnew_'+cid).css("display","none");

}


// ��� ���� DB������Ʈ
function comment_up(cid){

	var id	  = 'ctext_'+cid;
	var ctext = $("#"+id).val();

	if(isEmpty(ctext) == true){
		alert('��� ������ �Է����ּ���');
		document.getElementById(id).focus();
		return;
	}

	document.commentlist_form.type.value='up';
	document.commentlist_form.cid.value=cid;
	document.commentlist_form.ctext.value=ctext;

	$("form[name='commentlist_form']").submit();	
}


// ��� ����
function comment_del(cid){
	document.commentlist_form.type.value='del';
	document.commentlist_form.cid.value=cid;

	if(confirm("�����Ͻðڽ��ϱ�?")){
		$("form[name='commentlist_form']").submit();
	}

}

$(document).ready(function(){

	var options = { 
		dataType:  'json',
		beforeSubmit:  showRequest_modal_commentlist,  // pre-submit callback 
		success:       processJson_modal_commentlist  // post-submit callback 
	}; 

	$('.ajaxForm_commentlist').ajaxForm(options);

});


// pre-submit callback 
function showRequest_modal_commentlist(formData, jqForm, options) { 
	var queryString = $.param(formData); 
	return true; 
} 

// post-submit callback 
function processJson_modal_commentlist(data) { 
	console.log(data);
	if(data.message){
		//alert(data.message);
		// ����� alertâ ���ʿ� �Ǵ�
	}

	if(data.result==""){
		ajaxLodingTarket('ga_menu7_06_comment.php',$('.comment'),'&pid='+data.pid);
	}

}


</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>