<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

$inscode	= $_GET['inscode'];
$kcode		= $_GET['kcode'];
$tongha		= $_GET['tongha'];	// �����Ȳ���� �Ѿ�� ��� ��������� �̵��ϱ� ���� ����(Y:�����Ȳ���� �˾������� ���)


// ������ڵ� ��������
$sql  = "
		select  kskey
		from kwn
		where scode = '".$_SESSION['S_SCODE']."'
		  and inscode = '".$inscode."'
		  and kcode = '".$kcode."'	";
$result  = sqlsrv_query( $mscon, $sql );
$row =  sqlsrv_fetch_array($result); 

$kskey	= $row['kskey'];

?>

<!-- html���� -->
<style>
body{background-image: none;}

</style>

<div class="container">
	<div class="content_wrap" >
		<fieldset>

			<div class="data_pop_left" style="height:820px;min-height:800px;overflow:hidden;">
				<div class="popmenu">
					<ul class="menu_section">
						<li class="sub" data-name="01"><a href="#" ><img src="/bin/image/menu_03_min.png" class="icon">��������</a></li>
						<li class="sub" data-name="02"><a href="#" ><img src="/bin/image/menu_04_min.png" class="icon">������Ȳ</a></li>
						<li class="sub" data-name="03"><a href="#" ><img src="/bin/image/menu_05_min.png" class="icon">���Լ�����</a></li>
						<li class="sub" data-name="04"><a href="#" ><img src="/bin/image/menu_07_min.png" class="icon">���޼�����</a></li>
						<li class="sub" data-name="05"><a href="#" ><img src="/bin/image/menu_02_min.png" class="icon">��ຯ��</a></li>
						<li class="sub sub06" data-name="06"><a href="#" ><img src="/bin/image/menu_08_min.png" class="icon">������</a></li>
						<li class="sub" data-name="07"><a href="#" ><img src="/bin/image/menu_01_min.png" class="icon">����������</a></li>
						<li class="sub" data-name="08"><a href="#" ><img src="/bin/image/menu_10_min.png" class="icon">����� ����</a></li>
						<li class="sub" data-name="09"><a href="#" ><img src="/bin/image/menu_13_min.png" class="icon">SMS ����</a></li>
					</ul>
				</div>
			</div>

			<div class="data_pop_right" > <!--data_left start -->

				<div id="kwnDt_data" style="height:820px;min-height:800px;">

				</div>

			</div><!-- End data_pop_right -->


		</fieldset>

	</div><!-- // content_wrap -->
</div>
<!-- // container -->



<div id="layer" style="display:none;position:fixed;overflow:hidden;z-index:2;-webkit-overflow-scrolling:touch;">
<img src="//t1.daumcdn.net/postcode/resource/images/close.png" id="btnCloseLayer" style="cursor:pointer;position:absolute;right:-3px;top:-3px;z-index:2" onclick="closeDaumPostcode()" alt="�ݱ� ��ư">
</div>

<span id="guide" style="color:#999;display:none"></span>

<script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>

<style>
/* 
body{background-image:none;}
.container{margin:20px 20px 20px 20px;} 
 */
</style>

<script type="text/javascript">

// �����ȣ ã�� ȭ���� ���� element
var element_layer = document.getElementById('layer');

function closeDaumPostcode() {
	// iframe�� ���� element�� �Ⱥ��̰� �Ѵ�.
	element_layer.style.display = 'none';
}

function DaumPostcode(gubun) {
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
			// A:�����, B:�Ǻ�����
			if(gubun == 'A'){
				document.getElementById('post').value = data.zonecode;
				document.getElementById('addr').value = data.address;
				document.getElementById('addr_dt').focus();
			}else{
				document.getElementById('ppost').value = data.zonecode;
				document.getElementById('paddr').value = data.address;
				document.getElementById('paddr_dt').focus();				
			}

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

// ���� �޴� Ŭ���� ������� �޴� ��Ī(data-name)
$(".sub").click(function(){
	
	// �Ⱓ������㿡�� �ش� ��� ���� �� ���޴������� �̵�
	if('<?=$tongha?>' == 'Y'){
		var name = "06";
	}else{
		var name = $(this).data('name');
	}

	ajaxLodingTarket('ga_menu3_01_pop_sub'+name+'.php',$('#kwnDt_data'),'kcode=<?=$kcode?>&inscode=<?=$inscode?>&kskey=<?=$kskey?>');
}); 

$(document).ready(function(){

	// �˾� ���� �� �������� ����Ʈ��ȸ
	ajaxLodingTarket('ga_menu3_01_pop_sub01.php',$('#kwnDt_data'),'kcode=<?=$kcode?>&inscode=<?=$inscode?>');


	// ���޴����� ������ ��� �ش� �� ���� 
	if('<?=$tongha?>' == 'Y'){
		$(".sub").trigger("click");
	}

	// ���� �޴� Ŭ�� �� hover����, �ٸ� �޴� Ŭ�� �� ���� hover ����
	$(".menu_section li").on("click", function () {
		$(".menu_section li").removeClass("hover");
		//$(this).not(this).removeClass('hover');
		$(this).addClass("hover");
	});


});


</script>
<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>