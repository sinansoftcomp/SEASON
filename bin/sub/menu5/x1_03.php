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
	echo "<script> alert('�ش� �޴��� ���� ������ �����ϴ�. �����ڿ��� ���� �ٶ��ϴ�.'); self.close(); </script>";
}
 
$sdate1 =  date("Y-m-01");
$lastday = DATE('t', strtotime($sdate1));
$sdate2 =  date("Y-m-".$lastday);
/* ------------------------------------------------------------
	End Date �ʱⰪ ����
------------------------------------------------------------ */

$sql= "
		select top 1 'N2'+inscode code
		from inssetup
		WHERE scode = '".$_SESSION['S_SCODE']."' and useyn = 'Y'
		order by inscode 
		" ;
$qry =  sqlsrv_query($mscon, $sql);
$totalResult =  sqlsrv_fetch_array($qry); 
$topcode = $totalResult['code'];

sqlsrv_free_stmt($result);
sqlsrv_close($mscon);

?>

<!-- html���� -->
<style>
body{background-image: none;}
</style>

<div class="container">
	<div class="content_wrap">


		<div class="tit_wrap">
			<h3 class="tit_sub" style="margin-left:20px">������ ���ʼ���</h3>
			<!--<span class="btn_wrap" style="padding-right:20px">-->
			<span style = "margin-left:328px;">
				<a class="btn_s white hover_btn btn_search btn_off" style="width:100px;margin:0px; " onclick="inscharge_update('save');">����</a>
				<a class="btn_s white btn_off" style="width:100px;margin-left:-4px" onclick="inscharge_update('reset');">�ʱ�ȭ</a>
				<a class="btn_s white btn_off" style="width: 100px;margin-left:-4px" onclick="self.close();">�ݱ�</a>
			</span>
		</div>

		<div class="data_left_jojik" > 
			<div class="tb_type01 kwndatalist" style="overflow-y:hidden;" >
				<table class="gridhover">
					<colgroup>											
						<col width="100%">
					</colgroup>

					<thead>
						<tr class="rowTop">
							<th align="center">������</th>
						</tr>
					</thead>							
					</tr>

				</table>
				<div   id="tree-container" style="font-size: 12px"  >	</div>
			</div>		
		</div>

		<div class="data_right_jojik" id="inscharge" style="padding: 0px;overflow-x:auto;"> 

		</div>  

	</div>
</div>

<script type="text/javascript">

// ������ȸ �Լ�(bin/js/common.js ȣ��)
function common_ser(id){
	//$("#div_load_image").show();
	//var formData = $("#searchFrm").serialize();
	var formData = "id="+id;
	// ajax �������� ����
	ajaxLodingTarket('x1_03_data.php',$('#inscharge'),formData);
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

// Node �������� ��.
$('#tree-container').on("select_node.jstree", function (e, data) {
	var id = data.instance.get_node(data.selected).id;
	$("#id").val(id);

	if(id == "N10000"){
		alert("�����ڸ� �������ּ���.");
		return false;
	}
	common_ser(id);
});

$(document).ready(function(){	
	get_jstree();
	//window.parent.postMessage("������ > ������ ���ʼ���", "*");   // '*' on any domain �θ�� ������..        
});

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>