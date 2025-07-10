<!--<div style="font-size:30px;font-weight:700;margin:30px 0 0 30px">프로그램 준비중입니다.</div>-->
<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

$time = time();
$prev_month = strtotime("-1 month", $time);

$sdate1 =  date("Y-m",$prev_month);

if($cnt>0){
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);
	$message = '해당월은 이미 확정처리되었습니다.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
	echo json_encode($returnJson);
	exit;			
}

?>
<style>
body{background-image: none;}
</style>

<div class="container">
	<div class="content_wrap">

		<div class="tit_wrap">
			<h3 class="tit_sub" style="margin-top:10px">엑셀업로드</h3>
		</div>

		<form id="excelup_form" name="excelup_form" class="ajaxForm_excelup" method="post" action="ga_menu5_06_action.php" enctype="multipart/form-data">

			<div class="box_wrap sel_btn" style="margin-top:20px;height:70px"></br>
				
				<div class="row">
					<input type="file" id="file1" name="file1"
							 accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"></input>									
				</div>

			</div>
		</form>

		<div class="tit_wrap" align=right>
			<h4 id="wait" class="blink" style="color:#F15F5F"></h4>
		</div>

		<div align=center id="button" style="margin-top:10px">
			<span class="btn_wrap">
				<a href="javascript:;" class="btn_s white hover_btn" id="excelup" style="width:150px;" onclick="excelupPopOpen();">엑셀업로드</a>
			</span>
		</div>
		<div align="right" style="margin-top:20px"><b style="color:#E0844F"><em class="impor">*</em>엑셀리스트를 다운받은 후 업로드를 진행해주세요.</b></div>
	</div>
</div>


<script type="text/javascript">

	// 계산처리
	function excelupPopOpen(){
		if(confirm("엑셀업로드를 진행하시겠습니까?")){
			$("#div_load_image").css({
			   "position" : "absolute",
			   "top" : "25%",
			   "left" : "40%"
			});
			$("#div_load_image").show();

			$("#excelup").css("visibility","hidden");
			
			$("form[name='excelup_form']").submit();
		}
	}

	$(document).ready(function(){


		var options = { 
			dataType:  'json',
			beforeSubmit:  showRequest_modal_excelup,  // pre-submit callback 
			success:       processJson_modal_excelup  // post-submit callback 
		}; 

		$('.ajaxForm_excelup').ajaxForm(options);

	});

	// pre-submit callback 
	function showRequest_modal_excelup(formData, jqForm, options) { 
		var queryString = $.param(formData); 
		return true; 
	} 
	 
	// post-submit callback 
	function processJson_modal_excelup(data) { 
		console.log(data);
		$("#div_load_image").hide();
		$("#excelup").css("visibility","visibility");
		if(data.message){
			alert(data.message);
		}

		if(data.result==""){
			opener.$('.btn_search').trigger("click");	//조회버튼클릭
			self.close();
		}
	}

</script>


<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>