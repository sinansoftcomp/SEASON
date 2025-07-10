<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

// 사원기본정보
$sql= "
		select a.sname,f.subnm,
				case when isnull(a.bonbu,'') != '' then substring(b.bname,1,2) else '' end +
				case when isnull(a.bonbu,'') != '' and (isnull(a.jisa,'') != '' or isnull(a.jijum,'') != '' or isnull(a.team,'') != '')  then ' > ' else '' end +
				case when isnull(a.jisa,'') != '' then c.jsname else '' end +
				case when isnull(a.jisa,'') != '' and isnull(a.jijum,'') != '' then ' > ' else '' end +
				case when isnull(a.jijum,'') != '' then g.jname else '' end +
				case when isnull(a.jijum,'') != '' and isnull(a.team,'') != '' then ' > ' else '' end +
				case when isnull(a.team,'') != '' then e.tname else '' end as sosok
		from swon a left outer join common f on a.scode = f.scode and f.code = 'COM006' and a.pos = f.codesub
					left outer join bonbu(nolock) b on a.scode = b.scode and a.bonbu = b.bcode
					left outer join jisa(nolock) c on a.scode = c.scode and a.jisa = c.jscode
					left outer join jijum(nolock) g on a.scode = g.scode and a.jijum = g.jcode
					left outer join team(nolock) e on a.scode = e.scode and a.team = e.tcode
		where a.scode = '".$_SESSION['S_SCODE']."' and a.skey = '".$_GET['SKEY']."'
	";
$qry	= sqlsrv_query( $mscon, $sql );
$listData_0 = array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listData_0[]	= $fet;
}

// 당월업적
$sql= "
	select sum(cnt_1) cnt_1 , sum(mamt_1) mamt_1 , sum(hwanamt_1) hwanamt_1,
			sum(cnt_2) cnt_2 , sum(mamt_2) mamt_2 , sum(hwanamt_2) hwanamt_2,
			sum(cnt_3) cnt_3 , sum(mamt_3) mamt_3 , sum(hwanamt_3) hwanamt_3
	from(
		select  case when insilj = '1' then cnt else 0 end cnt_1 ,
				case when insilj = '1' then isnull(mamt,0) else 0 end mamt_1 ,
				case when insilj = '1' then isnull(hwanamt,0) else 0 end hwanamt_1 ,
				case when insilj = '2' then cnt else 0 end cnt_2 ,
				case when insilj = '2' then isnull(mamt,0) else 0 end mamt_2 ,
				case when insilj = '2' then isnull(hwanamt,0) else 0 end hwanamt_2 ,
				case when insilj = '3' then cnt else 0 end cnt_3 ,
				case when insilj = '3' then isnull(mamt,0) else 0 end mamt_3 ,
				case when insilj = '3' then isnull(hwanamt,0) else 0 end hwanamt_3 
		from(
			select a.insilj, count(*) cnt , sum(a.mamt) mamt , sum(a.hwanamt) hwanamt
			from sudet a  left outer join inssetup b on a.scode = b.scode and a.inscode = b.inscode			
			where a.scode = '".$_SESSION['S_SCODE']."' and a.yymm = '".$_GET['yymm']."' and a.skey = '".$_GET['SKEY']."' and a.sbit = '001' and suamt <> 0
			group by a.insilj
			) aa
		) aa
	";
$qry	= sqlsrv_query( $mscon, $sql );
$listData_1 = array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listData_1[]	= $fet;
}

