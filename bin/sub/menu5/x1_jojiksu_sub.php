<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");



$fyymm =  $_GET['fyymm']; 
 
$sql= "
		select b.NAME
		from sudet a left outer join inssetup b on a.scode = b.scode and a.inscode = b.inscode
					left outer join swon c on a.scode = c.scode and a.skey = c.skey
					left outer join bonbu e on c.scode = e.scode and c.bonbu = e.bcode
					left outer join jisa  f on c.scode = f.scode and c.jisa = f.jscode
					left outer join jijum g on c.scode = g.scode and c.jijum = g.jcode
					left outer join team h  on c.scode = h.scode and c.team = h.tcode	
		where a.SCODE =  '".$_SESSION['S_SCODE']."'  and  a.YYMM = '".$fyymm."'  
		group by a.inscode,b.name,b.num
		order by b.num,b.name
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


		select aa.*,bb.kamt10,bb.kamt11,bb.gamt12,bb.gamt13,bb.totkamt,bb.totgamt,bb.gamt1,bb.gamt2,bb.silamt
		from(
			select scode,yymm,bcode, bname,jscode,jsname,jcode,jname, ".$select_f." row_number()over(order by    bname, jsname,jname) rnum
			from(
				select scode,yymm,inscode,bcode,bname,jscode,jsname,jcode,jname,
						".$instit."
				from(
					select a.scode,a.yymm,a.inscode,b.name, e.bcode,e.bname , f.jscode,f.jsname,g.jcode,g.jname ,a.suamt 
					from sudet a left outer join inssetup b on a.scode = b.scode and a.inscode = b.inscode
								left outer join sumst c on a.scode = c.scode and a.yymm=c.yymm and a.skey=c.skey
								left outer join bonbu e on a.scode = e.scode and a.bonbu = e.bcode
								left outer join jisa  f on a.scode = f.scode and a.jisa = f.jscode
								left outer join jijum  g on a.scode = g.scode and c.jijum = g.jcode
					where a.SCODE =  '".$_SESSION['S_SCODE']."'   and  a.YYMM = '".$fyymm."'  and jscode = '".$_GET['jscode']."'
					) aa
				PIVOT(sum(suamt) for name in ( ".$instit." )) AS PVT
				) aa
			group by scode,yymm,bcode,bname,jscode,jsname ,jcode,jname
			) aa left outer join (select scode,yymm,bonbu,jisa,isnull(jijum,'') jijum,sum(kamt10) kamt10,sum(kamt11) kamt11, sum(gamt12) gamt12, sum(gamt13) gamt13,sum(gamt1) gamt1,sum(gamt2) gamt2,
								sum(kamt1)+sum(kamt2)+sum(kamt3)+sum(kamt10)+sum(kamt11) totkamt , sum(gamt1)+sum(gamt2) totgamt,
								(sum(kamt1)+sum(kamt2)+sum(kamt3)+sum(kamt10)+sum(kamt11)) - (sum(gamt1)+sum(gamt2)+sum(gamt13)) silamt
								from sumst
								where SCODE =  '".$_SESSION['S_SCODE']."'   and  YYMM = '".$fyymm."'  and jisa = '".$_GET['jscode']."'
								group by scode,yymm,bonbu,jisa,isnull(jijum,'')) bb on aa.scode = bb.scode and aa.yymm = bb.yymm and isnull(aa.jscode,'') = isnull(bb.jisa,'') and isnull(aa.jcode,'') = isnull(bb.jijum,'')

	) p
	";
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
		select ".$select_f." scode
		from(
						select scode,	".$instit."
						from(
							select a.scode,a.yymm,a.inscode,b.name, e.bcode,e.bname , f.jscode,f.jsname,g.jcode,g.jname ,a.suamt 
							from sudet a left outer join inssetup b on a.scode = b.scode and a.inscode = b.inscode
										left outer join sumst c on a.scode = c.scode and a.yymm=c.yymm and a.skey=c.skey
										left outer join bonbu e on a.scode = e.scode and a.bonbu = e.bcode
										left outer join jisa  f on a.scode = f.scode and a.jisa = f.jscode
										left outer join jijum  g on a.scode = g.scode and c.jijum = g.jcode
							where a.SCODE =  '".$_SESSION['S_SCODE']."'   and  a.YYMM = '".$fyymm."'  and jscode = '".$_GET['jscode']."'
							) aa
						PIVOT(sum(suamt) for name in ( ".$instit." )) AS PVT
				) aa
		group by scode
		";
$qry	= sqlsrv_query( $mscon, $sql );
$listinsTot = array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listinsTot[]	= $fet;
}

$sql ="
		select sum(kamt10) kamt10 , sum(kamt11) kamt11 , sum(kamt1)+sum(kamt2)+sum(kamt3) totkamt , 
				sum(gamt1) gamt1, sum(gamt2) gamt2 , sum(gamt12) gamt12 , sum(gamt13) gamt13 , sum(gamt1)+sum(gamt2)+sum(gamt13) totgamt,
				(sum(kamt1)+sum(kamt2)+sum(kamt3)+sum(kamt10)+sum(kamt12))-(sum(gamt1)+sum(gamt2)+sum(gamt13)) silamt
		from sumst a
		where a.SCODE =  '".$_SESSION['S_SCODE']."'   and  a.YYMM = '".$fyymm."'  and jisa = '".$_GET['jscode']."'
		";
