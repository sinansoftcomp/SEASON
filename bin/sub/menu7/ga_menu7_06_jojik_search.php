<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");



// 알림장 대상 찾기위한 조건(산하 조회)
if($_GET['post']=="Y"){
	$jik	=	$_SESSION['S_JIK'];		// 영업직위
	$master	=	$_SESSION['S_MASTER'];	// 관리자여부


	if($master != 'A'){
		if($jik == '5001'){
			$where = " where bcode = '".$_SESSION['S_BONBU']."' and level = '".$_GET['level']."' " ;
		}else if($jik == '4001'){
			$where = " where jscode = '".$_SESSION['S_JISA']."' and level = '".$_GET['level']."' " ;
		}else if($jik == '3001'){
			$where = " where jcode = '".$_SESSION['S_JIJUM']."' and level = '".$_GET['level']."' " ;
		}else if($jik == '2001'){
			$where = " where tcode = '".$_SESSION['S_TEAM']."' and level = '".$_GET['level']."' " ;
		}
	}else{
		$where = " where level = '".$_GET['level']."' " ;
	}
	
}


$sql	= "
			select bcode, substring(bname,1,6) bname, jscode, jsname,  jcode, jname, tcode, tname, num, level
			from(
				select bcode, bname, '' jscode, '' jsname, '' jcode, '' jname, '' tcode, '' tname, num, '1' level from bonbu
				where scode = '".$_SESSION['S_SCODE']."'

				union all

				select b.bcode, b.bname, a.jscode, a.jsname, '' jcode, '' jname, '' tcode, '' tname, a.num, '2' level 
				from jisa a
					left outer join bonbu b on a.scode = b.scode and a.upcode = b.bcode
				where a.scode = '".$_SESSION['S_SCODE']."'

				union all

				select c.bcode, c.bname, b.jscode, b.jsname, a.jcode, a.jname, '' tcode, '' tname,  a.num, '3' level 
				from jijum a
					left outer join jisa b on a.scode = b.scode and a.upcode = b.jscode
					left outer join bonbu c on a.scode = c.scode and b.upcode = c.bcode
				where a.scode = '".$_SESSION['S_SCODE']."'

				union all

				select d.bcode, d.bname, c.jscode, c.jsname, b.jcode, b.jname, a.tcode, a.tname, a.num, '4' level 
				from team a
					left outer join jijum b on a.scode = b.scode and a.upcode = b.jcode
					left outer join jisa c on a.scode = c.scode and a.upcode = c.jscode
					left outer join bonbu d on a.scode = d.scode and b.upcode = d.bcode
				where a.scode = '".$_SESSION['S_SCODE']."'
			) tbl ".$where."
			order by level, num
";


$result	= sqlsrv_query( $mscon, $sql );
$listData = array();
while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
	$listData[]	= $row;
}



?>
<style>
body{background-image: none;}
.box_wrap{margin-bottom:0px}
.tb_type01 th, .tb_type01 td {padding: 4px 0;}
</style>

<div class="box_wrap sel_btn">
	<form name="searchFrm" method="get" action="<?$_SERVER['PHP_SELF']?>">
		<a href="#" class="btn_s white" id="SearchBtn">검색</a>
		<a href="#" class="btn_s white" onclick="self.close();">닫기</a>

    </form>
</div>

<div class="tit_wrap" style="padding:0 10px">
	<div class="tb_type01 tb_fix">
		<table class="gridhover">
			<colgroup>
				<col width="25%">
				<col width="25%">
				<col width="25%">
				<col width="5%">
				<col width="20%">
			</colgroup>
			<thead>
			<tr>
				<th>본부</th>
				<th>지사</th>
				<th>지점</th>
				<th>팀</th>
				<th>조직레벨</th>
			</tr>
			</thead>
			<tbody>
			<?if(!empty($listData)){?>
			<?foreach($listData as $key => $val){extract($val);?>
			<tr rol-data1='<?=$bcode?>' rol-data2='<?=$bname?>' rol-data3='<?=$jscode?>' rol-data4='<?=$jsname?>' 
				rol-data5='<?=$jcode?>' rol-data6='<?=$jname?>' rol-data7='<?=$tcode?>' rol-data8='<?=$tname?>'
				rol-data9='<?=$level?>' 
				class="rowData"  >
				<td align="left"><?=$bname?></td>
				<td align="left"><?=$jsname?></td>
				<td align="left"><?=$jname?></td>
				<td align="left"><?=$tname?></td>
				<td align="center"><?=$conf['jojiklevel'][$level]?></td>
			</tr>
			<?}}?>
			</tbody>
		</table>
	</div>
</div>


<script type="text/javascript">

	//window.resizeTo("600", "780");                             // 윈도우 리사이즈

$(document).ready(function(){

	$(".rowData").click(function(){
		var idx=$(".rowData").index($(this));
		var bcode = $(".rowData").eq(idx).attr("rol-data1");
		var bname = $(".rowData").eq(idx).attr("rol-data2");
		var jscode = $(".rowData").eq(idx).attr("rol-data3");
		var jsname = $(".rowData").eq(idx).attr("rol-data4");
		var jcode = $(".rowData").eq(idx).attr("rol-data5");
		var jname = $(".rowData").eq(idx).attr("rol-data6");
		var tcode = $(".rowData").eq(idx).attr("rol-data7");
		var tname = $(".rowData").eq(idx).attr("rol-data8");
		var level = $(".rowData").eq(idx).attr("rol-data9");

		var code = '', name = ''

		if(level == '1'){
			code = bcode;
			name = bname;
		}else if(level == '2'){
			code = jscode;
			name = jsname;
		}else if(level == '3'){
			code = jcode;
			name = jname;
		}else if(level == '4'){
			code = tcode;
			name = tname;
		}

		opener.setrecvValue(code,name,level);
		self.close();

	});

});

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>