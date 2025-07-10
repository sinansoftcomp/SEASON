<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");


$sql	= "
			select bcode, lawdc, code, accone,
				   case when bcode = 'B3' and code in('BA','BB','BK','BI') then 'B3'
						when bcode = 'B3' and code in('BC','BD','BO','BP','BJ') then 'B4'
						when bcode = 'B3' and code in('BE','BF','BQ','BR') then 'B5'
						when bcode = 'B3' and code in('BG','BH') then 'B6'
						else bcode end tdbcode			
			from carspecial
			order by groupnum, innum " ;

$result	= sqlsrv_query( $mscon, $sql );
$listData = array();
while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
	$listData[]	= $row;
}



?>
<style>
body{background-image: none;}
.box_wrap{margin-bottom:0px;padding:15px 20px;}
.tb_type01 th, .tb_type01 td {padding: 4px 0;}
</style>

<div class="container container_bk">
	<div class="content_wrap">
		<fieldset>

			<div class="menu_group_top" style="border-bottom:0px solid;">
				<div class="menu_group">
					<span><i class="fa-solid fa-car-side mgr5 font_topcolor"></i>Ư������</span>
				</div>
			</div>

			<div class="tit_wrap">
				<div class="tb_type01 tb_fix" style="height:650px;overflow-y:auto;">
					<table class="gridhover">
						<colgroup>
							<col width="auto">
							<col width="10%">							
							<col width="10%">
							<col width="15%">
						</colgroup>
						<thead>
						<tr>
							<th>������ ��������</th>
							<th>���߿��ڵ�</th>
							<th>��ü�ڵ�</th>
							<th>1�Ⱓ���</th>
						</tr>
						</thead>
						<tbody>
						<?if(!empty($listData)){?>
						<?foreach($listData as $key => $val){extract($val);?>
						<tr rol-data1='<?=$code?>' class="rowData"  >
							<td class="<?=$tdbcode?> nothover" align="left" style="cursor:default"><?=$lawdc?></td>
							<td class="<?=$tdbcode?> nothover" style="cursor:default"><?=$bcode?></td>	
							<td onclick="datasend('<?=$code?>')"><?=$code?></td>
							<td onclick="datasend('<?=$code?>')"><?=$accone?></td>
						</tr>
						<?}}?>
						</tbody>
					</table>
				</div>
			</div>

		</fieldset>
	</div>
</div>

<script type="text/javascript">


// ������
function genRowspan(className){
    $("." + className).each(function() {
        var rows = $("." + className + ":contains('" + $(this).text() + "')");
        if (rows.length > 1) {
            rows.eq(0).attr("rowspan", rows.length);
            rows.not(":eq(0)").remove();
        }
    });
}


function datasend(code){
	opener.setCarSpecial(code);
	self.close();	
}

$(document).ready(function(){

	// ������
	const tdarr = ['D1','C2','C3','C4','C5','B4','C1','B3','B4','B5','B6','X0'];

	for (i = 0; i < tdarr.length; i++) {
		genRowspan(tdarr[i]);
	}

	/*
	$(".rowData").click(function(){
		var trData	= $(this).parent();
		var idx		= $(".rowData").index($(this));
		var code	= $(".rowData").eq(idx).attr("rol-data1");
	});
	*/

});

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>