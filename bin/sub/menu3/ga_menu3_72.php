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
	$sdate2 =  $_GET['SDATE2'];
}else{
	$sdate1 =  date("Y-m-d");
	$sdate2 =  date("Y-m-d");
}

// ���α׷� ���Խ� �ð���  �ҿ������ ����� ������ ��ȸ��ư ������ datadisplay�ϱ� ����.
 
$fyymmdd=  substr($sdate1,0,4).substr($sdate1,5,2); 

// �⺻ ������ ����
$page = ($_GET['page']) ? $_GET['page'] : 1;
$page_row	= 50;

$limit1 = ($page-1)*$page_row+1;
$limit2 = $page*$page_row;

$gubun = "P"; // ���
$gubunname = "������";

  
// �������ε� �����丮 ����Ʈ
$sql = " 
select *
from(
	select *, ROW_NUMBER()over(order by upldate desc, uplnum desc) rnum 
	from(
		select      substring(a.upldate,1,4) + '-' +substring(a.upldate,5,2) +'-'+ substring(a.upldate,7,2)  +'-'+a.gubun+'-'+a.gubunsub +'-'+ CONVERT(VARCHAR(5) , a.uplnum ) as  upno ,   a.scode,a.upldate, a.gubun,a.gubunsub,  a.uplnum, d.GNAME, a.filename, b.name, a.cnt, a.amt,
								 a.bigo  as bigo, substring(a.yymm,1,4) +'-'+ substring(a.yymm,5,2) yymm ,
								convert(varchar,a.idate,21) idate ,c.sname , isnull(a.fcnt,0) fcnt , isnull(a.famt,0) famt  ,a.code 
		from upload_history a left outer join insmaster b on a.code = b.code
								left outer join swon c on a.scode = c.scode and a.iswon = c.skey
								left outer join UPLOAD_EXCEL d on a.scode = d.scode and a.code = d.code and a.GUBUN = d.GUBUN  and a.gubunsub =d.gubunsub 
		where a.scode = '".$_SESSION['S_SCODE']."' and a.gubun = 'P'   and a.yymm = '".$fyymmdd."'
	) tbl
) p
	where rnum between ".$limit1." AND ".$limit2 ;

//print_r($sql) ;


 $qry = sqlsrv_query( $mscon, $sql );
$listData	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listData[]	= $fet;
}

// �������ε� �����丮 ����Ʈ �� �Ǽ�
$sql= "
	select 
		count(*) CNT
	from(
		select a.scode,a.upldate, a.gubun, a.gubunsub, a.uplnum,         a.filename, b.name, a.cnt, a.amt, a.bigo,convert(varchar,a.idate,21) idate ,c.sname , isnull(a.fcnt,0) fcnt , isnull(a.famt,0) famt
		from upload_history a left outer join insmaster b on a.code = b.code
								left outer join swon c on a.scode = c.scode and a.iswon = c.skey
		where a.scode = '".$_SESSION['S_SCODE']."' and a.gubun = 'P'  and a.yymm = '".$fyymmdd."'
	) p " ;

 


$qry =  sqlsrv_query($mscon, $sql);
$totalResult =  sqlsrv_fetch_array($qry); 


// ����� ��������
$sql= "select inscode, name from inssetup where scode = '".$_SESSION['S_SCODE']."' and useyn = 'Y' order by num, inscode";
$qry= sqlsrv_query( $mscon, $sql );
$insData	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
  $insData[] = $fet;
}

// ������ Ŭ���� ����
// �ε�
include_once($conf['rootDir'].'/include/class/Pagination.php');

