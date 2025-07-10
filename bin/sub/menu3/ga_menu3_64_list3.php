<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

$swon	= $_GET['swon'];
$yymmdd	= $_GET['yymmdd'];
 
$page	= $_GET['page'];
// 기본 페이지 셋팅
$page = ($_GET['page']) ? $_GET['page'] : 1;
$page_row	= 300;

$limit1 = ($page-1)*$page_row+1;
$limit2 = $page*$page_row;

 

//검색 데이터 구하기 
$sql= "
	select *
	from(
		select 
				a.*,  d.NAME, e.FILENAME ,	 row_number()over(order by a.INSCODE, a.iseq ) rnum
		from INS_SUNAB(nolock)  a	
		left outer join INSWON(nolock) b on  a.scode = b.scode and  a.INSCODE = b.INSCODE and a.KSMAN = b.BSCODE  
		left outer join swon(nolock)  c on  b.scode = c.scode and  b.SKEY = c.SKEY
		left outer join INSSETUP(nolock)  d on  a.scode = d.scode and  a.INSCODE = d.INSCODE
	   left outer join UPLOAD_HISTORY(nolock)  e on  a.scode = e.scode and  a.IPDATE = e.UPLDATE   and  a.GUBUN = e.GUBUN and  a.GUBUNSUB = e.GUBUNSUB and  a.INO = e.UPLNUM
		where a.scode = '".$_SESSION['S_SCODE']."'
		  and a.IPDATE	=  '".$yymmdd."' 
		  and c.skey	=  '".$swon."'   
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
 /*
echo '<pre>';
print_r($listData); 
echo '</pre>';
 */ 


// 데이터 총 건수와 합계 
//검색 데이터 구하기 
$sql= "
		select 
				count(*) CNT,sum(samt)  SAMT
		from INS_SUNAB(nolock) a
		left outer join INSWON(nolock) b on  a.scode = b.scode and  a.INSCODE = b.INSCODE and a.KSMAN = b.BSCODE  
		left outer join swon(nolock)  c on  b.scode = c.scode and  b.SKEY = c.SKEY
		left outer join INSSETUP(nolock)  d on  a.scode = d.scode and  a.INSCODE = d.INSCODE
	   left outer join UPLOAD_HISTORY(nolock)  e on  a.scode = e.scode and  a.IPDATE = e.UPLDATE   and  a.GUBUN = e.GUBUN and  a.GUBUNSUB = e.GUBUNSUB and  a.INO = e.UPLNUM
		where a.scode = '".$_SESSION['S_SCODE']."'
		  and a.IPDATE	=  '".$yymmdd."' 
		  and c.skey	=  '".$swon."' 
		  
		  " ;
 
 /*
 echo '<pre>';
echo $sql; 
echo '</pre>';
 */
$qry =  sqlsrv_query($mscon, $sql);
$totalResult =  sqlsrv_fetch_array($qry); 

// 전체보험사
$sql= "select inscode code, name, gubun from inssetup where scode = '".$_SESSION['S_SCODE']."' and useyn = 'Y' order by name";
$qry= sqlsrv_query( $mscon, $sql );
$instot	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
  $instot[] = $fet;
}

// 페이지 클래스 시작
// 로드
include_once($conf['rootDir'].'/include/class/Pagination.php');

// 설정
$pagination = new Pagination(array(
		'base_url' => $_SERVER['PHP_SELF']."?swon=".$swon."&yymmdd=".$yymmdd   ,
		'per_page' => $page_row,
		'total_rows' => $totalResult['CNT'],
		'cur_page' => $page,
));

sqlsrv_free_stmt($result);
sqlsrv_close($mscon);

?>

<!-- html영역 -->
<style>
</style>

