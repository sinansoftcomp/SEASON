<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

$gubun	= $_GET['gubun'];
$where = " ";

if($_GET['gubun']){
	$where .= " and  gubun    = '".$_GET['gubun']."'  ";
}

if($_GET['searchF1Text']){
	$where  .= " and name like '%".$_GET['searchF1Text']."%' ";
}


// 기본 페이지 셋팅
$page = ($_GET['page']) ? $_GET['page'] : 1;
$page_row	= 50;

$limit1 = ($page-1)*$page_row+1;
$limit2 = $page*$page_row;

//검색 데이터 구하기 
$sql= "
select *
from(
	select *, ROW_NUMBER()over(order by num,inscode) rnum 
	from(
		select inscode,gubun,name,num,url,tel,sid,useyn
		from inssetup
		where scode = '".$_SESSION['S_SCODE']."'  ".$where." 
	) tbl
) p
	where rnum between ".$limit1." AND ".$limit2 ;

$qry	= sqlsrv_query( $mscon, $sql );
$listData = array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listData[]	= $fet;
}
/*
echo "<pre>";
echo $sql;
echo "</pre>";
*/
// 데이터 총 건수
//검색 데이터 구하기 
$sql= "
	select 
		count(*) CNT
	from(
		select inscode,gubun,name,num,url,tel,sid,useyn
		from inssetup
		where scode = '".$_SESSION['S_SCODE']."'	 ".$where." 
	) p " ;

$qry =  sqlsrv_query($mscon, $sql);
$totalResult =  sqlsrv_fetch_array($qry); 


sqlsrv_free_stmt($result);
sqlsrv_close($mscon);

function format_phone($phone){
    $phone = preg_replace("/[^0-9]/", "", $phone);
    $length = strlen($phone);
    switch($length){
      case 11 :
          return preg_replace("/([0-9]{3})([0-9]{4})([0-9]{4})/", "$1-$2-$3", $phone);
          break;
      case 10:
          return preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "$1-$2-$3", $phone);
          break;
      default :
          return $phone;
          break;
    }
}

?>

<!-- html영역 -->
<style>
body{background-image: none;}
</style>

<div class="container">
	<div class="content_wrap">
		<fieldset>
			<legend>보험사관리</legend>
			<!-- 검색조건 -->
			<div class="box_wrap sel_btn">
				<form name="searchFrm" method="get" action="<?$_SERVER['PHP_SELF']?>">
					<fieldset>
						<div>
							<select name="gubun" id="gubun" class="srch_css" style="width:120px;margin-left:10px">
							  <option value="">보혐사유형</option>
							  <?foreach($conf['ins_gubun'] as $key => $val){?>
							  <option value="<?=$key?>" <?if($_GET['gubun']==$key) echo "selected"?>><?=$val?></option>
							  <?}?>
							</select>
		
							<input type="text" id="" name="" readonly="" class="sel_text srch_css" value="보험사명">
							<input type="text" name="searchF1Text" id="searchF1Text" style="width:125px" class="srch_css" value="<?=$_GET['searchF1Text']?>" >
							
							<span class="btn_wrap">
								<a class="btn_s white hover_btn  btn_search" style="width:100px;margin-right: 0px;" onclick="common_ser();">조회</a>
							</span>
						</div>
					</fieldset>
				</form>
			</div><!-- // box_wrap -->

			<div class="tit_wrap mt20;margin-top:25px">
			<div class="tb_type01 div_grid2" style="overflow-y:auto;">

				<table id='widthresize' class="gridhover">

					<colgroup>
						<col width="8%">
						<col width="9%">
						<col width="15%">
						<col width="12%">
						<col width="12%">							
						<col width="12%">											
						<col width="12%">
						<col width="auto">
					</colgroup>

					<thead>
					<tr>
						<th align="right">순번</th>	
						<th align="center">보험사코드( <?=$totalResult['CNT']?>건 )</th>
						<th align="right">보험사명</th>
						<th align="right">유형</th>		
						<th align="right">사용여부</th>	
						<th align="right">GA시스템URL</th>						
						<th align="right">전화번호</th>
						<th align="right">ADMIN ID</th>
					</tr>
					</thead>
					<tbody>
						<?if(!empty($listData)){?>
						<?foreach($listData as $key => $val){extract($val);?>
						<tr class="rowData" rol-date='<?=$inscode?>' style="cursor:pointer;">
							<td align="center"><?=$num?></td>
							<td align="center"><?=$inscode?></td>
							<td align="left"><?=$name?></td>
							<td align="center"><?=$conf['ins_gubun'][$gubun]?></td>
							<td align="center" ><input type = "checkbox" id="useyn" name="useyn" <?if($useyn=="Y"){?>checked<?}?> onClick="return false" / > </td>
							<td align="left"><?=$url?></td>							
							<td align="left" ><?=format_phone($tel)?></td>							
							<td align="left" ><?=$sid?></td>
						</tr>
						<?}}else{?>
							<tr>
								<td style="color:#8C8C8C" colspan=11>검색된 데이터가 없습니다</td>
							</tr>
						<?}?>
					</tbody>
				</table>

			</div><!-- // tb_type01 -->

		</fieldset>
	</div><!-- // content_wrap -->
