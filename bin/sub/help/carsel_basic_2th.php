<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

// 차량정보 리스트
$sql="
	select car_sub 
	from cardta(nolock)
	where car_brand = '".$_GET['car_brand']."'	
	  and car_nm = '".$_GET['car_nm']."'
	  and isnull(".$_GET['caryear'].",0) != 0
	group by car_sub ";

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
					<span>차량선택(승용차)</span>
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
							<th>제조회사</th><td><?=$_GET['car_brand']?></td>
							<th>차명</th><td><?=$_GET['car_nm']?></td>
						</tr>
						<tr>
							<th>연식</th><td><?=$_GET['caryeartxt']?></td>
							<th>차량등록일</th><td><?if(trim($_GET['cardate'])) echo date("Y-m-d",strtotime($_GET['cardate']))?></td>
						</tr>
					</tbody>
					</table>
				</form>
			</div>
			<!-- // tb_type01 view -->

			<div style="padding:10px 0;">
				<div class="tb_type01" style="height:300px;overflow-y:auto;border-top: 0px solid #47474a;">
					<table class="gridhover" >
						<colgroup>
							<col width="auto">

						</colgroup>

						<tbody>
							<?if(!empty($listData)){?>
							<?foreach($listData as $key => $val){extract($val);?>
							<tr class="rowData" rol-data='<?=$car_sub?>'>	
								<td align="left"><?=$car_sub?></td>
							</tr>
							<?}}else{?>
								<tr>
									<td style="color:#8C8C8C">검색된 데이터가 없습니다</td>
								</tr>
							<?}?>
						</tbody>
					</table>
				</div><!-- // tb_type01 -->
			</div>


		</fieldset>
	</div>

	<div class="btn_wrap_center" style="margin-top:10px;">
		<a href="#" class="btn_s white" onclick="selectcar_pop();">재검색</a>
		<a href="#" class="btn_s white" onclick="btn_close();">닫기</a>
	</div>

</div>

<!-- // popup_wrap -->

 </body>
</html>

<script type="text/javascript">


// 닫기
function btn_close(){	
	window.close();
}


// 데이터 입력 후 다음단계 이동
function next_step(car_sub){

	var left = Math.ceil((window.screen.width - 700)/2);
	var top = Math.ceil((window.screen.height - 500)/2);
	var popOpen	= window.open( "<?=$conf['homeDir']?>/sub/help/carsel_basic_3th.php?car_brand=<?=$_GET['car_brand']?>&car_nm=<?=$_GET['car_nm']?>&caryear=<?=$_GET['caryear']?>&caryeartxt=<?=$_GET['caryeartxt']?>&cardate=<?=$_GET['cardate']?>&fdate=<?=$_GET['fdate']?>&car_sub="+car_sub,"","width=700px,height=300px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
	popOpen.focus();
}


// 재검색
function selectcar_pop(){

	// 윈도우 상속을 위해 부모창에서 호출
	pwin.selectcar_pop();
	window.close();
}


//  숫자만 입력가능
function NumberOnInput(e)  {
  e.value = e.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1')
}


	
$(document).ready(function(){

	// 팝업크기 resize
	window.resizeTo("700", "550"); 

	// 리스트 클릭시 상세내용 조회
	$(".rowData > td").click(function(){
		var trData = $(this).parent();
		var idx    = $(".rowData").index($(trData));

		var car_sub  = $(".rowData").eq(idx).attr('rol-data'); 
	
		next_step(car_sub); 
	})

});

// opener상속
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