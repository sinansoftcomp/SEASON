<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

/*
	���Ѱ���
	bin/include/source/auch_chk.php
*/
$pageTemp	= explode("/",$_SERVER['PHP_SELF']);
$auth = auth_Ser($_SESSION['S_MASTER'], $pageTemp[count($pageTemp)-1], $_SESSION['S_SKEY'], $mscon);
if($auth != "Y"){
	sqlsrv_close($mscon);
	alert('�ش� �޴��� ���� ������ �����ϴ�. �����ڿ��� ���� �ٶ��ϴ�.');
	exit;
}
 
if ($_GET['SDATE1']) {
	$sdate1 =  $_GET['SDATE1'];
}else{
	$sdate1 =  date("Y-m");
}
$yymm_nm = substr($sdate1,0,4).substr($sdate1,5,2); 
/* ------------------------------------------------------------
	End Date �ʱⰪ ����
------------------------------------------------------------ */
$sql= "
		select top 1 'N10000' code
		from inssetup
		WHERE scode = '".$_SESSION['S_SCODE']."' and useyn = 'Y'
		order by inscode 
		" ;
$qry =  sqlsrv_query($mscon, $sql);
$totalResult =  sqlsrv_fetch_array($qry); 
$topcode = $totalResult['code'];

$sql= "
		select count(*) CNT
		from ins_ipmst
		WHERE scode = '".$_SESSION['S_SCODE']."' and yymm='".$yymm_nm."' and nmgubun is null
		" ;
$qry =  sqlsrv_query($mscon, $sql);
$Resultnm =  sqlsrv_fetch_array($qry); 
$nmcnt = $Resultnm['CNT'];

sqlsrv_free_stmt($result);
sqlsrv_close($mscon);

?>

<!-- html���� -->
<style>
body{background-image: none;}
</style>

<div class="container">
	<div class="content_wrap">
		<fieldset>

			<!-- �˻����� -->
			<div class="box_wrap sel_btn">
				<form name="searchFrm" id="searchFrm" method="get" action="<?$_SERVER['PHP_SELF']?>" enctype="multipart/form-data">
 				<input type="hidden" name="id" id="id" value="">
				<input type="hidden" name="excelcnt" id="excelcnt" value="">
					<fieldset>
						<div>
							<span class="ser_font">�����</span> 

							<span class="input_type date ml10" style="width:114px;margin-left: 0px;">
								<input type="text" class="Cal_ym" placeholder="YYYY-MM" id="SDATE1" name="SDATE1" value="<?=$sdate1?>" readonly>
							</span> 

							<select name="nmyn" id="nmyn" class="srch_css" style="width:100px;"> 		
							  <option value="">��ü����</option>
							  <option value="Y">��Ī</option>
							  <option value="N">���Ī</option>
							</select>							

							<p class="response_block" style="margin-left:10px">
								<span class="btn_wrap">			
									<a class="btn_s white hover_btn btn_search" style="width:150px;margin: 0px;">��ȸ</a>	
									<a class="btn_s white hover_btn btn_search2 excelBtn" style="width:150px;margin: 0px;">��������Ʈ</a>							
									<a href="javascript:;" class="btn_s white hover_btn" style="width:150px;" onclick="excelupPopOpen();">�������ε�</a>
									<a href="javascript:;" class="btn_s white hover_btn nmgubunbtn" style="width:150px;" onclick="nmgubun_update();">���Ī����</a>
								</span>	  
							</p>

						</div>
					</fieldset>
				</form>
			</div><!-- // box_wrap -->

			<div class="data_left_jojik" > <!--data_left start -->
				<div class="tb_type01 kwndatalist div_grid" style="overflow-y:auto;" >
					<table class="gridhover">
						<colgroup>											
							<col width="100%">
						</colgroup>

						<thead>
							<tr class="rowTop">
								<th align="center">�����</th>
							</tr>
						</thead>							
						</tr>
					</table>
					<div   id="tree-container">	</div>
				</div><!-- // tb_type01 -->			
			</div><!--data_left end -->

			<div class="data_right_jojik" id="nmswon" style="padding: 0px;overflow-x:auto;"> <!--data_right start -->

			</div>  <!--data_right end -->

		</fieldset>
	</div><!-- // content_wrap -->
