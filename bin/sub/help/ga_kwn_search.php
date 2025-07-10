<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

$page = ($_GET['page']) ? $_GET['page'] : 1;
$page_row	= $conf['pageRow']; // 페이지당 보여줄 rows수  기본 conf 25줄
$page_row	= "20"; // 페이지당 보여줄 수를 20개로 수정할경우 이런식으로 하면 됨

$limit1 = ($page-1)*$page_row+1;
$limit2 = $page*$page_row;

$where = "";

if($_GET['searchF1'] && $_GET['searchF1Text']){
	if($_GET['searchF1'] == 'sjuno'){
		$where  .= " and (a.snum like '%".$_GET['searchF1Text']."%' or Cast(dbo.DECRYPTKEY(a.sjuno) as varchar) like '%".$_GET['searchF1Text']."%') ";
	}else if($_GET['searchF1'] == 'tel'){
		$where  .= " and (a.tel like replace('%".$_GET['searchF1Text']."%','-','') or a.htel like replace('%".$_GET['searchF1Text']."%','-','')) ";
	}else if($_GET['skey'] && $_GET['searchF1'] == 's1'){	//	모집사원
		$where  .= " and a.gskey = '".$_GET['skey']."' ";	
	}else if($_GET['skey'] && $_GET['searchF1'] == 's2'){	//	관리사원
		$where  .= " and a.kskey = '".$_GET['skey']."' ";	
	}else{		
		$where  .= " and ".$_GET['searchF1']." like '%".$_GET['searchF1Text']."%' ";	
	}
}

$sql= "
	select *
	from(
		select 
				a.kcode,
				a.insilj,
				a.inscode,
				f.name insname,
				case when isnull(a.bonbu,'') != '' then b.bname else '' end +
				case when isnull(a.bonbu,'') != '' and (isnull(a.jisa,'') != '' or isnull(a.team,'') != '')  then ' > ' else '' end +
				case when isnull(a.jisa,'') != '' then c.jsname else '' end +
				case when isnull(a.jisa,'') != '' and isnull(a.team,'') != '' then ' > ' else '' end +
				case when isnull(a.team,'') != '' then e.tname else '' end as sosok,
				s1.sname gskey_nm,
				s2.sname kskey_nm,
				a.kname,
				case when isnull(a.htel,'') != '' then a.htel else a.tel end telno,
				a.pname,
				a.kdate,
				a.item,
				a.itemnm,
				a.mamt,
				a.hamt,
				a.kstbit,
				a.nbit,
				row_number()over(order by a.kdate desc, f.name, a.kname) rnum
		from kwn a	
			left outer join bonbu b on a.scode = b.scode and a.bonbu = b.bcode
			left outer join jisa c on a.scode = c.scode and a.jisa = c.jscode
			left outer join team e on a.scode = e.scode and a.team = e.tcode
			left outer join inssetup f on a.scode = f.scode and a.inscode = f.inscode
			left outer join swon s1 on s1.scode = a.scode and s1.skey = a.gskey
			left outer join swon s2 on s2.scode = a.scode and s2.skey = a.kskey
		where a.scode = '".$_SESSION['S_SCODE']."' ".$where."
		) p
	where rnum between ".$limit1." AND ".$limit2 ;

$result	= sqlsrv_query( $mscon, $sql );
$listData = array();
while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
	$listData[]	= $row;
}

// 데이터 총 건수
//검색 데이터 구하기 
$sql= "
		select 
				count(*) cnt
		from kwn a
			left outer join bonbu b on a.scode = b.scode and a.bonbu = b.bcode
			left outer join jisa c on a.scode = c.scode and a.jisa = c.jscode
			left outer join team e on a.scode = e.scode and a.team = e.tcode
			left outer join inssetup f on a.scode = f.scode and a.inscode = f.inscode
			left outer join swon s1 on s1.scode = a.scode and s1.skey = a.gskey
			left outer join swon s2 on s2.scode = a.scode and s2.skey = a.kskey
		where a.scode = '".$_SESSION['S_SCODE']."' ".$where." " ;

$qry =  sqlsrv_query($mscon, $sql);
$totalResult =  sqlsrv_fetch_array($qry); 
$totalpage = ceil($totalResult['cnt'] / $page_row);

// 페이지 클래스 시작
include_once($conf['rootDir'].'/include/class/pagination.php');

// 설정
$pagination = new Pagination(array(
		'base_url' => $_SERVER['PHP_SELF']."?searchF1=".$_GET['searchF1']."&searchF1Text=".$_GET['searchF1Text']."&skey=".$_GET['skey'],
		'per_page' => $page_row,
		'total_rows' => $totalResult['cnt'],
		'cur_page' => $page,
));

