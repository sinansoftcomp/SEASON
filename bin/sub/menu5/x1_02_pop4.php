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
			<col width="10%">
			<col width="30%">
			<col width="10%">
			<col width="10%">
			<col width="30%">
			<col width="10%">
		</colgroup>
		<tbody>
			<?for($i=0;$i<5;$i++){?>
			<tr>
				<?for($j=($i*2)+1;$j<=($i+1)*2;$j++){?>
				<th align="center"><?=$j?></th>
				<td>
					<span class="input_type" style="width:95%;margin-left:0px; ">
						<input type="text" name='<?="dataname".$j?>' id='<?="dataname".$j?>' value='<?=$listData_name[$j-1]['dataname']?>' style="text-align:left" class="input_type_noborder_ip">
					</span>			
				</td>
				<td>
					<select name='<?="gubun".$j?>' id='<?="gubun".$j?>' style="width:100%;"> 		
					  <option value="1" <?if($listData_name[$j-1]['gubun']=="1") echo "selected"?>>금액</option>
					  <option value="2" <?if($listData_name[$j-1]['gubun']=="2") echo "selected"?>>요율</option>
					</select>					
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