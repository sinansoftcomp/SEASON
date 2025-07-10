<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

$upldate	= $_GET['upldate'];
$gubun	= $_GET['gubun'];
$gubunsub	= $_GET['gubunsub'];
$uplnum	= $_GET['uplnum'];
  
$filename	= $_GET['filename'];
$page	= $_GET['page'];

// 기본 페이지 셋팅
$page = ($_GET['page']) ? $_GET['page'] : 1;
$page_row	= 100;

$limit1 = ($page-1)*$page_row+1;
$limit2 = $page*$page_row;

$ser_con = ($_GET['ser_con']) ? $_GET['ser_con'] : "";

if ($ser_con == 'Y') {
	$ser_con_where = '   and (samt) = 0 ';
}

//검색 데이터 구하기 
$sql= "
	select *
	from(
		select 
				*, row_number()over(order by a.iseq ) rnum
		from INS_SUNAB(nolock) a	
		where a.scode = '".$_SESSION['S_SCODE']."'
		  and a.ipdate	=  '".$upldate."' 
		  and a.gubun	=  '".$gubun."' 
		  and a.gubunsub	=  '".$gubunsub."'   
		  and a.ino	=  '".$uplnum."'  $ser_con_where
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
				count(*) CNT,sum(samt) samt
		from INS_SUNAB(nolock) a
		where a.scode = '".$_SESSION['S_SCODE']."'
		  and a.ipdate	=  '".$upldate."' 
		  and a.gubun	=  '".$gubun."' 
		  and a.gubunsub	=  '".$gubunsub."'   
		  and a.ino	=  '".$uplnum."'"  .$ser_con_where  ;
 
$qry =  sqlsrv_query($mscon, $sql);
$totalResult =  sqlsrv_fetch_array($qry); 


// 페이지 클래스 시작
// 로드
include_once($conf['rootDir'].'/include/class/Pagination.php');

// 설정
$pagination = new Pagination(array(
		'base_url' => $_SERVER['PHP_SELF']."?upldate=".$upldate."&gubun=".$gubun."&gubunsub=".$gubunsub ."&uplnum=".$uplnum."&filename=".$filename."&ser_con=".$ser_con    ,
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
 		
			<!-- 검색조건 -->
			<div class="tit_wrap mt20">
				<span class="tit_big">원수사별 수금 업로드 현황  <?= '  ['. $filename . ' ]'?></span> 
				<span class="btn_wrap">				
					<a class="btn_s white btn_search"  style="margin: 0; min-width:100px;" >전체조회</a>
					<a class="btn_s white btn_searchu"  style="min-width:100px;">미수납</a>
					<a class="btn_s white" style="min-width:100px;" onclick="kwn_close();">닫기</a>
				</span>	  
				<form name="searchFrm" method="get" action="<?$_SERVER['PHP_SELF']?>">
				<input type="hidden" name="upldate"  id="upldate"  value="<?=$upldate?>">
				<input type="hidden" name="gubun" id="gubun" value="<?=$gubun?>">
				<input type="hidden" name="gubunsub" id="gubunsub" value="<?=$gubunsub?>">
				<input type="hidden" name="uplnum" id="uplnum" value="<?=$uplnum?>">
				<input type="hidden" name="filename" id="filename" value="<?=$filename?>">
				<input type="hidden" name="ser_con" id="ser_con" value="<?=$ser_con?>">
				<input type="hidden" name="page" id="page" value="<?=$page?>"> 
				</form> 
			</div><!-- // box_wrap -->

			<div class="tb_type01 div_grid3" style="overflow-y:auto;">
				<table id="sort_table" class="gridhover"  style="width: 2600px;">
					<colgroup>
						<col width="50px">
						<col width="110px">
						<col width="110px">
						<col width="300px">
						<col width="150px">
						<col width="150px">
						<col width="150px">
						<col width="100px">  
						<col width="100px"> 
						<col width="100px"> 
						<col width="100px">
						<col width="200px">
						<col width="100px">
						<col width="100px">
						<col width="100px">
						<col width="150px">
						<col width="150px"> 
						<col width="100px">
						<col width="AUTO">

					</colgroup>
					<thead>
					<tr class="rowTop">
						<th align="center">순번</th>
						<th align="center">증권번호</th>
						<th align="right">상품코드</th>
						<th align="right">상품명</th>
						<th align="center">계약자</th>
						<th align="center">피보험자</th>
						<th align="center">사용인</th>						
						<th align="center">사용인명</th>						
						<th align="center">입금년월일</th>						
						<th align="center">최종월도</th>
						<th align="right">최종회차</th>
						<th align="right">영수금액</th>
						<th align="center">보험개시일</th>
						<th align="center">보험종료일</th>					
						<th align="right">계약상태</th>
						<th align="right">납입주기</th>
						<th align="right">납입방법</th>
						<th align="right">이체지정일</th>
						<th align="right">구증권번호</th>

					</tr>
					</thead>
	 
						<?if(!empty($listData)){?>
						
						<tr  class="summary sticky"style="top:32px">
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
						<th class="sum01"><?=number_format($totalResult['samt'])?></th>
						<th></th>
						<th></th>
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
						<tr class="rowData" rol-data='<?=$ORIDATA?>' rol-file='<?=$filename?>' rol-iseq='<?=$ISEQ?> '>
							<td align="left"><?=$ISEQ?></td>
 
							<td align="left"><?=preg_replace('/\r\n|\r|\n/', '', $KCODE) ?></td>
 

							<td align="left"><?=$ITEM?></td>
							<td align="left"><?=mb_substr($ITEMNM,0,30, 'euc-kr').'..'?></td>
							<td align="left" ><?=$KNAME?></td>
							<td align="left"><?=$PNAME?></td>
							<td align="left"><?=$KSMAN?></td>
							<td align="left" ><?=$KSMAN_NAME?></td>
							<td><?if(trim($YYMMDD)) echo date("Y-m-d",strtotime($YYMMDD))?></td>		
							<td><?if(trim($YYMM)) echo date("Y-m",strtotime($YYMM))?></td>		
							<td align="right"   ><?=$NCNT?></td>
							<td align="right"  ><?=number_format($SAMT)?></td>
							<td><?if(trim($ADATE)) echo date("Y-m-d",strtotime($ADATE))?></td>		
							<td><?if(trim($BDATE)) echo date("Y-m-d",strtotime($BDATE))?></td>		
							<td align="center"><?=$ISTBIT?></td>
							<td align="center"><?=$NJUKI?></td>
							<td align="center"><?=$NBIT?></td>
							<td align="center"><?=$LDAY?></td>
							<td align="center"><?=$GKCODE?></td>
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
	var top = Math.ceil((window.screen.height - 800)/2);
	var popOpen	= window.open("<?=$conf['homeDir']?>/sub/menu3/ga_menu3_62_list_pop.php?oridata="+oridata +"&filename=" +filename+"&iseq=" +iseq ,"insDt","width=1200px,height=600px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
	popOpen.focus();


}

function kwn_close(){	
	window.close();
	//opener.location.reload();
}


 
$(document).ready(function(){
		var upldate	= $("#upldate").val();
		var gubun	= $("#gubun").val(); 
		var gubunsub	= $("#gubunsub").val(); 
		var uplnum	= $("#uplnum").val(); 
		var ser_con	= $("#ser_con").val(""); 
		var page	= $("#page").val(); 
 


	// 조회
	$(".btn_search").click(function(){
		var upldate	= $("#upldate").val();
		var gubun	= $("#gubun").val(); 
		var gubunsub	= $("#gubunsub").val(); 
		var uplnum	= $("#uplnum").val(); 
		var filename	= $("#filename").val(); 
		var ser_con	= $("#ser_con").val(""); 
		var page	= $("#page").val(); 

		//alert(upldate);
		//alert(uplnum);
		$("form[name='searchFrm']").attr("method","get");
		$("form[name='searchFrm']").attr("target","");

		$("form[name='searchFrm'] input[name='upldate']").val(upldate);
		$("form[name='searchFrm'] input[name='gubun']").val(gubun);
		$("form[name='searchFrm'] input[name='gubunsub']").val(gubunsub);
		$("form[name='searchFrm'] input[name='uplnum']").val(uplnum);
		$("form[name='searchFrm'] input[name='filename']").val(filename);

		$("form[name='searchFrm'] input[name='ser_con']").val("");
		$("form[name='searchFrm'] input[name='page']").val(page);
		$("form[name='searchFrm']").submit();
	}); 


	$(".btn_searchu").click(function(){
		var upldate	= $("#upldate").val();
		var gubun	= $("#gubun").val(); 
		var gubunsub	= $("#gubunsub").val(); 
		var uplnum	= $("#uplnum").val(); 
		var filename	= $("#filename").val(); 
		var ser_con	= $("#ser_con").val("Y"); 
		var page	= $("#page").val(); 

		//alert(upldate);
		//alert(uplnum);
		$("form[name='searchFrm']").attr("method","get");
		$("form[name='searchFrm']").attr("target","");

		$("form[name='searchFrm'] input[name='upldate']").val(upldate);
		$("form[name='searchFrm'] input[name='gubun']").val(gubun);
		$("form[name='searchFrm'] input[name='gubunsub']").val(gubunsub);
		$("form[name='searchFrm'] input[name='uplnum']").val(uplnum);
		$("form[name='searchFrm'] input[name='filename']").val(filename);
		$("form[name='searchFrm'] input[name='ser_con']").val("Y");
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


	// 헤더 클릭
	$(".rowTop > th").click(function(){
		var trData = $(this).parent();

		var idx = $(trData).find("th").index($(this));

		// include/bottom.php 참조	
		sortTable("sort_table", idx, 2);
	})


});

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>