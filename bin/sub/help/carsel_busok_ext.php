<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");


$extdata	= $_GET['data'];

// Ư������ ����Ʈ
$sql	= "
			select 
					code,
					bigo,
					seq,
					seq%2 seq2
			from carext
			order by seq" ;

$result	= sqlsrv_query( $mscon, $sql );
$listData = array();
while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
	$listData[]	= $row;
}

// ��ü���� ��������
$sql	= "Select  Count(*) cnt
			from carext  ";

$qry =  sqlsrv_query($mscon, $sql);
$totalResult =  sqlsrv_fetch_array($qry); 

$totalCnt = (int)$totalResult['cnt'];

$cnt = 0;
// �ؿ� tr���� �������� �ַ��� Ȧ¦üũ
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
</style>

<div class="container container_bk">
	<div class="content_wrap">

		<div class="menu_group_top" style="border-bottom:0px solid;">
			<div class="menu_group">
				<span>�����ɼ�(Ư������)</span>
			</div>
		</div>

		
		<div class="tb_type01 tb_fix" style="height:380px;">
			<table class="gridhover">
				<colgroup>
					<col width="10%">
					<col width="40%">
					<col width="10%">
					<col width="40%">
				</colgroup>

				<tbody>
					<?for($i=0;$i<$cnt;$i++){?>
						<tr class="rowData">
							<?for($j=($i*2)+1;$j<=($i+1)*2;$j++){?>
								<td>
									<input type="checkbox" id="<?=$listData[$j-1]['code']?>">
									<input type="hidden" name = "<?=$listData[$j-1]['code']?>" value="<?=$listData[$j-1]['code']?>">
								</td>
								<!--<td align="left"><?=$listData[$j-1]['bigo']?></td>-->
								<td class="noborder" align="left"><span class="input_type"><input type="text" class = "<?=$listData[$j-1]['code']?>" value="<?=$listData[$j-1]['bigo']?>"></span></td>
							<?}?>
						</tr>
					<?}?>
				</tbody>
			</table>
		</div>
		
	</div>


	<div class="btn_wrap_center">
		<a href="#" class="btn_s white" onclick="next_step();">����</a>
		<a href="#" class="btn_s white" onclick="btn_close();">�ݱ�</a>
	</div>

	<p class="mt10 font_red">
	* ������ : ���ο� 6�ν����� �¿��� ���, ����/ȭ�� ���� �ʼ�<br>
	</p>
	<p>
	* TPMS : Ÿ�̾����а����ġ<br>
	* UBI���� : [����] ������������Ư�� �� �ش�<br>
	</p>
	<p class="mt10 font_blue">
	** ������Ż����,������������ ����<br>
	</p>
	<p>
	- ���� �ϳ��� �����ص� ������Ż���� Ư���� �����<br>
	- ������Ż���(LDWS) ��ɸ� �ִ� ���, ������Ż���� ����<br>
	- ������������(LKAS) ��ɸ� �ִ� ���, ������������ ����<br>
	- �� ��� ��� �ִ� ���, �Ѵ� ����<br>
	- �Ե��� ��츸, ������Ż���,������������ ���ÿ� ���� ���� ����<br>
	- �Ե��� ������ ������� � �׸��� �����ص� ���� ����<br>
	</p>
	<p class="mt10 font_blue">
	** �����浹����,�ڵ�������� ����<br>
	</p>
	<p>
	- ���� �ϳ��� �����ص� �����浹���� Ư���� �����<br>
	- �����浹���(FCW) ��ɸ� �ִ� ���, ������Ż���� ����<br>
	- �ڵ��������(AEB) ��ɸ� �ִ� ���, ������������ ����<br>
	- �� ��� ��� �ִ� ���, �Ѵ� ����<br>
	- �Ե��� ��츸, �����浹���,�ڵ�������� ���ÿ� ���� ���� ����<br>
	- �Ե��� ������ ������� � �׸��� �����ص� ���� ����<br>
	</p>


</div>


<script type="text/javascript">

// �ݱ�
function btn_close(){	
	window.close();
}


// ����
function next_step(){

	//alert(document.getElementById('ex1').innerText);
	
	var totcnt	= '<?=$totalCnt?>';
	var id		= '';	
	var chk		= '';
	var ext_data= "";
	var dash	= "";
	var ext_text= "";
	var comma	= "";

	for(var i=1; i<=totcnt; i++){
		id = 'ex'+i.toString();

		chk = $("#"+id).is(':checked');
		txt = $("."+id).val();

		if(chk == true){
			if(ext_data.length > 0) dash="-";
			ext_data = ext_data + dash + id;

			if(ext_text.length > 0) comma=",";
			ext_text = ext_text + comma + txt;
		}
	}

	opener.setCarBusok('A', ext_data, ext_text, '', 0);
	self.close();

	//console.log(ext_data);
	//console.log(ext_text);
}

$(document).ready(function(){

	window.resizeTo("600", "900");

	// �������ý�&���������� ������ �ڵ�����
	var data = "<?=$extdata?>";

	const arr_data = data.split('-');

	for(var i=0; i<arr_data.length; i++){
		$("#"+arr_data[i]).prop('checked',true);
	}

	

});

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>