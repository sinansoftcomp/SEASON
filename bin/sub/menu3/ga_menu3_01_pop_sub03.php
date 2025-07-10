<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

$inscode	= $_GET['inscode'];
$kcode		= $_GET['kcode'];

// 기본 페이지 셋팅
$page = ($_GET['page']) ? $_GET['page'] : 1;
$page_row	= 100;

$limit1 = ($page-1)*$page_row+1;
$limit2 = $page*$page_row;

// 수납정보 리스트
if($_GET['kcode']){

	//검색 데이터 구하기 
	$sql= "
		select *
		from(
			select 
					a.*,    a.SAMT6 + a.SAMT7 + a.SAMT8+ a.SAMT9+ a.SAMT10+ a.SAMT11+ a.SAMT12+ a.SAMT13 as  KITA_HAP ,
					a.SAMT1 +a.SAMT2 +a.SAMT3 +a.SAMT4 +a.SAMT5 +a.SAMT6 + a.SAMT7 + a.SAMT8+ a.SAMT9+ a.SAMT10+ a.SAMT11+ a.SAMT12+ a.SAMT13 as S_TOTAL ,
					g.filename,
					row_number()over(order by a.iseq ) rnum
			from INS_IPMST(nolock) a	
				left outer join upload_history(nolock) g on a.scode = g.scode and a.ipdate = g.upldate 
								and a.gubun = g.gubun and a.gubunsub = g.gubunsub and a.ino = g.uplnum
			where a.scode = '".$_SESSION['S_SCODE']."'
			  and a.kcode = '".$_GET['kcode']."'
			) p
		where rnum between ".$limit1." AND ".$limit2 ;

	$qry	= sqlsrv_query( $mscon, $sql );
	$listData = array();
	while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
		$listData[]	= $fet;
	}


	// 데이터 총 건수와 합계 
	//검색 데이터 구하기 
	$sql= "
			select 
					count(*) CNT,sum(samt1) samt1,sum(samt2) samt2,sum(samt3) samt3,sum(samt4) samt4,sum(samt5) samt5,
					sum(SAMT6 +  SAMT7 +  SAMT8+  SAMT9+  SAMT10+  SAMT11+  SAMT12+  SAMT13) as   kita_hap,  
					sum(SAMT1 + SAMT2 + SAMT3 + SAMT4 + SAMT5 + SAMT6 +  SAMT7 +  SAMT8+  SAMT9+  SAMT10+  SAMT11+  SAMT12+  SAMT13) as s_total ,sum(HSSU) hssu,
					sum(MAMT) mamt , sum(HWANAMT) hwanamt,  sum(BSU) bsu
			from INS_IPMST(nolock) a
			where a.scode = '".$_SESSION['S_SCODE']."'
			  and a.kcode = '".$_GET['kcode']."' " ;
	 
	$qry =  sqlsrv_query($mscon, $sql);
	$totalResult =  sqlsrv_fetch_array($qry);
	

	// 계약프로필 정보
	$sql="
		select 
				a.kcode,
				a.insilj,
				a.inscode,
				f.name insname,
				case when isnull(s2.bonbu,'') != '' then substring(b.bname,1,2) else '' end +
				case when isnull(s2.bonbu,'') != '' and (isnull(s2.jisa,'') != '' or isnull(s2.team,'') != '')  then ' > ' else '' end +
				case when isnull(s2.jisa,'') != '' then substring(c.jsname,1,4) else '' end +
				case when isnull(s2.jisa,'') != '' and isnull(s2.jijum,'') != '' then ' > ' else '' end +
				case when isnull(s2.jijum,'') != '' then substring(d.jname,1,4) else '' end +
				case when isnull(s2.jijum,'') != '' and isnull(s2.team,'') != '' then ' > ' else '' end +
				case when isnull(s2.team,'') != '' then e.tname else '' end as sosok,
				a.ksman,
				a.kdman,
				case when isnull(a.gskey,'') != '' then s1.sname+'('+a.gskey+')' else '' end gskey_nm,
				case when isnull(a.kskey,'') != '' then s2.sname+'('+a.kskey+')' else '' end kskey_nm,
				a.kname,
				case when isnull(a.htel,'') != '' then a.htel else a.tel end telno,
				a.addr+' '+a.addr_dt addr,
				a.pname,
				a.kdate,
				a.fdate,
				a.tdate,
				a.item,
				a.itemnm,
				case when isnull(a.item,'') != '' then dbo.GetCutStr('('+a.item+')'+a.itemnm,50,'..') else '' end item_nm,
				a.mamt,
				a.hamt,
				a.samt,
				a.kstbit
		from kwn(nolock) a	
			left outer join inssetup(nolock) f on a.scode = f.scode and a.inscode = f.inscode
			left outer join inswon(nolock) is1 on a.scode = is1.scode and a.ksman = is1.bscode
			left outer join inswon(nolock) is2 on a.scode = is2.scode and a.kdman = is2.bscode
			left outer join swon(nolock) s1 on s1.scode = a.scode and s1.skey = is1.skey
			left outer join swon(nolock) s2 on s2.scode = a.scode and s2.skey = is2.skey
			left outer join bonbu(nolock) b on s2.scode = b.scode and s2.bonbu = b.bcode
			left outer join jisa(nolock) c on s2.scode = c.scode and s2.jisa = c.jscode
			left outer join jijum(nolock) d on s2.scode = d.scode and s2.jijum = d.jcode
			left outer join team(nolock) e on s2.scode = e.scode and s2.team = e.tcode
		where a.scode = '".$_SESSION['S_SCODE']."'	
		  and a.inscode = '".$_GET['inscode']."'
		  and a.kcode = '".$_GET['kcode']."'
	";

	$qry	= sqlsrv_query( $mscon, $sql );
	extract($fet	= sqlsrv_fetch_array($qry));

}


