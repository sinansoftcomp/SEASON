<?
//include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

$gubun = "A"; // ���
$gubunname = "���";


?>

<!-- html���� -->
<style>
body{background-image: none;}
</style>


<div class="popup_wrap" style="display:block" ><!-- popup ���½� html,body�� overflow:hidden --> 
	  <div class="popup_con" style="width:710px">

	  	<h1 class="pop_tit" id="title_txt">��࿢�����ε�</h1>
			<a class="btn_close" onclick="modal_close();"><span class="blind">�ݱ�</span></a>
			<div class="popup_con_in">
				<div class="tit_wrap" style="margin-top:0px">
					<span class="btn_wrap">
						<a class="btn_s white" onclick="modal_close();">�ݱ�</a>
					</span>
				</div>


		<fieldset>
			<div class="data_center" style="width:700px"  id="id_input" >
				<div class="tb_type01 view">
					<form name="excelupload_form" class="ajaxForm" method="post" action="ga_menu3_52_action.php" ENCTYPE="multipart/form-data">
					<input type="hidden" name="upldate" id="upldate" value="<?=date("Y-m-d")?>">
					<input type="hidden" name="gubun" id="gubun" value="A">
					<input type="hidden" name="gubunsub" id="gubunsub" value="">
					<input type="hidden" name="uplnum" value="">
					<input type="hidden" name="type" id="type" value="">
					<input type="hidden" name="upload_yn" id="upload_yn" value="">

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
								<b style="font-size: x-large;">������ε�</b>
							</td>
						</tr>

						<tr style="height:50px" class="filetr">
							<th><em class="impor">*</em>���ϸ�</th>
							<td colspan=3>
								<!--<span class="input_type" style="width:37%;height:30px"><input type="text" name="filename" id="filename" value="" readonly></span>-->
								<input type="file" class="uploadFile" onchange="addFile(this);" accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" multiple="multiple" style="display:none;">
								<!--<a href="javascript:;" class="btn_s white upload_img" style="height:30px;padding-top:3px" >����ã��</a>-->
								<a href="javascript:;" class="btn_s white upload_img" style="width:20%;height:30px;padding-top:3px" >����ã��</a>
								<div id="file-list" class="file-list" style="height: 400px;"></div>
							</td>
						</tr>
						</tbody>
					</table>
					</form>
					<div class="tit_wrap" >
						<span class="btn_wrap" >
							<a class="btn_s white" style="min-width:150px;height:40px;padding-top:8px;font-size:15px; margin-top:10px; margin-right:5px" onclick="resetbtn();">�ݱ�</a>
							<a class="btn_s white uploadbtn"  id="up_but" style="min-width:150px;height:40px;padding-top:8px;font-size:15px; margin-top:10px" onclick="submitForm(this.form);">EXCEL ���ε�</a>
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


// �ݱ�
function modal_close(){	
	$("#modal2").hide();
	location.reload();
}

function disabledFalse() {
    document.querySelector('#up_but').disabled = ture;
}



$(document).ready(function(){
	var options = { 
		dataType:  'json',
		beforeSubmit:  showRequest_modal,  // pre-submit callback 
		success:       processJson_modal  // post-submit callback 
	}; 

 
	$('.ajaxForm').ajaxForm(options);

	// ���� �� ����� ����ȭ�� ���ε�
	if('<?=$_GET['save']?>' == 'Y'){
		opener.location.reload();
	}

 
 
});

// pre-submit callback 
function showRequest_modal(formData, jqForm, options) { 
	var queryString = $.param(formData); 
	return true; 
} 
 // post-submit callback 
function processJson_modal(data) { 
	$("#div_load_image").hide();
	console.log(data);
	if(data.message){
		location.reload();
		alert(data.message);
	}
	if(data.result==''){	// ������
	}
}


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

 

/* �� ���� */
function submitForm(frm) {

 

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

	var upload_yn	= document.getElementById('upload_yn').value;

	//alert(upload_yn);
	if (upload_yn == 'N')	{
		 alert('���� ���ε带 �� �� �����ϴ� !. ');
		 return;
	}
 
	
	formData.append("upldate", upldate);	// ���ε�����
	formData.append("gubun", gubun);		// ����
	formData.append("type", 'in');			// �۾�����
	formData.append("imgcnt", i);			// ���ε����ϼ�
 
	
	$("form[name='excelupload_form'] input[name='upload_yn']").val('N'); //���߾��ε� ������ 

	$("#div_load_image").show();

    $.ajax({
        url: 'ga_menu3_52_action.php',
        dataType: 'json',
		method : "POST",
        data: formData,
        async: true,
        timeout: 1200000,
        cache: false,
		contentType: false,
		processData: false,
        //headers: {'cache-control': 'no-cache', 'pragma': 'no-cache'},
		beforeSubmit:  showRequest_modal,  // pre-submit callback 
		success:       processJson_modal  // post-submit callback 

    })	
}

function resetbtn(){
	$("#file-list").empty();
 

	// �������ε� ��ư�Ӽ� �ʱ�ȭ
 	$("form[name='excelupload_form'] input[name='upload_yn']").val('Y');
    document.querySelector("input[type=file]").value = "";
	location.reload(); //�ʱ�ȭ�� �ȵ�
}

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>