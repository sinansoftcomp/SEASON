<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

// �������� ����Ʈ
$sql="
	select car_code, people_num, car_part, isnull(".$_GET['caryear'].",0) amt,
		   hyoung_sik, fuel,car_nm,
		   case when fracc <= ".$_GET['cardate']." then 'Y' else 'N' end fraccyn,
		   case when lineout <= ".$_GET['cardate']." then 'Y' else 'N' end lineoutyn,
		   case when connectcar <= ".$_GET['cardate']." then 'Y' else 'N' end connectcaryn 
	from cardtc(nolock)
	where car_brand = '".$_GET['car_brand']."'	
	  and car_nm = '".$_GET['car_nm']."'
	  and hyoung_sik = '".$_GET['car_kind']."'
	  and isnull(".$_GET['caryear'].",0) != 0 ";

$qry	= sqlsrv_query( $mscon, $sql );
$listData = array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listData[]	= $fet;
}


sqlsrv_free_stmt($result);
sqlsrv_close($mscon);


?>

<style>
body{background-image: none;}

</style>

<div class="container container_bk">
	<div class="content_wrap">
		<fieldset>

			<div class="menu_group_top" style="border-bottom:0px solid;">
				<div class="menu_group">
					<span>��������(����/ȭ����)</span>
				</div>
			</div>

		<!-- //box_gray -->
			<div class="tb_type01 view">
				<form name="2th_carsel" class="" method="post" >
					<table>
						<colgroup>
							<col width="18%">
							<col width="32%">
							<col width="18%">
							<col width="32%">
						</colgroup>
					<tbody>
						<tr>
							<th>����ȸ��</th><td><?=$_GET['car_brand']?></td>
							<th>����</th><td><?=$_GET['car_nm']?></td>
						</tr>
						<tr>
							<th>����</th><td><?=$_GET['caryeartxt']?></td>
							<th>���������</th><td><?if(trim($_GET['cardate'])) echo date("Y-m-d",strtotime($_GET['cardate']))?></td>
						</tr>
					</tbody>
					</table>
				</form>
			</div>
			<!-- // tb_type01 view -->

			<div style="padding:5px 0;">
				<div class="tb_type01" style="height:300px;overflow-y:auto;border-top: 0px solid #47474a;">
					<table class="gridhover" >
						<colgroup>
							<col width="15%">
							<col width="15%">
							<col width="auto">
						</colgroup>

						<thead>
							<tr class="rowTop">		
								<th align="center">��������(����)</th>
								<th align="center">ž���ο�/��</th>
								<th align="right">�⺻���</th>			
							</tr>
						</thead>

						<tbody>
							<?if(!empty($listData)){?>
							<?foreach($listData as $key => $val){extract($val);?>
							<tr class="rowData" rol-data='<?=$car_code?>' rol-data4='<?=$_GET['caryeartxt']?>' rol-data5='<?=$_GET['cardate']?>' rol-data6='<?=$hyoung_sik?>' rol-data7='<?=$car_nm?>' rol-data8='<?=$amt?>' rol-data9='<?=$fuel?>' rol-data11='<?=$car_part?>' rol-data12='<?=$fraccyn?>' rol-data13='<?=$lineoutyn?>' rol-data14='<?=$connectcaryn?>' rol-data15='<?=$people_num?>'>
								<td align="right"><?=number_format($amt)?></td>
								<td align="center"><?=$people_num?></td>
								<td align="left"><?=$car_part?></td>
							</tr>
							<?}}else{?>
								<tr>
									<td style="color:#8C8C8C">�˻��� �����Ͱ� �����ϴ�</td>
								</tr>
							<?}?>
						</tbody>
					</table>
				</div><!-- // tb_type01 -->
			</div>


		</fieldset>
	</div>

	<div class="btn_wrap_center" style="margin-top:10px;">
		<a href="#" class="btn_s white" onclick="selectcar_pop();">��˻�</a>
		<a href="#" class="btn_s white" onclick="btn_close();">�ݱ�</a>
	</div>

</div>

<!-- // popup_wrap -->

 </body>
</html>

<script type="text/javascript">


// �ݱ�
function btn_close(){	
	window.close();
}


// ������ �Է� �� �����ܰ� �̵�
function next_step(car_sub){

	var left = Math.ceil((window.screen.width - 700)/2);
	var top = Math.ceil((window.screen.height - 500)/2);
	var popOpen	= window.open( "<?=$conf['homeDir']?>/sub/help/carsel_basic_3th.php?car_brand=<?=$_GET['car_brand']?>&car_nm=<?=$_GET['car_nm']?>&caryear=<?=$_GET['caryear']?>&caryeartxt=<?=$_GET['caryeartxt']?>&cardate=<?=$_GET['cardate']?>&fdate=<?=$_GET['fdate']?>&car_sub="+car_sub,"","width=700px,height=300px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
	popOpen.focus();
}


// ��˻�
function selectcar_pop(){

	// ������ ����� ���� �θ�â���� ȣ��
	pwin.selectcar_pop();
	window.close();
}


	
$(document).ready(function(){

	// �˾�ũ�� resize
	window.resizeTo("700", "550"); 

	// ����Ʈ Ŭ���� �󼼳��� ��ȸ
	$(".rowData > td").click(function(){
		var trData = $(this).parent();
		var idx    = $(".rowData").index($(trData));

		var car_grade, bae_gi, car_sub, hi_repair;

		var car_code	= $(".rowData").eq(idx).attr('rol-data'); 

		var caryeartxt	= $(".rowData").eq(idx).attr('rol-data4'); 
		var cardate		= $(".rowData").eq(idx).attr('rol-data5'); 
		var hyoung_sik	= $(".rowData").eq(idx).attr('rol-data6'); 
		var car_sub		= $(".rowData").eq(idx).attr('rol-data7'); 
		var amt			= $(".rowData").eq(idx).attr('rol-data8'); 
		var fuel		= $(".rowData").eq(idx).attr('rol-data9'); 

		var car_part	= $(".rowData").eq(idx).attr('rol-data11'); 
		var fraccyn		= $(".rowData").eq(idx).attr('rol-data12'); 
		var lineoutyn	= $(".rowData").eq(idx).attr('rol-data13'); 
		var connectcaryn= $(".rowData").eq(idx).attr('rol-data14'); 
		var people_num	= $(".rowData").eq(idx).attr('rol-data15'); 

		if(fraccyn == 'Y'){
			car_part = car_part + ',�����浹����';
		}

		if(lineoutyn == 'Y'){
			car_part = car_part + ',������Ż����';
		}

		if(connectcaryn == 'Y'){
			car_part = car_part + ',Ŀ��Ƽ��ī';
		}
		
		pwin.setCarValue('B', car_code, car_grade, bae_gi, caryeartxt, cardate, hyoung_sik, car_sub, amt, fuel, hi_repair, car_part, people_num, 'N');
		self.close();
	})

});

// opener���
var pwin = null;
window.onload = function(){
    pwin = window.opener.opener;
	console.log(pwin);
    window.opener.close();
}


</script>



 

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>