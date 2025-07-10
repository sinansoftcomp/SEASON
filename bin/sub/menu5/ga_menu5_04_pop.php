<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

$skey = $_GET['skey'];

$where="";

//검색 데이터 구하기 
$sql	= "
		select *
		from(
			select *, ROW_NUMBER()over(order by skey,insilj , jsyymm desc) rnum 
			from(
				select a.scode,a.skey,b.sname,a.insilj,a.seq,a.jsyymm,a.jeyymm,a.mjiyul,a.ujiyul,a.jjiyul,
						convert(varchar,a.idate,120) idate , a.iswon , convert(varchar,a.udate,120) udate , a.uswon ,
						c.bname ,d.jsname,e.tname
				from sjiyul a left outer join swon b on a.scode = b.scode and a.skey = b.skey
								left outer join bonbu c on b.scode = c.scode and b.bonbu = c.bcode
								left outer join jisa d on b.scode = d.scode and b.jisa = d.jscode
								left outer join team e on b.scode = e.scode and b.team = e.tcode
				where a.scode = '".$_SESSION['S_SCODE']."' and a.skey = '".$skey."'
			) tbl
		) p
		" ;

$qry	= sqlsrv_query( $mscon, $sql );
$listData = array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listData[]	= $fet;
}

if($_GET['skey']){
	$type	= 'up';
	$sql  = "select swon.sname , convert(varchar,sjiyul.udate,121) udate
			from sjiyul left outer join swon on sjiyul.scode = swon.scode and sjiyul.skey = swon.skey
			where sjiyul.scode = '".$_SESSION['S_SCODE']."' and sjiyul.skey = '".$_GET['skey']."' and sjiyul.insilj = '1' and sjiyul.seq = 1 ";
	$result =  sqlsrv_query($mscon, $sql);
	$row =  sqlsrv_fetch_array($result); 

	$update		=	$row['udate'];;
	$upswon		=	$row['sname'];;
	$upswon_txt	=	'최종수정사원';
	$update_txt	=	'최종수정일시';
}else{
	$type	= 'in';
	$update		=	date("Y-m-d H:i:s");
	$upswon		=	$_SESSION['S_SNAME'];
	$upswon_txt	=	'등록사원';
	$update_txt	=	'등록일시';
}

