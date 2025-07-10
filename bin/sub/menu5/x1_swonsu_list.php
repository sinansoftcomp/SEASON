<?
//error_reporting(E_ALL); ini_set('display_errors', 1);
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

$FYYMM   = substr($_GET['SDATE1'],0,4).substr($_GET['SDATE1'],5,2);
$TYYMM  =  substr($_GET['SDATE2'],0,4).substr($_GET['SDATE2'],5,2);

$where = "";

// 조직도 트리 선택시 소속정보(swon 별칭 : s2 - kdman(사용인기준)) 
if($_GET['id']){
	
	$Ngubun = substr($_GET['id'],0,2);

	if($Ngubun == 'N1'){
		$bonbu = substr($_GET['id'],2,10);
		$where  .= " and e.bcode = '".$bonbu."' " ;
	}else if($Ngubun == 'N2'){
		$jisa = substr($_GET['id'],2,10);
		$where  .= " and f.jscode = '".$jisa."' " ;
	}else if($Ngubun == 'N3'){
		$jijum = substr($_GET['id'],2,10);
		$where  .= " and g.jcode = '".$jijum."' " ;
	}else if($Ngubun == 'N4'){
		$team = substr($_GET['id'],2,10);
		$where  .= " and h.tcode = '".$team."' " ;
	}else if($Ngubun == 'N5'){
		$ksman = substr($_GET['id'],2,10);
		$where  .= " and c.skey = '".$ksman."' " ;
	}
}
/* ------------------------------------------------------
	년도 / 검색일자 / 월 조회값 생성 End
------------------------------------------------------ */


// 기본 페이지 셋팅
$page = ($_GET['page']) ? $_GET['page'] : 1;
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
		group by a.inscode,b.name,b.num
		order by b.num
		 " ;

$qry= sqlsrv_query( $mscon, $sql );
$titList[]	=array();
$instit="";
$instit_cnt = 0; 
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$titList[]	= $fet['NAME'];
	$instit =   $instit.'['.$fet['NAME'].']';
	$instit_cnt =  $instit_cnt + 1;    //보험사 타이틀 갯수,  나중에 타이틀은 루핑 돌라고 
}

$instit = str_replace("][","],[",$instit); //--->타이틀을 sql 	PVT(크로스탭에서 사용한다) 	--타이틀 보험사가 동적이다. 


$select_sum = "";
for($i=1; $i<=$instit_cnt; $i++){
	$select_sum .= "SUM(CASE WHEN insilj = '1' AND insname = '".$titList[$i]."' THEN suamt ELSE 0 END) AS '".$titList[$i]."_일반', ";
	$select_sum .= "SUM(CASE WHEN insilj = '2' AND insname = '".$titList[$i]."' THEN suamt ELSE 0 END) AS '".$titList[$i]."_장기', ";
	$select_sum .= "SUM(CASE WHEN insilj = '3' AND insname = '".$titList[$i]."' THEN suamt ELSE 0 END) AS '".$titList[$i]."_자동차', ";
}



