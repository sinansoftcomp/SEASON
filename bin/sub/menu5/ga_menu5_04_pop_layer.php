<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

$sql= "
	select * from
	(
		select a.scode,a.skey,b.sname,a.insilj,a.seq,a.jsyymm,a.jeyymm,a.mjiyul,a.ujiyul,a.jjiyul,
				convert(varchar,a.idate,120) idate , a.iswon , convert(varchar,a.udate,120) udate , f.sname uswon ,
				c.bname ,d.jsname,e.tname
		from sjiyul a left outer join swon b on a.scode = b.scode and a.skey = b.skey
						left outer join bonbu c on b.scode = c.scode and b.bonbu = c.bcode
						left outer join jisa d on b.scode = d.scode and b.jisa = d.jscode
						left outer join team e on b.scode = e.scode and b.team = e.tcode
						left outer join swon f on a.scode = f.scode and a.uswon = f.skey
		where a.scode = '".$_SESSION['S_SCODE']."' and a.skey = '".$_GET['skey']."' and a.insilj = '".$_GET['insilj']."' and a.seq = '".$_GET['seq']."'
	) aa ";

$qry	= sqlsrv_query( $mscon, $sql );
extract($fet	= sqlsrv_fetch_array($qry));
/*
echo "<pre>";
echo $sql;
echo "</pre>";
*/
if($_GET['skey'] and $_GET['insilj'] and $_GET['seq']){
	$type="up";
}else{
	$type="in";
}

if($_GET['skey']){
	$searchbtn="off";
}else{
	$searchbtn="on";
}

$sql="select sname from swon where scode = '".$_SESSION['S_SCODE']."' and skey = '".$_GET['skey']."' ";
$result =  sqlsrv_query($mscon, $sql);
$row =  sqlsrv_fetch_array($result); 
$snameset		=	$row['sname'];

?>
<style>
.gridhover th {
    position: sticky;
    top: 0px;
}
</style>

<div id="main_tab4">
	<div class="tb_type01 view">
		<form name="sjiyul_form" class="" method="POST" action="ga_menu5_04_action.php" >
			<input type="hidden" name="type" value="<?=$type?>">	
			<input type="hidden" name="seq" value="<?=$seq?>">	
			<input type="hidden" name="insilj_f" value="<?=$_GET['insilj']?>">	
			<input type="hidden" name="udate" value="<?=$udate?>">
			<input type="hidden" name="uswon" value="<?=$uswon?>">

			<table style="height:400px;">
				<colgroup>
					<col width="23%">
					<col width="77%">

				</colgroup>
				<tbody>
				<tr>
					<th><em class="impor">*</em>사원코드</th>
					<td >
						<span class="input_type" style="width:70%;">
							<input type="text" name="skey" id="skey" value="<?=$skey?>" readonly>
						</span> 	
						<a href="#" class="btn_s white" onclick="SwonSearch();" <?if($searchbtn=="off"){?>style="display:none"<?}?>>검색</a>
					</td>
				</tr>
				<tr>
					<th><em class="impor">*</em>사원명</th>
					<td >
						<span class="input_type" style="width:70%;">
							<input type="text" name="sname" id="sname" value="<?=$sname?>" readonly>
						</span> 	
					</td>
				</tr>
				<tr>
					<th><em class="impor">*</em>상품군</th>
					<td >
						<select style="width:38%;" name="insilj" id="insilj"> 
							<?foreach($conf['insilj'] as $key => $val){?>
							<option value="<?=$key?>" <?if($insilj==$key) echo "selected"?> readonly><?=$val?></option>
							<?}?> 
						</select>	
					</td>
				</tr>
				<tr>
					<th><em class="impor">*</em>적용시작월</th>
					<td >
						<span class="input_type" style="width:100%;">
							<input type="text" class="Cal_ym" name="jsyymm" value="<? if(trim($jsyymm)) echo  date("Y-m",strtotime($jsyymm."01"));?> "  readonly>						
						</span> 	
					</td>
				</tr>
				<tr>
					<th><em class="impor">*</em>적용종료월</th>
					<td >
						<span class="input_type" style="width:100%;">
							<input type="text" class="Cal_ym" name="jeyymm" value="<? if(trim($jeyymm)) echo  date("Y-m",strtotime($jeyymm."01"));?> " readonly >						
						</span> 	
					</td>
				</tr>
				<tr>
					<th><em class="impor">*</em>모집지급율(%)</th>
					<td >
						<span class="input_type" style="width:100%;">
							<input type="text" name="mjiyul" id="mjiyul" value="<?=$mjiyul?>" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
						</span> 	
					</td>
				</tr>
				<tr>
					<th><em class="impor">*</em>유지지급율(%)</th>
					<td >
						<span class="input_type" style="width:100%;">
							<input type="text" name="ujiyul" id="ujiyul" value="<?=$ujiyul?>" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
						</span> 	
					</td>
				</tr>
				<tr>
					<th><em class="impor">*</em>증원지급율(%)</th>
					<td >
						<span class="input_type" style="width:100%;">
							<input type="text" name="jjiyul" id="jjiyul" value="<?=$jjiyul?>" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
						</span> 	
					</td>
				</tr>
				</tbody>
			</table>		
		</form>
	</div>
</div>


<div style="text-align: center">		

</div>



<script type="text/javascript">

$("#checked").click(function(){
	//만약 전체 선택 체크박스가 체크된상태일경우
	if($("#checked").prop("checked")) {
		//해당화면에 전체 checkbox들을 체크해준다
		$("input[type=checkbox]").prop("checked",true);
	// 전체선택 체크박스가 해제된 경우
	} else {
		//해당화면에 모든 checkbox들의 체크를해제시킨다.
		$("input[type=checkbox]").prop("checked",false);
	}
})


$(document).ready(function(){

	$("#skey").val('<?=$_GET["skey"]?>');
	$("#sname").val('<?=$snameset?>');

	var options = { 
		dataType:  'json',
		beforeSubmit:  showRequest_modal_ipgo,  // pre-submit callback 
		success:       processJson_modal_ipgo  // post-submit callback 
	}; 

	$('#ajax_sjiyul').ajaxForm(options);
});

// pre-submit callback 
function showRequest_modal_ipgo(formData, jqForm, options) { 
	var queryString = $.param(formData); 
	return true; 
} 
 
// post-submit callback 
function processJson_modal_ipgo(data) { 
	console.log(data);
	if(data.message){
		alert(data.message);
	}

	var code = data.code;

	if(data.result==''){
		// 성공시
		$("#modal").hide();
		ajaxLodingTarget("/bin/sub/menu5/ga_menu5_04_pop.php",'&code='+code+'&save=Y',event,$('.ipgopop'));    
	}
}

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>
