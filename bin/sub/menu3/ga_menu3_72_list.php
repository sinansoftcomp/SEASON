<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

$upldate	= $_GET['upldate'];
$gubun	= $_GET['gubun'];
$gubunsub	= $_GET['gubunsub'];
$uplnum	= $_GET['uplnum'];
  
$filename	= $_GET['filename'];
$page	= $_GET['page'];

// �⺻ ������ ����
$page = ($_GET['page']) ? $_GET['page'] : 1;
$page_row	= 200;

$limit1 = ($page-1)*$page_row+1;
$limit2 = $page*$page_row;

$ser_con = ($_GET['ser_con']) ? $_GET['ser_con'] : "";

if ($ser_con == 'Y') {
	$ser_con_where = '   and (samt1 + samt2 + samt3 + samt4 + samt5 + samt6 + samt7 + samt8 + samt9+ samt10+ samt11+ samt12+ samt13+hssu+bsu) <> 0 ';
}

//�˻� ������ ���ϱ� 
$sql= "
	select *
	from(
		select 
				*,    a.SAMT6 + a.SAMT7 + a.SAMT8+ a.SAMT9+ a.SAMT10+ a.SAMT11+ a.SAMT12+ a.SAMT13 as  KITA_HAP ,
				a.SAMT1 +a.SAMT2 +a.SAMT3 +a.SAMT4 +a.SAMT5 +a.SAMT6 + a.SAMT7 + a.SAMT8+ a.SAMT9+ a.SAMT10+ a.SAMT11+ a.SAMT12+ a.SAMT13 as S_TOTAL ,
				row_number()over(order by a.iseq ) rnum
		from INS_IPMST(nolock) a	
		where a.scode = '".$_SESSION['S_SCODE']."'
		  and a.ipdate	=  '".$upldate."' 
		  and a.gubun	=  '".$gubun."' 
		  and a.gubunsub	=  '".$gubunsub."'   
		  and a.ino	=  '".$uplnum."'  $ser_con_where
		) p
	where rnum between ".$limit1." AND ".$limit2 ;



$qry	= sqlsrv_query( $mscon, $sql );
$listData = array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listData[]	= $fet;
}

// ������ �� �Ǽ��� �հ� 
//�˻� ������ ���ϱ� 
$sql= "
		select 
				count(*) CNT,sum(samt1) samt1,sum(samt2) samt2,sum(samt3) samt3,sum(samt4) samt4,sum(samt5) samt5,
				sum(SAMT6 +  SAMT7 +  SAMT8+  SAMT9+  SAMT10+  SAMT11+  SAMT12+  SAMT13) as   kita_hap,  
				sum(SAMT1 + SAMT2 + SAMT3 + SAMT4 + SAMT5 + SAMT6 +  SAMT7 +  SAMT8+  SAMT9+  SAMT10+  SAMT11+  SAMT12+  SAMT13) as s_total ,sum(HSSU) hssu,
				sum(MAMT) mamt , sum(HWANAMT) hwanamt,  sum(BSU) bsu
		from INS_IPMST(nolock) a
		where a.scode = '".$_SESSION['S_SCODE']."'
		  and a.ipdate	=  '".$upldate."' 
		  and a.gubun	=  '".$gubun."' 
		  and a.gubunsub	=  '".$gubunsub."'   
		  and a.ino	=  '".$uplnum."'"  .$ser_con_where  ;
 
$qry =  sqlsrv_query($mscon, $sql);
$totalResult =  sqlsrv_fetch_array($qry); 

// ��ü�����
$sql= "select inscode code, name, gubun from inssetup where scode = '".$_SESSION['S_SCODE']."' and useyn = 'Y' order by name";
$qry= sqlsrv_query( $mscon, $sql );
$instot	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
  $instot[] = $fet;
}

// ������ Ŭ���� ����
// �ε�
include_once($conf['rootDir'].'/include/class/Pagination.php');