// 모집수수료(신계약 수수료)
$sql= "
	select inscode,name, sum(cnt_2) cnt_2 , sum(suamt_2) suamt_2 , sum(cnt_1) cnt_1 , sum(suamt_1) suamt_1 , sum(cnt_3) cnt_3  , sum(suamt_3) suamt_3 ,
			sum(suamt_1)+sum(suamt_2)+sum(suamt_3) suamt_tot
	from(
		select inscode,name,
				case when [1] is not null then [1] else 0 end suamt_1,
				case when [1] is not null then cnt else 0 end cnt_1,
				case when [2] is not null then [2] else 0 end suamt_2,
				case when [2] is not null then cnt else 0 end cnt_2,
				case when [3] is not null then [3] else 0 end suamt_3,
				case when [3] is not null then cnt else 0 end cnt_3
		from(
			select a.inscode , b.name , a.insilj ,count(*) cnt , sum(a.suamt) suamt
			from sudet a  left outer join inssetup b on a.scode = b.scode and a.inscode = b.inscode
			where a.scode = '".$_SESSION['S_SCODE']."' and a.yymm = '".$_GET['yymm']."' and a.skey = '".$_GET['SKEY']."' and a.sbit = '001' and a.suamt <> 0
			group by a.inscode ,b.name, a.insilj
			) aa
		PIVOT(sum(aa.suamt) for insilj in ( [1],[2],[3] )) AS PVT
		) aa
	group by inscode,name
	";
$qry	= sqlsrv_query( $mscon, $sql );
$listData_2 = array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listData_2[]	= $fet;
}

// 모집수수료(신계약 수수료 합계)
$sql= "
	select sum(cnt_2) cnt_2 , sum(suamt_2) suamt_2 , sum(cnt_1) cnt_1 , sum(suamt_1) suamt_1 , sum(cnt_3) cnt_3  , sum(suamt_3) suamt_3 ,
			sum(suamt_1)+sum(suamt_2)+sum(suamt_3) suamt_tot
	from(
		select inscode,
				case when [1] is not null then [1] else 0 end suamt_1,
				case when [1] is not null then cnt else 0 end cnt_1,
				case when [2] is not null then [2] else 0 end suamt_2,
				case when [2] is not null then cnt else 0 end cnt_2,
				case when [3] is not null then [3] else 0 end suamt_3,
				case when [3] is not null then cnt else 0 end cnt_3
		from(
			select a.inscode , a.insilj ,count(*) cnt , sum(a.suamt) suamt
			from sudet a  
			where a.scode = '".$_SESSION['S_SCODE']."' and a.yymm = '".$_GET['yymm']."' and a.skey = '".$_GET['SKEY']."' and a.sbit = '001' and a.suamt <> 0
			group by a.inscode , a.insilj
			) aa
		PIVOT(sum(aa.suamt) for insilj in ( [1],[2],[3] )) AS PVT
		) aa
	";
$qry	= sqlsrv_query( $mscon, $sql );
$listData_2_tot = array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listData_2_tot[]	= $fet;
}

// 모집수수료(모집 수수료)
$sql= "
	select inscode,name, sum(cnt_2) cnt_2 , sum(suamt_2) suamt_2 , sum(cnt_1) cnt_1 , sum(suamt_1) suamt_1 , sum(cnt_3) cnt_3  , sum(suamt_3) suamt_3 ,
			sum(suamt_1)+sum(suamt_2)+sum(suamt_3) suamt_tot
	from(
		select inscode,name,
				case when [1] is not null then [1] else 0 end suamt_1,
				case when [1] is not null then cnt else 0 end cnt_1,
				case when [2] is not null then [2] else 0 end suamt_2,
				case when [2] is not null then cnt else 0 end cnt_2,
				case when [3] is not null then [3] else 0 end suamt_3,
				case when [3] is not null then cnt else 0 end cnt_3
		from(
			select a.inscode , b.name , a.insilj ,count(*) cnt , sum(a.suamt) suamt
			from sudet a  left outer join inssetup b on a.scode = b.scode and a.inscode = b.inscode
			where a.scode = '".$_SESSION['S_SCODE']."' and a.yymm = '".$_GET['yymm']."' and a.skey = '".$_GET['SKEY']."' and a.sbit = '002' and a.suamt <> 0
			group by a.inscode ,b.name, a.insilj
			) aa
		PIVOT(sum(aa.suamt) for insilj in ( [1],[2],[3] )) AS PVT
		) aa
	group by inscode,name
	";
