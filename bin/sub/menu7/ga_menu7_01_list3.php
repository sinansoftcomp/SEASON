<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

$FYYMM   = substr($_REQUEST['SDATE1'],0,4).substr($_REQUEST['SDATE1'],5,2).'';
$TYYMM  =  substr($_REQUEST['SDATE2'],0,4).substr($_REQUEST['SDATE2'],5,2).'99';   //-->�Ѵ޸� ����.

$where = "";

// ������ Ʈ�� ���ý� �Ҽ�����(swon ��Ī : s2 - kdman(����α���)) 
if($_REQUEST['id']){
	
	$Ngubun = substr($_REQUEST['id'],0,2);

	if($Ngubun == 'N1'){
		$bonbu = substr($_REQUEST['id'],2,10);
		$where  .= " and c.BONBU = '".$bonbu."' " ;
	}else if($Ngubun == 'N2'){
		$jisa = substr($_REQUEST['id'],2,10);
		$where  .= " and c.JISA = '".$jisa."' " ;
	}else if($Ngubun == 'N3'){
		$jijum = substr($_REQUEST['id'],2,10);
		$where  .= " and c.JIJUM = '".$jijum."' " ;
	}else if($Ngubun == 'N4'){
		$team = substr($_REQUEST['id'],2,10);
		$where  .= " and c.TEAM = '".$team."' " ;
	}else if($Ngubun == 'N5'){
		$ksman = substr($_REQUEST['id'],2,10);
		$where  .= " and c.skey = '".$ksman."' " ;
	}
}

/* ------------------------------------------------------
	�⵵ / �˻����� / �� ��ȸ�� ���� End
------------------------------------------------------ */



// �⺻ ������ ����
$page = ($_REQUEST['page']) ? $_REQUEST['page'] : 1;
$page_row	= 3500;
$limit1 = ($page-1)*$page_row+1;
$limit2 = $page*$page_row;