<div class="container">
	<div class="content_wrap">
		<fieldset>
			<legend> </legend>
		
			<!-- 검색조건 -->
			<div class="tit_wrap mt20">
				<span class="tit_big">사용인별 수납 명세서 </span> 
				<span class="btn_wrap">				
					<a class="btn_s white btn_search"  style="margin: 0; min-width:100px;" >조회</a>
 					<a class="btn_s white" style="min-width:100px;" onclick="kwn_close();">닫기</a>
				</span>	  
				<form name="searchFrm" method="get" action="<?$_SERVER['PHP_SELF']?>">
				<input type="hidden" name="swon"  id="swon"  value="<?=$swon?>">
				<input type="hidden" name="yymmdd" id="yymmdd" value="<?=$yymmdd?>">
 				<input type="hidden" name="page" id="page" value="<?=$page?>"> 
				</form> 
			</div><!-- // box_wrap -->

			<div class="tb_type01 div_grid3" style="overflow-y:auto;">
				<table id="sort_table" class="gridhover"  style="width: 2700px;">
					<colgroup>
						<col width="110px">
						<col width="130px">
						<col width="130px">
						<col width="100px">
						<col width="160px">
						<col width="100px">  
						<col width="80px"> 
						<col width="100px"> 
						<col width="100px">
						<col width="110px">
						<col width="100px">
						<col width="100px">
						<col width="100px">
						<col width="100px">
						<col width="100px">
						<col width="100px">
						<col width="100px"> 
						<col width="100px">
						<col width="150px">
						<col width="450px">
						<col width="auto">

					</colgroup>
					<thead>

					<tr class="rowTop">
						<th align="center">수납처리일</th>
						<th align="center">원수사</th>
						<th align="center">증권번호</th>
						<th align="center">상품</th>
						<th align="center">상품명</th>						
						<th align="center">계약자</th>						
						<th align="center">피보험자</th>
						<th align="center">사용인</th>
						<th align="center">사용인명</th>	
	
						<th align="right">입금일자</th>
						<th align="right">최종월도</th>
						<th align="right">납입회차</th>						
						<th align="right">보험개시일</th>
						<th align="right">보험종료일</th>
						
						<th align="right">계약상태</th>
						<th align="right">영수금액</th>
						<th align="right">납입주기</th>
						<th align="right">납입방법</th>
						<th align="right">이체지정일</th>
						<th align="right">업로드파일</th>
						<th align="right">구증권번호</th>

					</tr>
					</thead>
						<?if(!empty($listData)){?>			
						<tr class="summary sticky"style="top:32px">
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th class="sum01"><?=number_format($totalResult['CNT'])?>건</th>							
						<th></th>
						<th class="sum01">수납계</th>
						<th class="sum01"><?=number_format($totalResult['SAMT'])?></th>							
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						</tr>
						<?}?>

 

					<tbody>
						<?if(!empty($listData)){?>
						<?foreach($listData as $key => $val){extract($val);?>
						<tr class="rowData" rol-data='<?=$ORIDATA?>' rol-file='<?=$FILENAME?>' rol-iseq='<?=$ISEQ?> '>
							<td><?if(trim($IPDATE)) echo date("Y-m-d",strtotime($IPDATE))?></td>		
							<td align="left"><?=$NAME?></td>
							<td align="left"><?=$KCODE?></td>
							<td align="left"><?=$ITEM?></td>
							<td align="left"><?=mb_substr($ITEMNM,0,10, 'euc-kr').'..'?></td>
							<td align="left" ><?=$KNAME?></td>
							<td align="left"><?=$PNAME?></td>
							<td align="left"><?=$KSMAN?></td>
							<td align="left" ><?=$KSMAN_NAME?></td>
							<td><?if(trim($YYMMDD)) echo date("Y-m-d",strtotime($YYMMDD))?></td>		
							<td><?if(trim($YYMM)) echo date("Y-m",strtotime($YYMM))?></td>		
							<td align="right"><?=$NCNT?></td>
							<td><?if(trim($ADATE)) echo date("Y-m-d",strtotime($ADATE))?></td>		
							<td><?if(trim($BDATE)) echo date("Y-m-d",strtotime($BDATE))?></td>
							<td align="left" ><?=$ISTBIT?></td>
							<td align="right"><?=number_format($SAMT)?></td>	 
							<td align="left" ><?=$NJUKI?></td>
							<td align="left" ><?=$NBIT?></td>
							<td align="left" ><?=$LDAY?></td>
							<td align="left"><?=$FILENAME?></td>
							<td align="left" ><?=$GKCODE?></td>

						</tr>
						<?}}else{?>
							<tr>
								<td style="color:#8C8C8C" colspan=14>검색된 데이터가 없습니다</td>
							</tr>
						<?}?>
					</tbody>
				</table>
			</div><!-- // tb_type01 -->

			<div style="text-align: center">		
				<ul class="pagination pagination-sm" style="margin: 5px 5px 0 5px">
				  <?=$pagination->create_links();?>
				</ul>
			</div>
 

		</fieldset>
	</div><!-- // content_wrap -->
</div>
<!-- // container -->
<!-- // wrap -->
<script type="text/javascript">


// 입금상세 display 
function ins_display(oridata,filename,iseq){

	var left = Math.ceil((window.screen.width - 1200)/2);
	var top = Math.ceil((window.screen.height - 1000)/2);
	var popOpen	= window.open("<?=$conf['homeDir']?>/sub/menu3/ga_menu3_72_list_pop.php?oridata="+oridata +"&filename=" +filename+"&iseq=" +iseq ,"insDt","width=1200px,height=800px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
	popOpen.focus();


}

function kwn_close(){	
	window.close();
	//opener.location.reload();
}


// 헤더 클릭
$(".rowTop > th").click(function(){
	var trData = $(this).parent();

	var idx = $(trData).find("th").index($(this));

	// include/bottom.php 참조	
	sortTable("sort_table", idx, 2);
})
 
$(document).ready(function(){
		var swon	= $("#swon").val();
		var yymm	= $("#yymm").val(); 
		var ser_con	= $("#ser_con").val(""); 
		var page	= $("#page").val(); 
 
	// 조회
	$(".btn_search").click(function(){
		var swon	= $("#swon").val();
		var yymmdd	= $("#yymmdd").val(); 
 		var page	= $("#page").val(); 

		//alert(upldate);
		//alert(uplnum);
		$("form[name='searchFrm']").attr("method","get");
		$("form[name='searchFrm']").attr("target","");

		$("form[name='searchFrm'] input[name='swon']").val(swon);
		$("form[name='searchFrm'] input[name='yymmdd']").val(yymmdd);
 		$("form[name='searchFrm'] input[name='page']").val(page);
		$("form[name='searchFrm']").submit();
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

	$( window ).resize(function() {		
		windowResize($(this));
	});
	
	var windowResize	= function(win){
		$(".tb_type01").height($(win).height()-100);
	};
	windowResize($( window ));

});

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>