$sql = "

	WITH PivotData AS (
		SELECT 
			a.scode, a.yymm, a.skey, c.sname, a.inscode, b.name AS insname, 
			e.bname, f.jsname, g.jname, h.tname, a.insilj, a.suamt, 
			e.num AS bnum, f.num AS jsnum, g.num AS jnum, h.num AS tnum, c.jik,
			CASE 
				WHEN tbit = '1' THEN '1' 
				WHEN tbit = '2' THEN '3' 
				WHEN tbit = '3' THEN '2' 
				WHEN tbit = '4' THEN '4' 
			END AS tbit
		FROM sudet a
		LEFT JOIN inssetup b ON a.scode = b.scode AND a.inscode = b.inscode
		LEFT JOIN swon c ON a.scode = c.scode AND a.skey = c.skey
		LEFT JOIN bonbu e ON c.scode = e.scode AND c.bonbu = e.bcode
		LEFT JOIN jisa f ON c.scode = f.scode AND c.jisa = f.jscode
		LEFT JOIN jijum g ON c.scode = g.scode AND c.jijum = g.jcode
		LEFT JOIN team h ON c.scode = h.scode AND c.team = h.tcode
		WHERE a.SCODE = '".$_SESSION['S_SCODE']."' AND a.YYMM >= '".$FYYMM."' AND a.YYMM <= '".$TYYMM."' $where
	),
	Pivoted AS (
		SELECT 
			scode, yymm, skey, sname, bname, jsname, jname, tname, 
			".$select_sum."
			ROW_NUMBER() OVER (ORDER BY yymm DESC, bnum, jsnum, jnum, tnum, jik DESC, tbit, skey) AS rnum
		FROM PivotData
		GROUP BY scode, yymm, skey, sname, bname, jsname, jname, tname, bnum, jsnum, jnum, tnum, jik, tbit
	),
	TotalAmount AS (
		SELECT 
			a.scode, a.yymm, a.skey, SUM(a.suamt) AS totsuamt
		FROM sudet a
				LEFT JOIN inssetup b ON a.scode = b.scode AND a.inscode = b.inscode
				LEFT JOIN swon c ON a.scode = c.scode AND a.skey = c.skey
				LEFT JOIN bonbu e ON c.scode = e.scode AND c.bonbu = e.bcode
				LEFT JOIN jisa f ON c.scode = f.scode AND c.jisa = f.jscode
				LEFT JOIN jijum g ON c.scode = g.scode AND c.jijum = g.jcode
				LEFT JOIN team h ON c.scode = h.scode AND c.team = h.tcode
		WHERE a.SCODE = '".$_SESSION['S_SCODE']."' AND a.YYMM >= '".$FYYMM."' AND a.YYMM <= '".$TYYMM."' $where
		GROUP BY a.scode, a.yymm, a.skey
	)
	SELECT p.*
	FROM (
		SELECT aa.*, bb.totsuamt
		FROM Pivoted aa
		LEFT JOIN TotalAmount bb ON aa.scode = bb.scode AND aa.yymm = bb.yymm AND aa.skey = bb.skey
	) p
	WHERE rnum BETWEEN ".$limit1." AND ".$limit2."
	ORDER BY rnum;
		
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

//--->수수입수수료 보험사를 타이틀로 구성하기위한 해당월의 보험사명 순서별로 리턴이 필요함 합계필드 일치하기위함  ORDER BY D.NUM 
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
		ORDER BY b.NUM
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

// 데이터 총 건수
//검색 데이터 구하기 
$sql= "
		select count(*) CNT
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


// 페이지 클래스 시작
// 로드
include_once($conf['rootDir'].'/include/class/Pagination.php');

