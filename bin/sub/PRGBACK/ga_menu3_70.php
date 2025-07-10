<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

// 기본 페이지 셋팅
$page = ($_GET['page']) ? $_GET['page'] : 1;
$page_row	= 18;

$limit1 = ($page-1)*$page_row+1;
$limit2 = $page*$page_row;

$gubun = "A"; // 계약
$gubunname = "계약";


// 엑셀업로드 히스토리 리스트
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

// 엑셀업로드 히스토리 리스트 총 건수
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


// 보험사 가져오기
$sql= "select inscode, name from inssetup where scode = '".$_SESSION['S_SCODE']."' and useyn = 'Y' order by num, inscode";
$qry= sqlsrv_query( $mscon, $sql );
$insData	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
  $insData[] = $fet;
}

// 페이지 클래스 시작
// 로드
include_once($conf['rootDir'].'/include/class/Pagination.php');

// 설정
$pagination = new Pagination(array(
		'base_url' => $_SERVER['PHP_SELF']."?scode=".$_GET['scode'],
		'per_page' => $page_row,
		'total_rows' => $totalResult['CNT'],
		'cur_page' => $page,
));

sqlsrv_free_stmt($result);
sqlsrv_close($mscon);

?>

<!-- html영역 -->
<style>
body{background-image: none;}
.container{margin:0px 0px 0px 10px;}
.box_wrap {margin-bottom:10px}
.tb_type01 th, .tb_type01 td {padding: 8px 0}



</style>

<div class="container">
	<div class="content_wrap">
		<fieldset>
			<legend>계약엑셀업로드</legend>
			<h2 class="tit_big">계약엑셀업로드</h2>

			<!-- 검색조건 -->
			<div class="box_wrap sel_btn">
				<form name="searchFrm" method="get" action="<?$_SERVER['PHP_SELF']?>">
					<fieldset>
							<!--<a href="#" class="btn_s navy" style="min-width:100px;" onclick="subPopOpen('','');">소분류추가</a>-->
					</fieldset>
				</form>
			</div><!-- // box_wrap -->

			<div class="data_left" style="width:70%">
				<div class="tit_wrap">
					<h3 class="tit_sub">엑셀업로드 히스토리 - <?=$gubunname?></h3>
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
							<th align="center">업로드일자</th>
							<th align="center">업로드No</th>
							<th align="center">보험사명</th>
							<th align="center">성공건수</th>
							<th align="center">실패건수</th>
							<th align="center">엑셀파일명</th>
							<th align="center">업로드사원</th>
							<th align="center">비고</th>
							<th align="center">업로드일시</th>
							<th align="center">삭제</th>
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
					<h3 class="tit_sub">엑셀업로드</h3>
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
							<th><em class="impor">*</em>업로드일자</th>
							<td colspan=3>
								<b><?=date("Y-m-d")?></b>
							</td>
						</tr>
						<tr style="height:50px">
							<th><em class="impor">*</em>구분</th>
							<td colspan=3>
								<b>계약업로드</b>
							</td>
						</tr>
						<tr style="height:50px">
							<th><em class="impor">*</em>보험사</th>
							<td colspan=3>
								<select name="code" id="code" style="width:100%;height:30px "> 		
								  <option value="">선택</option>
								  <?foreach($insData as $key => $val){?>
								  <option value="<?=$val['inscode']?>" <?if($code==$val['inscode']) echo "selected"?>><?=$val['name']?></option>
								  <?}?>
								</select>				
							</td>
						</tr>
						<tr style="height:50px" class="filetr">
							<th><em class="impor">*</em>파일명</th>
							<td colspan=3>
								<!--<span class="input_type" style="width:37%;height:30px"><input type="text" name="filename" id="filename" value="" readonly></span>-->
								<input type="file" class="uploadFile" onchange="addFile(this);" accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" multiple="multiple" style="display:none;">
								<!--<a href="javascript:;" class="btn_s white upload_img" style="height:30px;padding-top:3px" >파일찾기</a>-->
								<a href="javascript:;" class="btn_s white upload_img" style="width:20%;height:30px;padding-top:3px" >파일찾기</a>
								<div id="file-list" class="file-list"></div>
							</td>
						</tr>

						<tr style="height:50px">
							<th>비고</th>
							<td colspan=3>
								<span class="input_type" style="width:100%;height:30px"><input type="text" name="bigo" id="bigo" value=""></span> 
							</td>
						</tr>
						</tbody>
					</table>
					</form>
					<div class="tit_wrap">
						<span class="btn_wrap" >
							<a href="#" class="btn_s white" style="min-width:150px;height:40px;padding-top:8px;font-size:15px; margin-top:10px; margin-right:5px" onclick="resetbtn();">초기화</a>
							<a href="#" class="btn_s navy uploadbtn" style="min-width:150px;height:40px;padding-top:8px;font-size:15px; margin-top:10px" onclick="submitForm(this.form);">EXCEL 업로드</a>
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


	// 수정 및 저장시 메인화면 리로드
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

		// 8번째열 삭제탭에서는 팝업 안열리도록 설정(열 추가시 아래 해당 넘버 수정)
		if($(trData).find("td").index($(this))!='9'){
			var popOpen	= window.open("<?=$conf['homeDir']?>/sub/menu3/ga_menu3_70_list.php?upldate="+upldate+"&uplnum=" +uplnum ,"width=1200px,height=950px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes",target="_blank");
			popOpen.focus();
		}
 
	});


	// 삭제처리
	$(".delAction").click(function(){
		var idx  = $(".delAction").index($(this));
		var upldate  = $(".rowData").eq(idx).attr('rol-data');
		var uplnum  = $(".rowData").eq(idx).attr('rol-uplnum');

		$("form[name='excelupload_form'] input[name='upldate']").val(upldate);
		$("form[name='excelupload_form'] input[name='uplnum']").val(uplnum);

		if(confirm("삭제하시겠습니까?")){
			$("form[name='excelupload_form'] input[name='type']").val("del");
			$("form[name='excelupload_form']").submit();
		}		 
	})
});



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

// 버튼 disable
var isDisabled = true;

/* 폼 전송 */
function submitForm(frm) {

	if (isDisabled == false) {
		alert('초기화 이후 새로 등록해주세요!');
		return;
	}

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
	var bigo	= document.getElementById('bigo').value;

	formData.append("upldate", upldate);	// 업로드일자
	formData.append("gubun", gubun);		// 구분
	formData.append("type", type);			// 작업구분
	formData.append("imgcnt", i);			// 업로드파일수
	formData.append("bigo", bigo);			// 비고

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

	// 엑셀업로드 버튼속성 초기화
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

	// 성공시
	if(data.result==''){	
		// 엑셀업로드 버튼속성 disabled
		isDisabled = false;
		$('.uploadbtn').addClass('abtn_disable');		
		//opener.location.reload();
	}


}
 

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>