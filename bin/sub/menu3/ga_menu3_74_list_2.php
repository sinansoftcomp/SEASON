<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

$FYYMM   = substr($_REQUEST['SDATE1'],0,4).substr($_REQUEST['SDATE1'],5,2);
$TYYMM  =  substr($_REQUEST['SDATE2'],0,4).substr($_REQUEST['SDATE2'],5,2);

// ���ڵ���ȯ
$searchF1Text = iconv("UTF-8","EUCKR",$_REQUEST['searchF1Text']);
if($_REQUEST['searchF1'] && $_REQUEST['searchF1Text']){
	if($_REQUEST['searchF1'] == 'sjuno'){
		$where  .= " and (a.snum like '%".$_REQUEST['searchF1Text']."%' or Cast(dbo.DECRYPTKEY(a.sjuno) as varchar) like '%".$_REQUEST['searchF1Text']."%') ";
	}else if($_REQUEST['searchF1'] == 'tel'){
		$where  .= " and (a.tel like replace('%".$_REQUEST['searchF1Text']."%','-','') or a.htel like replace('%".$_REQUEST['searchF1Text']."%','-','')) ";
	}else if($_REQUEST['skey'] && $_REQUEST['searchF1'] == 's1'){	//	�������
		$where  .= " and a.ksman = '".$_REQUEST['skey']."' ";	
	}else if($_REQUEST['skey'] && $_REQUEST['searchF1'] == 's2'){	//	�������
		$where  .= " and a.kdman = '".$_REQUEST['skey']."' ";	
	}else{		
		$where  .= " and ".$_REQUEST['searchF1']." like '%".$searchF1Text."%' ";	
	}
}

$where = "";
// ������ Ʈ�� ���ý� �Ҽ�����(swon ��Ī : s2 - kdman(����α���)) 
if($_REQUEST['id']){
	
	$Ngubun = substr($_REQUEST['id'],0,2);

	if($Ngubun == 'N1'){
		$bonbu = substr($_REQUEST['id'],2,10);
		$where  .= " and e.bcode = '".$bonbu."' " ;
	}else if($Ngubun == 'N2'){
		$jisa = substr($_REQUEST['id'],2,10);
		$where  .= " and f.jscode = '".$jisa."' " ;
	}else if($Ngubun == 'N3'){
		$jijum = substr($_REQUEST['id'],2,10);
		$where  .= " and g.jcode = '".$jijum."' " ;
	}else if($Ngubun == 'N4'){
		$team = substr($_REQUEST['id'],2,10);
		$where  .= " and h.tcode = '".$team."' " ;
	}else if($Ngubun == 'N5'){
		$ksman = substr($_REQUEST['id'],2,10);
	}
}


/* ------------------------------------------------------
	�⵵ / �˻����� / �� ��ȸ�� ���� End
------------------------------------------------------ */

// �⺻ ������ ����
$page = ($_REQUEST['page']) ? $_REQUEST['page'] : 1;
$page_row	= 500;
$limit1 = ($page-1)*$page_row+1;
$limit2 = $page*$page_row;


$sql= "
	select *
	from(
				SELECT a.IPDATE,a.GUBUN	,a.GUBUNSUB, a.INO	,a.ISEQ, a.KCODE,a.KNAME,a.PNAME,a.CARNUM,substring( a.ITEMNM,1,30) ITEMNM ,	 a.YYMM ,a.KSMAN , b.BSCODE,c.SKEY, a.INSCODE,d.NAME,
								isnull(a.SAMT1,0)+isnull(a.SAMT2,0)+isnull(a.SAMT3,0)+isnull(a.SAMT4,0)+isnull(a.SAMT5,0)+isnull(a.SAMT6,0)+isnull(a.SAMT7,0)+isnull(a.SAMT8,0)
								+isnull(a.SAMT9,0)+isnull(a.SAMT10,0)+isnull(a.SAMT11,0)+isnull(a.SAMT12,0) +isnull(a.SAMT13,0)+isnull(a.HSSU,0)+isnull(a.BSU,0) CATOTAL,
								e.FILENAME	, a.ORIDATA, row_number()over(order by c.skey	) rnum 
				FROM INS_IPMST(nolock) a
				   left outer join INSWON(nolock) b on  a.scode = b.scode and  a.INSCODE = b.INSCODE and a.KSMAN = b.BSCODE  
				   left outer join swon(nolock)  c on  b.scode = c.scode and  b.SKEY = c.SKEY
				   left outer join INSSETUP(nolock)  d on  a.scode = d.scode and  a.INSCODE = d.INSCODE
				   left outer join UPLOAD_HISTORY(nolock)  e on  a.scode = e.scode and  a.IPDATE = e.UPLDATE   and  a.GUBUN = e.GUBUN and  a.GUBUNSUB = e.GUBUNSUB and  a.INO = e.UPLNUM
				where    a.scode =    '".$_SESSION['S_SCODE']."' and  a.YYMM >= '".$FYYMM."'  and   a.YYMM <= '".$TYYMM."' and b.sgubun = '1' and   (  isnull(b.BSCODE,'') = '' or isnull(a.ksman,'') = '' or isnull(c.SKEY,'') = ''  )
 
) p
where rnum between ".$limit1." AND ".$limit2 ;
 
