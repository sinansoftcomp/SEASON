<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");


$srchText	=	str_replace("-","",$_GET['srchText']);

$sql	= "select * 
			from inswon a
			where a.scode = '".$_SESSION['S_SCODE']."'
			  and a.skey = '".$_GET['skey']."'
			order by num"  ;


$result	= sqlsrv_query( $mscon, $sql );
$listData = array();
while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
	$listData[]	= $row;
}

$sql	= "Select  Count(*) cnt
			From swon
			Where scode = '".$_SESSION['S_SCODE']."'
			  and (ltrim(scode) Like '".$_GET['srchText']."%'  or ltrim(sname)  Like '%".$srchText."%')";

$qry =  sqlsrv_query($mscon, $sql);
$totalResult =  sqlsrv_fetch_array($qry); 
$totalpage = ceil($totalResult['cnt'] / $page_row);



?>
<style>
body{background-image: none;}
.box_wrap{margin-bottom:0px}
.tb_type01 th, .tb_type01 td {padding: 4px 0;}
</style>

<div class="box_wrap sel_btn">
	<form name="searchFrm" method="get" action="<?$_SERVER['PHP_SELF']?>">
		<input type="hidden" name='row' value='<?=$_GET['row']?>'>

		<div class="tit_wrap" style="margin-top:0px">
			<h2 class="tit_sub">원수사 정보등록</h2>
			<span class="btn_wrap">
				<a href="#" class="btn_s navy" onclick="SwonInsData('<?=$_GET['skey']?>','');">등록</a>
				<a href="#" class="btn_s white" onclick="self.close();">닫기</a>
			</span>
		</div>

    </form>
</div>

<div class="tit_wrap" style="padding:0 10px">
	<div class="tb_type01 tb_fix">
		<table class="gridhover">
			<colgroup>
				<col width="10%">
				<col width="15%">
				<col width="auto">
				<col width="10%">
				<col width="15%">
			</colgroup>
			<thead>
			<tr>
				<th>사원번호</th>
				<th>사원명</th>
				<th>소속정보</th>
				<th>직위</th>
				<th>연락처</th>
			</tr>
			</thead>
			<tbody>
			<?if(!empty($listData)){?>
			<?foreach($listData as $key => $val){extract($val);?>
			<tr rol-data1='<?=$skey?>' rol-data2='<?=$sname?>' class="rowData"  >
				<td><?=$skey?></td>
				<td><?=$sname?></td>
				<td align="left"><?=$sosok?></td>
				<td><?=$conf['jik'][$jik]?></td>
				<td><?=$htel?></td>
			</tr>
			<?}}?>
			</tbody>
		</table>
	</div>
</div>



<script type="text/javascript">

window.resizeTo("700", "580");                             // 윈도우 리사이즈


// 원수사정보 등록
function SwonInsData(skey, code){

	var left = Math.ceil((window.screen.width - 400)/2);
	var top = Math.ceil((window.screen.height - 320)/2);
	var popOpen	= window.open("<?=$conf['homeDir']?>/sub/help/ga_swonins_in.php?skey="+skey+"&code="+code,"swoninsData","width=500px,height=400px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
	popOpen.focus();
}

$(document).ready(function(){
	$("input[name='srchText']").focus();

	$("#SearchBtn").on("click", function(){	
		$("form[name='searchFrm']").submit();
	});


});

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>