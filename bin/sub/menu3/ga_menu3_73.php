<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

/*
	���Ѱ���
	bin/include/source/auch_chk.php
*/
$pageTemp	= explode("/",$_SERVER['PHP_SELF']);
$auth = auth_Ser($_SESSION['S_MASTER'], $pageTemp[count($pageTemp)-1], $_SESSION['S_SKEY'], $mscon);
if($auth != "Y"){
	sqlsrv_close($mscon);
	alert('�ش� �޴��� ���� ������ �����ϴ�. �����ڿ��� ���� �ٶ��ϴ�.');
	exit;
}


if ($_GET['SDATE1']) {
	$sdate1 =  $_GET['SDATE1'];
	$sdate2 =  $_GET['SDATE2'];
}else{
	$sdate1 =  date("Y-m-d");
	$sdate2 =  date("Y-m-d");
}


// ���α׷� ���Խ� �ð���  �ҿ������ ����� ������ ��ȸ��ư ������ datadisplay�ϱ� ����.
 if ( $_GET['SDATE1']  == "" || is_null( $_GET['SDATE1'])) {
	$fyymm =  "";
	$tyymm =  "";
 }else{
	$fyymm =  substr($sdate1,0,4).substr($sdate1,5,2); 
	$tyymm =  substr($sdate2,0,4).substr($sdate2,5,2); 
}



 $page = ($_GET['page']) ? $_GET['page'] : 1;

// �⺻ ������ ����
$page = ($_GET['page']) ? $_GET['page'] : 1;
$page_row	= 1000;

