<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

$FYYMM   = substr($_REQUEST['SDATE1'],0,4).substr($_REQUEST['SDATE1'],5,2).substr($_REQUEST['SDATE1'],8,2);
$TYYMM  =  substr($_REQUEST['SDATE2'],0,4).substr($_REQUEST['SDATE2'],5,2).substr($_REQUEST['SDATE2'],8,2);


$where = "";

// 조직도 트리 선택시 소속정보(swon 별칭 : s2 - kdman(사용인기준)) 
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
	년도 / 검색일자 / 월 조회값 생성 End
------------------------------------------------------ */




// 기본 페이지 셋팅
$page = ($_REQUEST['page']) ? $_REQUEST['page'] : 1;
$page_row	=100;
$limit1 = ($page-1)*$page_row+1;
$limit2 = $page*$page_row;

 

//--->수수입수수료 보험사를 타이틀로 구성하기위한 해당월의 보험사명 순서별로 리턴이 필요함 합계필드 일치하기위함  ORDER BY D.NUM (타이틀도 쓸놈만 가져온다  다가져오면 data언매칭됨)
$sql= "SELECT  d.NAME ,D.NUM 
			FROM INS_SUNAB(nolock) a
											left outer join INSWON(nolock) b on  a.scode = b.scode and  a.INSCODE = b.INSCODE and a.KSMAN = b.BSCODE  
											left outer join swon(nolock)  c on  b.scode = c.scode and  b.SKEY = c.SKEY
											left outer join INSSETUP(nolock)  d on  a.scode = d.scode and  a.INSCODE = d.INSCODE

											left outer join bonbu(nolock) e on c.scode = e.scode and c.bonbu = e.bcode
											left outer join jisa(nolock)  f on c.scode = f.scode and c.jisa = f.jscode
											left outer join jijum(nolock) g on c.scode = g.scode and c.jijum = g.jcode
											left outer join team(nolock) h  on c.scode = h.scode and c.team = h.tcode

											where a.SCODE =  '".$_SESSION['S_SCODE']."'  and  a.IPDATE >= '".$FYYMM."'  and   a.IPDATE <= '".$TYYMM."' $where
		 group by  d.NAME ,D.NUM
		 ORDER BY D.NUM 
		 " ;

/* 
echo '<pre>';
echo $sql;
echo '</pre>';
  */

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
if(!$instit){
	$instit = "[삼성생명]";
}

 /*
echo '<pre>';
PRINT_R($instit);
echo '</pre>';
  */ 

