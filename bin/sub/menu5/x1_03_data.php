<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

$inscode= substr($_REQUEST['id'],2,10);

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

<div class="tb_type01 kwndatalist" style="overflow-y:auto;width:600px; height:390px">	
	<form name="inscharge_form" id = 'inscharge_id_form' class="ajaxForm_inscharge" method="post" action="x1_03_action.php">
	<input type="hidden" name="inscode" id="inscode" value="<?=$inscode?>">
	<input type="hidden" name="type" id="type" value="">
		<table  class="gridhover">
			<colgroup>
				<col width="70px">
				<col width="300px">						
				<col width="150px">											
			</colgroup>
			<thead>
			<tr class="rowTop">
				<th align="center">정렬순번</th>
				<th align="center">규정명</th>						
				<th align="center">구분</th>					
			</tr>
			</thead>
			<tbody>
				<?for($i=1; $i<=10; $i++){?>
				<tr>
					<td align="center"><?=$i?></td>
					<td>
						<span class="input_type" style="width:95%;margin-left:0px; ">
							<input type="text" name='<?="dataname".$i?>' id='<?="dataname".$i?>' value='<?=$listData_name[$i-1]['dataname']?>' style="text-align:left" class="input_type_noborder_ip">
						</span>						
					</td>
					<td>
						<select name='<?="gubun".$i?>' id='<?="gubun".$i?>' style="width:50%;text-align:center"> 		
						  <option value="1" <?if($listData_name[$i-1]['gubun']=="1") echo "selected"?>>금액</option>
						  <option value="2" <?if($listData_name[$i-1]['gubun']=="2") echo "selected"?>>요율</option>
						</select>
					</td>
				</tr>
				<?}?>
			</tbody>
		</table>
	</form>

</div>


<script type="text/javascript">

// 저장 or 초기화
function inscharge_update(bit){
	$("form[name='inscharge_form'] input[name='type']").val(bit);
	var conm = '저장하시겠습니까?';
	if(bit=='reset'){
		conm = '초기화하시겠습니까?';
	}
	if(confirm(conm)){
		$("form[name='inscharge_form']").submit();
	}
}



$(document).ready(function(){

	var options = { 
		dataType:  'json',
		beforeSubmit:  showRequest_modal_inscharge,  // pre-submit callback 
		success:       processJson_modal_inscharge  // post-submit callback 
	}; 

	$('.ajaxForm_inscharge').ajaxForm(options);

});

// pre-submit callback 
function showRequest_modal_inscharge(formData, jqForm, options) { 
	var queryString = $.param(formData); 
	return true; 
} 
 
// post-submit callback 
function processJson_modal_inscharge(data) { 
	if(data.message){
		alert(data.message);
	}
	if(data.rtype=='save' || data.rtype=='reset'){
		$('#tree-container').jstree($("#N2"+data.inscode+"_anchor").trigger("click"));
	}

}

</script>


<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>