?>
<style>
body{background-image: none;}
.box_wrap{margin-bottom:0px;padding:15px 20px;}
.tb_type01 th, .tb_type01 td {padding: 4px 0;}

.srch_css{
	width:125px;
	margin-left:5px;
	height:24px;
	cursor:pointer;
}

</style>

<div class="box_wrap sel_btn">
	<form name="searchFrmHelp" method="get" action="<?$_SERVER['PHP_SELF']?>">
		<input type="hidden" name='row' value='<?=$_GET['row']?>'>

		<select name="searchF1" id="searchF1" class="srch_css" onchange="fn_srch(this.value);">
			<option value="kname" <?if($_GET['searchF1']=="kname") echo "selected"?>>계약자명</option>
			<option value="kcode" <?if($_GET['searchF1']=="kcode") echo "selected"?>>증권번호</option>
			<option value="sjuno"   <?if($_GET['searchF1']=="sjuno") echo "selected"?>>주민/사업자번호</option>
			<option value="s1"   <?if($_GET['searchF1']=="s1") echo "selected"?>>모집사원</option>
			<option value="s2"   <?if($_GET['searchF1']=="s2") echo "selected"?>>관리사원</option>
			<option value="tel"   <?if($_GET['searchF1']=="tel") echo "selected"?>>연락처</option>
		</select>

		<input type="hidden" name="skey" id="skey" value="<?=$_GET['skey']?>">
		<input type="hidden" name="ser_kcode" id="ser_kcode" value="<?=$_GET['ser_kcode']?>">
		<input type="text" name="searchF1Text" id="searchF1Text" class="srch_css" style="height:20px"" value="<?=$_GET['searchF1Text']?>" onkeyup="enterkey()">

		<a href="#" class="btn_s white" id="SearchBtn">검색</a>
		<a href="#" class="btn_s white" onclick="self.close();">닫기</a>

    </form>
</div>

<div class="tit_wrap" style="padding:0 20px">
	<div class="tb_type01 tb_fix">
		<table class="gridhover">
			<colgroup>
				<col width="10%">
				<col width="10%">
				<col width="13%">
				<col width="10%">
				<col width="9%">
				<col width="8%">
				<col width="9%">
				<col width="9%">
				<col width="22%">
			</colgroup>
			<thead>
			<tr>	
				<th align="center">증권번호</th>
				<th align="center">계약자</th>
				<th align="center">보험사</th>
				<th align="center">보험구분</th>	
				<th align="center">계약일자</th>
				<th align="center">계약상태</th>
				<th align="center">모집사원</th>
				<th align="center">보험료</th>
				<th align="center">상품명</th>
			</tr>
			</thead>

			<tbody>
				<?if(!empty($listData)){?>
				<?foreach($listData as $key => $val){extract($val);?>
				<tr class="rowData_help" rol-data1='<?=$kcode?>' rol-data2='<?=$kname?>'>	
					<td align="left"><?=$kcode?></td>
					<td align="center"><?=$kname?></td>
					<td align="center"><?=$insname?></td>
					<td align="center"><?=$conf['insilj'][$insilj]?></td>
					<td align="center"><?if(trim($kdate)) echo date("Y-m-d",strtotime($kdate))?></td>
					<td align="center"><?=$conf['kstbit'][$kstbit]?></td>
					<td align="center"><?=$gskey_nm?></td>
					<td align="right" class="font_blue"><?=number_format($mamt)?></td>
					<td align="left" style="width:250px" class="textover" title="<?=$itemnm?>"><?=$itemnm?></td>				
				</tr>
				<?}}else{?>
					<tr>
						<td style="color:#8C8C8C" colspan=14>검색된 데이터가 없습니다</td>
					</tr>
				<?}?>
			</tbody>
		</table>
	</div>
</div>

<div style="text-align: center">		
	<ul class="pagination pagination-sm" style="margin: 10px">
	  <?=$pagination->create_links();?>
	</ul>
</div>	


<script type="text/javascript">

	window.resizeTo("1200", "800");                             // 윈도우 리사이즈

$(document).ready(function(){
	$("input[name='searchF1Text']").focus();

	$("#SearchBtn").on("click", function(){	
		$("form[name='searchFrmHelp']").submit();
	});

	$(".rowData_help").click(function(){
		var idx			= $(".rowData_help").index($(this));
		var kcode		= $(".rowData_help").eq(idx).attr("rol-data1");
		var kname		= $(".rowData_help").eq(idx).attr("rol-data2");

		var row = "<?=$_GET['row']?>";

		opener.setKwnValue(row,kcode,kname);
		self.close();

	});

});

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>