$limit1 = ($page-1)*$page_row+1;
$limit2 = $page*$page_row;

 
//�˻� ������ ���ϱ� 
$sql= "select * from 
			(select *,row_number()over(order by INSCODE,seq) rnum  from 
				(select	a.ipdate,a.gubun,a.gubunsub,a.ino, a.INSCODE, '1' seq,   c.NAME, b.FILENAME,
						count(*) cnt, max(st1) st1, sum(a.SAMT1) samt1, max(st2) st2, sum(a.SAMT2) samt2 , max(st3) st3, sum(samt3) samt3, max(st4) st4, sum(a.SAMT4) samt4, max(st5) st5, sum(a.SAMT5) samt5, 
						sum(a.SAMT6+a.SAMT7+a.SAMT8+a.SAMT9+a.SAMT10+a.SAMT11+a.SAMT12 +a.SAMT13) kita,
						sum(a.SAMT1+a.SAMT2+a.SAMT3+a.SAMT4+a.SAMT5+a.SAMT6+a.SAMT7+a.SAMT8+a.SAMT9+a.SAMT10+a.SAMT11+a.SAMT12 +a.SAMT13) total,
						sum(a.HSSU) hssu, sum(a.BSU) bsu,
						sum(a.SAMT1+a.SAMT2+a.SAMT3+a.SAMT4+a.SAMT5+a.SAMT6+a.SAMT7+a.SAMT8+a.SAMT9+a.SAMT10+a.SAMT11+a.SAMT12 +a.SAMT13+a.HSSU+a.BSU) catotal
				from INS_IPMST(nolock) a	
				left outer join UPLOAD_HISTORY(nolock) b on a.scode = b.scode and a.ipdate = b.UPLDATE and a.gubun =b.GUBUN and a.GUBUNSUB = b.GUBUNsub and a.ino = b.UPLNUM
				left outer join inssetup(nolock) c on a.scode = c.scode and a.inscode = c.inscode
				where   a.scode = '".$_SESSION['S_SCODE']."' and  a.yymm >= '".$fyymm."'   and  a.yymm <= '".$tyymm."'  
				group by a.ipdate,a.gubun,a.gubunsub,a.ino,a.INSCODE, c.NAME,b.FILENAME 
			 
				union  all

					select	'','','',0,  a.INSCODE, '2' seq ,   '', '�����Ұ�', 
						count(*) cnt, '', sum(a.SAMT1) samt1, '', sum(a.SAMT2) samt2 , '', sum(samt3) samt3,'', sum(a.SAMT4) samt4,'', sum(a.SAMT5) samt5, 
						sum(a.SAMT6+a.SAMT7+a.SAMT8+a.SAMT9+a.SAMT10+a.SAMT11+a.SAMT12 +a.SAMT13) kita,
						sum(a.SAMT1+a.SAMT2+a.SAMT3+a.SAMT4+a.SAMT5+a.SAMT6+a.SAMT7+a.SAMT8+a.SAMT9+a.SAMT10+a.SAMT11+a.SAMT12 +a.SAMT13) total,
						sum(a.HSSU) hssu, sum(a.BSU) bsu,
						sum(a.SAMT1+a.SAMT2+a.SAMT3+a.SAMT4+a.SAMT5+a.SAMT6+a.SAMT7+a.SAMT8+a.SAMT9+a.SAMT10+a.SAMT11+a.SAMT12 +a.SAMT13+a.HSSU+a.BSU) catotal
						from INS_IPMST(nolock) a	
						left outer join UPLOAD_HISTORY(nolock) b on a.scode = b.scode and a.ipdate = b.UPLDATE and a.gubun =b.GUBUN and a.GUBUNSUB = b.GUBUNsub and a.ino = b.UPLNUM
						left outer join inssetup(nolock) c on a.scode = c.scode and a.inscode = c.inscode
				where  a.scode = '".$_SESSION['S_SCODE']."' and  a.yymm  >= '".$fyymm."'  and  a.yymm  <= '".$tyymm."' 
				group by a.INSCODE 
					
				union all

					select	'','','',0,  'zzzz', '3' seq ,   '', '�������հ�',
						count(*) cnt, '', sum(a.SAMT1) samt1, '', sum(a.SAMT2) samt2 , '', sum(samt3) samt3,'', sum(a.SAMT4) samt4,'', sum(a.SAMT5) samt5, 
						sum(a.SAMT6+a.SAMT7+a.SAMT8+a.SAMT9+a.SAMT10+a.SAMT11+a.SAMT12 +a.SAMT13) kita,
						sum(a.SAMT1+a.SAMT2+a.SAMT3+a.SAMT4+a.SAMT5+a.SAMT6+a.SAMT7+a.SAMT8+a.SAMT9+a.SAMT10+a.SAMT11+a.SAMT12 +a.SAMT13) total,
						sum(a.HSSU) hssu, sum(a.BSU) bsu,
						sum(a.SAMT1+a.SAMT2+a.SAMT3+a.SAMT4+a.SAMT5+a.SAMT6+a.SAMT7+a.SAMT8+a.SAMT9+a.SAMT10+a.SAMT11+a.SAMT12 +a.SAMT13+a.HSSU+a.BSU) catotal
						from INS_IPMST(nolock) a	
						left outer join UPLOAD_HISTORY(nolock) b on a.scode = b.scode and a.ipdate = b.UPLDATE and a.gubun =b.GUBUN and a.GUBUNSUB = b.GUBUNsub and a.ino = b.UPLNUM
						left outer join inssetup(nolock) c on a.scode = c.scode and a.inscode = c.inscode
				where  a.scode = '".$_SESSION['S_SCODE']."' and  a.yymm >= '".$fyymm."'  and  a.yymm <= '".$tyymm."'   ) aa
				) P
				where rnum between ".$limit1." AND ".$limit2 ;
 
 

//ó���� �Ⱥ��̴� ����� ������ ��ȸ��ư Ŭ���� �����ϱ�����(�뷮��data�� cpu����)
if (	$fyymm !=  "") {
		$qry	= sqlsrv_query( $mscon, $sql );
		$listData = array();
		while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
			$listData[]	= $fet;
		}
}
/*
echo '<pre>';
echo $sql; 
echo '</pre>';
*/

$totalResult['CNT'] = 100; //��ȭ�鿡 ��ü DISPLAY 
// ������ Ŭ���� ����
// �ε�
include_once($conf['rootDir'].'/include/class/Pagination.php');