// ����
$pagination = new Pagination(array(
		'base_url' => $_SERVER['PHP_SELF']."?SDATE1=".$sdate1."&btn=".$_GET['btn'],
		'per_page' => $page_row,
		'total_rows' => $totalResult['CNT'],
		'cur_page' => $page,
));

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
				<form name="searchFrm" method="get" action="<?$_SERVER['PHP_SELF']?>">
				<input type="hidden" name="upldate"  id="upldate"  value="<?=$upldate?>">
				<input type="hidden" name="gubun" id="gubun" value="<?=$gubun?>">
				<input type="hidden" name="gubunsub" id="gubunsub" value="<?=$gubunsub?>">
				<input type="hidden" name="uplnum" id="uplnum" value="<?=$uplnum?>">
				<input type="hidden" name="filename" id="filename" value="<?=$filename?>">
				<input type="hidden" name="ser_con" id="ser_con" value="<?=$ser_con?>">
				<input type="hidden" name="page" id="page" value="<?=$page?>"> 
				<input type="hidden" name="btn"  id="btn"  value="">
					<fieldset>
					<div>
						<span class="ser_font"> �����</span> 
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
								<a class="btn_s white "	name="mp"   id="mp"   onclick="d_ser('MP');">����</a>
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
							<span class="btn_wrap">			
								<a class="btn_s white hover_btn" style="width:150px;margin: 0px;" onclick="common_ser();">���ε���ȸ</a>										
								<a class="btn_s white" style="min-width:150px;" onclick="upload();">�Ǹż����������ε�</a>
							</span>	  
						</p>
					</div>
					</fieldset>
				</form>
			</div><!-- // box_wrap -->
 
			<div class="tb_type01 div_grid" style="overflow-y:auto;">
				<form name="excelupload_form" class="ajaxForm" method="post" action="ga_menu3_72_action.php" ENCTYPE="multipart/form-data">
				<input type="hidden" name="upldate" id="upldate" value="<?=date("Y-m-d")?>">
				<input type="hidden" name="gubun" id="gubun" value="P">
				<input type="hidden" name="gubunsub" id="gubunsub" value="">
				<input type="hidden" name="uplnum" value="">
				<input type="hidden" name="type" id="type" value="">

				<table class="gridhover" id="sort_table">

					<colgroup>											
						<col width="80px">
						<col width="230px">
						<col width="150px">
						<col width="70px">
						<col width="70px">
						<col width="420px">
						<col width="auto"> 
						<col width="120px">
						<col width="120px">
						<col width="50px">
					</colgroup>
					<thead>
					<tr class="rowTop">
						<th align="center">�����</th>
						<th align="center">���ε�����</th>
						<th align="center">������</th>
						<th align="center">�����Ǽ�</th>
						<th align="center">���аǼ�</th>
						<th align="center">�������ϸ�</th>
						<th align="center">ó�����</th> 
						<th align="center">���ε�No</th>
						<th align="center">���ε��Ͻ�</th>
						<th align="center">����</th>
					</tr>
					</thead>
					<tbody>
						<?if(!empty($listData)){?>
						<?foreach($listData as $key => $val){extract($val);?>
						<tr class="rowData" rol-data='<?=$upldate?>',     rol-gubun ='<?=$gubun?>' rol-gubunsub ='<?=$gubunsub?>'   rol-uplnum ='<?=$uplnum?>'    rol-filename ='<?=$filename?>'style="cursor:pointer;">
						<!--	<td align="center"><?=date("Y-m-d",strtotime($upldate)) ?></td> -->
							<td align="center"><?=$yymm?></td>
							<td align="left"><?=$GNAME?></td>
							<td align="left"><?=$name?></td>
							<td align="right" ><?=number_format($cnt)?></td>                     
							<td align="right" ><?=number_format($fcnt)?></td>
							<td align="left"><?=$filename?></td>
							<td align="left"><?=$bigo?></td> 
							<td align="center"><?=$upno?></td>
							<td align="center"><?=date("Y-m-d H:i:s",strtotime($idate))?></td>
							<td align="center"><i idata1="<?=$upldate?>" idata2="<?=$uplnum?>" class="w3-round yb_icon fa fa-trash-o delAction"  aria-hidden="true" style="border:0px;color:#999999;padding:0px 10px;margin-bottom:-1px;cursor:pointer;"></i></td>
						</tr>
						<?}}?>
						</tbody>
					</table>
					</form>
			</div> 
							<!-- ��� -->
			<div id="modal2" class="layerBody_item">
			</div>

			<div style="text-align: center">		
				<ul class="pagination pagination-sm" style="margin: 5px 5px 0 5px">
				  <?=$pagination->create_links();?>
				</ul>
			</div>
		</fieldset>
	</div><!-- // content_wrap -->