$qry	= sqlsrv_query( $mscon, $sql );
$listData_3 = array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listData_3[]	= $fet;
}

// 모집수수료(모집 수수료 합계)
$sql= "
	select sum(cnt_2) cnt_2 , sum(suamt_2) suamt_2 , sum(cnt_1) cnt_1 , sum(suamt_1) suamt_1 , sum(cnt_3) cnt_3  , sum(suamt_3) suamt_3 ,
			sum(suamt_1)+sum(suamt_2)+sum(suamt_3) suamt_tot
	from(
		select inscode,
				case when [1] is not null then [1] else 0 end suamt_1,
				case when [1] is not null then cnt else 0 end cnt_1,
				case when [2] is not null then [2] else 0 end suamt_2,
				case when [2] is not null then cnt else 0 end cnt_2,
				case when [3] is not null then [3] else 0 end suamt_3,
				case when [3] is not null then cnt else 0 end cnt_3
		from(
			select a.inscode , a.insilj ,count(*) cnt , sum(a.suamt) suamt
			from sudet a  
			where a.scode = '".$_SESSION['S_SCODE']."' and a.yymm = '".$_GET['yymm']."' and a.skey = '".$_GET['SKEY']."' and a.sbit = '002' and a.suamt <> 0
			group by a.inscode , a.insilj
			) aa
		PIVOT(sum(aa.suamt) for insilj in ( [1],[2],[3] )) AS PVT
		) aa
	";
$qry	= sqlsrv_query( $mscon, $sql );
$listData_3_tot = array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listData_3_tot[]	= $fet;
}

// 오버라이딩
$sql= "
		select count(*) cnt , sum(suamt) suamt
		from sudet a  
		where a.scode = '".$_SESSION['S_SCODE']."' and a.yymm = '".$_GET['yymm']."' and a.skey = '".$_GET['SKEY']."' and a.sbit = '003' and a.suamt <> 0
	";
$qry	= sqlsrv_query( $mscon, $sql );
$listData_4 = array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listData_4[]	= $fet;
}

// 수수료 총계
$sql= "
		select sum(suamt) suamt
		from sudet a  
		where a.scode = '".$_SESSION['S_SCODE']."' and a.yymm = '".$_GET['yymm']."' and a.skey = '".$_GET['SKEY']."' and a.suamt <> 0
		" ;
$qry =  sqlsrv_query($mscon, $sql);
$totalsuamt =  sqlsrv_fetch_array($qry); 
$total = $totalsuamt['suamt'];

// 최종지급액산출
$sql= "
		select kamt1+kamt2+kamt3 kamt , gamt1+gamt2 gamt , kamt1+kamt2+kamt3-(gamt1+gamt2) jiamt
		from sumst
		where scode = '".$_SESSION['S_SCODE']."' and yymm = '".$_GET['yymm']."' and skey = '".$_GET['SKEY']."'
	";
$qry	= sqlsrv_query( $mscon, $sql );
$listData_5 = array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listData_5[]	= $fet;
}

sqlsrv_free_stmt($result);
sqlsrv_close($mscon);
?>
<style>
body{background-image: none;}

.gridhover th{padding:3px 0;}
body { !important; -webkit-print-color-adjust:exact;}
@media print{
     @page{  size:auto; margin : 0mm;  }
 }

@media print{
	.tb_type01 td{font-size:6pt;}
 }

</style>



