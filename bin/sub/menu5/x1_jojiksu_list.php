<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

$FYYMM   = substr($_REQUEST['SDATE1'],0,4).substr($_REQUEST['SDATE1'],5,2);
$TYYMM  =  substr($_REQUEST['SDATE2'],0,4).substr($_REQUEST['SDATE2'],5,2);

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
		$where  .= " and c.skey = '".$ksman."' " ;
	}
}
/* ------------------------------------------------------
	�⵵ / �˻����� / �� ��ȸ�� ���� End
------------------------------------------------------ */


// �⺻ ������ ����
$page = ($_REQUEST['page']) ? $_REQUEST['page'] : 1;
$page_row	= 300;
$limit1 = ($page-1)*$page_row+1;
$limit2 = $page*$page_row;

 

$sql= "
		select b.NAME
		from sudet a left outer join inssetup b on a.scode = b.scode and a.inscode = b.inscode
					left outer join swon c on a.scode = c.scode and a.skey = c.skey
					left outer join bonbu e on c.scode = e.scode and c.bonbu = e.bcode
					left outer join jisa  f on c.scode = f.scode and c.jisa = f.jscode
					left outer join jijum g on c.scode = g.scode and c.jijum = g.jcode
					left outer join team h  on c.scode = h.scode and c.team = h.tcode	
		where a.SCODE =  '".$_SESSION['S_SCODE']."'  and  a.YYMM >= '".$FYYMM."'  and   a.YYMM <= '".$TYYMM."' $where
		group by a.inscode,b.name
		order by b.name
		 " ;

$qry= sqlsrv_query( $mscon, $sql );
$titList[]	=array();
$instit="";
$instit_cnt = 0; 
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$titList[]	= $fet['NAME'];
	$instit =   $instit.'['.$fet['NAME'].']';
	$instit_cnt =  $instit_cnt + 1;    //����� Ÿ��Ʋ ����,  ���߿� Ÿ��Ʋ�� ���� ����� 
}

$instit = str_replace("][","],[",$instit); //--->Ÿ��Ʋ�� sql 	PVT(ũ�ν��ǿ��� ����Ѵ�) 	--Ÿ��Ʋ ����簡 �����̴�. 

$select_f = "";
for($i=1; $i<=$instit_cnt; $i++){
	$select_f .= " sum([".$titList[$i]."]) '".$titList[$i]."' , ";
}

/*
echo '<pre>';
echo $sql;
echo '</pre>';
*/

$sql= "
	select *
	from(


		select aa.*,bb.totsuamt
		from(
			select scode,yymm,skey,sname , bname,jsname,jname,tname , ".$select_f." row_number()over(order by    bname, jsname, jname, tname,   skey) rnum
			from(
				select scode,yymm,skey,sname,inscode,bname,jsname,jname,tname,
						".$instit."
				from(
					select a.scode,a.yymm,a.inscode,b.name, e.bcode,e.bname , f.jscode,f.jsname ,a.suamt 
					from sudet a left outer join inssetup b on a.scode = b.scode and a.inscode = b.inscode
								left outer join sumst c on a.scode = c.scode and a.yymm=c.yymm and a.skey=c.skey
								left outer join bonbu e on a.scode = e.scode and a.bonbu = e.bcode
								left outer join jisa  f on a.scode = f.scode and a.jisa = f.jscode
					where a.SCODE =  '".$_SESSION['S_SCODE']."'   and  a.YYMM >= '".$FYYMM."'  and   a.YYMM <= '".$TYYMM."' $where
					) aa
				PIVOT(sum(suamt) for name in ( ".$instit." )) AS PVT
				) aa
			group by scode,yymm,skey,sname,bname,jsname,jname,tname
			) aa left outer join (select scode,yymm,bonbu,jisa,sum(kamt10) kamt10,sum(kamt11) kamt11
								from sumst
								where SCODE =  '".$_SESSION['S_SCODE']."'   and  YYMM >= '".$FYYMM."'  and   YYMM <= '".$TYYMM."'
								group by scode,yymm,bonbu,jisa) bb on aa.scode = bb.scode and aa.yymm = bb.yymm and aa.bcode = bb.bonbu and aa.jscode = bb.jisa
			

	) p
	where rnum between ".$limit1." AND ".$limit2." order by skey"
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

//--->�����Լ����� ����縦 Ÿ��Ʋ�� �����ϱ����� �ش���� ������ �������� ������ �ʿ��� �հ��ʵ� ��ġ�ϱ�����  ORDER BY D.NUM 
$sql ="
		SELECT  b.NAME,
				sum(suamt) catotal
		from sudet a left outer join inssetup b on a.scode = b.scode and a.inscode = b.inscode
					left outer join swon c on a.scode = c.scode and a.skey = c.skey
					left outer join bonbu e on c.scode = e.scode and c.bonbu = e.bcode
					left outer join jisa  f on c.scode = f.scode and c.jisa = f.jscode
					left outer join jijum g on c.scode = g.scode and c.jijum = g.jcode
					left outer join team h  on c.scode = h.scode and c.team = h.tcode	
		where a.SCODE =  '".$_SESSION['S_SCODE']."'   and  a.YYMM >= '".$FYYMM."'  and   a.YYMM <= '".$TYYMM."' $where
		group by   b.NAME, b.NUM 
		ORDER BY b.NAME
		";
$qry	= sqlsrv_query( $mscon, $sql );
$listinsTot = array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listinsTot[]	= $fet;
}