//--> union���� �������� ���ĪĪ ����� ��� �������̺��� �������� �ʾ� data�� �����Ǳ� �����̴�.
  $sql= "
	select *
	from(

			select     a.*,  e.BNAME,f.JSNAME,g.JNAME,h.TNAME,  row_number()over(order by  a.BONBU,a.JISA,a.JIJUM,a.TEAM  ) rnum from   	
					(SELECT a.SCODE, isnull(c.BONBU,'') BONBU ,isnull(c.JISA,'') JISA ,isnull(c.JIJUM,'') JIJUM ,isnull(c.TEAM,'') TEAM  , 
								sum(IMST1) IPMST1,sum(IMST2) IPMST2,sum(IMST3) IPMST3,sum(IMST4) IPMST4 ,SUM(SU1) SU1,SUM(SU2) SU2,SUM(SU3) SU3,SUM(SU4) SU4,   sum(IMST4) -  SUM(SU4) CATOT , SUM(SUNAB) SUNAB  ,
								sum(KWN_CNT1) KWN_CNT1,sum(KWN_AMT1) KWN_AMT1,sum(KWN_CNT2) KWN_CNT2,sum(KWN_AMT2) KWN_AMT2 ,sum(KWN_CNT3) KWN_CNT3,sum(KWN_AMT3) KWN_AMT3 ,sum(KWN_CNT4) KWN_CNT4,sum(KWN_AMT4) KWN_AMT4   
					FROM  mistot a  left outer join swon(nolock) c on a.scode = c.scode and a.skey = c.skey
					where a.scode =   '".$_SESSION['S_SCODE']."'     and  YYMM >= '".$FYYMM."'   and   YYMM <= '".$TYYMM."'  $where
					group by   a.scode,isnull(c.BONBU,''),isnull(c.JISA,''),isnull(c.JIJUM,''),isnull(c.TEAM,'')   ) a 

					left outer join bonbu(nolock) e on a.scode = e.scode and a.bonbu = e.bcode
					left outer join jisa(nolock)  f on a.scode = f.scode and a.jisa = f.jscode
					left outer join jijum(nolock) g on a.scode = g.scode and a.jijum = g.jcode
					left outer join team(nolock) h  on a.scode = h.scode and a.team = h.tcode	 		

	) p
	where rnum between ".$limit1." AND ".$limit2."  "
	;

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
	select COUNT(*) CNT ,  SUM(ISNULL(IPMST1,0)) IPMST1,  SUM(ISNULL(IPMST2,0)) IPMST2,  SUM(ISNULL(IPMST3,0)) IPMST3,  SUM(ISNULL(IPMST4,0)) IPMST4 ,
												SUM(ISNULL(SU1,0)) SU1,  	SUM(ISNULL(SU2,0)) SU2,  	SUM(ISNULL(SU3,0)) SU3,  	SUM(ISNULL(SU4,0)) SU4,  sum(IPMST4) -  SUM(SU4) CATOT , SUM(SUNAB) SUNAB  ,
												 SUM(ISNULL(KWN_CNT1,0)) KWN_CNT1,  SUM(ISNULL(KWN_AMT1,0)) KWN_AMT1,  
												 SUM(ISNULL(KWN_CNT2,0)) KWN_CNT2,  SUM(ISNULL(KWN_AMT2,0)) KWN_AMT2,  
												 SUM(ISNULL(KWN_CNT3,0)) KWN_CNT3,  SUM(ISNULL(KWN_AMT3,0)) KWN_AMT3  
	from(

			select     a.*,  e.BNAME,f.JSNAME,g.JNAME,h.TNAME,  row_number()over(order by  a.BONBU,a.JISA,a.JIJUM,a.TEAM  ) rnum from   	
					(SELECT a.SCODE, isnull(c.BONBU,'') BONBU ,isnull(c.JISA,'') JISA ,isnull(c.JIJUM,'') JIJUM ,isnull(c.TEAM,'') TEAM  , 
								sum(IMST1) IPMST1,sum(IMST2) IPMST2,sum(IMST3) IPMST3,sum(IMST4) IPMST4 ,SUM(SU1) SU1,SUM(SU2) SU2,SUM(SU3) SU3,SUM(SU4) SU4,   sum(IMST4) -  SUM(SU4) CATOT , SUM(SUNAB) SUNAB  ,
								sum(KWN_CNT1) KWN_CNT1,sum(KWN_AMT1) KWN_AMT1,sum(KWN_CNT2) KWN_CNT2,sum(KWN_AMT2) KWN_AMT2 ,sum(KWN_CNT3) KWN_CNT3,sum(KWN_AMT3) KWN_AMT3 ,sum(KWN_CNT4) KWN_CNT4,sum(KWN_AMT4) KWN_AMT4   
					FROM  mistot a  left outer join swon(nolock) c on a.scode = c.scode and a.skey = c.skey
					where a.scode =   '".$_SESSION['S_SCODE']."'     and  YYMM >= '".$FYYMM."'   and   YYMM <= '".$TYYMM."'  $where
					group by   a.scode,isnull(c.BONBU,''),isnull(c.JISA,''),isnull(c.JIJUM,''),isnull(c.TEAM,'')   ) a 

					left outer join bonbu(nolock) e on a.scode = e.scode and a.bonbu = e.bcode
					left outer join jisa(nolock)  f on a.scode = f.scode and a.jisa = f.jscode
					left outer join jijum(nolock) g on a.scode = g.scode and a.jijum = g.jcode
					left outer join team(nolock) h  on a.scode = h.scode and a.team = h.tcode	 			

			

	) p
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
		'base_url' => $_SERVER['PHP_SELF']."?SDATE1=". $_REQUEST['SDATE1']."&SDATE2=". $_REQUEST['SDATE2']."&id=".$_REQUEST['id']."&page=Y",
		'per_page' => $page_row,
		'total_rows' => $totalResult['CNT'],
		'cur_page' => $page,
));

sqlsrv_free_stmt($result);
sqlsrv_close($mscon);

?>
<style>
.rowspan th {
    padding: 2px 0;
}
</style>