/*
echo '<pre>';
echo $sql; 
echo '</pre>';
*/

$qry	= sqlsrv_query( $mscon, $sql );
$listData = array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listData[]	= $fet;
}

// ������ �� �Ǽ�
//�˻� ������ ���ϱ� 
$sql= "
	select COUNT(*) CNT, SUM( CATOTAL)   CATOTAL
	from(
				SELECT a.IPDATE,a.GUBUN	,a.GUBUNSUB, a.INO	,a.ISEQ, a.KCODE,a.KNAME,a.PNAME,a.CARNUM,substring( a.ITEMNM,1,30) ITEMNM ,	 a.YYMM ,a.KSMAN , b.BSCODE,c.SKEY, a.INSCODE,d.NAME,
								isnull(a.SAMT1,0)+isnull(a.SAMT2,0)+isnull(a.SAMT3,0)+isnull(a.SAMT4,0)+isnull(a.SAMT5,0)+isnull(a.SAMT6,0)+isnull(a.SAMT7,0)+isnull(a.SAMT8,0)
								+isnull(a.SAMT9,0)+isnull(a.SAMT10,0)+isnull(a.SAMT11,0)+isnull(a.SAMT12,0) +isnull(a.SAMT13,0)+isnull(a.HSSU,0)+isnull(a.BSU,0) CATOTAL
				FROM INS_IPMST(nolock) a
				   left outer join INSWON(nolock) b on  a.scode = b.scode and  a.INSCODE = b.INSCODE and a.KSMAN = b.BSCODE  
				   left outer join swon(nolock)  c on  b.scode = c.scode and  b.SKEY = c.SKEY
				   left outer join INSSETUP(nolock)  d on  a.scode = d.scode and  a.INSCODE = d.INSCODE
				   left outer join UPLOAD_HISTORY(nolock)  e on  a.scode = e.scode and  a.IPDATE = e.UPLDATE   and  a.GUBUN = e.GUBUN and  a.GUBUNSUB = e.GUBUNSUB and  a.INO = e.UPLNUM
				where    a.scode =    '".$_SESSION['S_SCODE']."' and  a.YYMM >= '".$FYYMM."'  and   a.YYMM <= '".$TYYMM."' and b.sgubun = '1' and   (  isnull(b.BSCODE,'') = '' or isnull(a.ksman,'') = '' or isnull(c.SKEY,'') = '')   ) P
		  
		  " ;
/*
echo '<pre>';
 echo $sql; 
echo '</pre>';
 */ 

$qry =  sqlsrv_query($mscon, $sql);
$totalResult =  sqlsrv_fetch_array($qry); 


// ������ Ŭ���� ����
// �ε�
include_once($conf['rootDir'].'/include/class/Pagination.php');

// ����
$pagination = new Pagination(array(
		'base_url' => $_SERVER['PHP_SELF']."?SDATE1=".$_REQUEST['SDATE1']."&SDATE2=".$_REQUEST['SDATE2']."&id=".$_REQUEST['id']."&page=Y",
		'per_page' => $page_row,
		'total_rows' => $totalResult['CNT'],
		'cur_page' => $page,
));

sqlsrv_free_stmt($result);
sqlsrv_close($mscon);

?>