?>
<style>
body{background: #FFFFFF;}
.box_wrap{margin-bottom:0px}
.tb_type01 th, .tb_type01 td {padding: 6px 0;}
.gridhover td {height:25px}
.gridhover th {height:25px}
</style>

<div class="tit_wrap ipgopop" style="padding-top:10px">
	<div class="tit_wrap">
		<h3 class="tit_sub" style="margin-left:20px">사원별 지급율 등록</h3>
		<span class="btn_wrap" style="padding-right:20px">
			<a href="#" class="btn_l white" style="min-width:100px;" onclick="sjiyul_new();">신규</a>
			<a href="#" class="btn_l navy" style="min-width:100px;" onclick="sjiyul_update();">저장</a>
			<a href="#" class="btn_l white" style="min-width:100px;" onclick="sjiyul_delete();">삭제</a>
			<a href="#" class="btn_l white" style="min-width:100px;" onclick="sjiyul_close();">닫기</a>
		</span>
	</div>
	<div>
	
		<div style="border-right:1px solid #D5D5D5; border-left:1px solid #D5D5D5; padding-left:20px;padding-right:20px; ">
			<div class="data_left" style="width:35%; ">
				

				<div id="tab-10" class="tab_con_wrap on tvatdt" style="border-bottom:2px solid black;margin-right:10px">
				</div>


	
			</div>
			<div class="data_right" style="width:65%;border-bottom:2px solid black;padding-left:0px">
				<div class="tb_type01" style="height:400px">
					<table class="gridhover">
						<colgroup>							
							<col width="14%">											
							<col width="14%">
							<col width="14%">
							<col width="10%">
							<col width="10%">
							<col width="10%">
						</colgroup>
						<thead>
						<tr style="height:49px">				
							<th align="center">상품군</th>
							<th align="center">적용시작월</th>
							<th align="center">적용종료월</th>	
							<th align="center">모집지급율</th>	
							<th align="center">유지지급율</th>	
							<th align="center">증원지급율</th>	
						</tr>
						</thead>
						<tbody>
							<?if(!empty($listData)){?>
							<?foreach($listData as $key => $val){extract($val);?>
							<tr class="rowData" rol-date='<?=$skey?>' rol-date2='<?=$insilj?>' rol-date3='<?=$seq?>' style="cursor:pointer;">					
								<!--<td align="center" class="grid_rowspan" style="pointer-events: none;"><?=$conf['insilj'][$insilj]?></td>-->
								<td align="center" <?if($insilj=="1"){?>style="color:#5587ED"<?}else if($insilj=="2"){?>style="color:#E0844F"<?}else if($insilj=="3"){?>style="color:#747474"<?}?>
								><?=$conf['insilj'][$insilj]?></td>
								<td align="center"><?if(trim($jsyymm)) echo date("Y-m",strtotime($jsyymm."01"));?></td>							
								<td align="center" ><?if(trim($jeyymm)) echo  date("Y-m",strtotime($jeyymm."01"));?></td>							
								<td align="center" ><?=$mjiyul?>%</td>
								<td align="center" ><?=$ujiyul?>%</td>
								<td align="center" style="height:38px" ><?=$jjiyul?>%</td>

							</tr>
							<?}}else{?>
								<tr>
									<td style="color:#8C8C8C;height:38px" colspan=11>검색된 데이터가 없습니다</td>
								</tr>
							<?}?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		
		<div class="tit_wrap" style="margin-top:15px;margin-right:20px">
			<span class="btn_wrap" style="margin-top:10px">		
				<span style="margin-left:15px" class="font_blue" id = "uswonset"><?=$upswon_txt?> : <?=$upswon?></span>
				<span style="margin-left:15px" class="font_blue" id = "udateset"><?=$update_txt?> : <?=$update?></span>					
			</span>
		</div>
	</div>
</div>

<script type="text/javascript">

// 사원 팝업
function SwonSearch(){
	var left = Math.ceil((window.screen.width - 600)/2);
	var top = Math.ceil((window.screen.height - 400)/2);
	var popOpen	= window.open("<?=$conf['homeDir']?>/sub/help/ga_swon_search.php","swonpop","width=500px,height=400px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
	popOpen.focus();
}

function setSwonValue(row,skey,name){
	location.href="<?=$conf['homeDir']?>/sub/menu5/ga_menu5_04_pop.php?skey="+skey+"&search=on";
	//$("#skey").val(skey);
	//$("#sname").val(name);
}

// 저장
function sjiyul_update(){

	var skey = $("form[name='sjiyul_form'] input[name='skey']").val();
	var insilj = $("#insilj").val();
	var jsyymm = $("form[name='sjiyul_form'] input[name='jsyymm']").val();
	var jeyymm = $("form[name='sjiyul_form'] input[name='jeyymm']").val();
	var mjiyul = $("form[name='sjiyul_form'] input[name='mjiyul']").val();
	var ujiyul = $("form[name='sjiyul_form'] input[name='ujiyul']").val();
	var jjiyul = $("form[name='sjiyul_form'] input[name='jjiyul']").val();

	if(isEmpty(skey) == true){
		alert('사원코드를 입력해주세요.');
	}else if(isEmpty(insilj) == true){
		alert('상품군을 선택해주세요.');
	}else if(isEmpty(jsyymm) == true){
		alert('적용시작월을 선택해주세요.');
	}else if(isEmpty(jeyymm) == true){
		alert('적용종료월을 선택해주세요.');
	}else if(isEmpty(mjiyul) == true){
		alert('모집지급율을 입력해주세요.');
	}else if(isEmpty(ujiyul) == true){
		alert('유지지급율을 입력해주세요.');
	}else if(isEmpty(jjiyul) == true){
		alert('증원지급율을 입력해주세요.');
	}else{
		if(confirm("저장하시겠습니까?")){
			$("form[name='sjiyul_form']").submit();
		}
	}
}

// 신규
function sjiyul_new(){
	location.href='ga_menu5_04_pop.php';
}

// 닫기
function sjiyul_close(){	
	self.close();
	opener.location.reload();
}

// 삭제
function sjiyul_delete(){
	var type   = $("form[name='sjiyul_form'] input[name='type']").val();

	if(type == "up"){
		if(confirm("삭제하시겠습니까?")){
			document.sjiyul_form.type.value='del';
			$("form[name='sjiyul_form']").submit();
		}
	}else{
		alert("삭제할 대상이 없습니다.");
	}
}

//window.resizeTo("1200", "350");                             // 윈도우 리사이즈

$(document).ready(function(){

	var skey = '<?=$skey?>';

	if('<?=$_GET["insilj"]?>' && '<?=$_GET["seq"]?>'){
		ajaxLodingTarket('ga_menu5_04_pop_layer.php',$('#tab-10'),'&skey='+skey+'&insilj='+'<?=$_GET["insilj"]?>'+'&seq='+'<?=$_GET["seq"]?>');
	}else{
		ajaxLodingTarket('ga_menu5_04_pop_layer.php',$('#tab-10'),'&skey='+skey+'&insilj='+''+'&seq='+'');
	}

	$(".rowData > td").click(function(){
		var trData = $(this).parent();
		var idx    = $(".rowData").index($(trData));
		var skey  = $(".rowData").eq(idx).attr('rol-date');
		var insilj  = $(".rowData").eq(idx).attr('rol-date2');
		var seq  = $(".rowData").eq(idx).attr('rol-date3');

		ajaxLodingTarket('ga_menu5_04_pop_layer.php',$('#tab-10'),'&skey='+skey+'&insilj='+insilj+'&seq='+seq);
		$("#uswonset").text('<?=$upswon_txt?>'+" : "+$("form[name='sjiyul_form'] input[name='uswon']").val());
		$("#udateset").text('<?=$update_txt?>'+" : "+$("form[name='sjiyul_form'] input[name='udate']").val());
	})

	// 수정 및 저장시 메인화면 리로드
	if('<?=$_GET['save']?>' == 'Y'){
		opener.location.reload();
	}

	$(".grid_rowspan").each(function(){
		var rows = $(".grid_rowspan:contains('"+$(this).text() + "')");

		if(rows.length>1){
			rows.eq(0).attr("rowspan",rows.length);
			rows.not(":eq(0)").remove();
		}
	});

});


</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>