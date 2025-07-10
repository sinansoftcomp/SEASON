<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

/*
	권한관리
	bin/include/source/auch_chk.php
*/
$pageTemp	= explode("/",$_SERVER['PHP_SELF']);
$auth = auth_Ser($_SESSION['S_MASTER'], $pageTemp[count($pageTemp)-1], $_SESSION['S_SKEY'], $mscon);
if($auth != "Y"){
	sqlsrv_close($mscon);
	alert('해당 메뉴에 대해 권한이 없습니다. 관리자에게 문의 바랍니다.');
	exit;
}


// 프로그램 진입시 시간이  소요됨으로 정산월 선택후 조회버튼 누르면 datadisplay하기 위함.
 if ( isset($_GET['fyymm']) ) {
	$fyymm =  substr($_GET['fyymm'],0,4).substr($_GET['fyymm'],5,2); 
 }else{
	$fyymm =  date("Y-m", strtotime("-2 month", strtotime(date("Y-m-d"))));
	$fyymm =  substr($fyymm,0,4).substr($fyymm,5,2); 
}


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
	$instit_cnt =  $instit_cnt + 1;    //보험사 타이틀 갯수,  나중에 타이틀은 루핑 돌라고 
}

$instit = str_replace("][","],[",$instit); //--->타이틀을 sql 	PVT(크로스탭에서 사용한다) 	--타이틀 보험사가 동적이다. 

$select_f = "";
for($i=1; $i<=$instit_cnt; $i++){
	$select_f .= " sum([".$titList[$i]."]) '".$titList[$i]."' , ";
}
if($select_f==""){
	$instit = "[삼성화재] ";
	$select_f = " sum([삼성화재]) 삼성화재 , ";
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
			select scode,yymm,bcode, bname,jscode,jsname, ".$select_f." row_number()over(order by    bname, jsname) rnum
			from(
				select scode,yymm,inscode,bcode,bname,jscode,jsname,
						".$instit."
				from(
					select a.scode,a.yymm,a.inscode,b.name, e.bcode,e.bname , isnull(f.jscode,'') jscode ,f.jsname ,a.suamt 
					from sudet a left outer join inssetup b on a.scode = b.scode and a.inscode = b.inscode
								left outer join sumst c on a.scode = c.scode and a.yymm=c.yymm and a.skey=c.skey
								left outer join bonbu e on a.scode = e.scode and a.bonbu = e.bcode
								left outer join jisa  f on a.scode = f.scode and a.jisa = f.jscode
					where a.SCODE =  '".$_SESSION['S_SCODE']."'   and  a.YYMM = '".$fyymm."'  
					) aa
				PIVOT(sum(suamt) for name in ( ".$instit." )) AS PVT
				) aa
			group by scode,yymm,bcode,bname,jscode,jsname 
			) aa left outer join (select scode,yymm,bonbu,isnull(jisa,'') jisa,sum(kamt10) kamt10,sum(kamt11) kamt11, sum(gamt12) gamt12, sum(gamt13) gamt13,sum(gamt1) gamt1,sum(gamt2) gamt2,
									sum(kamt1)+sum(kamt2)+sum(kamt3)+sum(kamt10)+sum(kamt11) totkamt , sum(gamt1)+sum(gamt2) totgamt,
									(sum(kamt1)+sum(kamt2)+sum(kamt3)+sum(kamt10)+sum(kamt11)) - (sum(gamt1)+sum(gamt2)+sum(gamt13)) silamt
								from sumst
								where SCODE =  '".$_SESSION['S_SCODE']."'   and  YYMM = '".$fyymm."'
								group by scode,yymm,bonbu,jisa) bb on aa.scode = bb.scode and aa.yymm = bb.yymm and aa.jscode = bb.jisa

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
		where a.SCODE =  '".$_SESSION['S_SCODE']."'   and  a.YYMM = '".$fyymm."' 
		group by   b.NAME, b.NUM 
		ORDER BY b.num,b.NAME
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
echo $sql; 
echo '</pre>';
*/

$sql ="
		select sum(kamt10) kamt10 , sum(kamt11) kamt11 , sum(kamt1)+sum(kamt2)+sum(kamt3) totkamt , 
				sum(gamt1) gamt1, sum(gamt2) gamt2 , sum(gamt12) gamt12 , sum(gamt13) gamt13 , sum(gamt1)+sum(gamt2)+sum(gamt13) totgamt,
				(sum(kamt1)+sum(kamt2)+sum(kamt3)+sum(kamt10)+sum(kamt12))-(sum(gamt1)+sum(gamt2)+sum(gamt13)) silamt
		from sumst a
		where a.SCODE =  '".$_SESSION['S_SCODE']."'   and  a.YYMM = '".$fyymm."'  
		";