</div>


<!-- // container -->
<!-- // wrap -->
<script type="text/javascript">
// ���ε� ���â
function upload(){
	$.ajaxLoding('ga_menu3_72_upload.php',$('.layerBody_item'),$('#modal2'));	
}

//--->�Ⱓ����
function d_ser(bit){
		// ��ư Ŭ�������� �Ѱ��ֱ�
		var lower_str = bit.toLowerCase();	// �ҹ��ں�ȯ
		$("#btn").val(lower_str);

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

//-->��ȸ 
function common_ser(){ 
		$("#div_load_image").show();
		$("form[name='searchFrm']").attr("method","get");
		$("form[name='searchFrm']").attr("target","");
		$("form[name='searchFrm']").submit();
}

 $(document).ready(function(){
	//window.parent.postMessage("���������>���Լ�������ε�", "*");   // '*' on any domain �θ�� ������..        

	// ��� Ŭ��
	$(".rowTop > th").click(function(){
		var trData = $(this).parent();

		var idx = $(trData).find("th").index($(this));

		// include/bottom.php ����	
		sortTable("sort_table", idx, 1);
	})

	// �� ���ý� �ش���� Ŭ���Ѱܹޱ�(function d_ser ���� ���� �ѱ�)
	var btn		= '<?=$_GET['btn']?>';
	if(btn){
		$(".box_wrap.sel_btn a").removeClass('on');
		$("#"+btn).addClass('on');
	}

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

	$(".rowData > td").click(function(){
		var trData = $(this).parent();
		var idx    = $(".rowData").index($(trData));

		var upldate  = $(".rowData").eq(idx).attr('rol-data');
		var gubun  = $(".rowData").eq(idx).attr('rol-gubun');
		var gubunsub  = $(".rowData").eq(idx).attr('rol-gubunsub');
		var uplnum  = $(".rowData").eq(idx).attr('rol-uplnum');
		var filename  = $(".rowData").eq(idx).attr('rol-filename');

		var left = Math.ceil((window.screen.width - 1200)/2);
		var top = Math.ceil((window.screen.height - 1000)/2);

		// 8��°�� �����ǿ����� �˾� �ȿ������� ����(�� �߰��� �Ʒ� �ش� �ѹ� ����)
		if($(trData).find("td").index($(this))!='9'){
			var popOpen	= window.open("<?=$conf['homeDir']?>/sub/menu3/ga_menu3_72_list.php?upldate="+upldate+"&gubun=" +gubun+"&gubunsub=" + gubunsub+"&uplnum=" +uplnum +"&filename=" +filename                    ,"width=1200px,height=950px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
			popOpen.focus();
		} 
	});


	// ����ó��
	$(".delAction").click(function(){
		var idx  = $(".delAction").index($(this));
		var upldate  = $(".rowData").eq(idx).attr('rol-data');
		var gubun  = $(".rowData").eq(idx).attr('rol-gubun');
		var gubunsub  = $(".rowData").eq(idx).attr('rol-gubunsub');
		var uplnum  = $(".rowData").eq(idx).attr('rol-uplnum');

		$("form[name='excelupload_form'] input[name='upldate']").val(upldate);
		$("form[name='excelupload_form'] input[name='gubun']").val(gubun);
		$("form[name='excelupload_form'] input[name='gubunsub']").val(gubunsub);
		$("form[name='excelupload_form'] input[name='uplnum']").val(uplnum);
		if(confirm("�����Ͻðڽ��ϱ�?")){
			$("form[name='excelupload_form'] input[name='type']").val("del");
			$("form[name='excelupload_form']").submit();
		}		 
	})
});

// pre-submit callback 
function showRequest_modal(formData, jqForm, options) { 
	var queryString = $.param(formData); 
	return true; 
} 
 // post-submit callback 
function processJson_modal(data) { 
	console.log(data);
	if(data.message){
		location.reload();
		alert(data.message);
	}
	if(data.result==''){	// ������
	}
}
 
</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>