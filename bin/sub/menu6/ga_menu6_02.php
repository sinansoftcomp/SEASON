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

// ����� ��������
$sql= "select inscode, name from inssetup where scode = '".$_SESSION['S_SCODE']."' and useyn = 'Y' order by num, inscode";
$qry= sqlsrv_query( $mscon, $sql );
$insData	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
  $insData[] = $fet;
}

sqlsrv_free_stmt($result);
sqlsrv_close($mscon);

?>
<!-- html���� -->
<style>
body{background-image: none;}
</style>


<div class="container" style="-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none">
	<div class="content_wrap">
		<fieldset>
			<!--
			<legend>�������</legend>
			<h2 class="tit_big">�������</h2>
			--> 
			<div class="box_wrap sel_btn">
				<form name="searchFrm" id="searchFrm" method="get" action="<?$_SERVER['PHP_SELF']?>">
					<input type="hidden" name="id" id="id" value="">
					<input type="hidden" name="page" id="page" value="">		

					<select name="datekind" id="datekind" class="srch_css" style="width:100px;"> 		
					  <option value="k_idate">�Է���</option>
					  <option value="k_fdate">���������</option>
					  <option value="k_tdate">����������</option>
					</select>
					<span class="input_type date ml10" style="width:114px;margin-left: 0px;">
						<input type="text" class="Calnew" placeholder="YYYY-MM-DD" id="SDATE1" name="SDATE1" value="<?=$sdate1?>">
					</span> 
					<span class="dash"> ~ </span>
					<span class="input_type date" style="width:114px">
						<input type="text" class="Calnew" placeholder="YYYY-MM-DD" id="SDATE2" name="SDATE2" value="<?=$sdate2?>">
					</span>

					<select name="rbit" id="rbit"  class="srch_css" style="width:100px;" >
					  <option value="">�������</option>
					  <?foreach($conf['rbit'] as $key => $val){?>
					  <option value="<?=$key?>" <?if($_GET['rbit']==$key) echo "selected"?>><?=$val?></option>
					  <?}?>
					</select>
					
					<input type="text" name="jumin" id="jumin" class="srch_css" style="min-width:180px;height:22px;border: 1px solid #d5d5d5" value="" placeholder="�������/����ڹ�ȣ/�Ǻ����ڸ�" >
					<input type="text" name="carnumber" id="carnumber" class="srch_css" style="min-width:130px;height:22px;border: 1px solid #d5d5d5" value="" placeholder="������ȣ" >

					<a class="btn_s white hover_btn btn_search btn_off" style="width:130px;margin-right: -3px;">��ȸ</a>
				</form>
			</div>

		 
			<div class="data_left_jojik" > <!--data_left start -->
				<div class="tb_type01 div_grid" style="overflow-y:auto;" >
					<div style="position:sticky;top:0px; z-index:1;">
						<table class="gridhover">
							<colgroup>											
								<col width="100%">
							</colgroup>
							<thead>
							<tr>
								<th class="jojik" align="center"> 
									<a class="swon_all" onclick="get_jstree_refresh('1');"><span>��ü</span></a>
									<a class="swon_jae" onclick="get_jstree_refresh('2');"><span class="hover">����<span></a>
									<a class="swon_tsa" onclick="get_jstree_refresh('3');"><span>����<span></a>
								</th>
							</tr>
							</thead>	 
						</table>
					</div>
					<div   id="tree-container" style="font-size: 13px;"  >	</div>
				</div><!-- // tb_type01 -->			
			</div><!--data_left end -->

			<div class="data_right_jojik" id="carestlist" style="padding: 0px;overflow-x:auto;"> <!--data_right start -->

			</div>  <!--data_right end -->

		</fieldset>
	</div> <!-- content_wrap end -->
</div> <!--container end-->

<span id="guide" style="color:#999;display:none"></span>
 
<script type="text/javascript">


// ������
function get_jstree(sbit) {
	$("#tree-container").jstree({  
		'core': {
			'data' : {
				"url"	 : "/bin/sub/help/jojik_swon_tree_json.php?sbit="+ sbit,
				"dataType" : "json"	
			}
		} 
		}).on("loaded.jstree",function(e,data){
			 $('#tree-container').jstree('open_all');				// ��ü����
	});
}
function get_jstree_refresh(sbit) {
    $('#tree-container').jstree("destory"); 
	$('#tree-container').jstree(true).settings.core.data.url =  "/bin/sub/help/jojik_swon_tree_json.php?sbit="+ sbit;
	$('#tree-container').jstree(true).refresh();
}


// Node �������� ��.
$('#tree-container').on("select_node.jstree", function (e, data) {
	$('#page').val("");
	var id = data.instance.get_node(data.selected).id;
	$("#id").val(id);
	common_ser();
});

// ������ȸ �Լ�(bin/js/common.js ȣ��)
function common_ser(){
	var formData = $("#searchFrm").serialize();
	//alert(formData);
	// ajax �������� ����
	ajaxLodingForm('ga_menu6_02_list.php',$('#carestlist'),formData);

}

// ��ȸ��ư Ŭ�� �� 
$(".btn_search").click(function(){
 	common_ser();
}); 

$(document).ready(function(){
	// �������� ����Ʈ
	ajaxLodingTarket('ga_menu6_02_list.php',$('#carestlist'),'datekind=k_idate&SDATE1=<?=$sdate1?>&SDATE2=<?=$sdate2?>');

	//$('#searchFrm').trigger("click");

	// ������ ȣ��(/bin/include/source/bottom �� ����)
	get_jstree('2');

	//window.parent.postMessage("�ڵ������� > �񱳰�����Ȳ", "*");   // '*' on any domain �θ�� ������..        


});

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>