<div class="tit_wrap ipgopop" style="padding-top:10px">


	<div class="tit_wrap" style="margin-top:0px" align="center">
		<h1>수수료 명세서</h1>
		
		<span class="btn_wrap" style="padding-right:29px">
			<a class="btn_s white no-print" onclick="jQuery('#print').print();" style="min-width:100px;">인쇄</a>
		</span>

	</div>
	
	<div style="padding:0px 30px;">

		<div class="tb_type01 view" >
			<table>
				<colgroup>											
					<col width="33%">
					<col width="33%">
					<col width="auto">
				</colgroup>
				<thead>
					<tr>
						<th align="center" style="font-size:14px">사원명 : <?=$listData_0[0]['sname']?> (<?=$listData_0[0]['subnm']?>)</th>
						<th align="center" style="font-size:14px">소속 : <?=$listData_0[0]['sosok']?></th>
						<th align="center" style="font-size:14px">업적월 : <?=date("Y-m",strtotime($_GET['yymm']."01"))?></th>
					</tr>
				</thead>			
			</table>
		</div>

		<div class="tit_wrap" style="margin-top:10px;">
			<h3 class="tit_sub">당월 업적</h3>
		</div>
		<div class="tb_type01 view">
			<table>
				<colgroup>											
					<col width="10%">
					<col width="10%">
					<col width="10%">
					<col width="10%">
					<col width="10%">
					<col width="10%">
					<col width="10%">
					<col width="10%">
					<col width="10%">
					<col width="auto">
				</colgroup>
				<thead>
					<tr>
						<th rowspan=2 align="center" style="border-right: 1px solid #c7c7c7;">구분</th>
						<th colspan=3 align="center" style="border-right: 1px solid #c7c7c7;">장기</th>
						<th colspan=3 align="center" style="border-right: 1px solid #c7c7c7;">일반</th>
						<th colspan=3 align="center">자동차</th>
					</tr>
					<tr>
						<th align="center">건수</th>
						<th align="center">보험료</th>
						<th align="center" style="border-right: 1px solid #c7c7c7;">환산보험료</th>
						<th align="center">건수</th>
						<th align="center">보험료</th>
						<th align="center" style="border-right: 1px solid #c7c7c7;">환산보험료</th>
						<th align="center">건수</th>
						<th align="center">보험료</th>
						<th align="center">환산보험료</th>
					</tr>
				</thead>			
				<tbody>
					<?if(!empty($listData_1)){?>

					<?foreach($listData_1 as $key => $val){extract($val);?>
					<tr class="rowData">
						<td align="left">신계약</td>
						<td align="right"><?=number_format($listData_1[$key]['cnt_2'])?></td>
						<td align="right"><?=number_format($listData_1[$key]['mamt_2'])?></td>
						<td align="right"><?=number_format($listData_1[$key]['hwanamt_2'])?></td>
						<td align="right"><?=number_format($listData_1[$key]['cnt_1'])?></td>
						<td align="right"><?=number_format($listData_1[$key]['mamt_1'])?></td>
						<td align="right"><?=number_format($listData_1[$key]['hwanamt_1'])?></td>
						<td align="right"><?=number_format($listData_1[$key]['cnt_3'])?></td>
						<td align="right"><?=number_format($listData_1[$key]['mamt_3'])?></td>
						<td align="right"><?=number_format($listData_1[$key]['hwanamt_3'])?></td>
					</tr>
					<?}?>
					<?}else{?>
					<tr>
						<td style="color:#8C8C8C" colspan=8>검색된 데이터가 없습니다</td>
					</tr>
					<?}?>
				</tbody>
			</table>
		</div>
		

		
		<div class="tit_wrap" style="margin-top:10px;">
			<h3 class="tit_sub">모집 수수료</h3>
			<a class="btn_s white no-print excelBtn" style="min-width:100px;">엑셀</a>
		</div>
		<div style="display:flex;">
			<div class="data_left tb_type01 rowspan" style = "width : 50%;">
				<table class="gridhover">
					<colgroup>											
						<col width="17%">
						<col width="10%">
						<col width="12%">
						<col width="10%">
						<col width="12%">
						<col width="10%">
						<col width="12%">
						<col width="auto">
					</colgroup>	
					<thead>
						<tr>
							<th colspan=8 align="center" style="border-right: 2px solid #47474a;">신계약 수수료</th>
						</tr>
						<tr>
							<th rowspan=2 align="center" style="border-right: 1px solid #c7c7c7;">보험사</th>
							<th colspan=2 align="center" style="border-right: 1px solid #c7c7c7;">장기</th>
							<th colspan=2 align="center" style="border-right: 1px solid #c7c7c7;">일반</th>
							<th colspan=2 align="center" style="border-right: 1px solid #c7c7c7;">자동차</th>
							<th rowspan=2 align="center" style="border-right: 2px solid #47474a;">합계</th>
						</tr>
						<tr>
							<th align="center">건수</th>
							<th align="center" style="border-right: 1px solid #c7c7c7;">수수료</th>
							<th align="center">건수</th>
							<th align="center" style="border-right: 1px solid #c7c7c7;">수수료</th>
							<th align="center">건수</th>
							<th align="center" style="border-right: 1px solid #c7c7c7;">수수료</th>
						</tr>
					</thead>
					<tbody>
						<?if(!empty($listData_2)){?>
						<tr class="summary">
							<th class="sumtext"><?= ' 합 계 ' ?></th>	
							<th class="sum01" align="right"><?=number_format($listData_2_tot[0]['cnt_2'])?></td>
							<th class="sum01" align="right"><?=number_format($listData_2_tot[0]['suamt_2'])?></td>
							<th class="sum01" align="right"><?=number_format($listData_2_tot[0]['cnt_1'])?></td>
							<th class="sum01" align="right"><?=number_format($listData_2_tot[0]['suamt_1'])?></td>
							<th class="sum01" align="right"><?=number_format($listData_2_tot[0]['cnt_3'])?></td>
							<th class="sum01" align="right"><?=number_format($listData_2_tot[0]['suamt_3'])?></td>
							<th class="sum01" align="right"><?=number_format($listData_2_tot[0]['suamt_tot'])?></td>							
						</tr>

						<?foreach($listData_2 as $key => $val){extract($val);?>
						<tr class="rowData" onclick="suspecPopOpen_sbit('<?=$_GET['SKEY']?>','<?=$_GET['yymm']?>','001','<?=$listData_2[$key]['inscode']?>')">
							<td align="left" class="div_layer"><?=$listData_2[$key]['name']?></td>
							<td align="right"><?=number_format($listData_2[$key]['cnt_2'])?></td>
							<td align="right"><?=number_format($listData_2[$key]['suamt_2'])?></td>
							<td align="right"><?=number_format($listData_2[$key]['cnt_1'])?></td>
							<td align="right"><?=number_format($listData_2[$key]['suamt_1'])?></td>
							<td align="right"><?=number_format($listData_2[$key]['cnt_3'])?></td>
							<td align="right"><?=number_format($listData_2[$key]['suamt_3'])?></td>
							<td align="right"><?=number_format($listData_2[$key]['suamt_tot'])?></td>
						</tr>
						<?}?>
						<?}else{?>
						<tr>
							<td style="color:#8C8C8C" colspan=8>검색된 데이터가 없습니다</td>
						</tr>
						<?}?>
					</tbody>
				</table>
			</div>
			

			<div class="data_right tb_type01 rowspan" style = "width : 49%;padding-left:0px" >
				<table class="gridhover">
					<colgroup>											
						<col width="17%">
						<col width="10%">
						<col width="12%">
						<col width="10%">
						<col width="12%">
						<col width="10%">
						<col width="12%">
						<col width="auto">
					</colgroup>	
					<thead>
						<tr>
							<th colspan=8 align="center">유지 수수료</th>
						</tr>
						<tr>
							<th rowspan=2 align="center" style="border-right: 1px solid #c7c7c7;">보험사</th>
							<th colspan=2 align="center" style="border-right: 1px solid #c7c7c7;">장기</th>
							<th colspan=2 align="center" style="border-right: 1px solid #c7c7c7;">일반</th>
							<th colspan=2 align="center" style="border-right: 1px solid #c7c7c7;">자동차</th>
							<th rowspan=2 align="center">합계</th>
						</tr>
						<tr>
							<th align="center">건수</th>
							<th align="center" style="border-right: 1px solid #c7c7c7;">수수료</th>
							<th align="center">건수</th>
							<th align="center" style="border-right: 1px solid #c7c7c7;">수수료</th>
							<th align="center">건수</th>
							<th align="center" style="border-right: 1px solid #c7c7c7;">수수료</th>
						</tr>
					</thead>
					<tbody>
						<?if(!empty($listData_3)){?>
						<tr class="summary">
							<th class="sumtext"><?= ' 합 계 ' ?></th>	
							<th class="sum01" align="right"><?=number_format($listData_3_tot[0]['cnt_2'])?></td>
							<th class="sum01" align="right"><?=number_format($listData_3_tot[0]['suamt_2'])?></td>
							<th class="sum01" align="right"><?=number_format($listData_3_tot[0]['cnt_1'])?></td>
							<th class="sum01" align="right"><?=number_format($listData_3_tot[0]['suamt_1'])?></td>
							<th class="sum01" align="right"><?=number_format($listData_3_tot[0]['cnt_3'])?></td>
							<th class="sum01" align="right"><?=number_format($listData_3_tot[0]['suamt_3'])?></td>
							<th class="sum01" align="right"><?=number_format($listData_3_tot[0]['suamt_tot'])?></td>							
						</tr>

						<?foreach($listData_3 as $key => $val){extract($val);?>
						<tr class="rowData" onclick="suspecPopOpen_sbit('<?=$_GET['SKEY']?>','<?=$_GET['yymm']?>','002','<?=$listData_3[$key]['inscode']?>')">
							<td align="left"><?=$listData_3[$key]['name']?></td>
							<td align="right"><?=number_format($listData_3[$key]['cnt_2'])?></td>
							<td align="right"><?=number_format($listData_3[$key]['suamt_2'])?></td>
							<td align="right"><?=number_format($listData_3[$key]['cnt_1'])?></td>
							<td align="right"><?=number_format($listData_3[$key]['suamt_1'])?></td>
							<td align="right"><?=number_format($listData_3[$key]['cnt_3'])?></td>
							<td align="right"><?=number_format($listData_3[$key]['suamt_3'])?></td>
							<td align="right"><?=number_format($listData_3[$key]['suamt_tot'])?></td>
						</tr>
						<?}?>
						<?}else{?>
						<tr>
							<td style="color:#8C8C8C" colspan=8>검색된 데이터가 없습니다</td>
						</tr>
						<?}?>

					</tbody>
				</table>			
			</div>
		</div>
	
		<div class="data_left" style = "width : 50%; margin-top:10px;">
			<h3 class="tit_sub" style="margin-bottom:5px">오버라이딩</h3>
			<div class="tb_type01 view">
				<table class="gridhover">
					<colgroup>											
						<col width="33%">
						<col width="33%">
						<col width="auto">
					</colgroup>
					<thead>
						<tr>
							<th align="center">구분</th>
							<th align="center">건수</th>
							<th align="center">수수료</th>
						</tr>
					</thead>
					<tbody>
						<?if($listData_4[0]['cnt']>0 and $listData_4[0]['suamt']>0){?>
						<tr class="rowData" onclick="suspecPopOpen_jang('<?=$_GET['SKEY']?>','<?=$_GET['yymm']?>','003')">
							<td align="left">본부장수수료</td>
							<td align="right"><?=number_format($listData_4[0]['cnt'])?></td>
							<td align="right"><?=number_format($listData_4[0]['suamt'])?></td>
						</tr>
						<?}else{?>
						<tr>
							<td style="text-align:center;color:#8C8C8C" colspan=3>검색된 데이터가 없습니다</td>
						</tr>
						<?}?>
					</tbody>
				</table>
			</div>
		</div>
		<div class="data_right" style = "width : 49%;padding-left:0px; margin-top:10px">
			<h3 class="tit_sub" style="margin-bottom:5px">기타 수수료</h3>
			<div class="tb_type01 view">
				<table>
					<colgroup>											
						<col width="33%">
						<col width="33%">
						<col width="auto">
					</colgroup>
					<thead>
						<tr>
							<th align="center">구분</th>
							<th align="center">건수</th>
							<th align="center">수수료</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td style="text-align:center;color:#8C8C8C" colspan=3>검색된 데이터가 없습니다</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>


		<div class="tit_wrap" style="margin-top:110px" align="right">
			<h3>수수료 총계 : <?=number_format($total)?></h3>
		</div>		
		
		<div class="tit_wrap" style="margin-top:10px;">
			<h3 class="tit_sub">최종지급액산출</h3>
		</div>
		<div class="tb_type01 view">
			<table>
				<colgroup>											
					<col width="12%">
					<col width="12%">
					<col width="12%">
					<col width="12%">
					<col width="12%">
					<col width="12%">
					<col width="12%">
					<col width="auto">
				</colgroup>	
				<thead>
					<tr>
						<th colspan=2 align="center">예외과표</th>
						<th colspan=2 align="center" style="border-right: 1px solid #c7c7c7;">과표합계</th>
						<th colspan=2 align="center">원천세</th>
						<th colspan=2 align="center">원천세기공제</th>

					</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan=2 align="right">0</td>
						<td colspan=2 align="right"><?=number_format($listData_5[0]['kamt'])?></td>
						<td colspan=2 align="right"><?=number_format($listData_5[0]['gamt'])?></td>
						<td colspan=2 align="right">0</td>
					</tr>
				</tbody>
				<thead>
					<tr>
						<th align="center">세후지급액</th>
						<th align="center">기지급공제</th>
						<th align="center">최종세후지급액</th>
						<th align="center" style="border-right: 1px solid #c7c7c7;">세후지급공제</th>
						<th align="center" style="border-right: 1px solid #c7c7c7;">미환수 잔액</th>
						<th colspan=3 align="center"><h3 style="color:#E0844F">실지급액</h3></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td align="right"><?=number_format($listData_5[0]['jiamt'])?></td>
						<td align="right">0</td>
						<td align="right">0</td>
						<td align="right">0</td>
						<td align="right">0</td>
						<td colspan=3 align="center"><h3 style="color:#E0844F"><?=number_format($listData_5[0]['jiamt'])?></h3></td>
					</tr>
				</tbody>
			</table>

		</div>

	</div>