// ����
$pagination = new Pagination(array(
		'base_url' => $_SERVER['PHP_SELF']."?SDATE1=".$sdate1."&SDATE2=".$sdate2."&btn=".$_GET['btn'],
		'per_page' => $page_row,
		'total_rows' => $totalResult['CNT'],
		'cur_page' => $page,
));

sqlsrv_free_stmt($result);
sqlsrv_close($mscon);

?>

<!-- html���� -->
<!-- html���� -->
<style>
 </style>

<div class="container">
	<div class="content_wrap">
		<fieldset>
 
			<!-- �˻����� -->
			<div class="box_wrap sel_btn">
				<form name="searchFrm" id="searchFrm" method="get" action="<?$_SERVER['PHP_SELF']?>">
 				<input type="hidden" name="id" id="id" value="">
				<input type="hidden" name="btn"  id="btn"  value="">
					<fieldset>
 
						<div>
							<span class="ser_font"> �����</span> 
							<button type="button" class="btn_prev" name="yp" id="yp" onclick="d_ser('YP');"><span class="blind">����</span></button>
							<span class="input_type date ml10" style="width:114px;margin-left: 0px;">
								<input type="text" class="Calnew" placeholder="YYYY-MM-DD" id="SDATE1" name="SDATE1" value="<?=$sdate1?>">
							</span> 
							<span class="dash"> ~ </span>
							<span class="input_type date" style="width:114px">
								<input type="text" class="Calnew" placeholder="YYYY-MM-DD" id="SDATE2" name="SDATE2" value="<?=$sdate2?>">
							</span>
							<button type="button" class="btn_next" name="yn" id="yn" onclick="d_ser('YN');"><span class="blind">����</span></button>
							
							<p class="response_block" style="margin-left:10px">
								<span class="btn_wrap">
									<a class="btn_s white "	name="mp"   id="mp"   onclick="d_ser('MP');">����</a>
									<a class="btn_s white"		name="md"   id="md"     onclick="d_ser('MD');">���</a>
								</span>					

								<span class="btn_wrap" style="margin-left:10px">							
									<a class="btn_s white" name="m1"  id="m1" onclick="d_ser('M1');">1��</a>
									<a class="btn_s white" name="m2"  id="m2" onclick="d_ser('M2');">2��</a>
									<a class="btn_s white" name="m3"  id="m3" onclick="d_ser('M3');">3��</a>
									<a class="btn_s white" name="m4"  id="m4" onclick="d_ser('M4');">4��</a>
									<a class="btn_s white" name="m5"  id="m5" onclick="d_ser('M5');">5��</a>
									<a class="btn_s white" name="m6"  id="m6" onclick="d_ser('M6');">6��</a>
									<a class="btn_s white" name="m7"  id="m7" onclick="d_ser('M7');">7��</a>
									<a class="btn_s white" name="m8"  id="m8" onclick="d_ser('M8');">8��</a>
									<a class="btn_s white" name="m9"  id="m9" onclick="d_ser('M9');">9��</a>
									<a class="btn_s white" name="m10"  id="m10" onclick="d_ser('M10');">10��</a>
									<a class="btn_s white" name="m11"  id="m11" onclick="d_ser('M11');">11��</a>
									<a class="btn_s white" name="m12"  id="m12" onclick="d_ser('M12');">12��</a>
								</span>
								<span class="btn_wrap">
									<a class="btn_s white hover_btn" style="width:150px;margin: 0px;" onclick="common_ser();">���Լ���������ǥ��ȸ</a>										
								</span>
							</p>
						</div>
					</fieldset>
				</form>
			</div><!-- // box_wrap -->
 
			<div class="tb_type01 div_grid" style="overflow-y:auto;">
				<table id="sort_table" class="gridhover" >
					<colgroup>
						<col width="100px">
						<col width="200px">
						<col width="100px">
						<col width="100px">
						<col width="100px">
						<col width="100px">

						<col width="100px">
						<col width="100px">
						<col width="100px">
						<col width="100px">
						<col width="100px">
						<col width="100px">
						
						<col width="100px">
						<col width="100px">
						<col width="100px">
						<col width="100px">
						<col width="100px">
						<col width="100px">

					</colgroup>
					<thead>
					<tr class="rowTop">
						<th align="center">������</th>
						<th align="center">���ϸ�</th>
						<th align="right">�Ǽ�</th>						
						<th align="right">������1</th>
						<th align="right">������1�ݾ�</th>
						<th align="right">������2</th>
						<th align="right">������2�ݾ�</th>
						<th align="right">������3</th>
						<th align="right">������3�ݾ�</th>
						<th align="right">������4</th>
						<th align="right">������4�ݾ�</th>
						<th align="right">������5</th>
						<th align="right">������5�ݾ�</th>
						<th align="right">��Ÿ������</th>
						<th align="right">�������հ�</th>
						<th align="right">ȯ���ݾ�</th>					
						<th align="right">��Ȱ�ݾ�</th>
						<th align="right">�����հ�</th>
					</tr>
					</thead>
					<tbody>
						<?if(!empty($listData)){?>
						<?foreach($listData as $key => $val){extract($val);?>
						<tr class="rowData" rol-data='<?=$ipdate?>',     rol-gubun ='<?=$gubun?>' rol-gubunsub ='<?=$gubunsub?>'   rol-uplnum ='<?=$ino?>'    rol-filename ='<?=$FILENAME?>' rol-seq ='<?=$seq?>' style="cursor:pointer;">

  							<td align="left"><?=$NAME?></td>
							<?if ($seq == '1') {?>
								<td align="left"><?=mb_substr($FILENAME,0,20, 'euc-kr').'..'?></td>
								<td align="right"><?=number_format($cnt)?></td>
								<td align="left"><?=$st1?></td>
								<td align="right" ><?=number_format($samt1)?></td>
								<td align="left"  ><?=$st2?></td>
								<td align="right"  ><?=number_format($samt2)?></td>
								<td align="left"><?=$st3?></td>
								<td align="right" ><?=number_format($samt3)?></td>
								<td align="left"  ><?=$st4?></td> 
								<td align="right"  ><?=number_format($samt4)?></td>
								<td align="left"><?=$st5?></td>
								<td align="right" ><?=number_format($samt5)?></td> 
								<td align="right"  ><?=number_format($kita)?></td>
								<td align="right"  ><?=number_format($total)?></td>
								<td align="right"  ><?=number_format($hssu)?></td>
								<td align="right" ><?=number_format($bsu)?></td>
								<td align="right"  ><?=number_format($catotal)?></td>
							<?}else{ ?>
								<td align="left" style ="font-size: revert-layer; color:  #e33a3a;"><?=$FILENAME?></td>								
								<td align="right"style ="font-size: revert-layer; color: #e33a3a;"><?=number_format($cnt)?></td>
								<td align="left"style ="font-size: revert-layer; color: #e33a3a;"><?=$st1?></td>
								<td align="right"style ="font-size: revert-layer; color: #e33a3a;" ><?=number_format($samt1)?></td>
								<td align="left" style ="font-size: revert-layer; color: #e33a3a;"><?=$st2?></td>
								<td align="right"style ="font-size: revert-layer; color: #e33a3a;"><?=number_format($samt2)?></td>
								<td align="left"style ="font-size: revert-layer; color: #e33a3a;"><?=$st3?></td>
								<td align="right" style ="font-size: revert-layer; color: #e33a3a;"><?=number_format($samt3)?></td>
								<td align="left" style ="font-size: revert-layer; color: #e33a3a;"><?=$st4?></td> 
								<td align="right"style ="font-size: revert-layer; color: #e33a3a;"><?=number_format($samt4)?></td>
								<td align="left"style ="font-size: revert-layer; color: #e33a3a;"><?=$st5?></td>
								<td align="right" style ="font-size: revert-layer; color: #e33a3a;"><?=number_format($samt5)?></td> 
								<td align="right"style ="font-size: revert-layer; color: #e33a3a;"><?=number_format($kita)?></td>
								<td align="right"style ="font-size: revert-layer; color: #e33a3a;"><?=number_format($total)?></td>
								<td align="right" style ="font-size: revert-layer; color: #e33a3a;"><?=number_format($hssu)?></td>
								<td align="right" style ="font-size: revert-layer; color: #e33a3a;"><?=number_format($bsu)?></td>
								<td align="right" style ="font-size: revert-layer; color: #e33a3a;"><?=number_format($catotal)?></td>
							<?}?>	
						</tr>
						<?}}else{?>
							<tr>
								<td style="color:#8C8C8C" colspan=14>�˻��� �����Ͱ� �����ϴ�</td>
							</tr>
						<?}?>
					</tbody>
				</table>
			</div><!-- // tb_type01 -->

			<div style="text-align: center">		
				<ul class="pagination pagination-sm" style="margin: 10px">
				  <?=$pagination->create_links();?>
				</ul>
			</div>

		</fieldset>
	</div><!-- // content_wrap -->