$sql = "

		WITH SunabData AS (
			SELECT 
				ISNULL(c.SKEY, 'XXXXX') AS SKEY,
				c.SNAME,
				d.NAME AS INSNAME,
				e.BNAME,
				f.JSNAME,
				g.JNAME,
				h.TNAME,
				c.HTEL1,
				c.HTEL2,
				c.HTEL3,
				a.IPDATE,
				SUM(ISNULL(a.SAMT, 0)) AS catotal
			FROM INS_SUNAB a
			LEFT JOIN INSWON b ON a.scode = b.scode AND a.INSCODE = b.INSCODE AND a.KSMAN = b.BSCODE  
			LEFT JOIN swon c ON b.scode = c.scode AND b.SKEY = c.SKEY
			LEFT JOIN INSSETUP d ON a.scode = d.scode AND a.INSCODE = d.INSCODE
			LEFT JOIN bonbu e ON c.scode = e.scode AND c.bonbu = e.bcode
			LEFT JOIN jisa f ON c.scode = f.scode AND c.jisa = f.jscode
			LEFT JOIN jijum g ON c.scode = g.scode AND c.jijum = g.jcode
			LEFT JOIN team h ON c.scode = h.scode AND c.team = h.tcode
			WHERE a.SCODE = '".$_SESSION['S_SCODE']."' AND a.IPDATE >= '".$FYYMM."' AND a.IPDATE <= '".$TYYMM."' and b.sgubun='1' $where
			GROUP BY c.SKEY, c.SNAME, d.NAME, e.BNAME, f.JSNAME, g.JNAME, h.TNAME, c.HTEL1, c.HTEL2, c.HTEL3, a.IPDATE
		),
		PivotedData AS (
			SELECT 
				SKEY, SNAME, BNAME, JSNAME, JNAME, TNAME, HTEL1, HTEL2, HTEL3, IPDATE,
				".$instit."
			FROM (
				SELECT SKEY, SNAME, INSNAME, BNAME, JSNAME, JNAME, TNAME, HTEL1, HTEL2, HTEL3, IPDATE, catotal
				FROM SunabData
			) src
			PIVOT (
				SUM(catotal)
				FOR INSNAME IN (".$instit.")
			) pvt
		),
		TotalAmount AS (
			SELECT 
				ISNULL(c.SKEY, 'XXXXX') AS SKEY,
				a.IPDATE,
				SUM(ISNULL(a.SAMT, 0)) AS skeytot
			FROM INS_SUNAB a
			LEFT JOIN INSWON b ON a.scode = b.scode AND a.INSCODE = b.INSCODE AND a.KSMAN = b.BSCODE  
			LEFT JOIN swon c ON b.scode = c.scode AND b.SKEY = c.SKEY
			LEFT JOIN INSSETUP d ON a.scode = d.scode AND a.INSCODE = d.INSCODE
			LEFT JOIN bonbu e ON c.scode = e.scode AND c.bonbu = e.bcode
			LEFT JOIN jisa f ON c.scode = f.scode AND c.jisa = f.jscode
			LEFT JOIN jijum g ON c.scode = g.scode AND c.jijum = g.jcode
			LEFT JOIN team h ON c.scode = h.scode AND c.team = h.tcode
			WHERE a.SCODE = '".$_SESSION['S_SCODE']."' AND a.IPDATE >= '".$FYYMM."' AND a.IPDATE <= '".$TYYMM."' and b.sgubun='1' $where
			GROUP BY c.SKEY, a.IPDATE
		)
		SELECT *
		FROM (
			SELECT 
				aa.SKEY AS swonskey,
				bb.skeytot,
				aa.*, 
				ROW_NUMBER() OVER (ORDER BY aa.BNAME, aa.JSNAME, aa.JNAME, aa.TNAME, aa.SKEY) AS rnum
			FROM PivotedData aa
			LEFT JOIN TotalAmount bb ON aa.SKEY = bb.SKEY AND aa.IPDATE = bb.IPDATE
		) p
		where rnum between ".$limit1." AND ".$limit2."
		ORDER BY rnum
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
					SELECT  d.NAME,
							sum(isnull(a.SAMT,0)) catotal
						FROM INS_SUNAB(nolock) a
						left outer join INSWON(nolock) b on  a.scode = b.scode and  a.INSCODE = b.INSCODE and a.KSMAN = b.BSCODE  
						left outer join swon(nolock)  c on  b.scode = c.scode and  b.SKEY = c.SKEY
						left outer join INSSETUP(nolock)  d on  a.scode = d.scode and  a.INSCODE = d.INSCODE

						left outer join bonbu(nolock) e on c.scode = e.scode and c.bonbu = e.bcode
						left outer join jisa(nolock)  f on c.scode = f.scode and c.jisa = f.jscode
						left outer join jijum(nolock) g on c.scode = g.scode and c.jijum = g.jcode
						left outer join team(nolock) h  on c.scode = h.scode and c.team = h.tcode

						where a.SCODE =  '".$_SESSION['S_SCODE']."'   and  a.IPDATE >= '".$FYYMM."'  and   a.IPDATE <= '".$TYYMM."' and b.sgubun='1' $where
					group by   d.NAME, D.NUM 
					ORDER BY D.NUM 
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

// 데이터 총 건수
//검색 데이터 구하기 
$sql= "
	select COUNT(*) CNT
	from(
				select aa.SKEY swonskey ,bb.skeytot, aa.*, row_number()over(order by aa.BNAME,aa.JSNAME,aa.JNAME,aa.TNAME, aa.SKEY		) rnum  from 
								(SELECT * FROM (
										SELECT isnull(c.SKEY,'XXXXX') SKEY ,c.SNAME, d.NAME,e.BNAME,f.JSNAME,g.JNAME,h.TNAME, c.HTEL1, c.HTEL2, c.HTEL3,A.IPDATE,
												sum(isnull(a.SAMT,0)) catotal
											FROM INS_SUNAB(nolock) a
											left outer join INSWON(nolock) b on  a.scode = b.scode and  a.INSCODE = b.INSCODE and a.KSMAN = b.BSCODE  
											left outer join swon(nolock)  c on  b.scode = c.scode and  b.SKEY = c.SKEY
											left outer join INSSETUP(nolock)  d on  a.scode = d.scode and  a.INSCODE = d.INSCODE

											left outer join bonbu(nolock) e on c.scode = e.scode and c.bonbu = e.bcode
											left outer join jisa(nolock)  f on c.scode = f.scode and c.jisa = f.jscode
											left outer join jijum(nolock) g on c.scode = g.scode and c.jijum = g.jcode
											left outer join team(nolock) h  on c.scode = h.scode and c.team = h.tcode

											where a.SCODE =  '".$_SESSION['S_SCODE']."'  and  a.IPDATE >= '".$FYYMM."'  and   a.IPDATE <= '".$TYYMM."' and b.sgubun='1' $where
										group by  c.SKEY,c.SNAME, d.NAME,e.BNAME,f.JSNAME,g.JNAME,h.TNAME, c.HTEL1, c.HTEL2, c.HTEL3,A.IPDATE
								) T1
								PIVOT ( sum(catotal) FOR  NAME IN( ".$instit ." )) AS PVT) aa
						left outer join 
								(SELECT isnull(c.SKEY,'XXXXX') SKEY, ipdate,
								sum(isnull(a.SAMT,0)) skeytot
								FROM INS_SUNAB(nolock) a
								left outer join INSWON(nolock) b on  a.scode = b.scode and  a.INSCODE = b.INSCODE and a.KSMAN = b.BSCODE  
								left outer join swon(nolock)  c on  b.scode = c.scode and  b.SKEY = c.SKEY
								left outer join INSSETUP(nolock)  d on  a.scode = d.scode and  a.INSCODE = d.INSCODE
								where a.SCODE = '".$_SESSION['S_SCODE']."'  and  a.IPDATE >= '".$FYYMM."'  and   a.IPDATE <= '".$TYYMM."'  and b.sgubun='1' 
								group by c.SKEY ,a.IPDATE) bb  on aa.SKEY = bb.SKEY  and aa.ipdate = bb.ipdate ) p 
		  
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
		'base_url' => $_SERVER['PHP_SELF']."?SDATE1=". $_REQUEST['SDATE1']."&SDATE2=". $_REQUEST['SDATE2']."&id=".$_REQUEST['id']."&page=Y",
		'per_page' => $page_row,
		'total_rows' => $totalResult['CNT'],
		'cur_page' => $page,
));

