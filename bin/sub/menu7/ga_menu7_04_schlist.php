<?

include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");


if($_GET['default']){
	$where	= " and   sdate like '".date("Ym",strtotime($_GET['default']))."%'";

	$pageTitle	= date("m월",strtotime($_GET['default'])).' 일정관리';
}else{
	$where	= " and   sdate = '".$_GET['sdate']."' ";

	$pageTitle	= date("m월 d일",strtotime($_GET['sdate'])).' 일정관리';
}

$sql= "
	select *
	from(
		select  a.seq,
				a.sdate,
				a.title,
				a.bigo,
				a.gubun,
				case when a.gubun = '1' then '전체' else '개인' end gubun_nm,
				a.status,
				case when a.status = '1' then '진행중' else '완료' end status_nm,
				case when a.gubun = '1' and status = '1' then '#F15F5F' 
					 when a.gubun = '1' and status = '2' then '#d5d5d5' 
					 when a.gubun = '2' and status = '1' then '#6799FF' 
					 when a.gubun = '2' and status = '2' then '#d5d5d5' 
					 else '#6799FF' end color
		from schd(nolock) a
		where a.scode = '".$_SESSION['S_SCODE']."'
		  and a.gubun = '1' ".$where."

		union all

		select  a.seq,
				a.sdate,
				a.title,
				a.bigo,
				a.gubun,
				case when a.gubun = '1' then '전체' else '개인' end gubun_nm,
				a.status,
				case when a.status = '1' then '진행중' else '완료' end status_nm,
				case when a.gubun = '1' and status = '1' then '#F15F5F' 
					 when a.gubun = '1' and status = '2' then '#d5d5d5' 
					 when a.gubun = '2' and status = '1' then '#6799FF' 
					 when a.gubun = '2' and status = '2' then '#d5d5d5' 
					 else '#6799FF' end color
		from schd(nolock) a
		where a.scode = '".$_SESSION['S_SCODE']."'
		  and a.gubun = '2' ".$where."
		  and a.skey = '".$_SESSION['S_SKEY']."'
		  ) tbl
	order by sdate, gubun
	";

$qry	= sqlsrv_query( $mscon, $sql );
$listData	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listData[]	= $fet;
}

?>
<style>

.cal_red{color:#F15F5F;font-weight:600;}
.cal_blue{color:#6799FF;font-weight:600;}

</style>

<div id="schd_sort" class="tb_type01 div_grid" style="overflow-y:auto;">
	<table class="gridhover">
		<colgroup>
			<col width="70px">
			<col width="70px">
			<col width="150px">
			<col width="auto">
			<col width="70px">
		</colgroup>
		<thead>
		<tr class="rowTop">
			<th align="center">일자</th>
			<th align="center">구분</th>
			<th align="center">제목</th>
			<th align="center">내용</th>
			<th align="center">진행</th>
		</tr>
		</thead>
		<tbody>
			<?if(!empty($listData)){?>
			<?foreach($listData as $key => $val){extract($val);
				if($gubun == '1'){
					$color = 'cal_red';
				}else{
					$color = 'cal_blue';
				}
			?>
			<tr class="rowData" onclick="fn_addsch(<?=$seq?>);">
				<td><?if(trim($sdate)) echo date("Y-m-d",strtotime($sdate))?></td>
				<td align="center" class="<?=$color?>"><?=$gubun_nm?></td>
				<td align="left" style="overflow: hidden; white-space: nowrap; -ms-text-overflow: ellipsis; -o-text-overflow: ellipsis;text-overflow: ellipsis;"><?=$title?></td>
				<td align="left" style="overflow: hidden; white-space: nowrap; -ms-text-overflow: ellipsis; -o-text-overflow: ellipsis;text-overflow: ellipsis;"><?=$bigo?></td>
				<td align="center"><?=$status_nm?></td>
			</tr>
			<?}}else{?>
				<tr>
					<td style="color:#8C8C8C" colspan=5>검색된 데이터가 없습니다</td>
				</tr>
			<?}?>
		</tbody>
	</table>
</div><!-- // tb_type01 -->



<script type="text/javascript">

$(document).ready(function(){

	$('.htitle').text('<?=$pageTitle?>');
});

</script>