</div>
<!-- // container -->
<!-- // wrap -->
<script type="text/javascript">


// ��� Ŭ��
$(".rowTop > th").click(function(){
	var trData = $(this).parent();

	var idx = $(trData).find("th").index($(this));

	// include/bottom.php ����	
	// �Ұ� �����ϴ� ������ ����
	//sortTable("sort_table", idx, 1);
})


// ������ȸ �Լ�(bin/js/common.js ȣ��)
function common_ser(){ 
		$("#div_load_image").show();
		$("form[name='searchFrm']").attr("method","get");
		$("form[name='searchFrm']").attr("target","");
		$("form[name='searchFrm']").submit();
}
 
//--->�Ⱓ����
function d_ser(bit){
		// ��ư Ŭ�������� �Ѱ��ֱ�
		var lower_str = bit.toLowerCase();	// �ҹ��ں�ȯ
		$("#btn").val(lower_str);

		var  sdate1	= document.getElementById('SDATE1').value;
		var  sdate2	= document.getElementById('SDATE2').value;
		var  str_date = bit + '&' + sdate1 + '&' + sdate2 ;
		
		//--������ ���� ��������
		str_date = date_on	(str_date);  //common.js ����  bin>js>common.js

		var bdate = str_date.split('&');
		$("form[name='searchFrm'] input[name='SDATE1']").val(bdate[0]); 
		$("form[name='searchFrm'] input[name='SDATE2']").val(bdate[1]); 
		
		//--->������ ���� �ٲ�� SERVER ���ϰɸ� 
		if (bit != 'YP' && bit != 'YN' ){
			common_ser();
		}
 }
 
