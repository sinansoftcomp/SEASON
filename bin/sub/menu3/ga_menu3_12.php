<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

//var_dump( $_SESSION );

/* ------------------------------------------------------------
	Date �ʱⰪ ����
------------------------------------------------------------ */
if ($_GET['SDATE1']) {
	$sdate1 =  $_GET['SDATE1'];
	$sdate2 =  $_GET['SDATE2'];
}else{
	$sdate1 =  date("Y-m-01");
	$sdate2 =  date("Y-m-d");
}

/* ------------------------------------------------------------
	End Date �ʱⰪ ����
------------------------------------------------------------ */

// ��ü�����
$sql= "select inscode code, name, gubun from inssetup where scode = '".$_SESSION['S_SCODE']."' and useyn = 'Y' order by name";
$qry= sqlsrv_query( $mscon, $sql );
$instot	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$instot[] = $fet;
}


// �����°�(�ѱۻ��°� �״�� ��ȸ)
$sql= "select codesub code, subnm name from common where scode = '".$_SESSION['S_SCODE']."' and code = 'COM010' order by num ";
$qry= sqlsrv_query( $mscon, $sql );
$selkstbit	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$selkstbit[] = $fet;
}


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
body{background-image: none;}

.tb_type01 table thead th {
    border-bottom: 1px solid #c7c7c7;
    background: #f9f9f9;
    font-size: 13px;
    font-weight: 600;
}

.tb_type01 td {
    background: #fff;
    font-size: 12px;
	font-weight: 500;
    border-bottom: 1px solid #e9e9e9;
}

</style>
					

<div class="container">
	<div class="content_wrap">
		<fieldset>

			<!-- �˻����� -->
			<div class="box_wrap sel_btn">
				<form name="searchFrm" id="searchFrm" method="get" action="<?$_SERVER['PHP_SELF']?>">
				<input type="hidden" name="id" id="id" value="">
				<input type="hidden" name="page" id="page" value="">
					<fieldset>
						<legend>������ �Ⱓ�� �˻�</legend>
						<div class="row">
							<button type="button" class="btn_prev" name="yp" id="yp" onclick="d_ser('YP');"><span class="blind">����</span></button>

							<span class="input_type date" style="width:114px">
								<input type="text" class="Calnew" placeholder="YYYY-MM-DD" id="SDATE1" name="SDATE1" value="<?=$sdate1?>">
							</span> 
							<span class="dash"> ~ </span>
							<span class="input_type date" style="width:114px">
								<input type="text" class="Calnew" placeholder="YYYY-MM-DD" id="SDATE2" name="SDATE2" value="<?=$sdate2?>">
							</span>

							<button type="button" class="btn_next" name="yn" id="yn" onclick="d_ser('YN');"><span class="blind">����</span></button>

							<p class="response_block" style="margin-left:10px">
								<span class="btn_wrap">
									<a class="btn_s white"	name="mp"   id="mp" onclick="d_ser('MP');">����</a>
									<a class="btn_s white on"		name="md"   id="md" onclick="d_ser('MD');">���</a>
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
							</p>
							

						</div>

							<select name="inscode" id="inscode" class="srch_css" style="margin-left:0;width:141px">				
							  <option value="">�����</option>
							  <?foreach($instot as $key => $val){?>
							  <option value="<?=$val['code']?>"><?=$val['name']?></option>
							  <?}?>
							</select>
							<select name="insilj" id="insilj" class="srch_css" style="margin-left:21px;width:141px"> 
								<option value="">��ǰ��</option>
								<?foreach($conf['insilj'] as $key => $val){?>
								<option value="<?=$key?>"><?=$val?></option>
								<?}?>
							</select>	
							<select name="kstbit" id="kstbit" class="srch_css" style="width:115px;margin-left:10px"> 
								<option value="">������</option>
								<?foreach($selkstbit as $key => $val){?>
								<option value="<?=$val['code']?>"><?=$val['name']?></option>
								<?}?>
							</select>	


							<select name="searchF1" id="searchF1" class="srch_css" onchange="fn_srch(this.value);" style="width:114px;margin-left:8px">
								<option value="a.kname">����ڸ�</option>
								<option value="a.kcode">���ǹ�ȣ</option>
								<option value="s1">�������</option>
								<option value="s2">�����</option>
								<option value="tel">����ó</option>
							</select>
							<input type="hidden" name="skey" id="skey" value="<?=$skey?>">
							<input type="text" name="searchF1Text" id="searchF1Text" class="srch_css" style="height:20px;width:161px;margin-left:3px" value="<?=$searchF1Text?>" onkeyup="enterkey()">
							<!--btn_off Ŭ���� ���� ��ư Ŭ���ص� �����ܿ� x-->
							<span class="btn_wrap" >
								<a class="btn_s white hover_btn btn_search btn_off" style="width:113px;margin:0" onclick="common_ser();">��ȸ</a>
								<a class="btn_s white btn_off" id="btn_ins" style="width:113px;" onclick="KwnIns('','');">�����</a>
								<a class="btn_s white btn_off" id="btn_ins" style="width:113px;" onclick="KwnIns('','');">SMS����</a>
							</span>
							<span class="btn_topwrap">
								
							</span>


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
						<tr >
							<th align="center">������</th>
						</tr>
						</thead>	
						
						<th class="summary sticky" style="height:14px;"> </th>
						</tr>
					</table>
					<div   id="tree-container">	</div>
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