// 설정
$pagination = new Pagination(array(
		'base_url' => $_SERVER['PHP_SELF']."?SDATE1=". $_GET['SDATE1']."&SDATE2=". $_GET['SDATE2']."&id=".$_GET['id'],
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
			<?if(!$instit_cnt){?>
				<col width="80px">
				<col width="80px">
				<col width="80px">				
			<?}?>
			<?for($i=1;$i<= $instit_cnt;$i++ ){?> 
				<col width="80px">
				<col width="80px">
				<col width="80px">
  			<?}?>
			<col width="auto">

		</colgroup>

		<thead>
			<tr class="rowTop">
				<th rowspan='2' align="center">정산월</th>
				<th rowspan='2' align="center">사원</th>
				<th rowspan='2' align="center">사원명</th>
				<th rowspan='2' align="center" style=" border-right: 1px solid #c7c7c7;">소속</th>				

				<th rowspan='2' align="center" style=" border-right: 1px solid #c7c7c7;">수수료합계</th>
				<?if(!$instit_cnt){?>
					<th></th>
					<th align="left">보험사</th>
					<th style=" border-right: 1px solid #c7c7c7;"></th>
				<?}?>
				<?for($i=1;$i<= $instit_cnt;$i++ ){?> 
					<th></th>
					<th align="left"><?=$titList[$i] ?></th>
					<th style=" border-right: 1px solid #c7c7c7;"></th>
				<?}?>
				<th align="center"></th>	
			</tr>
			<tr> 
				<?if(!$instit_cnt){?>
					<th align="center">일반</th>
					<th align="center">장기</th>
					<th align="center" style="border-right: 1px solid #c7c7c7;">자동차</th>
				<?}?>
				<?for($i=1;$i<= $instit_cnt;$i++ ){?> 
					<th align="center">일반</th>
					<th align="center">장기</th>
					<th align="center" style="border-right: 1px solid #c7c7c7;">자동차</th>
				<?}?>
				<th align="center"></th>	
			</tr>
		</thead>		

		<tbody>

			<tr class="summary sticky"style="top:39px">
				<th></th>
				<th></th>
				<th></th>
				<th class="sumtext" style="border-right: 1px solid #c7c7c7;"><?= ' 합 계 ' ?></th>							

				<th class="sum01" style=" border-right: 1px solid #c7c7c7;" align="right"  ><?=number_format($listinsTot_tot )?></th>		
				<?if(!$instit_cnt){?>
					<th></th>
					<th class="sum01"></th>				
					<th style=" border-right: 1px solid #c7c7c7;"></th>
				<?}?>				
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
				<td align="center"><?=date("Y-m",strtotime($yymm.'01'))?></td>
				<td align="left"><?=$skey?></td>
				<td align="left"><?=$sname?></td>
				<?$sosok = substr($bname,0,4).'>'. substr($jsname,0,4).'>'. substr($jname,0,4).'>'. substr($tname,0,4)   ?>
				<?$sosok = str_replace('>>','>',$sosok)?> 
				<?$sosok = str_replace('>>','>',$sosok)?> 
				<td align="left" style=" border-right: 1px solid #c7c7c7;"><?=$sosok?></td>

				<td align="right"><?=number_format($totsuamt)?></td>

				<?for($i = 1; $i <=  $instit_cnt ; $i++) {?>
						<td align="right" style=" border-left: 1px solid #c7c7c7;"><?=number_format($listData[$key][$titList[$i].'_일반'])?></td>   <!--크로스탭으로 회면디스플레이  -->
						<td align="right"><?=number_format($listData[$key][$titList[$i].'_장기'])?></td>   <!--크로스탭으로 회면디스플레이  -->
						<td align="right" style=" border-right: 1px solid #c7c7c7;"><?=number_format($listData[$key][$titList[$i].'_자동차'])?></td>   <!--크로스탭으로 회면디스플레이  -->
	 			<?}?>

				<td></td>
			</tr>
			<?}}else{?>
				<tr>
					<td style="color:#8C8C8C" colspan=24>검색된 데이터가 없습니다</td>
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


// 헤더 클릭
$(".rowTop > th").click(function(){
	var trData = $(this).parent();

	var idx = $(trData).find("th").index($(this));
	// include/bottom.php 참조
	sortTable("sort_table_swonlist", idx, 3);
})
 

$(document).ready(function(){

	$('#cnt').val('<?=$totalResult["CNT"]?>');


	var page="";
	if("<?=$_GET['page']?>"){
		page = "<?=$_GET['page']?>";
	}else{
		page = "1";
	}
	$("#page").val(page);
	// page 함수 ajax페이지 존재시 별도 처리
	$(".kwnlist a").click(function(){
		$('#page').val('Y');
		var res = $(this).attr("href").split("?");
		if(res[0] && res[1]){
			//alert(res[0]+"//"+res[1]);
			 //data_right_jojik div id값 적용
			ajaxLodingTarget(res[0],res[1],event,$('#kwnlist'));    
		}
		return false;
	});
 
	// 리스트 클릭시 상세내용 조회
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