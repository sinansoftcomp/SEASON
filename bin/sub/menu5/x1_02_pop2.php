<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

if($_GET['type']=='in'){
	$yymm = $_GET['yymm_s'];
	$inscode = $_GET['inscode_s'];
}else{
	$yymm = $_GET['yymm'];
	$inscode = $_GET['inscode'];
}

$sql= "	
		select a.scode,a.yymm,a.inscode , b.name , 
				a.dataset1 , a.dataset2 , a.dataset3 , a.dataset4 , a.dataset5 , a.dataset6 , a.dataset7 , a.dataset8 , a.dataset9 , a.dataset10 ,
				row_number()over(order by a.yymm desc , a.inscode asc) rnum
		from INSCHARGE_SET a left outer join inssetup b on a.inscode = b.inscode
		where a.SCODE =  '".$_SESSION['S_SCODE']."'  and  a.YYMM = '".$yymm."' and a.inscode = '".$inscode."' 
		";
$qry	= sqlsrv_query( $mscon, $sql );
$listData_sub = array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listData_sub[]	= $fet;
}

$sql= "	
		select scode,inscode,datacode,dataname,gubun
		from inscharge_nameset
		where scode = '".$_SESSION['S_SCODE']."' and inscode = '".$inscode."'
		order by convert(int,substring(datacode,8,2))
		";
$qry	= sqlsrv_query( $mscon, $sql );
$listData_name = array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listData_name[]	= $fet;
}

$cnt = 0;
// 밑에 tr개수 동적으로 주려고 홀짝체크
if(count($listData_name) % 2 == 1 ){
	$cnt = ceil(count($listData_name)/2);
}else{
	$cnt = count($listData_name)/2;
}

/*
echo '<pre>';
echo $sql; 
echo '</pre>';
*/ 
sqlsrv_free_stmt($result);
sqlsrv_close($mscon);

?>

<div>	
	<table class="gridhover2" >
		<colgroup>
			<col width="18%">
			<col width="32%">
			<col width="18%">
			<col width="32%">
		</colgroup>
		<tbody>
			<?for($i=0;$i<$cnt;$i++){?>
			<tr>
				<?for($j=($i*2)+1;$j<=($i+1)*2;$j++){?>
				<th align="left"><?=$listData_name[$j-1]['dataname']?></th>
				<td>
					<?if($listData_name[$j-1]['dataname']){?>
					<span class="input_type" style="width:95%;margin-left:0px; ">
						<?if($listData_name[$j-1]['gubun']=='1'){?>
						<input type="text" name='<?="dataset".$j?>' id='<?="dataset".$j?>' value='<?=number_format((int)$listData_sub[0]['dataset'.$j])?>' style="text-align:right" class="input_type_noborder_ip">
						<?}else{?>
						<input type="text" name='<?="dataset".$j?>' id='<?="dataset".$j?>' value='<?=$listData_sub[0]['dataset'.$j]?>' style="text-align:right" class="input_type_noborder_ip">
						<?}?>
					</span>
					<?}?>
				</td>
				<?}?>
			</tr>
			<?}?>
		</tbody>
	</table>
</div><!-- // tb_type01 -->


<script type="text/javascript">

$(document).ready(function(){

	// page 함수 ajax페이지 존재시 별도 처리
	$(".kwnlist a").click(function(){
		$('#page').val('Y');
		var res = $(this).attr("href").split("?");
		if(res[0] && res[1]){
			//alert(res[0]+"//"+res[1]);
			 //data_right_jojik div id값 적용
			ajaxLodingTarget(res[0],res[1],event,$('#kwnlist'));    
		}
		return false;
	});

});

</script>


<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>