<div class="tb_type01 kwndatalist div_grid2 rowspan" style="overflow-y:auto;">	
	<table id="sort_table_swonlist" class="gridhover" style="min-width:1600px; "  >
		<colgroup>
 
 
 
			<col width="auto">

			<col width="110px">
			<col width="110px">
			<col width="110px">
			<col width="110px">

			<col width="110px">
  			<col width="110px">
			<col width="110px">
			<col width="110px">


			<col width="110px">
			<col width="110px">

			<col width="110px">
  			<col width="110px">
			<col width="100px">

		</colgroup>

		<thead>
			<tr class="rowTop">
				<th rowspan=2 align="center"  style=" border-right: 1px solid #c7c7c7;">�Ҽ�</th>	
 

				<th colspan=4 align="left" style="border-right: 1px solid #c7c7c7;">���Լ�����</th>
				<th colspan=4 align="left" style="border-right: 1px solid #c7c7c7;">���޼�����</th>
				<th rowspan=2 align="center" style="border-left:1px solid #c7c7c7;">������(A-B)</th>
				<th rowspan=2 align="center" style="border-left:1px solid #c7c7c7;">�����ݾ�</th>
				<th colspan=3 align="center" style="border-left: 1px solid #c7c7c7;">���</th>
			</tr>
			<tr> 
	 
				<th align="center">�Ϲ�</th>
				<th align="center">���</th>
				<th align="center">�ڵ���</th>
				<th align="center" style="border-right: 1px solid #c7c7c7;">�Ұ�(A)</th>

				<th align="center">�Ϲ�</th>
				<th align="center">���</th>
				<th align="center">�ڵ���</th>
				<th align="center" style="border-right: 1px solid #c7c7c7;">�Ұ�(B)</th>
 
				<th align="center" style="border-left: 1px solid #c7c7c7;">�Ϲ�</th>
				<th align="center">���</th>
				<th align="center" style="border-right: 1px solid #c7c7c7;">�ڵ���</th>
  
			</tr>
		</thead>		


		<tbody>
			<tr  class="summary sticky"style="top:45px;">
 
				<th style="text-align: center;color: crimson; border-right: 1px solid #c7c7c7;" ><?='��   ��'?></th>
	
 

				<th style="padding-right: 10px;text-align: right;" ><?=number_format($totalResult['IPMST1'])?></th>
				<th style="padding-right: 10px;text-align: right;"><?=number_format($totalResult['IPMST2'])?></th>
				<th style="padding-right: 10px;text-align: right;"><?=number_format($totalResult['IPMST3'])?></th>
				<th style="padding-right: 10px;text-align: right;color: crimson; border-right: 1px solid #c7c7c7;" ><?=number_format($totalResult['IPMST4'])?></th>
	 


				<th style="padding-right: 10px;text-align: right;"  ><?=number_format($totalResult['SU1'])?></th>
				<th style="padding-right: 10px;text-align: right;" ><?=number_format($totalResult['SU2'])?></th>
				<th style="padding-right: 10px;text-align: right;" ><?=number_format($totalResult['SU3'])?></th>
				<th style="padding-right: 10px;text-align: right; color: crimson; border-right: 1px solid #c7c7c7;"><?=number_format($totalResult['SU4'])?></th>

				<th style="padding-right: 10px;text-align: right; border-right: 1px solid #c7c7c7;"><?=number_format($totalResult['CATOT'])?></th>
				<th style="padding-right: 10px;text-align: right; border-right: 1px solid #c7c7c7;"><?=number_format($totalResult['SUNAB'])?></th>

 
				<th style="padding-right: 10px;text-align: right;"  ><?=number_format($totalResult['KWN_AMT1']).'/'.number_format($totalResult['KWN_CNT1']).'��'   ?></th>
				<th style="padding-right: 10px;text-align: right;"  ><?=number_format($totalResult['KWN_AMT2']).'/'.number_format($totalResult['KWN_CNT2']).'��'    ?></th>
				<th style="padding-right: 10px;text-align: right;"  ><?=number_format($totalResult['KWN_AMT3']).'/'.number_format($totalResult['KWN_CNT3']).'��'    ?></th>
			
			</tr>

 
			<?if(!empty($listData)){?>

			<?foreach($listData as $key => $val){extract($val);?>
			<tr class="rowData" rol-data='<?=$swonskey?>', rol-yymm='<?=$yymm?>'>
	
				<?$sosok = substr($BNAME,0,4).'>'. $JSNAME.'>'.$JNAME.'>'.$TNAME   ?>
				<?$sosok = str_replace('>>','>',$sosok)?> 
				<?$sosok = str_replace('>>','>',$sosok)?> 
				<? if(empty($BNAME) ) {$sosok = '���Ī ������';} ?> 
	
				<td align="left"  style=" border-right: 1px solid #c7c7c7;" ><?=$sosok?></td>
 

				<td align="right" ><?=number_format($IPMST1)?></td>
				<td align="right" ><?=number_format($IPMST2)?></td>
				<td align="right" ><?=number_format($IPMST3)?></td>
				<td align="right"  style="color: crimson; border-right: 1px solid #c7c7c7;" ><?=number_format($IPMST4)?></td>
	 


				<td align="right" ><?=number_format($SU1)?></td>
				<td align="right" ><?=number_format($SU2)?></td>
				<td align="right" ><?=number_format($SU3)?></td>
				<td align="right"   style=" color: crimson; border-right: 1px solid #c7c7c7;"><?=number_format($SU4)?></td>

				<td align="right"   style=" border-right: 1px solid #c7c7c7;"><?=number_format($CATOT)?></td>
				<td align="right"   style=" border-right: 1px solid #c7c7c7;"><?=number_format($SUNAB)?></td>

 
				<td align="right" ><?=number_format($KWN_AMT1).'/'.number_format($KWN_CNT1).'��'    ?></td>
				<td align="right" ><?=number_format($KWN_AMT2).'/'.number_format($KWN_CNT2).'��'    ?></td>
				<td align="right" ><?=number_format($KWN_AMT3).'/'.number_format($KWN_CNT3).'��'    ?></td>


				<td></td>
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


// ��� Ŭ��
$(".rowTop > th").click(function(){
	var trData = $(this).parent();

	var idx = $(trData).find("th").index($(this));
	// include/bottom.php ����
	sortTable("sort_table_swonlist", idx, 3);
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

});

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>