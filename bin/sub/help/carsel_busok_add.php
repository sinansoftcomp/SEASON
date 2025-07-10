<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");


$extdata	= $_GET['data'];
$carseq		= $_GET['carseq'];

if($carseq){
	// 등록내역이 있다면 금액 같이 조회
	$sql	= "
		select a.code, a.bigo, a.seq,
			   isnull(b.amt,0) amt
		from caradd a
			left outer join(
					select *
					from carestadd 
					where scode = '".$_SESSION['S_SCODE']."'
					  and carseq = '".$carseq."'
					  ) b on a.code = b.code
			order by a.seq " ;

}else{
	// 특별요율 리스트(등록이력이 없을 경우)
	$sql	= "
				select 
						code,
						bigo,
						seq,
						0 amt
				from caradd
				order by seq" ;
}


$result	= sqlsrv_query( $mscon, $sql );
$listData = array();
while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
	$listData[]	= $row;
}

// 전체개수 가져오기
$sql	= "Select  Count(*) cnt
			from caradd  ";

$qry =  sqlsrv_query($mscon, $sql);
$totalResult =  sqlsrv_fetch_array($qry); 

$totalCnt = (int)$totalResult['cnt'];


// 조회시 전체금액
if($carseq){
	$sql	= "select sum(isnull(b.amt,0)) totamt
			from caradd a
				left outer join(
						select *
						from carestadd 
						where scode = '".$_SESSION['S_SCODE']."'
						  and carseq = '".$carseq."'
						  ) b on a.code = b.code ";

	$qry =  sqlsrv_query($mscon, $sql);
	$totalamt =  sqlsrv_fetch_array($qry); 

	$totamt = (int)$totalamt['totamt'];
}

$cnt = 0;
// 밑에 tr개수 동적으로 주려고 홀짝체크
if(count($listData) % 2 == 1 ){
	$cnt = ceil(count($listData)/2);
}else{
	$cnt = count($listData)/2;
}


//echo $cnt;

?>
<style>
body{background-image: none;}
.box_wrap{margin-bottom:0px;padding:15px 20px;}
.pop_btn{height:24px; line-height:22px;}

.input_type{border:0px solid;height:20px;}

.tb_type01 td.noborder{padding:3px 0;}
.amtstring{color:#FF0000;}
</style>

<div class="container container_bk">
	<div class="content_wrap">

		<div class="menu_group_top" style="border-bottom:0px solid;">
			<div class="menu_group"><span>차량 추가부속품</span></div>
			<div class="menu_group" style="margin-left:340px;"><span style="font-size:12px;font-weight:600;">(금액단위:만원)</span></div>
		</div>

		<div class="tb_type01 tb_fix" style="height:500px;">
			<table class="gridhover">
				<colgroup>
					<col width="20%">
					<col width="30%">
					<col width="20%">
					<col width="30%">
				</colgroup>

				<tbody>
					<?for($i=0;$i<$cnt;$i++){?>
						<tr class="rowData">
							<?for($j=($i*2)+1;$j<=($i+1)*2;$j++){?>
								<td class="noborder" align="left" style="background:#f9f9f9;">
									<span class="input_type" style="background:#f9f9f9;"><input type="text" class = "<?=$listData[$j-1]['code']?>" value="<?=$listData[$j-1]['bigo']?>" readonly></span>
								</td>

								<td class="noborder" align="left" style="margin-left:20px;">
									<span class="input_type_number"><input type="text" id = "<?=$listData[$j-1]['code']?>" class="numberInput yb_right checkVal" value="<?=number_format($listData[$j-1]['amt'])?>"></span>
								</td>
							<?}?>
						</tr>
					<?}?>
				</tbody>
			</table>
		</div>
		
	</div>

	<div class="tit_wrap" style="margin-bottom:10px;font-weight:700;font-size:13px;">		
		<span class="btn_wrap">
			<span style="margin-right:15px" class="font_blue">부속품합계 : </span>
			<span style="margin-right:10px" class="font_blue" id="totalsum"><?=number_format($totamt)?></span><span style="margin-right:45px" class="font_blue">만원</span>
		</span>
	</div>


	<div class="btn_wrap_center">
		<a href="#" class="btn_s white" onclick="next_step();">적용</a>
		<a href="#" class="btn_s white" onclick="btn_close();">닫기</a>
	</div>

	<p class="mt10 font_red">
	* IM은 도난방지 장치 입니다.<br>
	* ACC는 지능형 안전주행 장치 입니다.<br>
	</p>
	<p class="">
	* 금액을 입력한 제품에 대해서 적용됩니다.
	</p>

</div>


<script type="text/javascript">

// 닫기
function btn_close(){	
	window.close();
}


// 적용
function next_step(){

	//alert(document.getElementById('ex1').innerText);
	
	var totcnt	= '<?=$totalCnt?>';
	var id		= '';	
	
	var add_data= "";
	var dash	= "";
	var add_text= "";
	var comma	= "";
	var add_amt	= "";
	var dash2	= "";

	for(var i=1; i<=totcnt; i++){
		id = 'ad'+i.toString();

		dataamt = uncomma($("#"+id).val());
		amt		= parseInt(uncomma($("#"+id).val()));
		txt		= $("."+id).val();

		txtdata = txt + '(' + String(amt) + ')';

		if(amt > 0){
			if(add_data.length > 0) dash="-";
			add_data = add_data + dash + id;

			if(add_text.length > 0) comma=",";
			add_text = add_text + comma + txtdata;

			if(add_amt.length > 0) dash2="-";
			add_amt = add_amt + dash2 + dataamt;
		}
	}

	var totalsum = uncomma($("#totalsum").text());

	/*console.log(add_data);
	console.log(add_text);
	console.log(add_amt);
	console.log(totalsum);*/

	opener.setCarBusok('B', add_data, add_text, add_amt, totalsum);
	self.close();
}


// 금액합계 자동계산
$(".checkVal").keyup(function(){

	var totcnt	= '<?=$totalCnt?>';
	var	id		= '';
	var totamt	= 0;
	var amt		= 0;

	for(var i=1; i<=totcnt; i++){
		id = 'ad'+i.toString();

		amt		= uncomma($("#"+id).val());

		if(!amt) amt=0;
		totamt	= parseInt(totamt) + parseInt(amt);
	}

	txt_color();
	$("#totalsum").text(comma(totamt));
});


// 금액색상으로 잘 보이게 처리
function txt_color(){
	var totcnt	= '<?=$totalCnt?>';
	var data	= "<?=$extdata?>";
	var	id		= '';
	var amt		= 0;

	for(var i=1; i<=totcnt ; i++){
		id = 'ad'+i.toString();
		amt		= uncomma($("#"+id).val());

		//console.log(id+amt);

		if(!amt) amt=0;

		if(amt > 0){
			$("#"+id).addClass('amtstring');
		}else{
			$("#"+id).removeClass('amtstring');
		}
	}	
}

$(document).ready(function(){

	window.resizeTo("600", "750");

	txt_color();

	

});

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>