// �����
function KwnIns(inscode,kcode){	

	var left = Math.ceil((window.screen.width - 1200)/2);
	var top = Math.ceil((window.screen.height - 950)/2);
	var page= document.getElementById('page').value;

	var popOpen	= window.open("<?=$conf['homeDir']?>/sub/menu3/ga_menu3_01_pop.php?inscode="+inscode+"&kcode="+kcode+"&page="+page,"KwnDt","width=1250px,height=860px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");

	popOpen.focus();		
}

// ���� ������ �˻�
function enterkey() {
	if (window.event.keyCode == 13) {
		common_ser();   	
    }

	// �� ���� �� ���Ű�� �ʱ�ȭ 
	var data = $('#searchF1Text').val();
	if(data.replace(/\s/g, "").length == 0){
		$("#skey").val('');
	}
}


// ������� �� ������� ��ȸ �� ����˾�(������ ���� ��ȸ) 
function fn_srch(val){

	if(val == 's1' || val == 's2'){								// ������� �� ������� ��ȸ �� �˾���ȸ / ��ǲ�ڽ� ���Է»��� ó��
		$("#searchF1Text").attr("readonly",true);
		$("#searchF1Text").css("backgroundColor","#EAEAEA");

		var left = Math.ceil((window.screen.width - 800)/2);
		var top = Math.ceil((window.screen.height - 800)/2);
		var popOpen	= window.open("<?=$conf['homeDir']?>/sub/help/ga_swon_search.php","swonpop","width=500px,height=400px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
		popOpen.focus();
	}else{		// �� �� �����ȸ ���簪 Ŭ���� / ��ǲ�ڽ� �Է°��ɻ���
		$("#skey").val('');
		$('#searchF1Text').val('');

		$("#searchF1Text").attr("readonly",false);
		$("#searchF1Text").css("backgroundColor","#fff");
	}

}


function setSwonValue(row,code,name){
	$("#skey").val(code);
	$('#searchF1Text').val(name);
}


// ������ȸ �Լ�(bin/js/common.js ȣ��)
function common_ser(){
	$("#div_load_image").show();
	var formData = $("#searchFrm").serialize();

	//console.log(formData);

	// ajax �������� ����
	ajaxLodingForm('ga_menu3_12_list.php',$('#kwnlist'),formData);

}


// ������
function get_jstree() {
	$("#tree-container").jstree({  
		'core': {
			'data' : {
				"url"	 : "/bin/sub/help/jojik_tree_json.php",
				"dataType" : "json"	
			}
		} 
		}).on("loaded.jstree",function(e,data){
			$('#tree-container').jstree('open_all');				// ��ü����
			//$('#tree-container').jstree("open_node", '<?=$fbonbu?>');	// �������� �����ͼ� 1������ ����
	});
}


// Node �������� ��.
$('#tree-container').on("select_node.jstree", function (e, data) {
	var id = data.instance.get_node(data.selected).id;
	$('#page').val("");
	$("#id").val(id);
	common_ser();
 
});


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
			common_ser();
		}
 }


$(document).ready(function(){	
	// �������� ����Ʈ
	ajaxLodingTarket('ga_menu3_12_list.php',$('#kwnlist'),'&SDATE1=<?=$sdate1?>&SDATE2=<?=$sdate2?>');

	// ������ ȣ��(/bin/include/source/bottom �� ����)
	get_jstree();

	window.parent.postMessage("���������� > ��������Ȳ", "*");   // '*' on any domain �θ�� ������..        

});

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>