$qry	= sqlsrv_query( $mscon, $sql );
$listData_tot = array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listData_tot[]	= $fet;
}

sqlsrv_free_stmt($result);
sqlsrv_close($mscon);

?>

<!-- html���� -->
<!-- html���� -->
<style>
body{background-image: none;}
</style>


<div class="tb_type01 " style="overflow-y:auto;">
	<table id="sort_table" class="gridhover" style="min-width: 1500px;">
		<colgroup>
			<col width="70px">
			<col width="70px">
			<col width="90px">
			<?for($i=1;$i<= $instit_cnt;$i++ ){?> 
				<col width="70px">
			<?}?>
			<col width="70px">
			<col width="70px">
			<col width="90px">
			<col width="70px">
			<col width="70px">
			<col width="70px">
			<col width="70px">
			<col width="70px">
			<col width="100px">
		</colgroup>
		<thead>
		<tr class="rowTop">
			<th align="center">�����</th>
			<th align="center">�����ڵ�</th>
			<th align="center">������</th>
			<?for($i=1;$i<= $instit_cnt;$i++ ){?> 
				<th align="left"><?=$titList[$i] ?></th>
			<?}?>
			<th align="center">��Ÿ������</th>
			<th align="center">���ܰ�ǥ</th>
			<th align="center">�������</th>
			<th align="center">��������</th>
			<th align="center">�ҵ漼</th>
			<th align="center">�ֹμ�</th>
			<th align="center">���İ���</th>
			<th align="center">�����հ�</th>
			<th align="center">�����޾�</th>
		</tr>
		</thead>
		<tbody>

			<tr class="summary sticky"style="top:34px">
				<?if(isset($listinsTot[0][$titList[1]])){?>
				<th></th>
				<th></th>
				<th class="sumtext"><?= ' �� �� ' ?></th>								
				<? for($i = 0; $i <  $instit_cnt ; $i++) { ?> 
					<th class="sum01"><?=number_format($listinsTot[0][$titList[$i+1]])  ?></th>				
				<?}?>
				<th class="sum01"><?=number_format($listData_tot[0]['kamt10']) ?></th>
				<th class="sum01"><?=number_format($listData_tot[0]['kamt11']) ?></th>
				<th class="sum01"><span style="font-weight:bold"><?=number_format($listData_tot[0]['totkamt']) ?></span></th>
				<th class="sum01"><?=number_format($listData_tot[0]['gamt12']) ?></th>
				<th class="sum01"><?=number_format($listData_tot[0]['gamt1']) ?></th>
				<th class="sum01"><?=number_format($listData_tot[0]['gamt2']) ?></th>
				<th class="sum01"><?=number_format($listData_tot[0]['gamt13']) ?></th>
				<th class="sum01"><span style="font-weight:bold"><?=number_format($listData_tot[0]['totgamt']) ?></span></th>
				<th class="sum01"><span style="font-weight:bold"><?=number_format($listData_tot[0]['silamt']) ?></span></th>
				<?}?>
			</tr>

			<?if(!empty($listData)){?>
			<?foreach($listData as $key => $val){extract($val);?>
			<tr class="rowData" style="cursor:pointer;">
				<td align="center"><?=date("Y-m",strtotime($yymm."01"))?></td>
				<td align="left"><?=$jcode?></td>
				<td align="left"><?=$jname?></td>
				<?for($i = 1; $i <=  $instit_cnt ; $i++) {?>
					<td align="right"><?=number_format($listData[$key][$titList[$i]])?></td>   <!--ũ�ν������� ȸ����÷���  -->
				<?}?>
				<td align="right"><?=number_format($kamt10)?></td>
				<td align="right"><?=number_format($kamt11)?></td>
				<td align="right"><span style="font-weight:bold"><?=number_format($totkamt)?></span></td>
				<td align="right"><?=number_format($gamt12)?></td>
				<td align="right"><?=number_format($gamt1)?></td>
				<td align="right"><?=number_format($gamt2)?></td>
				<td align="right"><?=number_format($gamt13)?></td>
				<td align="right"><span style="font-weight:bold"><?=number_format($totgamt)?></span></td>
				<td align="right"><span style="font-weight:bold"><?=number_format($silamt)?></span></td>
			</tr>
			<?}}else{?>
				<tr>
					<td style="color:#8C8C8C" colspan=21>�˻��� �����Ͱ� �����ϴ�</td>
				</tr>
			<?}?>
		</tbody>
	</table>

</div><!-- // tb_type01 -->

<!-- // container -->
<!-- // wrap -->
<script type="text/javascript">


$(document).ready(function(){

	$("#div_load_image").hide();
	

	// �� ���ý� �ش���� Ŭ���Ѱܹޱ�(function d_ser ���� ���� �ѱ�)
	var btn		= '<?=$_GET['btn']?>';
	if(btn){
		$(".box_wrap.sel_btn a").removeClass('on');
		$("#"+btn).addClass('on');
	}



});
</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>