</div>
<!-- // container -->
<!-- // wrap -->

<script type="text/javascript" src="<?=$conf['homeDir']?>/js/colResizable-1.6.min.js"></script>
<script type="text/javascript">

$(function(){
$('#widthresize').colResizable({liveDrag:true});
});


function inssetPopOpen(inscode){
	
	var left = Math.ceil((window.screen.width - 700)/2);
	var top = Math.ceil((window.screen.height - 400)/2);
	var popOpen	= window.open("<?=$conf['homeDir']?>/sub/menu1/ga_menu1_03_pop.php?inscode="+inscode,"insset","width=690px,height=360px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
	popOpen.focus();
	
	//$.ajaxLoding('ga_menu1_03_pop.php',$('.layerBody_inssetup'),$('#modal2'),'inscode='+inscode);	
}

// 저장
function insset_update(){
	$("#useyn").val("Y");
	
	var num = $("form[name='insset_form'] input[name='num']").val();
	if(isEmpty(num) == true){
		alert('정렬순번을 입력해주세요.');
		return false
	}

	if(confirm("저장하시겠습니까?")){
		$("form[name='insset_form']").submit();
	}
}

$(document).ready(function(){
	var idv		= '<?=$IDV?>';
	var fyy		= '<?=$_GET['FYY']?>';
	var bit		= '<?=$_GET['BIT']?>';


	window.parent.postMessage("시스템관리 > 보험사관리", "*");   // '*' on any domain 부모로 보내기

	// 검색버튼 클릭시 class on 활성/비활성
	if(idv){
		$(".box_wrap.sel_btn a").removeClass('on');
		$("#"+idv).addClass('on');
	}else{
		if(fyy){
			$(".box_wrap.sel_btn a").removeClass('on');
		}
	}

	// 조회
	$(".btn_search").click(function(){
		$("form[name='searchFrm']").attr("method","get");
		$("form[name='searchFrm']").attr("target","");
		$("form[name='searchFrm']").submit();
	}); 

	// Enter 이벤트
	$("#searchF1Text").keydown(function(key) {
		if (key.keyCode == 13) {
			$("form[name='searchFrm']").attr("method","get");
			$("form[name='searchFrm']").attr("target","");
			$("form[name='searchFrm']").submit();
		}
	});

	$(".rowData > td").click(function(){
		var trData = $(this).parent();
		var idx    = $(".rowData").index($(trData));
		var inscode  = $(".rowData").eq(idx).attr('rol-date'); //조직코드
		inssetPopOpen(inscode);

	})

});

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>