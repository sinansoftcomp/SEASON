<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

$page = ($_GET['page']) ? $_GET['page'] : 1;
$page_row	= $conf['pageRow']; // 페이지당 보여줄 rows수  기본 conf 25줄
$page_row	= "20"; // 페이지당 보여줄 수를 20개로 수정할경우 이런식으로 하면 됨


$limit1 = ($page-1)*$page_row+1;
$limit2 = $page*$page_row;


$arrdata	=	$_GET['arrdata'];
$arrdatanm	=	$_GET['arrdatanm'];

$srchText	=	str_replace("-","",$_GET['srchText']);

$sql	= "Select rnum,
				  *
			From (select a.skey,
						a.sname,
						case when isnull(a.bonbu,'') != '' then substring(b.bname,1,2) else '' end +
						case when isnull(a.bonbu,'') != '' and (isnull(a.jisa,'') != '' or isnull(a.team,'') != '')  then ' > ' else '' end +
						case when isnull(a.jisa,'') != '' then substring(c.jsname,1,4) else '' end +
						case when isnull(a.jisa,'') != '' and isnull(a.jijum,'') != '' then ' > ' else '' end +
						case when isnull(a.jijum,'') != '' then substring(g.jname,1,4) else '' end +
						case when isnull(a.jijum,'') != '' and isnull(a.team,'') != '' then ' > ' else '' end +
						case when isnull(a.team,'') != '' then e.tname else '' end as sosok,
						a.jik,
						a.htel1+'-'+a.htel2+'-'+a.htel3 as htel,
						row_number() over (order by a.skey) as rnum
					from swon a
						left outer join bonbu b on a.scode = b.scode and a.bonbu = b.bcode
						left outer join jisa c on a.scode = c.scode and a.jisa = c.jscode
						left outer join jijum g on a.scode = g.scode and a.jijum = g.jcode
						left outer join team e on a.scode = e.scode and a.team = e.tcode
					Where a.scode = '".$_SESSION['S_SCODE']."'
					  and (ltrim(skey) Like '%".$_GET['srchText']."%'  or ltrim(sname)  Like '%".$srchText."%')
				 ) P
			WHERE rnum between ".$limit1." AND ".$limit2  ;


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

// 페이지 클래스 시작
include_once($conf['rootDir'].'/include/class/pagination.php');

// 설정
$pagination = new Pagination(array(
		'base_url' => $_SERVER['PHP_SELF']."?srchText=".$_GET['srchText']."&arrdata=".$_GET['arrdata']."&arrdatanm=".$_GET['arrdatanm'],
		'per_page' => $page_row,
		'total_rows' => $totalResult['cnt'],
		'cur_page' => $page,
));


//$aa = "<script>document.write (data11);</script>" ;

?>


<div class="tit_wrap" style="padding:0 10px">
	<div class="tb_type01 tb_fix">
		<table class="gridhover">
			<colgroup>
				<col width="5%">
				<col width="10%">
				<col width="12%">
				<col width="auto">
				<col width="10%">
				<col width="13%">
			</colgroup>
			<thead>
			<tr>
				<th>선택</th>
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
				<td><input type="checkbox" name="select" class="select" value="<?=$skey?>"></td>
				<td align="left"><?=$skey?></td>
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


<div style="text-align: center">		
	<ul class="pagination pagination-sm swonlist" style="margin: 10px">
	  <?=$pagination->create_links();?>
	</ul>
</div>	



<script type="text/javascript">

// page 함수 ajax페이지 존재시 별도 처리
$(".swonlist a").click(function(){
	$('#page').val('<?=$page?>');
	var res = $(this).attr("href").split("?");
	if(res[0] && res[1]){
		//alert(res[0]+"//"+res[1]);
		 //data_right_jojik div id값 적용
		ajaxLodingTarget(res[0],res[1],event,$('#swonlist'));    
	}
	return false;
});


// 페이지이동 및 검색처리시에도 이전 페이지 돌아가면 선택된값 체크되어 보여주기
function checkbox_sel(){
	var arr	= $("#arrdata").val().split(",");

	var cnt = arr.length;

	for(var i=0; i<cnt; i++){
		$('input:checkbox[name=select]').each(function(){
			if(this.value == arr[i]){
				this.checked = true;
			}
		})
	}
}


$(document).ready(function(){


	checkbox_sel();

	// 체크박스 선택시 사원번호&사원명 인풋박스 추가/빼기
	$(".rowData").click(function() {
		var idx=$(".rowData").index($(this));
		var comma	= "";
		var code	= $(".rowData").eq(idx).attr("rol-data1");
		var name	= $(".rowData").eq(idx).attr("rol-data2");
		var arrdata = "";
		var arrdatanm = "";

		var arr		= "";
		var arr2	= "";

		if($(".rowData .select").eq(idx).prop('checked')==true) {			// 체크박스 선택시
			
			arrdata		= $("#arrdata").val();
			arrdatanm	= $("#arrdatanm").val();

			if(arrdata.length > 0) comma=",";

			arrdata		= arrdata + comma + code;		// 사원번호 배열
			arrdatanm	= arrdatanm + comma + name;		// 사원명 배열

			$("#arrdata").val(arrdata);
			$("#arrdatanm").val(arrdatanm);

	
		}else if($(".rowData .select").eq(idx).prop('checked')==false) {	// 체크해제 시 

			arr	= $("#arrdata").val().split(",");
			arr2= $("#arrdatanm").val().split(",");

			//console.log(arr);

			// 사원번호
			for(var i in arr) if ( arr[i]==code) arr.splice(i, 1);	
			$("#arrdata").val(arr.join(","));		// 사원번호 배열

			// 사원명
			for(var i in arr2) if ( arr2[i]==name) arr2.splice(i, 1);
			$("#arrdatanm").val(arr2.join(","));	// 사원명 배열	
			
		} // End if

	});

});
</script>
