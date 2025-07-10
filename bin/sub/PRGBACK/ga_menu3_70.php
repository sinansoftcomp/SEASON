<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

// �⺻ ������ ����
$page = ($_GET['page']) ? $_GET['page'] : 1;
$page_row	= 18;

$limit1 = ($page-1)*$page_row+1;
$limit2 = $page*$page_row;

$gubun = "A"; // ���
$gubunname = "���";


// �������ε� �����丮 ����Ʈ
$sql = " 
select *
from(
	select *, ROW_NUMBER()over(order by upldate desc,uplnum desc) rnum 
	from(
		select a.scode,a.upldate, a.uplnum, a.gubun, a.filename, b.name, a.cnt, a.amt, a.bigo,convert(varchar,a.idate,21) idate ,c.sname , isnull(a.fcnt,0) fcnt , isnull(a.famt,0) famt
		from upload_history a left outer join insmaster b on a.code = b.code
								left outer join swon c on a.scode = c.scode and a.iswon = c.skey
		where a.scode = '".$_SESSION['S_SCODE']."' and a.gubun = 'A'
	) tbl
) p
	where rnum between ".$limit1." AND ".$limit2 ;

 $qry = sqlsrv_query( $mscon, $sql );
$listData	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listData[]	= $fet;
}

// �������ε� �����丮 ����Ʈ �� �Ǽ�
$sql= "
	select 
		count(*) CNT
	from(
		select a.scode,a.upldate, a.uplnum, a.gubun, a.filename, b.name, a.cnt, a.amt, a.bigo,convert(varchar,a.idate,21) idate ,c.sname , isnull(a.fcnt,0) fcnt , isnull(a.famt,0) famt
		from upload_history a left outer join insmaster b on a.code = b.code
								left outer join swon c on a.scode = c.scode and a.iswon = c.skey
		where a.scode = '".$_SESSION['S_SCODE']."' and a.gubun = 'A'
	) p " ;


$qry =  sqlsrv_query($mscon, $sql);
$totalResult =  sqlsrv_fetch_array($qry); 


// ����� ��������
$sql= "select inscode, name from inssetup where scode = '".$_SESSION['S_SCODE']."' and useyn = 'Y' order by num, inscode";
$qry= sqlsrv_query( $mscon, $sql );
$insData	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
  $insData[] = $fet;
}

// ������ Ŭ���� ����
// �ε�
include_once($conf['rootDir'].'/include/class/Pagination.php');

// ����
$pagination = new Pagination(array(
		'base_url' => $_SERVER['PHP_SELF']."?scode=".$_GET['scode'],
		'per_page' => $page_row,
		'total_rows' => $totalResult['CNT'],
		'cur_page' => $page,
));

sqlsrv_free_stmt($result);
sqlsrv_close($mscon);

?>

<!-- html���� -->
<style>
body{background-image: none;}
.container{margin:0px 0px 0px 10px;}
.box_wrap {margin-bottom:10px}
.tb_type01 th, .tb_type01 td {padding: 8px 0}



</style>