$listinsTot_tot = 0; 
for($i = 0; $i <  $instit_cnt ; $i++) {
	$listinsTot_tot = $listinsTot_tot +$listinsTot[$i]['catotal']  ; 
} 


/*
echo '<pre>';
//PRINT_R($listinsTot[$i]['catotal']."</br>") ;
//echo($titList[2]);
echo $sql; 
echo '</pre>';
 */

// ������ �� �Ǽ�
//�˻� ������ ���ϱ� 
$sql= "
		select count(*)
		from(
			select scode,yymm,skey
			from sudet a
			where a.SCODE =  '".$_SESSION['S_SCODE']."'   and  a.YYMM >= '".$FYYMM."'  and   a.YYMM <= '".$TYYMM."'
			group by scode,yymm,skey
			) aa
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
    padding: 0px 0;
}
</style>

<div class="tb_type01 kwndatalist div_grid rowspan" style="overflow-y:auto;">	
	<table id="sort_table_swonlist" class="gridhover" style="min-width: 3200px; "  >
		<colgroup>
			<col width="70px">
			<col width="80px">
			<col width="80px">
			<col width="110px">

			<col width="100px">

			<?for($i=1;$i<= $instit_cnt;$i++ ){?> 
				<col width="80px">
				<col width="80px">
				<col width="80px">
  			<?}?>
			<col width="auto">

		</colgroup>

		<thead>
			<tr class="rowTop">
				<th rowspan='2' align="center">�����</th>
				<th rowspan='2' align="center">���</th>
				<th rowspan='2' align="center">�����</th>
				<th rowspan='2' align="center" style=" border-right: 1px solid #c7c7c7;">�Ҽ�</th>				

				<th rowspan='2' align="center" style=" border-right: 1px solid #c7c7c7;">�������հ�</th>
	 
				<?for($i=1;$i<= $instit_cnt;$i++ ){?> 
					<th></th>
					<th align="left"><?=$titList[$i] ?></th>
					<th style=" border-right: 1px solid #c7c7c7;"></th>
				<?}?>
				<th align="center"></th>	
			</tr>
			<tr> 
				<?for($i=1;$i<= $instit_cnt;$i++ ){?> 
					<th align="center">�Ϲ�</th>
					<th align="center">���</th>
					<th align="center" style="border-right: 1px solid #c7c7c7;">�ڵ���</th>
				<?}?>
				<th align="center"></th>	
			</tr>
		</thead>		

		<tbody>

			<tr class="summary sticky"style="top:37px">
				<th></th>
				<th></th>
				<th></th>
				<th class="sumtext" style="border-right: 1px solid #c7c7c7;"><?= ' �� �� ' ?></th>							

				<th class="sum01" style=" border-right: 1px solid #c7c7c7;" align="right"  ><?=number_format($listinsTot_tot )?></th>							
				<? for($i = 0; $i <  $instit_cnt ; $i++) { ?> 
					<th></th>
					<th class="sum01"><?=number_format($listinsTot[$i]['catotal'])  ?></th>				
					<th style=" border-right: 1px solid #c7c7c7;"></th>
				<?}?>  	 
				<th></th>
			</tr>

			<?if(!empty($listData)){?>

			<?foreach($listData as $key => $val){extract($val);?>
			<tr class="rowData" rol-data='<?=$skey?>', rol-yymm='<?=$yymm?>'>
				<td align="center"><?=date("Y-m",strtotime($yymm))?></td>
				<td align="left"><?=$skey?></td>
				<td align="left"><?=$sname?></td>
				<?$sosok = substr($bname,0,4).'>'. substr($jsname,0,4).'>'. substr($jname,0,4).'>'. substr($tname,0,4)   ?>
				<?$sosok = str_replace('>>','>',$sosok)?> 
				<?$sosok = str_replace('>>','>',$sosok)?> 
				<td align="left" style=" border-right: 1px solid #c7c7c7;"><?=$sosok?></td>

				<td align="right"><?=number_format($totsuamt)?></td>

				<?for($i = 1; $i <=  $instit_cnt ; $i++) {?>
						<td align="right" style=" border-left: 1px solid #c7c7c7;"><?=number_format($listData[$key][$titList[$i].'_�Ϲ�'])?></td>   <!--ũ�ν������� ȸ����÷���  -->
						<td align="right"><?=number_format($listData[$key][$titList[$i].'_���'])?></td>   <!--ũ�ν������� ȸ����÷���  -->
						<td align="right" style=" border-right: 1px solid #c7c7c7;"><?=number_format($listData[$key][$titList[$i].'_�ڵ���'])?></td>   <!--ũ�ν������� ȸ����÷���  -->
	 			<?}?>

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
	<ul class="pagination pagination-sm kwnlist" style="margin: 5px">
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
 
	// ����Ʈ Ŭ���� �󼼳��� ��ȸ
	$(".rowData > td").click(function(){
		var trData = $(this).parent();
		var idx    = $(".rowData").index($(trData));

		var swon  = $(".rowData").eq(idx).attr('rol-data');
		var yymm  = $(".rowData").eq(idx).attr('rol-yymm');

		var left = Math.ceil((window.screen.width - 1200)/2);
		var top = Math.ceil((window.screen.height - 1000)/2);
		var popOpen	= window.open("<?=$conf['homeDir']?>/sub/menu5/x1_swonsu_list_detail.php?swon="+swon +"&yymm=" +yymm, "width=1200px,height=800px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
		popOpen.focus();
	})
 
});

</script>


<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>