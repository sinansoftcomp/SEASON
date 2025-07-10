<?
//error_reporting(E_ALL); ini_set('display_errors', 1);
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


//$_SESSION['S_MASTER'];

$where = "";
if(!$_GET['SDATE1']){
	$sdate1 =  date('Y-m-d',strtotime(date("Y-m-d")."-3 year"));
	$lastday = DATE('t', strtotime($sdate1));
	$sdate2 =  date("Y-m-".$lastday);
}else{
	$sdate1 = substr($_GET['SDATE1'],0,4)."-".substr($_GET['SDATE1'],5,2)."-".substr($_GET['SDATE1'],8,2);
	$sdate2 = substr($_GET['SDATE2'],0,4)."-".substr($_GET['SDATE2'],5,2)."-".substr($_GET['SDATE2'],8,2);
}

if(!empty($_GET['SDATE1'])){
	$sdate1w = substr($_GET['SDATE1'],0,4).substr($_GET['SDATE1'],5,2).substr($_GET['SDATE1'],8,2);
	$sdate2w = substr($_GET['SDATE2'],0,4).substr($_GET['SDATE2'],5,2).substr($_GET['SDATE2'],8,2);
	$where .= " and convert(varchar(8),a.idate,112) between '".$sdate1w."' and '".$sdate2w."' ";
}
if(!empty($_GET['gubun'])){
	$where .= " and a.gubun = '".$_GET['gubun']."'";
}
if(!empty($_GET['searchF1Text'])){
	$where .= " and a.title like '%".$_GET['searchF1Text']."%' ";
}


/* ------------------------------------------------------------
	End Date 초기값 세팅
------------------------------------------------------------ */

// 기본 페이지 셋팅
$page = ($_GET['page']) ? $_GET['page'] : 1;
$page_row	= 35;

$limit1 = ($page-1)*$page_row+1;
$limit2 = $page*$page_row;


$sql = "
	select *
	from(
		select a.scode,a.pid,a.gubun, a.recv,recvnm,
			   a.title,a.msg,a.jocnt,a.topsort, a.color,
			   convert(varchar(8),a.idate,112) idate,a.iswon,a.udate,a.uswon,iswon.sname,
			   case when datediff(day, a.idate, convert(varchar,getdate(),112)) <= 5 then 'Y' else 'N' end as newbit,
				datediff(hour,a.idate,getdate()) ntime , row_number() over(order by topsort desc , a.idate desc) rnum
		from postlist a 			
			left outer join bonbu b on a.scode = b.scode and a.recv = b.bcode
			left outer join jisa js on a.scode = js.scode and a.recv = js.jscode
			left outer join jijum j on a.scode = j.scode and a.recv = j.jcode
			left outer join team t on a.scode = t.scode and a.recv = t.tcode
			left outer join swon s on a.scode = s.scode and a.recv = s.skey
			left outer join swon iswon on a.scode = iswon.scode and a.iswon = iswon.skey
		where a.scode = '".$_SESSION['S_SCODE']."' $where
		) p
	where rnum between ".$limit1." AND ".$limit2 ;		

$qry	= sqlsrv_query( $mscon, $sql );
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
		select count(*) CNT
		from postlist a
		where scode = '".$_SESSION['S_SCODE']."' $where
	";

$qry = sqlsrv_query( $mscon, $sql );
$totalResult  = sqlsrv_fetch_array($qry);


sqlsrv_free_stmt($result);
sqlsrv_close($mscon);

include_once($conf['rootDir'].'/include/class/Pagination.php');

// 설정
$pagination = new Pagination(array(
		'base_url' => $_SERVER['PHP_SELF']."?SDATE1=".$_GET['SDATE1']."&SDATE2=".$_GET['SDATE2']."&gubun=".$_GET['gubun']."&searchF1Text=".$_GET['searchF1Text'],
		'per_page' => $page_row,
		'total_rows' => $totalResult['CNT'],
		'cur_page' => $page,
));

?>

<!-- html영역 -->
<style>
body{background-image: none;}