$(document).ready(function(){

	$("#div_load_image").hide();
	//window.parent.postMessage("��������� > ������� ���ε� ����������ǥ", "*");   // '*' on any domain �θ�� ������..        


	// �� ���ý� �ش���� Ŭ���Ѱܹޱ�(function d_ser ���� ���� �ѱ�)
	var btn		= '<?=$_GET['btn']?>';
	if(btn){
		$(".box_wrap.sel_btn a").removeClass('on');
		$("#"+btn).addClass('on');
	}

 	// ����Ʈ Ŭ���� �󼼳��� ��ȸ
	$(".rowData > td").click(function(){
		var trData = $(this).parent();
		var idx    = $(".rowData").index($(trData));

		var upldate  = $(".rowData").eq(idx).attr('rol-data');
		var gubun  = $(".rowData").eq(idx).attr('rol-gubun');
		var gubunsub  = $(".rowData").eq(idx).attr('rol-gubunsub');
		var uplnum  = $(".rowData").eq(idx).attr('rol-uplnum');
		var filename  = $(".rowData").eq(idx).attr('rol-filename'); 
		var seq  = $(".rowData").eq(idx).attr('rol-seq'); 

		if (seq  != '1')	{
			return;
		}


		var left = Math.ceil((window.screen.width - 1200)/2);
		var top = Math.ceil((window.screen.height - 1000)/2); 
		var popOpen	= window.open("<?=$conf['homeDir']?>/sub/menu3/ga_menu3_72_list.php?upldate="+upldate+"&gubun=" +gubun+"&gubunsub=" + gubunsub+"&uplnum=" +uplnum +"&filename=" +filename                    ,"width=1200px,height=950px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
		popOpen.focus();
 
	})

});
</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>