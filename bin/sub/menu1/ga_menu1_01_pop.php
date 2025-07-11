<?
//error_reporting(E_ALL); ini_set('display_errors', 1);

if (session_status() == PHP_SESSION_NONE) {
	session_start();
}

include($_SERVER['DOCUMENT_ROOT']."/".$_SESSION['S_SCODE']."/bin/include/source/head.php");

$gcode	= $_GET['gcode'];

// ������ ��������
if($_GET['gcode']){
	$type	= 'up';
	$txtnm	= '��������';

	$sql= "
		select 
				a.scode,
				a.gcode,
				a.kname,
				a.sbit,
				dbo.decryptkey(a.sjuno) sjuno,
				substring(dbo.decryptkey(a.sjuno),1,6) sjuno1,
				substring(dbo.decryptkey(a.sjuno),7,7) sjuno2,
				a.snum,
				substring(a.snum,1,3) snum1,
				substring(a.snum,4,2) snum2,
				substring(a.snum,6,5) snum3,
				case when a.sbit = '1' then dbo.decryptkey(a.sjuno) else a.snum end secdata,
				a.comnm,
				a.cupnm,
				a.psrate,
				a.emailsel,
				a.email,
				a.telbit,
				a.tel1,
				a.tel2,
				a.tel3,
				a.tel1+'-'+a.tel2+'-'+a.tel3 tel,
				a.htelbit,
				a.htel1,
				a.htel2,
				a.htel3,
				a.htel1+'-'+a.htel2+'-'+a.htel3 htel,
				case when len(isnull(a.htel1,'')+'-'+isnull(a.htel2,'')+'-'+isnull(a.htel3,'')) > 2 then isnull(a.htel1,'')+'-'+isnull(a.htel2,'')+'-'+isnull(a.htel3,'') 
					 else isnull(a.tel1,'')+'-'+isnull(a.tel2,'')+'-'+isnull(a.tel3,'') end totaltel,
				a.addbit,
				a.post,
				a.addr,
				a.addr_dt,
				a.bigo,
				a.sugi,
				a.kdate,
				convert(varchar,a.idate,21) idate,
				a.iswon,
				b.sname isname,
				a.udate,
				a.uswon,
				c.sname usname,
				a.ksman,
				e.sname ksname,
				case when isnull(a.htel1,'')+isnull(a.htel2,'')+isnull(a.htel3,'') <> '' and len(isnull(a.htel1,'')+isnull(a.htel2,'')+isnull(a.htel3,'')) >= 10 and substring(isnull(a.htel1,''),1,2) = '01'
								then 'Y' else 'N' end smsyn ,
				row_number()over(order by a.kdate desc, a.gcode desc) rnum
		from kwngo(nolock) a
			left outer join swon(nolock) b on a.scode = b.scode and a.iswon = b.skey
			left outer join swon(nolock) c on a.scode = c.scode and a.uswon = c.skey
			left outer join swon(nolock) e on a.scode = e.scode and a.ksman = e.skey
		where a.scode = '".$_SESSION['S_SCODE']."' 
		  and a.gcode = '".$_GET['gcode']."' ";

	$qry	= sqlsrv_query( $mscon, $sql );
	extract($fet	= sqlsrv_fetch_array($qry));


	// �������೻��(���)
	$sql= "
		select 
				a.kcode,
				a.num,
				a.tondat,
				a.tontim,
				a.gubun,
				b.subnm,
				c.sname,
				a.tontxt
		from atongha(nolock) a
			left outer join common(nolock) b on a.scode = b.scode and a.gubun = b.codesub and b.code = 'COM008'
			left outer join swon(nolock) c on a.scode = c.scode and a.iswon = c.skey
		where a.scode = '".$_SESSION['S_SCODE']."'
		  and a.kcode = '".$_GET['gcode']."'  
		order by a.num desc ";

	$qry	= sqlsrv_query( $mscon, $sql );
	$listData = array();
	while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
		$listData[]	= $fet;
	}


}else{
	$type	= 'in';
	$txtnm	= '�����';
}

// �ּұ���
$sql= "select codesub code, subnm name from common where scode = '".$_SESSION['S_SCODE']."' and code = 'COM001' and useyn = 'Y' order by num,codesub";
$qry= sqlsrv_query( $mscon, $sql );
$comData1	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
  $comData1[] = $fet;
}

