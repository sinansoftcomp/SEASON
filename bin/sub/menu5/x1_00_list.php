<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

// �������. ���Ŀ� COMPANY���̺��� �����ð�.
$X = "X1";

$where = " ";
// ������ Ʈ�� ���ý� �Ҽ�����(swon ��Ī : s2 - kdman(����α���)) 
 
if($_REQUEST['id']){
	$Ngubun           = substr($_REQUEST['id'],0,2);
	if($Ngubun == 'N2'){
		$jik = substr($_REQUEST['id'],2,10);
			$where .= " and a.jik = '".$jik."'";
	}
}

$search_swon = iconv("UTF-8","EUCKR",$_REQUEST['searchF2Text']); 
$search_swon= preg_replace("/\s+/", "", $search_swon);  

 
if($_REQUEST['insilj']){
	$where .= " and a.insilj = '".$_REQUEST['insilj']."' ";
}
if($_REQUEST['code']){
	$where .= " and a.inscode = '".$_REQUEST['code']."'";
}

// �⺻ ������ ����
$page = ($_REQUEST['page']) ? $_REQUEST['page'] : 1;
$page_row	= 100;
$limit1 = ($page-1)*$page_row+1;
$limit2 = $page*$page_row;


for($i=1;$i<=100;$i++){
	//$select .= "jiyul".$i." ,";
	$select .= "isnull(jiyul".$i.",0.000) jiyul".$i." ,";
}

//�˻� ������ ���ϱ� 
$sql= "
	select * 
	from(
		select *, ROW_NUMBER()over(order by jik,inscode,insilj,seq) rnum 
		from(
			select a.jik, a.inscode,a.insilj,a.seq,a.jsyymm,a.jeyymm , h.subnm,
					".$select."
					case when a.inscode = '00000' then '����' else g.name end insname 
			from x1_jirule(nolock) a  
							left outer join INSSETUP(nolock) g on  a.scode = g.scode and a.inscode = g.inscode
							left outer join common(nolock) h  on a.scode = h.scode and h.CODE = 'COM006' and  a.jik = h.CODESUB	
			where a.scode = '".$_SESSION['S_SCODE']."' ".$where."
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

// ������ �� �Ǽ�
$sql= "
		select count(*) CNT 
		from x1_jirule(nolock) a  
		where a.scode = '".$_SESSION['S_SCODE']."' ".$where."
		" ;
$qry =  sqlsrv_query($mscon, $sql);
$totalResult =  sqlsrv_fetch_array($qry); 
$cnt = $totalResult['CNT'];

sqlsrv_free_stmt($qry);
sqlsrv_close($mscon);

// ������ Ŭ���� ����
// �ε�
include_once($conf['rootDir'].'/include/class/Pagination.php');

// ����
$pagination = new Pagination(array(
		'base_url' => $_SERVER['PHP_SELF']."?insilj=".$_REQUEST['insilj']."&id=".$_REQUEST['id']."&page=Y",
		'per_page' => $page_row,
		'total_rows' => $totalResult['CNT'],
		'cur_page' => $page,
));

?>

<!-- html���� -->
<style>
body{background-image: none;}

</style>


<div class="tb_type01 kwndatalist div_grid" style="overflow-y:auto;">	
		<input type="hidden" name="type" id="type" value="">
		<input type="hidden" name="skey_del" id="skey_del" value="">
		<input type="hidden" name="inscode_del" id="inscode_del" value="">
		<input type="hidden" name="insilj_del" id="insilj_del" value="">
		<input type="hidden" name="seq_del" id="seq_del" value="">
		<table  id="sort_table" class="gridhover" id="grid"  style="width: 8500px;">
			<colgroup>
				<col width="90px">
				<col width="220px">						
				<col width="80px">											
				<col width="90px">
				<col width="150px">
				<col width="90px">
				<col width="100px">
				<col width="100px">
				<?for($i=1;$i<=100;$i++){?>
				<col width="auto">
				<?}?>
			</colgroup>
			<thead>
			<tr class="rowTop">
				
				<th align="center">����</th>
				
				
				<th align="center">������</th>
								<th align="center">��ǰ����</th>
				<th align="center">��������</th>
				<th align="center">��������</th>
				<?for($i=1;$i<=100;$i++){?>
				<th align="center"><?=$i?>ȸ��</th>
				<?}?>						
			</tr>
			</thead>
			<tbody>
				<?if(!empty($listData)){?>
				<?foreach($listData as $key => $val){extract($val);?>
				<tr class="rowData" data1='<?=$jik?>' data2='<?=$inscode?>' data3='<?=$insilj?>' data4='<?=$seq?>'>
					<td align="left"><?=$subnm?></td>
					<td align="left"><?=$insname?></td>
					<td align="left" <?if($insilj=="1"){?>style="color:#5587ED"<?}else if($insilj=="2"){?>style="color:#E0844F"<?}else if($insilj=="3"){?>style="color:#747474"<?}?>
					><?=$conf['insilj'][$insilj]?></td>
					<td><? if(trim($jsyymm)) echo  date("Y-m-d",strtotime($jsyymm));?></td>
					<td><? if(trim($jeyymm)) echo  date("Y-m-d",strtotime($jeyymm));?></td>
					<?for($i=1;$i<=100;$i++){?>
					<td align="right"><?=$listData[$key]["jiyul".$i]?></td>
					<?}?>	
				</tr>
				<?}}else{?>
					<tr>
						<td style="color:#8C8C8C" colspan=11>�˻��� �����Ͱ� �����ϴ�</td>
					</tr>
				<?}?>
			</tbody>
		</table>
</div>

<div style="text-align: center">		
	<ul class="pagination pagination-sm sjirulelist" style="margin: 5px 5px 0 5px">
	  <?=$pagination->create_links();?>
	</ul>
</div>

<!-- // container -->
<!-- // wrap -->
<script type="text/javascript">

// ��� Ŭ��
$(".rowTop > th").click(function(){
	var trData = $(this).parent();

	var idx = $(trData).find("th").index($(this));

	// include/bottom.php ����	
	sortTable("sort_table", idx, 1);
})

$(document).ready(function(){
	// page �Լ� ajax������ ����� ���� ó��
	$(".sjirulelist a").click(function(){
		$('#page').val('Y');
		//alert('asdsadsadsadsadsa');
		var res = $(this).attr("href").split("?");
		if(res[0] && res[1]){
			//alert(res[0]+"//"+res[1]);
			// data_right_jojik div id�� ����
			ajaxLodingTarget(res[0],res[1],event,$('#sjirulelist'));    
		}
		return false;
	});

	$(".rowData > td").click(function(){
		var trData = $(this).parent();
		var idx    = $(".rowData").index($(trData));
		var jik  = $(".rowData").eq(idx).attr('data1');	//����
		var inscode  = $(".rowData").eq(idx).attr('data2');	//������ڵ�
		var insilj  = $(".rowData").eq(idx).attr('data3');	//��ǰ����
		var seq  = $(".rowData").eq(idx).attr('data4');		//����
		sjirulePopOpen(jik,inscode,insilj,seq);

	});

});

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>