<div class="tb_type01 kwndatalist div_grid" style="overflow-y:auto;">	
	<table id="sort_table" class="gridhover" style="min-width: 2200px;">
		<colgroup>
			<col width="80px">
			<col width="120px">
			<col width="110px">
			<col width="110px">
			<col width="110px">

			<col width="90px">
			<col width="90px">
			<col width="90px">
			<col width="90px">
			<col width="90px">


			<col width="280px">
			<col width="130px">
			<col width="auto">

		</colgroup>
		<thead>
		<tr class="rowTop">
			<th align="center">�����</th>
			<th align="center">���ε��ȣ</th>
			<th align="center">���ǹ�ȣ</th>
			<th align="center">�����</th>				
			<th align="center">�Ǻ�����</th>

			<th align="center">���ε�����</th>
			<th align="center">��������</th>
			<th align="center">GA���</th>
			<th align="center">���Լ�����</th>
			<th align="center">������ȣ</th>

			<th align="center">��ǰ��</th>
			<th align="center">������</th>
			<th align="center">���ε� ���ϸ�</th>
		</tr>
		</thead>			
			<tr class="summary sticky"style="top:32px">
			<th></th>
			<th></th>
			<th class="sum01"><?= ' ���Ī �Ǽ� ' ?></th>							
			<th></th>
			<th class="sum01"><?=number_format($totalResult['CNT'] )?></th>							
			<th></th>
			<th></th>
			<th></th>
			<th class="sum01"><?=number_format($totalResult['CATOTAL'] )?></th>			
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			</tr>

		<tbody>
			<?if(!empty($listData)){?>
			<?$ii = 0?>
			<?foreach($listData as $key => $val){extract($val);?>
			<tr class="rowData" rol-data='<?=$ORIDATA?>' rol-file='<?=$FILENAME?>' rol-iseq='<?=$ISEQ?> '>
				<td align="left"><?=$YYMM?></td>
				<td align="left"><?=$IPDATE.'-'.$GUBUN.'-'.$GUBUNSUB.'-'.$INO.'-'.$ISEQ?></td>
				<td align="left"><?=$KCODE?></td>
				<td align="left"><?=$KNAME?></td>
				<td align="left"><?=$PNAME?></td>

				<td align="left"><?=$KSMAN?></td>
				<td align="left"><?=$BSCODE?></td>
				<td align="left"><?=$SKEY?></td>
				<td align="right" class="sum01"><?=number_format($CATOTAL)?></td>
				<td align="left"><?=$CARNUM?></td>


				<td align="left"><?=$ITEMNM?></td>
				<td align="left"><?=$NAME.'('.$INSCODE.')'   ?></td>
				<td align="left"><?=$FILENAME?></td>
			</tr>
			<?}}else{?>
				<tr>
					<td style="color:#8C8C8C" colspan=24>�˻��� �����Ͱ� �����ϴ�</td>
				</tr>
			<?}?>
		</tbody>
	</table>
</div><!-- // tb_type01 -->

<div style="text-align: center">		
	<ul class="pagination pagination-sm kwnlist" style="margin: 5px 5px 0 5px">
	  <?=$pagination->create_links();?>
	</ul>
</div>

<script type="text/javascript">

function ins_display(oridata,filename,iseq){

	var left = Math.ceil((window.screen.width - 1200)/2);
	var top = Math.ceil((window.screen.height - 1000)/2);
	var popOpen	= window.open("<?=$conf['homeDir']?>/sub/menu3/ga_menu3_72_list_pop.php?oridata="+oridata +"&filename=" +filename+"&iseq=" +iseq ,"insDt","width=1200px,height=800px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
	popOpen.focus();
}


// ��� Ŭ��
$(".rowTop > th").click(function(){
	var trData = $(this).parent();

	var idx = $(trData).find("th").index($(this));

	// include/bottom.php ����	
	sortTable("sort_table", idx, 2);
})


$(document).ready(function(){

	// page �Լ� ajax������ ����� ���� ó��
	$(".kwnlist a").click(function(){
		$('#page').val('Y');
		var res = $(this).attr("href").split("?");
		if(res[0] && res[1]){
			//alert(res[0]+"//"+res[1]);
			 //data_right_jojik div id�� ����
			ajaxLodingTarget(res[0],res[1],event,$('#kwnlist'));    
		}
		return false;
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

 

});

</script>


<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>