sqlsrv_free_stmt($result);
sqlsrv_close($mscon);

?>


<div class="tb_type01 kwndatalist div_grid" style="overflow-y:auto;">	
	<table id="sort_table" class="gridhover" style="min-width: 3200px;">
		<colgroup>
			<col width="80px">
			<col width="80px">
			<col width="100px">
			<col width="150px">

			<col width="90px">
			<col width="120px">

			<?for($i=1;$i<= $instit_cnt;$i++ ){?> 
						<col width="100px">
  			<?}?>
			<col width="auto">

		</colgroup>
		<thead>
		<tr class="rowTop">
			<th align="center">입금처리일</th>
			<th align="center">사원</th>
			<th align="center">사원명</th>
			<th align="center">소속</th>				
			<th align="center">휴대폰</th>
			<th align="center">수금합계</th>
 
			<?for($i=1;$i<= $instit_cnt;$i++ ){?> 
				<th align="left"><?=$titList[$i] ?></th>
  			<?}?>
			<th align="center"></th>	
 
		</tr>
		</thead>			
			<tr class="summary sticky"style="top:32px">
			<th></th>
			<th></th>
			<th></th>
			<th class="sum01"><?= ' 합 계 ' ?></th>							
			<th></th>
			<th class="sum01"><?=number_format($listinsTot_tot )?></th>							
			<? for($i = 0; $i <  $instit_cnt ; $i++) { ?> 
				<th class="sum01"><?=number_format($listinsTot[$i]['catotal'])  ?></th>		
			<?}?>  	 
			<th></th>
			</tr>

		<tbody>
			<?if(!empty($listData)){?>
			<?$ii = 0?>
			<?foreach($listData as $key => $val){extract($val);?>
			<tr class="rowData" rol-data='<?=$swonskey?>', rol-yymmdd='<?=$IPDATE?>'>
 
				<td align="center"><?if(trim($IPDATE)) echo date("Y-m-d",strtotime($IPDATE));?></td>
				<?if ($swonskey == 'XXXXX') { ?>
					<td align="left"><?='비매칭코드'?></td>
				<?}else{?>
					<td align="left"><?=$swonskey?></td>
				<?}?> 
				<td align="left"><?=$SNAME?></td>
				<?$sosok = substr($BNAME,0,4).'>'.substr($JSNAME,0,4).'>'.substr($JNAME,0,8).'>'.$TNAME   ?>
				<?$sosok = str_replace('>>','>',$sosok)?> 
				<?$sosok = str_replace('>>','>',$sosok)?> 
				<td align="left"><?=$sosok?></td>
				<td align="left"><?=$HTEL1.'-'.$HTEL2.'-'.$HTEL3?></td>
				<td align="right" ><?=number_format($skeytot)?></td>

				<?for($i = 1; $i <=  $instit_cnt ; $i++) {?>
						<td align="right"><?=number_format($listData[$ii][$titList[$i]])?></td>   <!--크로스탭으로 회면디스플레이  -->
	 			<?}?>
				<?$ii = $ii + 1 ?>

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
	<ul class="pagination pagination-sm kwnlist" style="margin: 5px 5px 0 5px">
	  <?=$pagination->create_links();?>
	</ul>
</div>

<script type="text/javascript">


// 헤더 클릭
$(".rowTop > th").click(function(){
	var trData = $(this).parent();

	var idx = $(trData).find("th").index($(this));

	// include/bottom.php 참조	
	sortTable("sort_table", idx, 2);
})
 

$(document).ready(function(){

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
		var yymmdd  = $(".rowData").eq(idx).attr('rol-yymmdd');

		var left = Math.ceil((window.screen.width - 1200)/2);
		var top = Math.ceil((window.screen.height - 1000)/2);
		var popOpen	= window.open("<?=$conf['homeDir']?>/sub/menu3/ga_menu3_64_list3.php?swon="+swon +"&yymmdd=" +yymmdd, "width=1200px,height=800px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
		popOpen.focus();
	})

});

</script>


<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>