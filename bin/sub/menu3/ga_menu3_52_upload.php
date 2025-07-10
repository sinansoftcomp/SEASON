<?
//include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

$gubun = "A"; // 계약
$gubunname = "계약";


?>

<!-- html영역 -->
<style>
body{background-image: none;}
</style>


<div class="popup_wrap" style="display:block" ><!-- popup 오픈시 html,body에 overflow:hidden --> 
	  <div class="popup_con" style="width:710px">

	  	<h1 class="pop_tit" id="title_txt">계약엑셀업로드</h1>
			<a class="btn_close" onclick="modal_close();"><span class="blind">닫기</span></a>
			<div class="popup_con_in">
				<div class="tit_wrap" style="margin-top:0px">
					<span class="btn_wrap">
						<a class="btn_s white" onclick="modal_close();">닫기</a>
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
							<th><em class="impor">*</em>업로드일자</th>
							<td colspan=3>
								<b><?=date("Y-m-d")?></b>
							</td>
						</tr>
						<tr style="height:50px">
							<th><em class="impor">*</em>구분</th>
							<td colspan=3>
								<b style="font-size: x-large;">계약료업로드</b>
							</td>
						</tr>

						<tr style="height:50px" class="filetr">
							<th><em class="impor">*</em>파일명</th>
							<td colspan=3>
								<!--<span class="input_type" style="width:37%;height:30px"><input type="text" name="filename" id="filename" value="" readonly></span>-->
								<input type="file" class="uploadFile" onchange="addFile(this);" accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" multiple="multiple" style="display:none;">
								<!--<a href="javascript:;" class="btn_s white upload_img" style="height:30px;padding-top:3px" >파일찾기</a>-->
								<a href="javascript:;" class="btn_s white upload_img" style="width:20%;height:30px;padding-top:3px" >파일찾기</a>
								<div id="file-list" class="file-list" style="height: 400px;"></div>
							</td>
						</tr>
						</tbody>
					</table>
					</form>
					<div class="tit_wrap" >
						<span class="btn_wrap" >
							<a class="btn_s white" style="min-width:150px;height:40px;padding-top:8px;font-size:15px; margin-top:10px; margin-right:5px" onclick="resetbtn();">닫기</a>
							<a class="btn_s white uploadbtn"  id="up_but" style="min-width:150px;height:40px;padding-top:8px;font-size:15px; margin-top:10px" onclick="submitForm(this.form);">EXCEL 업로드</a>
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


// 닫기
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

	// 수정 및 저장시 메인화면 리로드
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
	if(data.result==''){	// 성공시
	}
}


/* 다중 파일 업로드 */
var fileNo = 0;
var filesArr = new Array();

/* 첨부파일 추가 */
function addFile(obj){
    var maxFileCnt = 10;   // 첨부파일 최대 개수
    var attFileCnt = document.querySelectorAll('.filebox').length;    // 기존 추가된 첨부파일 개수
    var remainFileCnt = maxFileCnt - attFileCnt;    // 추가로 첨부가능한 개수
    var curFileCnt = obj.files.length;  // 현재 선택된 첨부파일 개수


    // 첨부파일 개수 확인
    if (curFileCnt > remainFileCnt) {
        alert("첨부파일은 최대 " + maxFileCnt + "개 까지 첨부 가능합니다.");
    } else {
        for (const file of obj.files) {
            // 첨부파일 검증
            if (validation(file)) {
                // 파일 배열에 담기
                var reader = new FileReader();
                reader.onload = function () {
                    filesArr.push(file);
                };
                reader.readAsDataURL(file);

                // 목록 추가
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
    // 초기화
    document.querySelector("input[type=file]").value = "";
}

/* 첨부파일 검증 */
function validation(obj){
    const fileTypes = ['application/pdf', 'image/gif', 'image/jpeg', 'image/png', 'image/bmp', 'image/tif', 'application/haansofthwp', 'application/x-hwp'];

    if (obj.name.length > 100) {
        alert("파일명이 100자 이상인 파일은 제외되었습니다.");
        return false;
    } else if (obj.size > (1000 * 1024 * 1024)) {  // 추후 어느정도는 제재필요
        alert("최대 파일 용량인 1000MB를 초과한 파일은 제외되었습니다.");
        return false;
    } else if (obj.name.lastIndexOf('.') == -1) {
        alert("확장자가 없는 파일은 제외되었습니다.");
        return false;
    } else {
        return true;
    }
}

/* 첨부파일 삭제 */
function deleteFile(num) {
    document.querySelector("#file" + num).remove();
    filesArr[num].is_delete = true;
}

 

/* 폼 전송 */
function submitForm(frm) {

 

	//alert(filesArr.length);
    // 폼데이터 담기
    //var form = document.querySelector("form");
    var formData = new FormData(frm);
    for (var i = 0; i < filesArr.length; i++) {
        // 삭제되지 않은 파일만 폼데이터에 담기
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
		 alert('이중 업로드를 할 수 없습니다 !. ');
		 return;
	}
 
	
	formData.append("upldate", upldate);	// 업로드일자
	formData.append("gubun", gubun);		// 구분
	formData.append("type", 'in');			// 작업구분
	formData.append("imgcnt", i);			// 업로드파일수
 
	
	$("form[name='excelupload_form'] input[name='upload_yn']").val('N'); //이중업로드 방지용 

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
 

	// 엑셀업로드 버튼속성 초기화
 	$("form[name='excelupload_form'] input[name='upload_yn']").val('Y');
    document.querySelector("input[type=file]").value = "";
	location.reload(); //초기화가 안됨
}

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>