// 페이지 클래스 시작
// 로드
include_once($conf['rootDir'].'/include/class/Pagination.php');

// 설정
$pagination = new Pagination(array(
		'base_url' => $_SERVER['PHP_SELF']."?kcode=".$kcode."&inscode=".$kcode."&kskey=".$kskey,
		'per_page' => $page_row,
		'total_rows' => $totalResult['CNT'],
		'cur_page' => $page,
));

sqlsrv_free_stmt($result);
sqlsrv_close($mscon);


?>

<!-- html영역 -->
<style>
body{background-image: none;}

.tb_type01.view{
	margin-bottom:10px;
}

</style>



<div class="tit_wrap mt20">

	<span class="btn_wrap">
		<a href="#" class="btn_s white" style="min-width:100px;" onclick="pop_close();">닫기</a>
	</span>
</div>

<div class="tb_type01 view">
	<table class="">
			<colgroup>
				<col width="10%">
				<col width="20%">
				<col width="10%">
				<col width="20%">
				<col width="10%">
				<col width="auto">
			</colgroup>
		<tbody class="kwndata">

			<tr>	
				<th>증권번호</th><td><?=$kcode?></td>
				<th>계약자</th><td><?=$kname?></td>
				<th>피보험자</th><td><?=$pname?></td>
			</tr>
			<tr>	
				<th>보험사</th><td><?=$insname?></td>
				<th>보험구분</th><td><?=$conf['insilj'][$insilj]?></td>
				<th>계약상태</th><td><?=$kstbit?></td>
			</tr>
			<tr>					
				<th>모집사원</th><td><?=$gskey_nm?></td>
				<th>사용인</th><td><?=$kskey_nm?></td>
				<th>소속</th><td><?=$sosok?></td>
			</tr>
			<tr>	
				<th>계약일자</th><td><?if(trim($kdate)) echo date("Y-m-d",strtotime($kdate))?></td>				
				<th>보험료</th><td class="font_blue"><?=number_format($mamt).' 원'?></td>
				<th>상품</th><td title="<?=$itemnm?>"><?=$item_nm?></td>
			</tr>
		</tbody>
	</table>
</div>

