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
 
$sdate1 =  date("Y-m-01");
$lastday = DATE('t', strtotime($sdate1));
$sdate2 =  date("Y-m-".$lastday);
/* ------------------------------------------------------------
	End Date �ʱⰪ ����
------------------------------------------------------------ */
// ������ ù��° ���� Ʈ�� �������� ��
$sql= "select top 1 'N1'+bcode fbonbu from bonbu where scode = '".$_SESSION['S_SCODE']."' order by num ";
$result  = sqlsrv_query( $mscon, $sql );
$row =  sqlsrv_fetch_array($result); 

$fbonbu	=	$row['fbonbu'];

sqlsrv_free_stmt($result);
sqlsrv_close($mscon);

?>

<!-- html���� -->
<style>

</style>

<div class="container">
	<div class="content_wrap">
		<fieldset>

			<!-- �˻����� -->
			<div class="box_wrap sel_btn">
				<form name="searchFrm" id="searchFrm" method="get" action="<?$_SERVER['PHP_SELF']?>">
 				<input type="hidden" name="id" id="id" value="">
 				<input type="hidden" name="btn" id="btn" value="">
					<fieldset>
 
						<div class="">
							<span  class="ser_font" style="font-size: larger;"> �����</span> 
							<button type="button" class="btn_prev" name="yp" id="yp" onclick="d_ser('YP');"><span class="blind">����</span></button>
							<span class="input_type date ml10" style="width:114px;margin-left: 0px;">
								<input type="text" class="Calnew" placeholder="YYYY-MM-DD" id="SDATE1" name="SDATE1" value="<?=$sdate1?>">
							</span> 
							<span class="dash"> ~ </span>
							<span class="input_type date" style="width:114px">
								<input type="text" class="Calnew" placeholder="YYYY-MM-DD" id="SDATE2" name="SDATE2" value="<?=$sdate2?>">
							</span>
							<button type="button" class="btn_next" name="yn" id="yn" onclick="d_ser('YN');"><span class="blind">����</span></button>
							
							<p class="response_block" style="margin-left:10px">
								<span class="btn_wrap">
									<a class="btn_s white"	name="mp"   id="mp"   onclick="d_ser('MP');">����</a>
									<a class="btn_s white"		name="md"   id="md"     onclick="d_ser('MD');">���</a>
								</span>					
								<span class="btn_wrap" style="margin-left:10px">							
									<a class="btn_s white" name="m1"  id="m1" onclick="d_ser('M1');">1��</a>
									<a class="btn_s white" name="m2"  id="m2" onclick="d_ser('M2');">2��</a>
									<a class="btn_s white" name="m3"  id="m3" onclick="d_ser('M3');">3��</a>
									<a class="btn_s white" name="m4"  id="m4" onclick="d_ser('M4');">4��</a>
									<a class="btn_s white" name="m5"  id="m5" onclick="d_ser('M5');">5��</a>
									<a class="btn_s white" name="m6"  id="m6" onclick="d_ser('M6');">6��</a>
									<a class="btn_s white" name="m7"  id="m7" onclick="d_ser('M7');">7��</a>
									<a class="btn_s white" name="m8"  id="m8" onclick="d_ser('M8');">8��</a>
									<a class="btn_s white" name="m9"  id="m9" onclick="d_ser('M9');">9��</a>
									<a class="btn_s white" name="m10"  id="m10" onclick="d_ser('M10');">10��</a>
									<a class="btn_s white" name="m11"  id="m11" onclick="d_ser('M11');">11��</a>
									<a class="btn_s white" name="m12"  id="m12" onclick="d_ser('M12');">12��</a>
								</span>
								<input type="hidden" name="skey" id="skey" value="">
								<input type="text" name="sname" id="sname" class="srch_css" style="height:26px;width:161px;margin-left:3px;border:1px solid" value="" placeholder="����˻�" onclick="skey_ser();" onkeyup="skey_chk();">
								<span class="btn_wrap">
									<a class="btn_s white hover_btn btn_search " style="width:140px;margin: 0px;" onclick="common_ser('A');">������ ��������</a>
									<a class="btn_s white hover_btn  " style="width:140px;margin: 0px;" onclick="common_ser_tot('B');">�����纰 ��������</a>
									<a class="btn_s white hover_btn  " style="width:140px;margin: 0px;" onclick="common_ser_tot_tot('C');">������ ��������ǥ</a>
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
							<tr class="rowTop" style="height: 47px;">
								<th rowspan=2 align="center">������</th>
							</tr>
						</thead>							
						</tr>
						<th class="summary sticky" style="height:13px;" > </th>
					</table>
					<div   id="tree-container" style="font-size: 12px"  >	</div>
				</div><!-- // tb_type01 -->			
			</div><!--data_left end -->

			<div class="data_right_jojik" id="kwnlist" style="padding: 0px;overflow-x:auto;"> <!--data_right start -->

			</div>  <!--data_right end -->


		</fieldset>
	</div><!-- // content_wrap -->
