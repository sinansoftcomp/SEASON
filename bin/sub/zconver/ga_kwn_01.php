<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

//https://gojs.net/latest/samples/index.html  Ʈ������� 

// �⺻ ������ ����
$page = ($_GET['page']) ? $_GET['page'] : 1;
$page_row	= 35;

$limit1 = ($page-1)*$page_row+1;
$limit2 = $page*$page_row;

$gubun = "A"; // ����ڵ�������
$gubunname = "����ڵ�������";


// �������ε� �����丮 ����Ʈ
$sql = " 
select *
from(
	select *, ROW_NUMBER()over(order by upldate desc, uplnum desc) rnum 
	from(
		select      substring(a.upldate,1,4) + '-' +substring(a.upldate,5,2) +'-'+ substring(a.upldate,7,2)  +'-'+a.gubun+'-'+a.gubunsub +'-'+ CONVERT(VARCHAR(5) , a.uplnum ) as  upno ,   a.scode,a.upldate, a.gubun,a.gubunsub,  a.uplnum, d.GNAME, a.filename, b.name, a.cnt, a.amt,
								'����� :' + substring(a.yymm,1,4) + '-' +substring(a.yymm,5,2)  +'  '+a.bigo  as bigo,
								convert(varchar,a.idate,21) idate ,c.sname , isnull(a.fcnt,0) fcnt , isnull(a.ucnt,0) ucnt, isnull(a.famt,0) famt  ,a.code 
		from upload_history a left outer join insmaster b on a.code = b.code
								left outer join swon c on a.scode = c.scode and a.iswon = c.skey
								left outer join UPLOAD_EXCEL d on a.scode = d.scode and a.code = d.code and a.GUBUN = d.GUBUN  and a.gubunsub =d.gubunsub 
		where a.scode = '".$_SESSION['S_SCODE']."' and a.gubun = 'A'
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
		where a.scode = '".$_SESSION['S_SCODE']."' and a.gubun = 'A'
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
		'base_url' => $_SERVER['PHP_SELF']."?scode=".$_GET['scode'],
		'per_page' => $page_row,
		'total_rows' => $totalResult['CNT'],
		'cur_page' => $page,
));

sqlsrv_free_stmt($result);
sqlsrv_close($mscon);

?>

<!-- html���� -->
<style>
body{background-image: none;}
.container{margin:0px 0px 0px 10px;}
.box_wrap {margin-bottom:10px}
.tb_type01 th, .tb_type01 td {padding: 8px 0;}

.tb_type01 td.txtoverfl{
	overflow:hidden;	
	white-space : nowrap;		
	text-overflow: ellipsis;

}

.srch_css{
	width:125px;
	margin-left:5px;
	height:24px;
	cursor:pointer;
}
 
table.gridhover thead { position: sticky; top: 0; } 

.underline{
	color:#7474ea;
	text-decoration: underline;
	cursor:pointer;
}

</style>

<div class="container">
	<div class="content_wrap">
		<fieldset>
			<legend>�����Ȳ�������������ε�</legend>
			<h2 class="tit_big">�����Ȳ�������������ε�</h2>

			<!-- �˻����� -->
			<div class="tit_wrap mt20">
				<span class="btn_wrap">
   			 	    <a href="#" class="btn_l white" style="min-width:100px;" onclick="upload();">�����Ȳ�������������ε�</a>
				</span>	  
				<form name="searchFrm" method="get" action="<?$_SERVER['PHP_SELF']?>">
				<input type="hidden" name="upldate"  id="upldate"  value="<?=$upldate?>">
				<input type="hidden" name="gubun" id="gubun" value="<?=$gubun?>">
				<input type="hidden" name="gubunsub" id="gubunsub" value="<?=$gubunsub?>">
				<input type="hidden" name="uplnum" id="uplnum" value="<?=$uplnum?>">
				<input type="hidden" name="filename" id="filename" value="<?=$filename?>">
				<input type="hidden" name="ser_con" id="ser_con" value="<?=$ser_con?>">
				<input type="hidden" name="page" id="page" value="<?=$page?>"> 
				</form> 
			</div><!-- // box_wrap -->
			<div class="tb_type01" style="overflow-y:auto;">
				<form name="excelupload_form" class="ajaxForm" method="post" action="ga_menu3_52_action.php" ENCTYPE="multipart/form-data">
				<input type="hidden" name="upldate" id="upldate" value="<?=date("Y-m-d")?>">
				<input type="hidden" name="gubun" id="gubun" value="A">
				<input type="hidden" name="gubunsub" id="gubunsub" value="">
				<input type="hidden" name="uplnum" value="">
				<input type="hidden" name="type" id="type" value="">

				<table class="gridhover"    >

					<colgroup>											
						<col width="9%">
						<col width="13%">
						<col width="8%">
						<col width="5%">
						<col width="5%">
						<col width="5%">
						<col width="15%">
						<col width="auto">
						<col width="5%">
						<col width="9%">
						<col width="5%">
					</colgroup>

					<thead>
					<tr>
						<th align="center">���ε�No</th>
						<th align="center">���ε�����</th>
						<th align="center">������</th>
						<th align="center">�ű԰Ǽ�</th>
						<th align="center">�����Ǽ�</th>
						<th align="center">���аǼ�</th>
						<th align="center">�������ϸ�</th>
						<th align="center">ó�����</th>
						<th align="center">���ε���</th>
						<th align="center">���ε��Ͻ�</th>
						<th align="center">����</th>
					</tr>
					</thead>
					<tbody>
						<?if(!empty($listData)){?>
						<?foreach($listData as $key => $val){extract($val);?>
						<tr class="rowData" rol-data='<?=$upldate?>',     rol-gubun ='<?=$gubun?>' rol-gubunsub ='<?=$gubunsub?>'   rol-uplnum ='<?=$uplnum?>'    rol-filename ='<?=$filename?>'style="cursor:pointer;">
						<!--	<td align="center"><?=date("Y-m-d",strtotime($upldate)) ?></td> -->
							<td align="center"><?=$upno?></td>
							<td align="left"><?=$GNAME?></td>
							<td align="left"><?=$name?></td>
							<td align="right" ><?=number_format($cnt)?></td>                     
							<td align="right" ><?=number_format($ucnt)?></td>
							<td align="right" ><?=number_format($fcnt)?></td>
							<td align="left"><?=$filename?></td>
							<td align="left"><?=$bigo?></td>
							<td align="left"><?=$sname?></td>
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
				<ul class="pagination pagination-sm" style="margin: 10px">
				  <?=$pagination->create_links();?>
				</ul>
			</div>

		</fieldset>
	</div><!-- // content_wrap -->
</div>


<!-- // container -->
<!-- // wrap -->
<script type="text/javascript">
// ��ǰ���� ���â
function upload(){
	$.ajaxLoding('ga_kwn_01_upload.php',$('.layerBody_item'),$('#modal2'));	
}

 $(document).ready(function(){
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
		if($(trData).find("td").index($(this))!='10'){
			var popOpen	= window.open("<?=$conf['homeDir']?>/sub/menu3/ga_swon1_01_upload.php?upldate="+upldate+"&gubun=" +gubun+"&gubunsub=" + gubunsub+"&uplnum=" +uplnum +"&filename=" +filename                    ,"width=1200px,height=950px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
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


	$( window ).resize(function() {		
		windowResize($(this));
	});
		var windowResize	= function(win){
		$(".tb_type01").height($(win).height()-170);
	};
	windowResize($( window ));

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>