// �̸��ϱ���
$sql= "select codesub code, subnm name from common where scode = '".$_SESSION['S_SCODE']."' and code = 'COM002' and useyn = 'Y' order by num,codesub";
$qry= sqlsrv_query( $mscon, $sql );
$comData2	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
  $comData2[] = $fet;
}

// ����ó����
$sql= "select codesub code, subnm name from common where scode = '".$_SESSION['S_SCODE']."' and code = 'COM003' and useyn = 'Y' order by num,codesub";
$qry= sqlsrv_query( $mscon, $sql );
$comData3	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
  $comData3[] = $fet;
}

// SMS GET������ ��ȣȭ
$where = " and a.gcode = '".$_GET['gcode']."'";
$where = Encrypt_where($where,$secret_key,$secret_iv);

?>

<!-- html���� -->
<style>
body{background-image: none;}

</style>

<div class="container">
	<div class="content_wrap">
		<fieldset>
			<legend>������ : �����߰�����</legend>
			<div class="tit_wrap">
				<span class="btn_wrap" >
					<a href="#" class="btn_s white" style="min-width:100px;" onclick="smspop();">SMS����</a>
					<a href="#" class="btn_s white" style="min-width:100px;" onclick="kwngo_new();">�ű�</a>
					<a href="#" class="btn_s white" style="min-width:100px;" onclick="kwngo_update();">����</a>
					<a href="#" class="btn_s white" style="min-width:100px;" onclick="kwngo_delete();">����</a>
					<a href="#" class="btn_s white" style="min-width:100px;" onclick="kwngo_close();">�ݱ�</a>
				</span>
			</div>

			<!-- //box_gray -->
			<div class="tb_type01 view">
				<form name="kwngo_form" class="ajaxForm_kwngo" method="post" action="ga_menu2_01_action.php">
				<input type="hidden" name="type" value="<?=$type?>">
					<table>
						<colgroup>
							<col width="18%">
							<col width="32%">
							<col width="18%">
							<col width="32%">
						</colgroup>
					<tbody>
						<tr>
							<th><em class="impor">*</em>����ȣ<em class="font_red bold"> (�ڵ�����)</em></th>
							<td><span class="input_type" style="width:300px" id="kwngo_input"><input type="text" name="gcode" id="gcode" value="<?=$gcode?>"></span></td>
							<th><em class="impor">*</em>����</th>
							<td><span class="input_type" style="width:300px"><input type="text" name="kname" id="kname" value="<?=trim($kname)?>"></span></td>
						</tr>
						<tr>
							<th>������</th>
							<td>
								<input type="radio" class="sbit" name="sbit" id="sbit1" value="1" <?if(trim($sbit)=='1') echo "checked";?>><label for="sbit1">���� </label>&nbsp;&nbsp;&nbsp;
								<input type="radio" class="sbit" name="sbit" id="sbit2" value="2" <?if(trim($sbit)=='2') echo "checked";?>><label for="sbit2">�����</label>
							</td>
							<th class="sjuno_tr">�ֹε�Ϲ�ȣ</th>							
							<td class="sjuno_tr">
								<span class="input_type" style="width:130px"><input type="text" value="<?=trim($sjuno1)?>" id="sjuno1" name="sjuno1" maxlength="6" oninput="NumberOnInput(this)"></span> - 
								<span class="input_type" style="width:160px"><input type="text" value="<?=trim($sjuno2)?>" id="sjuno2" name="sjuno2" maxlength="7" oninput="NumberOnInput(this)"></span>
							</td>
							<th class="snum_tr" style="display:none"><em class="impor">*</em>����ڹ�ȣ</th>
							<td class="snum_tr" style="display:none">
								<span class="input_type" style="width:70px"><input type="text" value="<?=trim($snum1)?>" id="snum1" name="snum1" maxlength="3" oninput="NumberOnInput(this)"></span> - 
								<span class="input_type" style="width:80px"><input type="text" value="<?=trim($snum2)?>" id="snum2" name="snum2" maxlength="2" oninput="NumberOnInput(this)"></span> -
								<span class="input_type" style="width:130px"><input type="text" value="<?=trim($snum3)?>" id="snum3" name="snum3" maxlength="5" oninput="NumberOnInput(this)"></span>
							</td>
							
						</tr>
						<tr>
							<th>�����/����</th>
							<td>
								<span class="input_type" style="width:144px"><input type="text" name="comnm" id="comnm" value="<?=trim($comnm)?>"></span> /
								<span class="input_type" style="width:144px"><input type="text" name="cupnm" id="cupnm" value="<?=trim($cupnm)?>"></span>
							</td>
							<th>��డ�ɼ�</th>
							<td>
								<select name="psrate" id="psrate" style="width:70px"> 
									  <?foreach($conf['psrate'] as $key => $val){?>
									  <option value="<?=$val?>" <?if($psrate==$val) echo "selected"?>><?=$val?></option>
									  <?}?>
								</select>
								<span style="margin-left:5px;display: inline-block;"> %</span>
							</td>
						</tr>
						<tr>
							<th>����ó</th>
							<td>
								<select name="tel1" id="tel1" style="width:90px"> 
									<option value="">����</option>
									  <?foreach($conf['pNum'] as $key => $val){?>
									  <option value="<?=$val?>" <?if($tel1==$val) echo "selected"?>><?=$val?></option>
									  <?}?>
								</select> -
								<span class="input_type" style="width:95px">
									<input type="text" name="tel2" id="tel2" value="<?=trim($tel2)?>" maxlength="4" title="��ȭ��ȣ �߰��ڸ� �Է�" oninput="NumberOnInput(this)">
								</span> -
								<span class="input_type" style="width:95px">
									<input type="text" name="tel3" id="tel3" value="<?=trim($tel3)?>" maxlength="4" title="��ȭ��ȣ ���ڸ� �Է�" oninput="NumberOnInput(this)">
								</span> 
							</td>
							<th><em class="impor">*</em>�޴���ȭ</th>
							<td>
								<select name="htel1" id="htel1" style="width:90px"> 
									<option value="">����</option>
									  <?foreach($conf['pNum'] as $key => $val){?>
									  <option value="<?=$val?>" <?if($htel1==$val) echo "selected"?>><?=$val?></option>
									  <?}?>
								</select> -
								<span class="input_type" style="width:95px">
									<input type="text" name="htel2" value="<?=trim($htel2)?>" maxlength="4" title="��ȭ��ȣ �߰��ڸ� �Է�" oninput="NumberOnInput(this)">
								</span> -
								<span class="input_type" style="width:95px">
									<input type="text" name="htel3" value="<?=trim($htel3)?>" maxlength="4" title="��ȭ��ȣ ���ڸ� �Է�" oninput="NumberOnInput(this)">
								</span> 
							</td>
						</tr>
						<tr>
							<th>����/�������</th>
							<td>
								<span class="input_type input_kdate date" style="width:300px" id="input_kdate"><input type="text" class="Calnew updis_date" name="kdate" value="<?if($kdate) echo date("Y-m-d",strtotime($kdate));?>" readonly></span> 
							</td>
							<th><em class="impor">*</em>�����</th>
							<td class="span_reset">
								<span class="input_type" style="width:100px"><input type="text" name="ksman" id="ksman" value="<?=trim($ksman)?>"></span>
								<a href="javascript:SwonSearch();" class="btn_s white">�˻�</a>
								<span class="ksname" style="width:140px;margin-left:5px"><?=trim($ksname)?></span>
							</td>
						</tr>
						<tr>
							<th>�ּ�</th>
							<td colspan="3">
								<span class="input_type"><input type="text" name="post" id="post" value="<?=$post?>" onclick="DaumPostcode();" readonly></span>
								<a href="javascript:DaumPostcode();" class="btn_s white" style="width:100px">�����ȣ ã��</a>
								<p class="mt5">
									<span class="input_type" style="width:390px"><input type="text" name="addr" id="addr" value="<?=$addr?>" readonly></span> 
									<span class="input_type" style="width:400px"><input type="text" name="addr_dt" id="addr_dt" value="<?=trim($addr_dt)?>"></span> 
								</p>
							</td>
						</tr>
						<tr>
							<th>�̸���</th>
							<td>			
								<span class="input_type" style="width:300px"><input type="text" name="email" id="email" value="<?=trim($email)?>"></span>
							</td>
							<th>�����ͱ���</th>
							<td>
								<input type="radio" class="sugi updis" name="sugi" id="sugi1" value="1" <?if(trim($sugi)=='1') echo "checked";?>><label for="sugi1">���� </label>&nbsp;&nbsp;&nbsp;
								<input type="radio" class="sugi updis" name="sugi" id="sugi2" value="2" <?if(trim($sugi)=='2') echo "checked";?>><label for="sugi2">DB��</label>
							</td>
						</tr>
						<tr>
							<th>���</th>
							<td colspan=3>
								<textarea style="width: 100%;height: 70px" name="bigo"><?=trim($bigo)?></textarea>
							</td>
						</tr>
					</tbody>
					</table>
				</form>
			</div>
			<!-- // tb_type01 -->


			<div class="tit_wrap" style="margin-top:5px">
				<div class="tit_wrap">
					<span class="btn_wrap">						
						<a href="#" class="btn_s white" id="tongha_btn" style="min-width:100px;" onclick="atongha_new('<?=$gcode?>','');">�����</a>						
					</span>
				</div>
			</div>

			<div class="tb_type02" style="max-height:310px;overflow-y:auto;">

				<table class="gridhover">

					<colgroup>
						<col width="12%">
						<col width="10%">
						<col width="12%">						
						<col width="12%">							
						<col width="auto">
					</colgroup>

					<thead>
					<tr>
						<th align="center">����</th>
						<th align="right">�ð�</th>
						<th align="right">����</th>
						<th align="right">�����</th>						
						<th align="right">��㳻��</th>
					</tr>
					</thead>
					<tbody>
						<?if(!empty($listData)){?>
						<?foreach($listData as $key => $val){extract($val);?>
						<tr class="rowData" style="cursor:pointer;" onclick="atongha_new('<?=$kcode?>','<?=$num?>')">
							<td align="center"><?if(trim($tondat)) echo date("Y-m-d",strtotime($tondat));?></td>
							<td align="center"><?=$tontim?></td>
							<td align="center"><?=$subnm?></td>							
							<td align="center"><?=$sname?></td>
							<td align="left"><?=$tontxt?></td>
						</tr>
						<?}}else{?>
							<tr>
								<td style="color:#8C8C8C" colspan=11>�˻��� �����Ͱ� �����ϴ�</td>
							</tr>
						<?}?>
					</tbody>
				</table>

			</div><!-- // tb_type02 -->



		</fieldset>

	</div><!-- // content_wrap -->