</div>

<form name="searchFrm" id="searchFrm" method="get" action="<?$_SERVER['PHP_SELF']?>">
	<input type="hidden" name="yymm" id="yymm" value="<?=$_GET['yymm']?>">
	<input type="hidden" name="skey" id="skey" value="<?=$_GET['SKEY']?>">
</form>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript" src="<?=$conf['homeDir']?>/common/js/jQuery.print.js"></script>

<script type="text/javascript">

function suspecPopOpen_sbit(skey,yymm,sbit,inscode){

	var left = Math.ceil((window.screen.width - 900)/2);
	var top = Math.ceil((window.screen.height - 680)/2);
	var popOpen	= window.open("<?=$conf['homeDir']?>/sub/menu5/x1_suspec_list_detail_sbit.php?SKEY="+skey+"&yymm="+yymm+"&sbit="+sbit+"&inscode="+inscode,"suspec_sbit","width=900px,height=600px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
}
function suspecPopOpen_jang(skey,yymm,sbit){

	var left = Math.ceil((window.screen.width - 900)/2);
	var top = Math.ceil((window.screen.height - 680)/2);
	var popOpen	= window.open("<?=$conf['homeDir']?>/sub/menu5/x1_suspec_list_detail_jang.php?SKEY="+skey+"&yymm="+yymm+"&sbit="+sbit,"suspec_sbit","width=900px,height=600px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
}

$(document).ready(function(){
	$(".excelBtn").click(function(){
		if(confirm("엑셀로 내려받으시겠습니까?")){
			$("form[name='searchFrm']").attr("action","x1_suspec_list_detail_excel.php");
			$("form[name='searchFrm']").submit();
			$("form[name='searchFrm']").attr("action","<?$_SERVER['PHP_SELF']?>");
		}
	});
});

</script>


<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>