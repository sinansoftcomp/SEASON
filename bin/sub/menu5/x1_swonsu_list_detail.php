<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

$swon	= $_GET['swon'];
$yymm	= $_GET['yymm'];
 
$page	= $_GET['page'];
// 기본 페이지 셋팅
$page = ($_GET['page']) ? $_GET['page'] : 1;
$page_row	= 1000;

$limit1 = ($page-1)*$page_row+1;
$limit2 = $page*$page_row;


//검색 데이터 구하기 
$sql= "	
	select *
	from(
					select a.*, row_number()over(order by inscode, seq,  a.ipdate,a.gubun, a.gubunsub, a.ino,a.iseq) rnum from (
						SELECT f.sname+'(' +a.skey+')' sname,  a.inscode, '1' seq , a.scode,   a.skey , a.yymm , b.name ,c.ksman, c.ksman_name, a.kcode,a.insilj, c.itemnm,  a.sbit ,a.mmcnt,a.kamt, a.jyul ,a.suamt, a.ipdate,a.gubun, a.gubunsub, 
						a.ino,a.iseq	, e.FILENAME,c.ORIDATA	,c.KNAME 
						FROM sudet a 
							left outer join INSSETUP b on  a.scode = b.scode and  a.INSCODE = b.INSCODE  
							left outer join ins_ipmst c on  a.scode = c.scode and  a.IPDATE = c.IPDATE and a.gubun = c.gubun and a.gubunsub = c.gubunsub and a.INO = c.INO and a.iseq =c.iseq
						   left outer join UPLOAD_HISTORY  e on  a.scode = e.scode and  a.IPDATE = e.UPLDATE   and  a.GUBUN = e.GUBUN and  a.GUBUNSUB = e.GUBUNSUB and  a.INO = e.UPLNUM
   						   left outer join swon f on a.scode = f.scode and a.skey = f.skey
						where a.scode = '".$_SESSION['S_SCODE']."' and  a.yymm= '".$yymm."' and a.skey =   '".$swon."' 

						union all

						SELECT '',a.inscode, '2',    '',   '' , '' , '' ,'', '', '','', '소  계',   '0','0',sum(a.kamt), '0' ,sum(a.suamt), '0','0', '0', '0','0','','',''
						FROM sudet a 
							left outer join INSSETUP b on  a.scode = b.scode and  a.INSCODE = b.INSCODE  
							left outer join ins_ipmst c on  a.scode = c.scode and  a.IPDATE = c.IPDATE and a.gubun = c.gubun and a.gubunsub = c.gubunsub and a.INO = c.INO and a.iseq =c.iseq
						where a.scode ='".$_SESSION['S_SCODE']."' and  a.yymm  = '".$yymm."'    and a.skey =  '".$swon."' 
						group by a.INSCODE

						union all 

						SELECT '', '', '0',    '',   '' , '' , '' ,'', '', '','', '합  계',   '0','0',sum(a.kamt), '0' ,sum(a.suamt), '0','0', '0', '0','0','','','' 
						FROM sudet a 
							left outer join INSSETUP b on  a.scode = b.scode and  a.INSCODE = b.INSCODE  
							left outer join ins_ipmst c on  a.scode = c.scode and  a.IPDATE = c.IPDATE and a.gubun = c.gubun and a.gubunsub = c.gubunsub and a.INO = c.INO and a.iseq =c.iseq
						where a.scode = '".$_SESSION['S_SCODE']."' and  a.yymm =  '".$yymm."'  and a.skey =  '".$swon."' ) a

					 
		
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

// 데이터 총 건수와 합계 
//검색 데이터 구하기 
$sql= "
					select count(*) CNT from (
						SELECT a.inscode, '1' seq , a.scode,   a.skey , a.yymm , b.name ,c.ksman, c.ksman_name, a.kcode,a.insilj, c.itemnm,  a.sbit ,a.mmcnt,a.kamt, a.jyul ,a.suamt, a.ipdate,a.gubun, a.gubunsub, a.ino,a.iseq										
						FROM sudet a 
							left outer join INSSETUP b on  a.scode = b.scode and  a.INSCODE = b.INSCODE  
							left outer join ins_ipmst c on  a.scode = c.scode and  a.IPDATE = c.IPDATE and a.gubun = c.gubun and a.gubunsub = c.gubunsub and a.INO = c.INO and a.iseq =c.iseq
						where a.scode = '".$_SESSION['S_SCODE']."' and  a.yymm= '".$yymm."' and a.skey =   '".$swon."' 

						union all

						SELECT a.inscode, '2',    '',   '' , '' , '' ,'', '', '','', '소  계',   '0','0',sum(a.kamt), '0' ,sum(a.suamt), '0','0', '0', '0','0'
						FROM sudet a 
							left outer join INSSETUP b on  a.scode = b.scode and  a.INSCODE = b.INSCODE  
							left outer join ins_ipmst c on  a.scode = c.scode and  a.IPDATE = c.IPDATE and a.gubun = c.gubun and a.gubunsub = c.gubunsub and a.INO = c.INO and a.iseq =c.iseq
						where a.scode ='".$_SESSION['S_SCODE']."' and  a.yymm  = '".$yymm."'    and a.skey =  '".$swon."' 
						group by a.INSCODE

						union all 

						SELECT 'zzzzzz', '3',    '',   '' , '' , '' ,'', '', '','', '합  계',   '0','0',sum(a.kamt), '0' ,sum(a.suamt), '0','0', '0', '0','0'
						FROM sudet a 
							left outer join INSSETUP b on  a.scode = b.scode and  a.INSCODE = b.INSCODE  
							left outer join ins_ipmst c on  a.scode = c.scode and  a.IPDATE = c.IPDATE and a.gubun = c.gubun and a.gubunsub = c.gubunsub and a.INO = c.INO and a.iseq =c.iseq
						where a.scode = '".$_SESSION['S_SCODE']."' and  a.yymm =  '".$yymm."'  and a.skey =  '".$swon."' ) a
		  
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
		'base_url' => $_SERVER['PHP_SELF']."?swon=".$swon."&yymm=".$yymm    ,
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
<script> 
    document.title = "사용인지급수수료";
 </script>
<div class="container">
	<div class="content_wrap">
		<fieldset>
			<legend> </legend>
		
			<!-- 검색조건 -->
			<div class="tit_wrap mt20">
				<span class="tit_big">사용인별 지급수수료 상세내역 </span> 
				<span class="btn_wrap">				
					<a href="#" class="btn_l white" style="min-width:100px;" onclick="kwn_close();">닫기</a>
				</span>	  
				<form name="searchFrm" method="get" action="<?$_SERVER['PHP_SELF']?>">
				<input type="hidden" name="swon"  id="swon"  value="<?=$swon?>">
				<input type="hidden" name="yymm" id="yymm" value="<?=$yymm?>">
				<input type="hidden" name="ser_con" id="ser_con" value="<?=$ser_con?>">
				<input type="hidden" name="page" id="page" value="<?=$page?>"> 
				</form> 
			</div><!-- // box_wrap -->

			<div class="tb_type01" style="overflow-y:auto;">
				<table class="gridhover"  style="width: 2100px;">
					<colgroup>
						<col width="50px">
						<col width="110px">
						<col width="110px">
						<col width="100px">
						<col width="100px">
						<col width="130px">
						<col width="100px">  
						<col width="100px">  
						<col width="100px"> 

						<col width="90px">
						<col width="110px">
						<col width="100px">
						<col width="100px">
						<col width="450px"> 

						<col width="100px">
						<col width="auto">

					</colgroup>
					<thead>
					<tr>
						<th align="center">정산월</th>
						<th align="center">수수료지급사원</th>
						<th align="center">원수사</th>
						<th align="center">사용인</th>
						<th align="center">사용인명</th>

						<th align="center">증권번호</th>						
						<th align="center">계약자</th>						
						<th align="center">보종</th>						
						<th align="center">지급수수료구분</th>

						<th align="center">납입회차</th>					
						<th align="right">수입수수료</th>
						<th align="right">지급율</th>
						<th align="right">지급수수료</th>
						<th align="center">상품명</th>

						<th align="right">수입수수료입력일</th>
						<th align="right">처리RPA_NO </th>
					</tr>
					</thead>
						<?if(!empty($listData)){?>						
						<tr class="summary sticky"style="top:32px">
						</tr>
						<?}?>
					<tbody>
						<?if(!empty($listData)){?>
						<?foreach($listData as $key => $val){extract($val);?>
						<tr class="rowData" rol-data='<?=$ORIDATA?>' rol-file='<?=$FILENAME?>' rol-iseq='<?=$iseq?> '  rol-seq='<?=$seq?>'   >
							<?if ($seq == '1') {?>
								
								<td align="center"><?=date("Y-m",strtotime($yymm))?></td>
								<td align="left"><?=$sname?></td>
								<td align="left"><?=$name?></td>
								<td align="left"><?=$ksman?></td>
								<td align="left"><?=$ksman_name?></td>								
								<td align="left" ><?=$kcode?></td>

								<td align="left" ><?=$KNAME?></td>
								<td align="left"><?=$conf['insilj'][$insilj]?></td>
								<td align="left"><?=$sbit?></td>
								<td align="right"><?=number_format($mmcnt)?></td>

								<td align="right"><?=number_format($kamt)?></td>
								<td align="right"><?=number_format($jyul)?></td>
								<td align="right"><?=number_format($suamt)?></td>
								<td align="left"><?=$itemnm?></td>

								<td align="center"><?=date("Y-m-d",strtotime($ipdate))?></td>
								<td align="left"><?=$ipdate.'-'.$gubun.'-'.$gubun.'-'.$gubunsub.'-'.$ino.'-'.$iseq?></td>

							<?}else{ ?>
								<td ></td>
								<td></td>
								<td ></td>
								<td ></td>
								<td ></td>
								<td ></td>
								<td ></td>
								<td ></td>
								<td ></td>
								<td ></td>
								<td align="right" style ="font-size: larger; color: #e33a3a;"><?=number_format($kamt)?></td>
								<td ></td>
								<td align="right" style ="font-size: larger; color: #e33a3a;"><?=number_format($suamt)?></td>
								<td align="center" style ="font-size: larger; color: #e33a3a;"><?=$itemnm?></td>

								<td ></td>
								<td ></td>
							<?}?>	

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
				<ul class="pagination pagination-sm" style="margin: 10px">
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


 
$(document).ready(function(){
		var swon	= $("#swon").val();
		var yymm	= $("#yymm").val(); 
		var ser_con	= $("#ser_con").val(""); 
		var page	= $("#page").val(); 
 


	// 조회
	$(".btn_search").click(function(){
		var swon	= $("#swon").val();
		var yymm	= $("#yymm").val(); 
		var ser_con	= $("#ser_con").val(""); 
		var page	= $("#page").val(); 

		//alert(upldate);
		//alert(uplnum);
		$("form[name='searchFrm']").attr("method","get");
		$("form[name='searchFrm']").attr("target","");

		$("form[name='searchFrm'] input[name='swon']").val(swon);
		$("form[name='searchFrm'] input[name='yymm']").val(yymm);
		$("form[name='searchFrm'] input[name='ser_con']").val("");
		$("form[name='searchFrm'] input[name='page']").val(page);
		$("form[name='searchFrm']").submit();
	}); 


	// 리스트 클릭시 상세내용 조회(원수사 오리지널 )
	$(".rowData > td").click(function(){
		var trData = $(this).parent();
		var idx    = $(".rowData").index($(trData));

		var oridata  = $(".rowData").eq(idx).attr('rol-data'); 
		var filename  = $(".rowData").eq(idx).attr('rol-file'); 
		var iseq  = $(".rowData").eq(idx).attr('rol-iseq'); 
	
		
		var seq  = $(".rowData").eq(idx).attr('rol-seq'); 
 		var swon	= $("#swon").val();
		var yymm	= $("#yymm").val(); 
					
		if (seq == "1"){
			ins_display(oridata,filename,iseq); 
		}else{ 
			var popOpen	= window.open("<?=$conf['homeDir']?>/sub/menu3/ga_menu3_74_list3.php?swon="+swon +"&yymm=" +yymm, '_blank');
			popOpen.focus(); 
		}
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