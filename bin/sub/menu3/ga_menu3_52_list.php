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

 
//검색 데이터 구하기 
$sql= "
	select *
	from(
		select 
				*,dbo.decryptkey(a.sjuno) ASJUNO , dbo.decryptkey(PSJUNO) APSJUNO ,  row_number()over(order by a.UPLSEQ ) rnum
		from kwn(nolock) a	
		where a.scode = '".$_SESSION['S_SCODE']."'
		  and a.UPLDATE	=  '".$upldate."' 
		  and a.gubun	=  '".$gubun."' 
		  and a.gubunsub	=  '".$gubunsub."'   
		  and a.UPLNUM	=  '".$uplnum."' 
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
				count(*) CNT,SUM(MAMT) samt1,SUM(HAMT) samt2,SUM(SAMT) samt3
		from kwn(nolock) a
		where a.scode = '".$_SESSION['S_SCODE']."'
		  and a.UPLDATE	=  '".$upldate."' 
		  and a.gubun	=  '".$gubun."' 
		  and a.gubunsub	=  '".$gubunsub."'   
		  and a.UPLNUM	=  '".$uplnum."'"    ;
 
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
			<legend>계약업로드</legend>

			
			<!-- 검색조건 -->
			<div class="tit_wrap mt20">
				<span class="tit_big" >원수사별 계약업로드 현황  <?= '  ['. $filename . ' ]'?></span> 
				<span class="btn_wrap">				
					<a class="btn_s white btn_search"  style="margin: 0; min-width:100px;" >조회</a>
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
				<table id="sort_table" class="gridhover"  style="width:2500px;">
					<colgroup>
	 
						<col width="50px">
						<col width="100px">
						<col width="100px">
						<col width="250px">
						<col width="100px">  
						<col width="120px"> 
						<col width="100px"> 
						<col width="100px">
						<col width="100px">
						<col width="100px">
						<col width="100px">
						<col width="100px">
						<col width="130px">
						<col width="130px">
						<col width="130px">
						<col width="100px"> 
						<col width="100px">
						<col width="100px">
						<col width="100px">
						<col width="auto">
					</colgroup>
					<thead>
					<tr class="rowTop">
						<th align="center">순번</th>
						<th align="center">증권번호</th>
						<th align="center">상품명CODE</th>
						<th align="center">상품명</th>						
						<th align="center">상품구분</th>						
						<th align="center">계약자</th>	
						<th align="center">주민번호</th>						
						<th align="center">사용인</th>
						<th align="center">사용인명</th>
						<th align="center">계약일</th>					
						<th align="center">계약개시일</th>
						<th align="center">계약종료일</th>
						<th align="center">계약상태</th>
						<th align="right">보험료</th>
						<th align="right">환산월초</th>
						<th align="right">수정보험료</th>
						<th align="center">보험기간</th>
						<th align="center">납입주기</th>
						<th align="center">피보험자</th>
						<th align="center">피보험자주민</th>
						<th align="center">차량번호</th>
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
						<th  class="sum01"><?=number_format($totalResult['CNT'])?>건</th>	
						<th></th>
						<th></th>
						<th class="sum01"><?=number_format($totalResult['samt1'])?></th>							
						<th  class="sum01"><?=number_format($totalResult['samt2'])?></th>
						<th  class="sum01"><?=number_format($totalResult['samt3'])?></th>
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
						<tr class="rowData" rol-data='<?=$ORIDATA?>' rol-file='<?=$filename?>' rol-iseq='<?=$UPLSEQ?> '>
							<td align="left"><?=$UPLSEQ?></td>
							<td align="left"><?=$KCODE?></td>
							<td align="left"><?=$ITEM?></td>
							<td align="left"><?=mb_substr($ITEMNM,0,25, 'euc-kr').'..'?></td>   
							<td align="center"><?=$conf['insilj'][$INSILJ]?></td>
							<td align="left"><?=mb_substr($KNAME,0,6, 'euc-kr').'..'?></td>     <!--계약자-->
							<td align="left"><?=$ASJUNO?></td>  <!--계약자주민번호-->
							<td align="left"><?=$KSMAN?></td>				 <!--사용인코드-->
							<td align="center" ><?=$KSMAN_NAME?></td>  <!--사용인명  추후 조인-->
							<td><?if(trim($KDATE)) echo date("Y-m-d",strtotime($KDATE))?></td>	<!--계약일-->
							<td><?if(trim($FDATE)) echo date("Y-m-d",strtotime($FDATE))?></td>		<!--개약개시일-->
							<td><?if(trim($TDATE)) echo date("Y-m-d",strtotime($TDATE))?></td>	<!--계약종료일-->	 
 							<td align="left"  ><?=$KSTBIT?></td><!--계약상태-->
							<td   align="right"  ><?=number_format($MAMT)?></td>   <!--보험료-->
							<td align="right" ><?=number_format($HAMT)?></td> <!--환산월초-->
							<td align="right"><?=number_format($SAMT)?></td>  <!--수정보험료 -->
							<td align="left"><?$INSTERM?></td>  <!--보험기간 -->
							<td align="left"  ><?=$NBIT?></td> <!--납입주기 -->
							<td align="left"><?=mb_substr($PNAME,0,6, 'euc-kr').'..'?></td>     <!--피보험자 -->
							<td align="left"   ><?=$APSJUNO ?></td> <!--피보험자 -->
							<td align="left"><?=$CARNUM?></td> <!--차량번호 -->
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
	var top = Math.ceil((window.screen.height - 900)/2);
	var popOpen	= window.open("<?=$conf['homeDir']?>/sub/menu3/ga_menu3_52_list_pop.php?oridata="+oridata +"&filename=" +filename+"&iseq=" +iseq ,"insDt","width=1200px,height=750px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
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