<!-- //box_gray -->
<div class="tb_type01" style="height:650px;overflow-y:auto;border-top: 1px solid #47474a;">
	<table id="sort_sub03" class="gridhover">
		<colgroup>
			<col width="50px">
			<col width="90px">
			<col width="90px">
			<col width="70px">
			<col width="100px"> <!--수수료합-->

			<col width="100px">  <!--환수-->
			<col width="100px">  <!--부활-->
			<col width="100px">  <!--영수보험료-->
			<col width="100px">  <!--환산보험료-->
			
			<col width="100px">  <!--영수일-->
			<col width="auto">  <!--계약상태-->

		</colgroup>
		<thead>
		<tr class="rowTop">
			<th align="center">정산월</th>
			<th align="center">사용인</th>
			<th align="center">사용인명</th>
			<th align="center">납입회차</th>					
			<th align="right">수수료합계</th>

			<th align="right">환수금액</th>
			<th align="right">부활금액</th>
			<th align="right">영수보험료</th>
			<th align="right">환산보험료</th>
			
			<th align="center">영수일</th>
			<th align="center">계약상태</th>
		</tr>
		</thead>
			<?if(!empty($listData)){?>						
			<tr class="summary">
			<th></th>
			<th></th>
			<th></th>
			<th class="sum02" ><?=number_format($totalResult['CNT'])?>건</th>							
			<th class="sum01" ><?=number_format($totalResult['s_total'])?></th>

			<th class="sum01" ><?=number_format($totalResult['hssu'])?></th>
			<th class="sum01" ><?=number_format($totalResult['bsu'])?></th>
			<th class="sum01" ><?=number_format($totalResult['mamt'])?></th>
			<th class="sum01" ><?=number_format($totalResult['hwanamt'])?></th>

			<th></th>
			<th></th>
			</tr>
			<?}?>
		<tbody>
			<?if(!empty($listData)){?>
			<?foreach($listData as $key => $val){extract($val);?>
			<tr class="rowData" rol-data='<?=$ORIDATA?>' rol-file='<?=$filename?>' rol-iseq='<?=$ISEQ?> '>
				<td align="center"><?if(trim($YYMM)) echo date("Y-m",strtotime($YYMM))?></td>
				<td align="left"><?=$KSMAN?></td>
				<td align="center" ><?=$KSMAN_NAME?></td>
				<td align="right" class="font_red"><?=number_format($NCNT)?></td> 
				<td align="right" class="font_red"><?=number_format($S_TOTAL)?></td>

				<td align="right"><?=number_format($HSSU)?></td>
				<td align="right"><?=number_format($BSU)?></td>
				<td align="right"><?=number_format($MAMT)?></td>
				<td align="right"><?=number_format($HWANAMT)?></td>
				
				<td><?if(trim($EDATE)) echo date("Y-m-d",strtotime($EDATE))?></td>	
				<td align="center"><?=$ISTBIT?></td>	
				
			</tr>
			<?}}else{?>
				<tr>
					<td style="color:#8C8C8C" colspan=11>검색된 데이터가 없습니다</td>
				</tr>
			<?}?>
		</tbody>
	</table>
</div><!-- // tb_type01 -->

<div style="text-align: center">		
	<ul class="pagination pagination-sm pop_sub03" style="margin: 10px">
	  <?=$pagination->create_links();?>
	</ul>
</div>


<script type="text/javascript">

// 닫기
function pop_close(){	
	window.close();
	//opener.location.reload();
}


// 헤더 클릭
$(".rowTop > th").click(function(){
	var trData = $(this).parent();

	var idx = $(trData).find("th").index($(this));

	// include/bottom.php 참조(맨윗줄 summary 있을 경우 마지막변수값 Y로 호출)
	sortTable("sort_sub03", idx, 2);
})


// 수수료업로드 상세
function ins_display(oridata,filename,iseq){

	var left = Math.ceil((window.screen.width - 1200)/2);
	var top = Math.ceil((window.screen.height - 1000)/2);
	var popOpen	= window.open("<?=$conf['homeDir']?>/sub/menu3/ga_menu3_72_list_pop.php?oridata="+oridata +"&filename=" +filename+"&iseq=" +iseq ,"insDt","width=1200px,height=800px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
	popOpen.focus();

}


$(document).ready(function(){

	// page 함수 ajax페이지 존재시 별도 처리
	$(".pop_sub03 a").click(function(){
		var res = $(this).attr("href").split("?");
		if(res[0] && res[1]){
			ajaxLodingTarget(res[0],res[1],event,$('#kwnDt_data'));    
		}
		return false;
	});


	// 리스트 클릭시 상세내용 조회
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