<div class="container">
	<div class="content_wrap">
		<fieldset>
			<legend>��࿢�����ε�</legend>
			<h2 class="tit_big">��࿢�����ε�</h2>

			<!-- �˻����� -->
			<div class="box_wrap sel_btn">
				<form name="searchFrm" method="get" action="<?$_SERVER['PHP_SELF']?>">
					<fieldset>
							<!--<a href="#" class="btn_s navy" style="min-width:100px;" onclick="subPopOpen('','');">�Һз��߰�</a>-->
					</fieldset>
				</form>
			</div><!-- // box_wrap -->

			<div class="data_left" style="width:70%">
				<div class="tit_wrap">
					<h3 class="tit_sub">�������ε� �����丮 - <?=$gubunname?></h3>
				</div>
				<div class="tb_type01">

					<table class="gridhover">

						<colgroup>											
							<col width="9%">
							<col width="9%">
							<col width="9%">
							<col width="9%">
							<col width="9%">
							<col width="15%">
							<col width="9%">
							<col width="auto">
							<col width="9%">
							<col width="2%">
						</colgroup>

						<thead>
						<tr>
							<th align="center">���ε�����</th>
							<th align="center">���ε�No</th>
							<th align="center">������</th>
							<th align="center">�����Ǽ�</th>
							<th align="center">���аǼ�</th>
							<th align="center">�������ϸ�</th>
							<th align="center">���ε���</th>
							<th align="center">���</th>
							<th align="center">���ε��Ͻ�</th>
							<th align="center">����</th>
						</tr>
						</thead>
						<tbody>
							<?if(!empty($listData)){?>
							<?foreach($listData as $key => $val){extract($val);?>
							<tr class="rowData" rol-data='<?=$upldate?>', rol-uplnum ='<?=$uplnum?>'  style="cursor:pointer;">
								<td align="center"><?=date("Y-m-d",strtotime($upldate)) ?></td>
								<td align="center"><?=$uplnum?></td>
								<td align="center"><?=$name?></td>
								<td align="center"><?=$cnt?></td>
								<td align="center"><?=$fcnt?></td>
								<td align="center"><?=$filename?></td>
								<td align="center"><?=$sname?></td>
								<td align="center"><?=$bigo?></td>
								<td align="center"><?=date("Y-m-d H:i:s",strtotime($idate))?></td>
								<td align="center"><i idata1="<?=$upldate?>" idata2="<?=$uplnum?>" class="w3-round yb_icon fa fa-trash-o delAction"  aria-hidden="true" style="border:0px;color:#999999;padding:0px 10px;margin-bottom:-1px;cursor:pointer;"></i></td>

							</tr>
							<?}}?>
						</tbody>
					</table>

				</div><!-- // tb_type01 -->

				<div style="text-align: center">		
					<ul class="pagination pagination-sm" style="margin: 10px">
					  <?=$pagination->create_links();?>
					</ul>
				</div>
			</div> <!--data_left end-->

			<div class="data_right" style="width:30%">
				<div class="tit_wrap">
					<h3 class="tit_sub">�������ε�</h3>
				</div>
				<div class="tb_type01 view">
					<form name="excelupload_form" class="ajaxForm" method="post" action="ga_menu3_70_action.php" ENCTYPE="multipart/form-data">
					<input type="hidden" name="upldate" id="upldate" value="<?=date("Y-m-d")?>">
					<input type="hidden" name="gubun" id="gubun" value="A">
					<input type="hidden" name="uplnum" value="">
					<input type="hidden" name="type" id="type" value="">
					<table>
						<colgroup>
							<col width="18%">
							<col width="32%">
							<col width="18%">
							<col width="32%">
						</colgroup>
						<tbody>
						<tr style="height:50px">
							<th><em class="impor">*</em>���ε�����</th>
							<td colspan=3>
								<b><?=date("Y-m-d")?></b>
							</td>
						</tr>
						<tr style="height:50px">
							<th><em class="impor">*</em>����</th>
							<td colspan=3>
								<b>�����ε�</b>
							</td>
						</tr>
						<tr style="height:50px">
							<th><em class="impor">*</em>�����</th>
							<td colspan=3>
								<select name="code" id="code" style="width:100%;height:30px "> 		
								  <option value="">����</option>
								  <?foreach($insData as $key => $val){?>
								  <option value="<?=$val['inscode']?>" <?if($code==$val['inscode']) echo "selected"?>><?=$val['name']?></option>
								  <?}?>
								</select>				
							</td>
						</tr>
						<tr style="height:50px" class="filetr">
							<th><em class="impor">*</em>���ϸ�</th>
							<td colspan=3>
								<!--<span class="input_type" style="width:37%;height:30px"><input type="text" name="filename" id="filename" value="" readonly></span>-->
								<input type="file" class="uploadFile" onchange="addFile(this);" accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" multiple="multiple" style="display:none;">
								<!--<a href="javascript:;" class="btn_s white upload_img" style="height:30px;padding-top:3px" >����ã��</a>-->
								<a href="javascript:;" class="btn_s white upload_img" style="width:20%;height:30px;padding-top:3px" >����ã��</a>
								<div id="file-list" class="file-list"></div>
							</td>
						</tr>

						<tr style="height:50px">
							<th>���</th>
							<td colspan=3>
								<span class="input_type" style="width:100%;height:30px"><input type="text" name="bigo" id="bigo" value=""></span> 
							</td>
						</tr>
						</tbody>
					</table>
					</form>
					<div class="tit_wrap">
						<span class="btn_wrap" >
							<a href="#" class="btn_s white" style="min-width:150px;height:40px;padding-top:8px;font-size:15px; margin-top:10px; margin-right:5px" onclick="resetbtn();">�ʱ�ȭ</a>
							<a href="#" class="btn_s navy uploadbtn" style="min-width:150px;height:40px;padding-top:8px;font-size:15px; margin-top:10px" onclick="submitForm(this.form);">EXCEL ���ε�</a>
						</span>
					</div>
				</div>
			</div>

		</fieldset>
	</div><!-- // content_wrap -->