</div>
<!-- // container -->
<!-- // wrap -->
<script type="text/javascript">

var button = ""; // ���� ��ư Ŭ���ߴ��� �˱����� ����
 
//  
function common_ser(bit){
	$("#btn").val(bit); 
	$("#div_load_image").show();
	var formData = $("#searchFrm").serialize();
	// ajax �������� ����
	ajaxLodingForm('ga_menu7_02_list.php',$('#kwnlist'),formData);
}
 
function common_ser_tot(bit){
	$("#btn").val(bit); 
	$("#div_load_image").show();
	var formData = $("#searchFrm").serialize();
	// ajax �������� ����
	ajaxLodingForm('ga_menu7_02_list2.php',$('#kwnlist'),formData);
}
function common_ser_tot_tot(bit){
	$("#btn").val(bit); 
	$("#div_load_image").show();
	var formData = $("#searchFrm").serialize();
	// ajax �������� ����
	ajaxLodingForm('ga_menu7_02_list3.php',$('#kwnlist'),formData);
}
//--->�Ⱓ����
function d_ser(bit){
		var  sdate1	= document.getElementById('SDATE1').value;
		var  sdate2	= document.getElementById('SDATE2').value;
		var  str_date = bit + '&' + sdate1 + '&' + sdate2 ;
		
		//--������ ���� ��������
		str_date = date_on	(str_date);  //common.js ����  bin>js>common.js

		var bdate = str_date.split('&');
		$("form[name='searchFrm'] input[name='SDATE1']").val(bdate[0]); 
		$("form[name='searchFrm'] input[name='SDATE2']").val(bdate[1]); 
  
		
		//--->������ ���� �ٲ�� SERVER ���ϰɸ� 
		if (bit != 'YP' && bit != 'YN' ){
			 var button = $('#btn').val();
			 if (button == "A")	 {
				common_ser(button);		
			 }
			 if (button == "B")	 {
				common_ser_tot(button);		
			 }
			 if (button == "C")	 {
				common_ser_tot_tot(button);		
			 }
		}
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
			//$('#tree-container').jstree("open_node", '<?=$fbonbu?>');	// �������� �����ͼ� 1������ ����
	});
}

// ����Ż�
function skey_ser(){
		var left = Math.ceil((window.screen.width - 800)/2);
		var top = Math.ceil((window.screen.height - 800)/2);
		var popOpen	= window.open("<?=$conf['homeDir']?>/sub/help/ga_swon_search.php","swonpop","width=500px,height=400px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
		popOpen.focus();	
}


function setSwonValue(row,code,name){
	$("#skey").val(code);
	$("#sname").val(name);
	// ������ý� ��ȸ
	common_ser();
}


// �˻����� ����� ������ �����ȣ �ʱ�ȭ 
function skey_chk() {
	// �� ���� �� ���Ű�� �ʱ�ȭ 
	var data = $('#sname').val();
	if(data.replace(/\s/g, "").length == 0){
		$("#skey").val('');
	}
}

// Node �������� ��.
$('#tree-container').on("select_node.jstree", function (e, data) {
	var id = data.instance.get_node(data.selected).id;
	$("#id").val(id); 
	 var button = $('#btn').val();
  	 if (button == "A")	 {
		common_ser(button);		
	 }
 	 if (button == "B")	 {
		common_ser_tot(button);		
	 }
 	 if (button == "C")	 {
		common_ser_tot_tot(button);		
	 }
});

$(window).on("load", function() {
 	$(".btn_search").trigger("click");
});

$(document).ready(function(){	

	// �������� ����Ʈ
	//ajaxLodingTarket('x1_swonsu_list.php',$('#kwnlist'),'');

	// ������ ȣ��(/bin/include/source/bottom �� ����)
	get_jstree();

	//window.parent.postMessage("�濵���� > �����纰 �����м�", "*");   // '*' on any domain �θ�� ������..        

});

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>