</div>
<!-- // container -->


<div id="layer" style="display:none;position:fixed;overflow:hidden;z-index:2;-webkit-overflow-scrolling:touch;">
<img src="//t1.daumcdn.net/postcode/resource/images/close.png" id="btnCloseLayer" style="cursor:pointer;position:absolute;right:-3px;top:-3px;z-index:2" onclick="closeDaumPostcode()" alt="�ݱ� ��ư">
</div>


<style>
/* 
body{background-image:none;}
.container{margin:20px 20px 20px 20px;} 
 */
</style>

<span id="guide" style="color:#999;display:none"></span>

<script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<script type="text/javascript">


// ����� �˾�
function atongha_new(kcode,num){

	if(!kcode){
		var kcode = $("form[name='kwngo_form'] input[name='gcode']").val();
	}

	var left = Math.ceil((window.screen.width - 700)/2);
	var top = Math.ceil((window.screen.height - 500)/2);
	var popOpen	= window.open("<?=$conf['homeDir']?>/sub/menu2/ga_menu2_01_atongha_pop.php?bit=1&gcode="+kcode+"&num=" +num  ,"atongha","width=700px,height=300px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
	popOpen.focus();
}

// �����ȣ ã�� ȭ���� ���� element
var element_layer = document.getElementById('layer');

function closeDaumPostcode() {
	// iframe�� ���� element�� �Ⱥ��̰� �Ѵ�.
	element_layer.style.display = 'none';
}

function DaumPostcode() {
	new daum.Postcode({
		oncomplete: function(data) {
			// �˻���� �׸��� Ŭ�������� ������ �ڵ带 �ۼ��ϴ� �κ�.

			// �� �ּ��� ���� ��Ģ�� ���� �ּҸ� �����Ѵ�.
			// �������� ������ ���� ���� ��쿣 ����('')���� �����Ƿ�, �̸� �����Ͽ� �б� �Ѵ�.
			var fullAddr = data.address; // ���� �ּ� ����
			var extraAddr = ''; // ������ �ּ� ����

			// �⺻ �ּҰ� ���θ� Ÿ���϶� �����Ѵ�.
			if(data.addressType === 'R'){
				//���������� ���� ��� �߰��Ѵ�.
				if(data.bname !== ''){
					extraAddr += data.bname;
				}
				// �ǹ����� ���� ��� �߰��Ѵ�.
				if(data.buildingName !== ''){
					extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
				}
				// �������ּ��� ������ ���� ���ʿ� ��ȣ�� �߰��Ͽ� ���� �ּҸ� �����.
				fullAddr += (extraAddr !== '' ? ' ('+ extraAddr +')' : '');
			}

			// �����ȣ�� �ּ� ������ �ش� �ʵ忡 �ִ´�.
			document.getElementById('post').value = data.zonecode;
			document.getElementById('addr').value = data.address;
			//document.getElementById('bcode').value = data.bcode;

			document.getElementById('addr_dt').focus();

			// iframe�� ���� element�� �Ⱥ��̰� �Ѵ�.
			// (autoClose:false ����� �̿��Ѵٸ�, �Ʒ� �ڵ带 �����ؾ� ȭ�鿡�� ������� �ʴ´�.)
			var guideTextBox = document.getElementById("guide");
			guideTextBox.style.display = 'none';

			//element_layer.style.display = 'none';
		},
		width : '100%',
		height : '100%',
		maxSuggestItems : 5
	}).open();

	// iframe�� ���� element�� ���̰� �Ѵ�.
	//element_layer.style.display = 'block';

	// iframe�� ���� element�� ��ġ�� ȭ���� ����� �̵���Ų��.
	//initLayerPosition();
}

// �������� ũ�� ���濡 ���� ���̾ ����� �̵���Ű���� �ϽǶ�����
// resize�̺�Ʈ��, orientationchange�̺�Ʈ�� �̿��Ͽ� ���� ����ɶ����� �Ʒ� �Լ��� ���� ���� �ֽðų�,
// ���� element_layer�� top,left���� ������ �ֽø� �˴ϴ�.
function initLayerPosition(){
	var width = 500; //�����ȣ���񽺰� �� element�� width
	var height = 650; //�����ȣ���񽺰� �� element�� height
	var borderWidth = 5; //���ÿ��� ����ϴ� border�� �β�

	// ������ ������ ������ ���� element�� �ִ´�.
	element_layer.style.width = width + 'px';
	element_layer.style.height = height + 'px';
	element_layer.style.border = borderWidth + 'px solid';
	// ����Ǵ� ������ ȭ�� �ʺ�� ���� ���� �����ͼ� �߾ӿ� �� �� �ֵ��� ��ġ�� ����Ѵ�.
	element_layer.style.left = (((window.innerWidth || document.documentElement.clientWidth) - width)/2 - borderWidth) + 'px';
	element_layer.style.top = (((window.innerHeight || document.documentElement.clientHeight) - height)/2 - borderWidth) + 'px';
}


// ��� ��� �˾�
function SwonSearch(){
	var left = Math.ceil((window.screen.width - 800)/2);
	var top = Math.ceil((window.screen.height - 800)/2);
	var popOpen	= window.open("<?=$conf['homeDir']?>/sub/help/ga_swon_search.php","swonpop","width=600px,height=800px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
	popOpen.focus();
}

function setSwonValue(row,code,name){
	$("#ksman").val(code);
	$('.ksname').text(name);
}

//  ���ڸ� �Է°���
function NumberOnInput(e)  {
  e.value = e.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1')
}

// �ű�
function kwngo_new(){
	location.href='ga_menu2_01_pop.php';
}


// ����
function kwngo_delete(){
	if(confirm("�����Ͻðڽ��ϱ�?")){
		document.kwngo_form.type.value = 'del';
		$("form[name='kwngo_form']").submit();
	}
}


// ����
function kwngo_update(){

	var kname   = $("form[name='kwngo_form'] input[name='kname']").val();
	var sbit    = $(':radio[name="sbit"]:checked').val();
	var sjuno1	= $("form[name='kwngo_form'] input[name='sjuno1']").val();
	var sjuno2	= $("form[name='kwngo_form'] input[name='sjuno2']").val();
	var snum1	= $("form[name='kwngo_form'] input[name='snum1']").val();
	var snum2	= $("form[name='kwngo_form'] input[name='snum2']").val();
	var snum3	= $("form[name='kwngo_form'] input[name='snum3']").val();

	var htel1   = $("form[name='kwngo_form'] select[name='htel1']").val();
	var htel2   = $("form[name='kwngo_form'] input[name='htel2']").val();
	var htel3   = $("form[name='kwngo_form'] input[name='htel3']").val();
	var ksman   = $("form[name='kwngo_form'] input[name='ksman']").val();

	var scode	= '<?=$_SESSION['S_SCODE']?>';


	if(isEmpty(kname) == true){
		alert('������ �Է��� �ּ���.');
		document.getElementById('sname').focus();
	}else if(isEmpty(htel1) == true || isEmpty(htel2) == true || isEmpty(htel3) == true){
		alert('�޴���ȭ�� �Է��� �ּ���.');
		document.getElementById('htel1').focus();
	}else if(isEmpty(ksman) == true){
		alert('������� �Է��� �ּ���.');
		document.getElementById('sspwd').focus();
	}else{
		if(confirm("�����Ͻðڽ��ϱ�?")){
			$('.updis_date').attr('disabled', false);	// disalbe ��� Ǯ���ְ� ����
			$("form[name='kwngo_form']").submit();
		}
	}
	
}


// �ݱ�
function kwngo_close(){	
	window.close();
	opener.location.reload();
}


function smspop(){
	var sdate1 = '';
	var sdate2 = '';
	var where = '<?=$where?>';
	var cnt = '1';
	var sms_type = 'sms_kwngo_gun';

	if('<?=$type?>' != 'up'){
		alert("������ �����Ͱ� �����ϴ�.");
		return false;
	}	

	if('<?=$smsyn?>' != 'Y'){
		alert("�������� �޴���ȭ��ȣ�� �����ϼ���.");
		return false;
	}

	var left = Math.ceil((window.screen.width - 500)/2);
	var top = Math.ceil((window.screen.height - 400)/2);
	var popOpen	= window.open("<?=$conf['homeDir']?>/sub/help/sms_pop.php?sdate1="+sdate1+"&sdate2="+sdate2+"&where="+where+"&cnt="+cnt+"&sms_type="+sms_type,"smskwngo3","width=500px,height=400px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
	popOpen.focus();
}


// ������� �� ���ε�
function reset(){
	var gcode = $("form[name='kwngo_form'] input[name='gcode']").val();

	location.href='ga_menu2_01_pop.php?gcode='+gcode;
}

$(document).ready(function(){

	// �����ͱ��� �����Ұ�
	$('.updis').attr('disabled', true);
	$('#sbit1').prop('checked',true);

	var sugi    = $(':radio[name="sugi"]:checked').val();

	// db������� ��� ������� �����Ұ�
	if(sugi == '2'){
		$('.updis_date').attr('disabled', true);
	}


	// ����ȣ �Է� �Ұ�
	$("input[name=gcode]").attr("readonly",true);
	$("#kwngo_input").css("backgroundColor","#EAEAEA");

	// ������ �����(����/�����)
	$("input[name='sbit']").change(function(){
		var sbit = $("input[name='sbit']:checked").val();

		if(sbit == '1'){
			$(".sjuno_tr").css("display","");
			$(".snum_tr").css("display","none");
		}else{
			$(".sjuno_tr").css("display","none");
			$(".snum_tr").css("display","");	
		}
	});

	// ȭ�� ���� �� �����п� ���� ������ ��ȸ
	var sbit_chk    = $(':radio[name="sbit"]:checked').val();
	if(sbit_chk == '1'){
		$(".sjuno_tr").css("display","");
		$(".snum_tr").css("display","none");
	}else{
		$(".sjuno_tr").css("display","none");
		$(".snum_tr").css("display","");	
	}

	// �������೻�� ����� �ű��϶��� �������� �ʰ�
	var type = '<?=$type?>';
	if(type == 'in'){
		$('#tongha_btn').css("display", "none");
	}else{
		$('#tongha_btn').css("display", "");
		$('.updis_date').attr('disabled', true);
		//$("#input_kdate").css("backgroundColor","#EAEAEA");
	}

	var options = { 
		dataType:  'json',
		beforeSubmit:  showRequest_modal_kwngo,  // pre-submit callback 
		success:       processJson_modal_kwngo  // post-submit callback 
	}; 

	$('.ajaxForm_kwngo').ajaxForm(options);

});

// pre-submit callback 
function showRequest_modal_kwngo(formData, jqForm, options) { 
	var queryString = $.param(formData); 
	return true; 
} 
 
// post-submit callback 
function processJson_modal_kwngo(data) { 
	if(data.message){
		alert(data.message);
		//opener.location.reload();
	}

	if(data.result==''){
		if(data.rtype == 'in'){
			document.kwngo_form.type.value = 'up';
			document.kwngo_form.gcode.value = data.gcode;
			$('#tongha_btn').css("display", "");
		}else if(data.rtype == 'del'){
			kwngo_new();
		}
		opener.location.reload();
	}

}

</script>
<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>