</div>


<!-- // container -->
<!-- // wrap -->
<script type="text/javascript">

$(".upload_img").click(function(){		

	$(this).prev(".uploadFile").click();

});

$(document).ready(function(){


	// ���� �� ����� ����ȭ�� ���ε�
	if('<?=$_GET['save']?>' == 'Y'){
		opener.location.reload();
	}

	$(".rowData > td").click(function(){
		var trData = $(this).parent();
		var idx    = $(".rowData").index($(trData));
		var upldate  = $(".rowData").eq(idx).attr('rol-data');
		var uplnum  = $(".rowData").eq(idx).attr('rol-uplnum');

		var left = Math.ceil((window.screen.width - 1200)/2);
		var top = Math.ceil((window.screen.height - 1000)/2);

		// 8��°�� �����ǿ����� �˾� �ȿ������� ����(�� �߰��� �Ʒ� �ش� �ѹ� ����)
		if($(trData).find("td").index($(this))!='9'){
			var popOpen	= window.open("<?=$conf['homeDir']?>/sub/menu3/ga_menu3_70_list.php?upldate="+upldate+"&uplnum=" +uplnum ,"width=1200px,height=950px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes",target="_blank");
			popOpen.focus();
		}
 
	});


	// ����ó��
	$(".delAction").click(function(){
		var idx  = $(".delAction").index($(this));
		var upldate  = $(".rowData").eq(idx).attr('rol-data');
		var uplnum  = $(".rowData").eq(idx).attr('rol-uplnum');

		$("form[name='excelupload_form'] input[name='upldate']").val(upldate);
		$("form[name='excelupload_form'] input[name='uplnum']").val(uplnum);

		if(confirm("�����Ͻðڽ��ϱ�?")){
			$("form[name='excelupload_form'] input[name='type']").val("del");
			$("form[name='excelupload_form']").submit();
		}		 
	})
});



/* ���� ���� ���ε� */

var fileNo = 0;
var filesArr = new Array();

/* ÷������ �߰� */
function addFile(obj){
    var maxFileCnt = 10;   // ÷������ �ִ� ����
    var attFileCnt = document.querySelectorAll('.filebox').length;    // ���� �߰��� ÷������ ����
    var remainFileCnt = maxFileCnt - attFileCnt;    // �߰��� ÷�ΰ����� ����
    var curFileCnt = obj.files.length;  // ���� ���õ� ÷������ ����


    // ÷������ ���� Ȯ��
    if (curFileCnt > remainFileCnt) {
        alert("÷�������� �ִ� " + maxFileCnt + "�� ���� ÷�� �����մϴ�.");
    } else {
        for (const file of obj.files) {
            // ÷������ ����
            if (validation(file)) {
                // ���� �迭�� ���
                var reader = new FileReader();
                reader.onload = function () {
                    filesArr.push(file);
                };
                reader.readAsDataURL(file);

                // ��� �߰�
                let htmlData = '';
                htmlData += '<div id="file' + fileNo + '" class="filebox">';
                htmlData += '   <p class="name">' + file.name + '</p>';
                htmlData += '   <a class="delete" onclick="deleteFile(' + fileNo + ');"><i class="far fa-minus-square"></i></a>';
                htmlData += '</div>';
                $('.file-list').append(htmlData);
                fileNo++;
            } else {
                continue;
            }
        } // End for

    }
    // �ʱ�ȭ
    document.querySelector("input[type=file]").value = "";
}