</div>

<form id="nmgubun_form" name="nmgubun_form" class="ajaxForm_nmgubun" method="post" action="ga_menu5_06_action3.php">
	<input type="hidden" name="type_nm" id="type_nm" value="nmgubun">
	<input type="hidden" name="yymm_nm" id="yymm_nm" value="">
</form>

<!-- // container -->
<!-- // wrap -->
<script type="text/javascript">

 // ��ȸ��ư Ŭ�� �� 
$(".btn_search").click(function(){
 	common_ser();
}); 

// ������ȸ �Լ�(bin/js/common.js ȣ��)
function common_ser(){
	var formData = $("#searchFrm").serialize();

	// ajax �������� ����
	ajaxLodingTarket('ga_menu5_06_list.php',$('#nmswon'),formData);
}

// ������
function get_jstree() {
	$("#tree-container").jstree({  
		'core': {
			'data' : {
				"url"	 : "/bin/sub/help/jojik_inssetup_tree_json.php",
				"dataType" : "json"	
			}
		} 
		}).on("loaded.jstree",function(e,data){
			 $('#tree-container').jstree('open_all');				// ��ü����
			 $('#tree-container').jstree($("#"+"<?=$topcode?>"+"_anchor").trigger("click"));
	});
}

function excelupPopOpen(){

	var left = Math.ceil((window.screen.width - 400)/2);
	var top = Math.ceil((window.screen.height - 400)/2);
	var popOpen	= window.open("<?=$conf['homeDir']?>/sub/menu5/ga_menu5_06_pop.php","excelup1","width=550px,height=270px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
	//popOpen.focus();
}

function nmgubun_update(){

	//var ksman = $("form[name='excelupg_form'] input[name='ksman']").val();
	if(confirm('���Ī������ �����Ͻðڽ��ϱ�?')){
		$("form[name='nmgubun_form']").submit();
	}
}

// Node �������� ��.
$('#tree-container').on("select_node.jstree", function (e, data) {
	var id = data.instance.get_node(data.selected).id;
	$("#id").val(id); 
	common_ser(id);
});

$(document).ready(function(){	
	
	var yymm_nm = '<?=$sdate1?>';
	yymm_nm=yymm_nm.substr(0,4)+yymm_nm.substr(5,2);
	$('#yymm_nm').val(yymm_nm);

	$('#SDATE1').change(function(){
		yymm_nm = this.value;
		yymm_nm=yymm_nm.substr(0,4)+yymm_nm.substr(5,2);
		$('#yymm_nm').val(yymm_nm);
	});	

	$(".excelBtn").click(function(){
		if($('#excelcnt').val() == 0 ){
			alert('�������� �����Ͱ� �������� �ʽ��ϴ�.');
		}else{
			if(confirm("������ ���������ðڽ��ϱ�?")){
				$("form[name='searchFrm']").attr("action","ga_menu5_06_excellist.php");
				$("form[name='searchFrm']").submit();
				$("form[name='searchFrm']").attr("action","<?$_SERVER['PHP_SELF']?>");
			}
		}
	});

	get_jstree();

	var options = { 
		dataType:  'json',
		beforeSubmit:  showRequest_modal_nmgubun,  // pre-submit callback 
		success:       processJson_modal_nmgubun  // post-submit callback 
	}; 

	$('.ajaxForm_nmgubun').ajaxForm(options);


});

// pre-submit callback 
function showRequest_modal_nmgubun(formData, jqForm, options) { 
	var queryString = $.param(formData); 
	return true; 
} 
 
// post-submit callback 
function processJson_modal_nmgubun(data) { 
	if(data.message){
		alert(data.message);
	}

	if(data.result==""){
		common_ser();
	}
}

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>