// ����
$pagination = new Pagination(array(
		'base_url' => $_SERVER['PHP_SELF']."?upldate=".$upldate."&gubun=".$gubun."&gubunsub=".$gubunsub ."&uplnum=".$uplnum."&filename=".$filename."&ser_con=".$ser_con    ,
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
			<div class="tit_wrap mt20">
				<span  class="tit_big">�����纰 ��������ε� ��Ȳ  <?= '  ['. $filename . ' ]'?>  </span>

				<span class="btn_wrap">				
					<a class="btn_s white btn_search"  style="margin: 0; min-width:100px;" >��ü��ȸ</a>
					<a class="btn_s white btn_searchu"  style="min-width:100px;">��������</a>
					<a class="btn_s white" style="min-width:100px;" onclick="kwn_close();">�ݱ�</a>
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

			<div class="tb_type01 div_grid3" style="overflow-y:auto;">
				<table id="sort_table" class="gridhover"  style="width: 4750px;">
					<colgroup>
						<col width="50px">
						<col width="50px">
						<col width="100px">
						<col width="70px">
						<col width="150px">
						<col width="80px"> <!--�����code-->
						<col width="80px"><!--��������code-->
						<col width="90px"><!--��������-->
						<col width="70px">
						<col width="90px">
						<col width="90px">
						<col width="90px">
						<col width="90px">
						<col width="90px">
						<col width="90px">
						<col width="90px">
						<col width="90px"> 
						<col width="90px">
						<col width="90px">
						<col width="90px">
						<col width="90px">
						<col width="110px"> <!--��������-->
						<col width="90px">  <!--ȯ��-->
						<col width="90px">  <!--��Ȱ-->
						<col width="90px">  <!--���������-->
						<col width="90px">  <!--ȯ�꺸���-->
						<col width="90px">  <!--������-->
						<col width="90px">  <!--���Թ��-->
						<col width="90px">  <!--�����ֱ�-->
						<col width="90px">  <!--���谳����-->
						<col width="90px">  <!--����������	-->
						<col width="90px">  <!--�����û����	-->
						<col width="90px">  <!--û����-->
						<col width="90px">  <!--������-->
						<col width="90px"> <!--�Ǻ�����-->
						<col width="90px">  <!--������ȣ-->
						<col width="90px">
						<col width="90px">
						<col width="90px">
						<col width="90px">
						<col width="90px">
						<col width="auto">

					</colgroup>
					<thead>
					<tr class="rowTop">
						<th align="center">�����</th>
						<th align="center">����</th>
						<th align="center">���ǹ�ȣ</th>
						<th align="center">��ǰ��CODE</th>
						<th align="center">��ǰ��</th>						
						<th align="center">�����</th>						
						<th align="center">�����</th>
						<th align="center">����θ�</th>
						<th align="center">����ȸ��</th>					
						<th align="right">������1</th>
						<th align="right">������1Amt</th>
						<th align="right">������2</th>
						<th align="right">������2amt</th>
						<th align="right">������3</th>
						<th align="right">������3amt</th>
						<th align="right">������4</th>
						<th align="right">������4amt</th>
						<th align="right">������5</th>
						<th align="right">������5amt</th>
						<th align="right">��Ÿ</th>
						<th align="right">��Ÿamt</th>
						<th align="right">�������հ�</th>
						<th align="right">ȯ���ݾ�</th>
						<th align="right">��Ȱ�ݾ�</th>
						<th align="right">���������</th>
						<th align="right">ȯ�꺸���</th>
						<th align="center">������</th>
						<th align="right">���Թ��</th>
						<th align="center">�����ֱ�</th>
						<th align="center">���谳����</th>
						<th align="center">����������</th>
						<th align="center">�����û����</th>
						<th align="center">û����</th>
						<th align="center">������</th>
						<th align="center">�Ǻ�����</th>						
						<th align="center">������ȣ</th>
						<th align="center">�輭����</th>
						<th align="center">�㺸�ڵ�</th>
						<th align="center">�㺸��</th>
						<th align="center">�����ǹ�ȣ</th>
						<th align="center">����Ⱓ</th>
						<th align="center">���ԱⰣ</th> 
					</tr>
					</thead>
						<?if(!empty($listData)){?>						
						<tr class="summary sticky"style="top:32px">
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th class="sum01"><?=number_format($totalResult['CNT'])?>��</th>							
						<th class="sum01">������1��</th>
						<th class= "sum01"><?=number_format($totalResult['samt1'])?></th>							
						<th class="sum01">������2��</th>
						<th class="sum01"><?=number_format($totalResult['samt2'])?></th>
						<th class="sum01">������3��</th>
						<th class="sum01"><?=number_format($totalResult['samt3'])?></th>
						<th class="sum01">������4��</th>
						<th class="sum01"><?=number_format($totalResult['samt4'])?></th>
						<th class="sum01">������5��</th>
						<th class="sum01"><?=number_format($totalResult['samt5'])?></th>
						<th class="sum01">��Ÿ��</th>
						<th class="sum01"><?=number_format($totalResult['kita_hap'])?></th>
						<th class="sum01"><?=number_format($totalResult['s_total'])?></th>
						<th class="sum01"><?=number_format($totalResult['hssu'])?></th>
						<th class="sum01"><?=number_format($totalResult['bsu'])?></th>
						<th class="sum01"><?=number_format($totalResult['mamt'])?></th>
						<th class="sum01"><?=number_format($totalResult['hwanamt'])?></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						</tr>
						<?}?>
					<tbody>
						<?if(!empty($listData)){?>
						<?foreach($listData as $key => $val){extract($val);?>
						<tr class="rowData" rol-data='<?=$ORIDATA?>' rol-file='<?=$filename?>' rol-iseq='<?=$ISEQ?> '>
							<td align="center"><?=$YYMM?></td>
							<td align="left"><?=$ISEQ?></td>
							<td align="left"><?=$KCODE?></td>
							<td align="left"><?=$ITEM?></td>
							<td align="left"><?=mb_substr($ITEMNM,0,11, 'euc-kr').'..'?></td>
							<td align="center" ><?=$KNAME?></td>
							<td align="left"><?=$KSMAN?></td>
							<td align="center" ><?=$KSMAN_NAME?></td>
							<td class="sum01" ><?=$NCNT?></td>
							<td align="right" class="font_blue" ><?=$ST1?></td>
							<td align="right"class="font_blue"><?=number_format($SAMT1)?></td>
							<td align="right"><?=$ST2?></td>
							<td align="right"><?=number_format($SAMT2)?></td>
							<td align="right" class="font_blue" ><?=$ST3?></td>
							<td align="right"class="font_blue"><?=number_format($SAMT3)?></td>
							<td align="right"><?=$ST4?></td>
							<td align="right"><?=number_format($SAMT4)?></td>
							<td align="right" class="font_blue" ><?=$ST5?></td>
							<td align="right"class="font_blue"><?=number_format($SAMT5)?></td>
							<td align="right"><?='�ܼ�����'?></td>									 
							<td align="right"><?=number_format($KITA_HAP)?></td>	 
							<td align="right" ><?=number_format($S_TOTAL)?></td>
							<td align="right"><?=number_format($HSSU)?></td>
							<td align="right"><?=number_format($BSU)?></td>
							<td align="right"><?=number_format($MAMT)?></td>
							<td align="right"><?=number_format($HWANAMT)?></td>
							<td align="left"><?=$ISTBIT?></td>
							<td align="left"><?=mb_substr($NBIT,0,7, 'euc-kr')?></td>
							<td align="left"><?=$NJUKI?></td>
							<td><?if(trim($ADATE)) echo date("Y-m-d",strtotime($ADATE))?></td>		
							<td><?if(trim($BDATE)) echo date("Y-m-d",strtotime($BDATE))?></td>		
							<td><?if(trim($CDATE)) echo date("Y-m-d",strtotime($CDATE))?></td>		
							<td><?if(trim($DDATE)) echo date("Y-m-d",strtotime($DDATE))?></td>		
							<td><?if(trim($EDATE)) echo date("Y-m-d",strtotime($EDATE))?></td>					 
							<td align="center"><?=$PNAME?></td>
							<td align="center"><?=$CARNUM?></td>

							<td align="left"><?=$BCON?></td>
							<td align="left"><?=$DCODE?></td>
							<td align="left"><?=mb_substr($DNAME,0,7, 'euc-kr')?></td>
							<td align="left"><?=$GKCODE?></td>
							<td align="left"><?=$BKIKAN?></td>
							<td align="left"><?=$NKIKAN?></td>
						</tr>
						<?}}else{?>
							<tr>
								<td style="color:#8C8C8C" colspan=14>�˻��� �����Ͱ� �����ϴ�</td>
							</tr>
						<?}?>
					</tbody>
				</table>
			</div><!-- // tb_type01 -->

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


// �Աݻ� display 
function ins_display(oridata,filename,iseq){

	var left = Math.ceil((window.screen.width - 1200)/2);
	var top = Math.ceil((window.screen.height - 800)/2);
	var popOpen	= window.open("<?=$conf['homeDir']?>/sub/menu3/ga_menu3_72_list_pop.php?oridata="+oridata +"&filename=" +filename+"&iseq=" +iseq ,"insDt","width=1200px,height=700px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
	popOpen.focus();


}

function kwn_close(){	
	window.close();
	//opener.location.reload();
}


 
$(document).ready(function(){
		var upldate	= $("#upldate").val();
		var gubun	= $("#gubun").val(); 
		var gubunsub	= $("#gubunsub").val(); 
		var uplnum	= $("#uplnum").val(); 
		var ser_con	= $("#ser_con").val(""); 
		var page	= $("#page").val(); 
 


	// ��ȸ
	$(".btn_search").click(function(){
		var upldate	= $("#upldate").val();
		var gubun	= $("#gubun").val(); 
		var gubunsub	= $("#gubunsub").val(); 
		var uplnum	= $("#uplnum").val(); 
		var filename	= $("#filename").val(); 
		var ser_con	= $("#ser_con").val(""); 
		var page	= $("#page").val(); 

		//alert(upldate);
		//alert(uplnum);
		$("form[name='searchFrm']").attr("method","get");
		$("form[name='searchFrm']").attr("target","");

		$("form[name='searchFrm'] input[name='upldate']").val(upldate);
		$("form[name='searchFrm'] input[name='gubun']").val(gubun);
		$("form[name='searchFrm'] input[name='gubunsub']").val(gubunsub);
		$("form[name='searchFrm'] input[name='uplnum']").val(uplnum);
		$("form[name='searchFrm'] input[name='filename']").val(filename);

		$("form[name='searchFrm'] input[name='ser_con']").val("");
		$("form[name='searchFrm'] input[name='page']").val(page);
		$("form[name='searchFrm']").submit();
	}); 


	$(".btn_searchu").click(function(){
		var upldate	= $("#upldate").val();
		var gubun	= $("#gubun").val(); 
		var gubunsub	= $("#gubunsub").val(); 
		var uplnum	= $("#uplnum").val(); 
		var filename	= $("#filename").val(); 
		var ser_con	= $("#ser_con").val("Y"); 
		var page	= $("#page").val(); 

		//alert(upldate);
		//alert(uplnum);
		$("form[name='searchFrm']").attr("method","get");
		$("form[name='searchFrm']").attr("target","");

		$("form[name='searchFrm'] input[name='upldate']").val(upldate);
		$("form[name='searchFrm'] input[name='gubun']").val(gubun);
		$("form[name='searchFrm'] input[name='gubunsub']").val(gubunsub);
		$("form[name='searchFrm'] input[name='uplnum']").val(uplnum);
		$("form[name='searchFrm'] input[name='filename']").val(filename);
		$("form[name='searchFrm'] input[name='ser_con']").val("Y");
		$("form[name='searchFrm'] input[name='page']").val(page);

		$("form[name='searchFrm']").submit();
	}); 

	// ����Ʈ Ŭ���� �󼼳��� ��ȸ
	$(".rowData > td").click(function(){
		var trData = $(this).parent();
		var idx    = $(".rowData").index($(trData));

		var oridata  = $(".rowData").eq(idx).attr('rol-data'); 
		var filename  = $(".rowData").eq(idx).attr('rol-file'); 
		var iseq  = $(".rowData").eq(idx).attr('rol-iseq'); 
		
		ins_display(oridata,filename,iseq); 
	})


	// ��� Ŭ��
	$(".rowTop > th").click(function(){
		var trData = $(this).parent();

		var idx = $(trData).find("th").index($(this));

		// include/bottom.php ����	
		sortTable("sort_table", idx, 2);
	})


});

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>