/* ÷������ ���� */
function validation(obj){
    const fileTypes = ['application/pdf', 'image/gif', 'image/jpeg', 'image/png', 'image/bmp', 'image/tif', 'application/haansofthwp', 'application/x-hwp'];

    if (obj.name.length > 100) {
        alert("���ϸ��� 100�� �̻��� ������ ���ܵǾ����ϴ�.");
        return false;
    } else if (obj.size > (1000 * 1024 * 1024)) {  // ���� ��������� �����ʿ�
        alert("�ִ� ���� �뷮�� 1000MB�� �ʰ��� ������ ���ܵǾ����ϴ�.");
        return false;
    } else if (obj.name.lastIndexOf('.') == -1) {
        alert("Ȯ���ڰ� ���� ������ ���ܵǾ����ϴ�.");
        return false;
    } else {
        return true;
    }
}

/* ÷������ ���� */
function deleteFile(num) {
    document.querySelector("#file" + num).remove();
    filesArr[num].is_delete = true;
}

// ��ư disable
var isDisabled = true;

/* �� ���� */
function submitForm(frm) {

	if (isDisabled == false) {
		alert('�ʱ�ȭ ���� ���� ������ּ���!');
		return;
	}

	//alert(filesArr.length);
    // �������� ���
    //var form = document.querySelector("form");
    var formData = new FormData(frm);
    for (var i = 0; i < filesArr.length; i++) {
        // �������� ���� ���ϸ� �������Ϳ� ���
        if (!filesArr[i].is_delete) {
			console.log(filesArr[i]);
            formData.append("attach_file[]", filesArr[i]);
        }
    }

	var upldate = document.getElementById('upldate').value;
	var gubun	= document.getElementById('gubun').value;
	var type	= document.getElementById('type').value;
	var bigo	= document.getElementById('bigo').value;

	formData.append("upldate", upldate);	// ���ε�����
	formData.append("gubun", gubun);		// ����
	formData.append("type", type);			// �۾�����
	formData.append("imgcnt", i);			// ���ε����ϼ�
	formData.append("bigo", bigo);			// ���

    $.ajax({
        url: 'ga_menu3_70_action.php',
        dataType: 'json',
		method : "POST",
        data: formData,
        async: true,
        timeout: 30000,
        cache: false,
		contentType: false,
		processData: false,
        //headers: {'cache-control': 'no-cache', 'pragma': 'no-cache'},
        beforeSubmit:  showRequest_modal_excel,  // pre-submit callback 
		success:       processJson_modal_excel  // post-submit callback 
    })
	
}





function resetbtn(){
	$("#file-list").empty();
	$("#code").val("");
	$("#bigo").val("");

	// �������ε� ��ư�Ӽ� �ʱ�ȭ
	isDisabled = true;	
	$('.uploadbtn').removeClass('abtn_disable');
}




// pre-submit callback 
function showRequest_modal_excel(formData, jqForm, options) { 
	var queryString = $.param(formData); 
	return true; 
} 
 
// post-submit callback 
function processJson_modal_excel(data) { 
	//console.log(data);
	if(data.message){
		alert(data.message);
		//opener.location.reload();
	}

	// ������
	if(data.result==''){	
		// �������ε� ��ư�Ӽ� disabled
		isDisabled = false;
		$('.uploadbtn').addClass('abtn_disable');		
		//opener.location.reload();
	}


}
 

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>