/* 공지 */
.new {color:#F15F5F; padding-left:5px;font-size:10px;line-height:20px;}
@keyframes blink-effect {
  50% {opacity: 0;}
}

.blink {animation: blink-effect 1s step-end infinite;}
.list_plus{float:right;font-size:14px;line-height:20px;margin-right:10px;margin-top:3px;}
.list_plus:hover{cursor: pointer;}
#gongjilist{overflow-y:auto;padding:0 10px;font-size:14px;height:94vh;}
</style>

<div class="container">
	<div class="content_wrap">
		<fieldset>

			<div class="box_wrap sel_btn">
				<form name="searchFrm" id="searchFrm" method="get" action="<?$_SERVER['PHP_SELF']?>">
					<span class="ser_font">작성일자</span> 
					<span class="input_type date ml10" style="width:114px;margin-left: 0px;">
						<input type="text" class="Calnew" placeholder="YYYY-MM-DD" id="SDATE1" name="SDATE1" value="<?=$sdate1?>" readonly>
					</span> 
					<span class="dash"> ~ </span>
					<span class="input_type date" style="width:114px">
						<input type="text" class="Calnew" placeholder="YYYY-MM-DD" id="SDATE2" name="SDATE2" value="<?=$sdate2?>" readonly>
					</span>

					<select name="gubun" id="gubun"style="width:150px;FONT-SIZE: 14px;"> 
						<option value="">구분</option>
						<?foreach($conf['post_gubun'] as $key => $val){?>
						<option value="<?=$key?>" <?if($_GET['gubun']==$key) echo "selected"?>><?=$val?></option>
						<?}?>
					</select>	

					<span  class="ser_font" style="margin-left:10px">제목</span> 
					<input type="text" name="searchF1Text" id="searchF1Text" style="width:300px;border:1px solid #b7b7b7;height:24px;" value="<?=$_GET['searchF1Text']?>" >

					<span class="btn_wrap" style="margin-left: 10px;">				
						<a class="btn_s white btn_search btn_off"  style="margin: 0; min-width:100px;">조회</a>
						<a class="btn_s white btn_off"  style="margin: 0; min-width:100px;" onclick="post_insert();">글쓰기</a>
					</span>	 
				</form>
			</div>

			<div class="tb_type01 kwndatalist div_grid" style="overflow-y:auto;">	
				<table class="gridhover">
					<colgroup>					
						<col style="width:11%">
						<col style="width:15%">					
						<col style="width:auto">
						<col style="width:10%">
						<col style="width:8%">
						<col style="width:7%">
					</colgroup>
					<thead>
					<tr>			
						<th>구분</th>	
						<th>대상</th>						
						<th>제목</th>	
						<th>작성자</th>
						<th>작성일</th>
						<th>조회수</th>
					</tr>
					</thead>
					<tbody>
					<?if(!empty($listData)){?>
					<?foreach($listData as $key => $val){extract($val);
						if($color == '2'){
							$style_color = 'color:#f9650e;';
						}else if($color == '3'){
							$style_color = 'color:#1266FF;';
						}else if($color == '4'){
							$style_color = 'color:#f9b300;';
						}else if($color == '5'){
							$style_color = 'color:#8041D9;';
						}else if($color == '6'){
							$style_color = 'color:#2F9D27;';
						}else{
							$style_color = 'color:#333;';
						}	
						
						if($topsort == 'Y'){
							$style_color .= 'font-weight:700;';
						}else{
							$style_color .= 'font-weight:500;';
						}
					?>
					<tr class="rowData" rol-date1='<?=$pid?>' >						
						<td style="text-align:center"><?=$conf['post_gubun'][$gubun]?></td>
						<td style="text-align:left"><?=$recvnm?></td>
						<td style="text-align:left;<?=$style_color?>"><?=$title?><?if($newbit == 'Y'){?><span class="new blink">New!</span><?}?></td>
						<td style="text-align:left"><?=$sname?> (<?=$iswon?>)</td>
						<td style="text-align:center"><?=date("Y-m-d",strtotime($idate))?></td>		
						<td><?=$jocnt?></td>
					</tr>
					<?}}?>
					</tbody>
				</table>
			</div>
			<div style="text-align: center">		
				<ul class="pagination pagination-sm itemlist" style="margin: 5px 5px 0 5px">
				  <?=$pagination->create_links();?>
				</ul>
			</div>
		</fieldset>
	</div><!-- // content_wrap -->
</div>
<!-- // container -->
<!-- // wrap -->
<script type="text/javascript">

function post_insert(){

	if('<?=$_SESSION['S_MASTER']?>' == 'A' || '<?=$_SESSION['S_JIK']?>' == '5001' || '<?=$_SESSION['S_JIK']?>' == '4001' || '<?=$_SESSION['S_JIK']?>' == '3001' || '<?=$_SESSION['S_JIK']?>' == '2001'){

		var left = Math.ceil((window.screen.width - 800)/2);
		var top = Math.ceil((window.screen.height - 500)/2);
		var popOpen	= window.open("<?=$conf['homeDir']?>/sub/menu7/ga_menu7_06_write.php","postpop","width=800px,height=500px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
		popOpen.focus();
	}else{
		alert('관리자 및 본부장/지사장/지점장/팀장만 작성이 가능합니다.');
	}
}

function post_read(pid){
	var left = Math.ceil((window.screen.width - 800)/2);
	var top = Math.ceil((window.screen.height - 500)/2);
	var popOpen	= window.open("<?=$conf['homeDir']?>/sub/menu7/ga_menu7_06_read.php?pid="+pid,"postpop","width=800px,height=500px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
	popOpen.focus();
}

$(document).ready(function(){	
	// 조회
	$(".btn_search").click(function(){
		$("form[name='searchFrm']").attr("method","get");
		$("form[name='searchFrm']").attr("target","");
		$("form[name='searchFrm']").submit();
	});   

	$(".rowData > td").click(function(){
		var trData = $(this).parent();
		var idx    = $(".rowData").index($(trData));
		var pid  = $(".rowData").eq(idx).attr('rol-date1'); 
		post_read(pid);

	})
});

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>