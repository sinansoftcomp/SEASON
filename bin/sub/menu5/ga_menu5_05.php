<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

/*
	권한관리
	bin/include/source/auch_chk.php
*/
$pageTemp	= explode("/",$_SERVER['PHP_SELF']);
$auth = auth_Ser($_SESSION['S_MASTER'], $pageTemp[count($pageTemp)-1], $_SESSION['S_SKEY'], $mscon);
if($auth != "Y"){
	sqlsrv_close($mscon);
	echo "<script> alert('해당 메뉴에 대해 권한이 없습니다. 관리자에게 문의 바랍니다.'); self.close(); </script>";
}


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
.progress-bar {
	width: 100%;
	background-color: transparent;
}

.progress {
	height: 7px;
	margin-bottom: 10px;
	background-color: #4374D9;
	color: white;
	text-align: center;
	transition: width 0.5s;
}

@keyframes blink-effect {
  50% {
    opacity: 0;
  }
}

.blink {
  animation: blink-effect 1s step-end infinite;
}
</style>

<div class="container">
	<div class="content_wrap">


		<div class="tit_wrap">
			<h3 class="tit_sub" style="margin-top:10px">지급수수료 계산처리</h3>
		</div>

		<form id="sucalc_form" name="sucalc_form" class="ajaxForm_sucalc" method="post" action="ga_menu5_05_action.php" >
			<input type="hidden" name="pname" value="">
			<input type="hidden" name="hbit" value="">

			<div class="box_wrap sel_btn" style="margin-top:20px;height:70px"></br>
				
				<div class="row">
					<span  class="ser_font" style="font-size: large;margin-left:170px"> 정산월</span>
					<span class="input_type date ml10" style="width:114px;margin-left: 0px;">
						<input type="text" class="Cal_ym" placeholder="YYYY-MM" id="SDATE1" name="SDATE1" value="<?=$sdate1?>">
					</span> 
				</div>
				<div class="progress-bar" style="margin-top:14px">
					<div class="progress" id="jisu_progress"></div>
				</div>

			</div>
		</form>

		<div class="tit_wrap" align=right>
			<h4 id="wait" class="blink" style="color:#F15F5F"></h4>
		</div>

		<div align=center id="button" style="margin-top:10px">
			<span class="btn_wrap">
				<a href="#" class="btn_s white hover_btn btn_search " style="width:140px;" onclick="sucalc_update();">계산처리</a>
				<a href="#" class="btn_s white hover_btn btn_search " style="width:140px;" onclick="suhwak_update();">확정처리</a>
			</span>
		</div>

	</div>
</div>

<script type="text/javascript">

	// 계산처리
	function sucalc_update(){
		if(confirm("계산처리를 진행하시겠습니까?")){
			$("#button").hide();
			updateProgressBar('jisu_progress', 25);
			$("form[name='sucalc_form'] input[name='pname']").val('process_1');
			$("form[name='sucalc_form']").submit();
		}
	}

	// 확정처리
	function suhwak_update(){
		if(confirm("확정처리를 진행하시겠습니까?")){
			$("form[name='sucalc_form'] input[name='hbit']").val('hwak');
			$("form[name='sucalc_form']").submit();
		}
	}

	// 프로그레스 바 업데이트 함수
	function updateProgressBar(id, percentage) {
		const progressBar = document.getElementById(id);
		progressBar.style.width = percentage + '%';

		const wait = document.getElementById("wait");
		if(percentage != 0){
			wait.textContent = '잠시만 기다려주세요... ( ' + percentage + '% )';
		}else{
			wait.textContent = '';
		}
		
	}

	$(document).ready(function(){

		// 초기화
		updateProgressBar('jisu_progress', 0);

		var options = { 
			dataType:  'json',
			beforeSubmit:  showRequest_modal_sucalc,  // pre-submit callback 
			success:       processJson_modal_sucalc  // post-submit callback 
		}; 

		$('.ajaxForm_sucalc').ajaxForm(options);

	});

	// pre-submit callback 
	function showRequest_modal_sucalc(formData, jqForm, options) { 
		//var checkdata = $("#sucalc_form").serialize()
		//alert(checkdata);
		var queryString = $.param(formData); 
		return true; 
	} 
	 
	// post-submit callback 
	function processJson_modal_sucalc(data) { 
		// 실패시
		if(data.result=='error'){
			$("#button").show();
			updateProgressBar('jisu_progress', 0);
			alert(data.message);
			location.reload();
		}

		// 성공시
		if(data.result=='suc'){
			//opener.location.reload();
			if(data.pname == "process_1"){
				updateProgressBar('jisu_progress', 50);
				$("form[name='sucalc_form'] input[name='pname']").val('process_2');
				$("form[name='sucalc_form']").submit();
			}else if(data.pname == "process_2"){
				updateProgressBar('jisu_progress', 75);
				$("form[name='sucalc_form'] input[name='pname']").val('process_3');
				$("form[name='sucalc_form']").submit();
			}else if(data.pname == "process_3"){
				updateProgressBar('jisu_progress', 100);
				$("form[name='sucalc_form'] input[name='pname']").val('process_4');
				$("form[name='sucalc_form']").submit();
			}else if(data.pname == "process_4"){
				$("#button").show();
				updateProgressBar('jisu_progress', 0);
				alert(data.message);
				location.reload();
			}

			if(data.rtype == "up"){
				alert(data.message);
				location.reload();
			}
		}
	}

</script>


<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>