$qry	= sqlsrv_query( $mscon, $sql );
$listData_tot = array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listData_tot[]	= $fet;
}

sqlsrv_free_stmt($result);
sqlsrv_close($mscon);

?>

<!-- html영역 -->
<!-- html영역 -->
<style>
body{background-image: none;}
 </style>

<div class="container ">
	<div class="content_wrap">
		<fieldset>
 
			<!-- 검색조건 -->
			<div class="box_wrap sel_btn">
				<form name="searchFrm" id="searchFrm" method="get" action="<?$_SERVER['PHP_SELF']?>">
 				<input type="hidden" name="id" id="id" value="">
				<input type="hidden" name="btn"  id="btn"  value="">
				<input type="hidden" name="excelcnt" id="excelcnt" value="">
					<fieldset>
 
						<div>
							<span class="ser_font"> 정산월</span> 
							<span class="input_type date ml10" style="width:114px;margin-left: 0px;">
								<input type="text" class="Cal_ym" placeholder="YYYY-MM" id="fyymm" name="fyymm" value="<?=$fyymm?>" readonly>
							</span> 
							
							<p class="response_block" style="margin-left:10px">
								<span class="btn_wrap">
									<a href="#" class="btn_s white"	name="mp"   id="mp"   onclick="d_ser('MP');">전월</a>
									<a href="#" class="btn_s white"		name="md"   id="md"     onclick="d_ser('MD');">당월</a>
								</span>		
								<span class="btn_wrap" style="margin-left:10px">							
									<a class="btn_s white" name="m1"  id="m1" onclick="d_ser('M1');">1월</a>
									<a class="btn_s white" name="m2"  id="m2" onclick="d_ser('M2');">2월</a>
									<a class="btn_s white" name="m3"  id="m3" onclick="d_ser('M3');">3월</a>
									<a class="btn_s white" name="m4"  id="m4" onclick="d_ser('M4');">4월</a>
									<a class="btn_s white" name="m5"  id="m5" onclick="d_ser('M5');">5월</a>
									<a class="btn_s white" name="m6"  id="m6" onclick="d_ser('M6');">6월</a>
									<a class="btn_s white" name="m7"  id="m7" onclick="d_ser('M7');">7월</a>
									<a class="btn_s white" name="m8"  id="m8" onclick="d_ser('M8');">8월</a>
									<a class="btn_s white" name="m9"  id="m9" onclick="d_ser('M9');">9월</a>
									<a class="btn_s white" name="m10"  id="m10" onclick="d_ser('M10');">10월</a>
									<a class="btn_s white" name="m11"  id="m11" onclick="d_ser('M11');">11월</a>
									<a class="btn_s white" name="m12"  id="m12" onclick="d_ser('M12');">12월</a>
								</span>
								<span class="btn_wrap">
									<a class="btn_s white hover_btn btn_search" style="width:150px;margin: 0px;" onclick="common_ser();">조회</a>
									<a class="btn_s white hover_btn btn_search2 excelBtn" style="width:150px;margin: 0px;display:none">엑셀</a>										
								</span>
							</p>
						</div>
					</fieldset>
				</form>
			</div><!-- // box_wrap -->
			<h5 class="tit_big">지사별총괄장</h5>
			<div class="tb_type01" style="overflow-y:auto;height:350px">
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
						<th align="center">정산월</th>
						<th align="center">지사코드</th>
						<th align="center">지사명</th>
						<?for($i=1;$i<= $instit_cnt;$i++ ){?> 
							<th align="left"><?=$titList[$i] ?></th>
						<?}?>
						<th align="center">기타수수료</th>
						<th align="center">예외과표</th>
						<th align="center">수수료계</th>
						<th align="center">세전공제</th>
						<th align="center">소득세</th>
						<th align="center">주민세</th>
						<th align="center">세후공제</th>
						<th align="center">세액합계</th>
						<th align="center">실지급액</th>
					</tr>
					</thead>
					<tbody>

						<tr class="summary sticky"style="top:34px">
							<th></th>
							<th></th>
							<th class="sumtext"><?= ' 합 계 ' ?></th>							
					
							<? for($i = 0; $i <  $instit_cnt ; $i++) { ?> 
								<th class="sum01"><?=number_format($listinsTot[$i]['catotal'])  ?></th>				
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
						</tr>

						<?if(!empty($listData)){?>
						<?foreach($listData as $key => $val){extract($val);?>
						<tr class="rowData" rol-data1='<?=$fyymm?>',  rol-data2 ='<?=$jscode?>' style="cursor:pointer;" >
							<td align="center"><?=date("Y-m",strtotime($yymm."01"))?></td>
  							<td align="left"><?=$jscode?></td>
							<td align="left"><?=$jsname?></td>
							<?for($i = 1; $i <=  $instit_cnt ; $i++) {?>
								<td align="right"><?=number_format($listData[$key][$titList[$i]])?></td>   <!--크로스탭으로 회면디스플레이  -->
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
								<td style="color:#8C8C8C" colspan=14>검색된 데이터가 없습니다</td>
							</tr>
						<?}?>
					</tbody>
				</table>

			</div><!-- // tb_type01 -->
			<h5 class="tit_big">지점별총괄장</h5>
			<div id="tab-1" style="height:350px">
	
			</div>



		</fieldset>
	</div><!-- // content_wrap -->
</div>
<!-- // container -->
<!-- // wrap -->
<script type="text/javascript">

// 테이블열 클릭시 강조함수
function HighLightTR(target, backColor) {
	var orgBColor = '#ffffff';
	var tbody = target.parentNode;
	var trs = tbody.getElementsByTagName('tr');

	for ( var i = 0; i < trs.length; i++ ) {
		if ( trs[i] != target ) {
			trs[i].style.backgroundColor = orgBColor;
		} else {
			trs[i].style.backgroundColor = backColor;
		}
	} 
}

// 헤더 클릭
$(".rowTop > th").click(function(){
	var trData = $(this).parent();

	var idx = $(trData).find("th").index($(this));

	// include/bottom.php 참조	
	// 소계 존재하는 페이지 보류
	//sortTable("sort_table", idx, 1);
})

function formatDate(dateString) {
    // 'YYYY-MM-DD' 형식에서 'YYYY' 부분을 추출
    const year = dateString.substring(0, 4);
    // 'YYYY-MM-DD' 형식에서 'MM' 부분을 추출
    const month = dateString.substring(5, 7);
    // 결과 문자열을 연결
    return year + month;
}

//--->기간선택
function d_ser(bit){
		var  fyymm	= document.getElementById('fyymm').value;
		var  str_date = bit + '&' + fyymm ;
		
		//--선택한 일자 가져오기
		str_date = date_on	(str_date);  //common.js 참조  bin>js>common.js
		var bdate = str_date.split('&');

		//alert(formatDate(bdate[0]));
		$("form[name='searchFrm'] input[name='fyymm']").val(bdate[0]); 
		
		//--->선택이 빨리 바뀌면 SERVER 부하걸림 
		if (bit != 'YP' && bit != 'YN' ){
			common_ser();
		}
 }

// 공통조회 함수(bin/js/common.js 호출)
function common_ser(){ 
		$("#div_load_image").show();
		$("form[name='searchFrm']").attr("method","get");
		$("form[name='searchFrm']").attr("target","");
		$("form[name='searchFrm']").submit();
}
 


$(document).ready(function(){

	ajaxLodingTarket('x1_jojiksu_sub.php',$('#tab-1'),'&fyymm='+'<?=$fyymm?>'+'&jscode=');

	$("#div_load_image").hide();
	//window.parent.postMessage("수수료관리 > 조직별 수수료", "*");   // '*' on any domain 부모로 보내기..        


	// 월 선택시 해당월에 클릭넘겨받기(function d_ser 에서 변수 넘김)
	var btn		= '<?=$_GET['btn']?>';
	if(btn){
		$(".box_wrap.sel_btn a").removeClass('on');
		$("#"+btn).addClass('on');
	}

 	// 리스트 클릭시 상세내용 조회
	$(".rowData > td").click(function(){
		var trData = $(this).parent();
		var idx    = $(".rowData").index($(trData));
		var fyymm  = $(".rowData").eq(idx).attr('rol-data1');
		var jscode = $(".rowData").eq(idx).attr('rol-data2');
		ajaxLodingTarket('x1_jojiksu_sub.php',$('#tab-1'),'&fyymm='+fyymm+'&jscode='+jscode);
 
	})

	$(".excelBtn").click(function(){
		if($('#excelcnt').val() == 0 ){
			alert('내려받을 데이터가 존재하지 않습니다.');
		}else{
			if(confirm("엑셀로 내려받으시겠습니까?")){
				$("form[name='searchFrm']").attr("action","x1_jojiksu_excel.php");
				$("form[name='searchFrm']").submit();
				$("form[name='searchFrm']").attr("action","<?$_SERVER['PHP_SELF']?>");
			}
		}
	});

});
</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>