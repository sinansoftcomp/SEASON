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
$page_row	= 100;

$limit1 = ($page-1)*$page_row+1;
$limit2 = $page*$page_row;

 
//�˻� ������ ���ϱ� 
$sql= "
	select *
	from(
		select 
				*,dbo.decryptkey(a.sjuno) ASJUNO , dbo.decryptkey(PSJUNO) APSJUNO ,  row_number()over(order by a.UPLSEQ ) rnum
		from kwn(nolock) a	
		where a.scode = '".$_SESSION['S_SCODE']."'
		  and a.UPLDATE	=  '".$upldate."' 
		  and a.gubun	=  '".$gubun."' 
		  and a.gubunsub	=  '".$gubunsub."'   
		  and a.UPLNUM	=  '".$uplnum."' 
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
				count(*) CNT,SUM(MAMT) samt1,SUM(HAMT) samt2,SUM(SAMT) samt3
		from kwn(nolock) a
		where a.scode = '".$_SESSION['S_SCODE']."'
		  and a.UPLDATE	=  '".$upldate."' 
		  and a.gubun	=  '".$gubun."' 
		  and a.gubunsub	=  '".$gubunsub."'   
		  and a.UPLNUM	=  '".$uplnum."'"    ;
 
$qry =  sqlsrv_query($mscon, $sql);
$totalResult =  sqlsrv_fetch_array($qry); 

 
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
			<legend>�����ε�</legend>

			
			<!-- �˻����� -->
			<div class="tit_wrap mt20">
				<span class="tit_big" >�����纰 �����ε� ��Ȳ  <?= '  ['. $filename . ' ]'?></span> 
				<span class="btn_wrap">				
					<a class="btn_s white btn_search"  style="margin: 0; min-width:100px;" >��ȸ</a>
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
				<table id="sort_table" class="gridhover"  style="width:2500px;">
					<colgroup>
	 
						<col width="50px">
						<col width="100px">
						<col width="100px">
						<col width="250px">
						<col width="100px">  
						<col width="120px"> 
						<col width="100px"> 
						<col width="100px">
						<col width="100px">
						<col width="100px">
						<col width="100px">
						<col width="100px">
						<col width="130px">
						<col width="130px">
						<col width="130px">
						<col width="100px"> 
						<col width="100px">
						<col width="100px">
						<col width="100px">
						<col width="auto">
					</colgroup>
					<thead>
					<tr class="rowTop">
						<th align="center">����</th>
						<th align="center">���ǹ�ȣ</th>
						<th align="center">��ǰ��CODE</th>
						<th align="center">��ǰ��</th>						
						<th align="center">��ǰ����</th>						
						<th align="center">�����</th>	
						<th align="center">�ֹι�ȣ</th>						
						<th align="center">�����</th>
						<th align="center">����θ�</th>
						<th align="center">�����</th>					
						<th align="center">��ళ����</th>
						<th align="center">���������</th>
						<th align="center">������</th>
						<th align="right">�����</th>
						<th align="right">ȯ�����</th>
						<th align="right">���������</th>
						<th align="center">����Ⱓ</th>
						<th align="center">�����ֱ�</th>
						<th align="center">�Ǻ�����</th>
						<th align="center">�Ǻ������ֹ�</th>
						<th align="center">������ȣ</th>
					</tr>
					</thead>
						<?if(!empty($listData)){?>						
						<tr  class="summary sticky"style="top:32px">
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
						<th  class="sum01"><?=number_format($totalResult['CNT'])?>��</th>	
						<th></th>
						<th></th>
						<th class="sum01"><?=number_format($totalResult['samt1'])?></th>							
						<th  class="sum01"><?=number_format($totalResult['samt2'])?></th>
						<th  class="sum01"><?=number_format($totalResult['samt3'])?></th>
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
						<tr class="rowData" rol-data='<?=$ORIDATA?>' rol-file='<?=$filename?>' rol-iseq='<?=$UPLSEQ?> '>
							<td align="left"><?=$UPLSEQ?></td>
							<td align="left"><?=$KCODE?></td>
							<td align="left"><?=$ITEM?></td>
							<td align="left"><?=mb_substr($ITEMNM,0,25, 'euc-kr').'..'?></td>   
							<td align="center"><?=$conf['insilj'][$INSILJ]?></td>
							<td align="left"><?=mb_substr($KNAME,0,6, 'euc-kr').'..'?></td>     <!--�����-->
							<td align="left"><?=$ASJUNO?></td>  <!--������ֹι�ȣ-->
							<td align="left"><?=$KSMAN?></td>				 <!--������ڵ�-->
							<td align="center" ><?=$KSMAN_NAME?></td>  <!--����θ�  ���� ����-->
							<td><?if(trim($KDATE)) echo date("Y-m-d",strtotime($KDATE))?></td>	<!--�����-->
							<td><?if(trim($FDATE)) echo date("Y-m-d",strtotime($FDATE))?></td>		<!--���ళ����-->
							<td><?if(trim($TDATE)) echo date("Y-m-d",strtotime($TDATE))?></td>	<!--���������-->	 
 							<td align="left"  ><?=$KSTBIT?></td><!--������-->
							<td   align="right"  ><?=number_format($MAMT)?></td>   <!--�����-->
							<td align="right" ><?=number_format($HAMT)?></td> <!--ȯ�����-->
							<td align="right"><?=number_format($SAMT)?></td>  <!--��������� -->
							<td align="left"><?$INSTERM?></td>  <!--����Ⱓ -->
							<td align="left"  ><?=$NBIT?></td> <!--�����ֱ� -->
							<td align="left"><?=mb_substr($PNAME,0,6, 'euc-kr').'..'?></td>     <!--�Ǻ����� -->
							<td align="left"   ><?=$APSJUNO ?></td> <!--�Ǻ����� -->
							<td align="left"><?=$CARNUM?></td> <!--������ȣ -->
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
	var top = Math.ceil((window.screen.height - 900)/2);
	var popOpen	= window.open("<?=$conf['homeDir']?>/sub/menu3/ga_menu3_52_list_pop.php?oridata="+oridata +"&filename=" +filename+"&iseq=" +iseq ,"insDt","width=1200px,height=750px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
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