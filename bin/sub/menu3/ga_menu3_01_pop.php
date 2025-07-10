<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

$inscode	= $_GET['inscode'];
$kcode		= $_GET['kcode'];
$tongha		= $_GET['tongha'];	// 상담현황에서 넘어올 경우 상담탭으로 이동하기 위한 변수(Y:상담현황에서 팝업오픈할 경우)


// 사용인코드 가져오기
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

<!-- html영역 -->
<style>
body{background-image: none;}

</style>

<div class="container">
	<div class="content_wrap" >
		<fieldset>

			<div class="data_pop_left" style="height:820px;min-height:800px;overflow:hidden;">
				<div class="popmenu">
					<ul class="menu_section">
						<li class="sub" data-name="01"><a href="#" ><img src="/bin/image/menu_03_min.png" class="icon">계약상세정보</a></li>
						<li class="sub" data-name="02"><a href="#" ><img src="/bin/image/menu_04_min.png" class="icon">수금현황</a></li>
						<li class="sub" data-name="03"><a href="#" ><img src="/bin/image/menu_05_min.png" class="icon">수입수수료</a></li>
						<li class="sub" data-name="04"><a href="#" ><img src="/bin/image/menu_07_min.png" class="icon">지급수수료</a></li>
						<li class="sub" data-name="05"><a href="#" ><img src="/bin/image/menu_02_min.png" class="icon">계약변동</a></li>
						<li class="sub sub06" data-name="06"><a href="#" ><img src="/bin/image/menu_08_min.png" class="icon">상담관리</a></li>
						<li class="sub" data-name="07"><a href="#" ><img src="/bin/image/menu_01_min.png" class="icon">원수사정보</a></li>
						<li class="sub" data-name="08"><a href="#" ><img src="/bin/image/menu_10_min.png" class="icon">사용인 실적</a></li>
						<li class="sub" data-name="09"><a href="#" ><img src="/bin/image/menu_13_min.png" class="icon">SMS 내역</a></li>
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
<img src="//t1.daumcdn.net/postcode/resource/images/close.png" id="btnCloseLayer" style="cursor:pointer;position:absolute;right:-3px;top:-3px;z-index:2" onclick="closeDaumPostcode()" alt="닫기 버튼">
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

// 우편번호 찾기 화면을 넣을 element
var element_layer = document.getElementById('layer');

function closeDaumPostcode() {
	// iframe을 넣은 element를 안보이게 한다.
	element_layer.style.display = 'none';
}

function DaumPostcode(gubun) {
	new daum.Postcode({
		oncomplete: function(data) {
			// 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.

			// 각 주소의 노출 규칙에 따라 주소를 조합한다.
			// 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
			var fullAddr = data.address; // 최종 주소 변수
			var extraAddr = ''; // 조합형 주소 변수

			// 기본 주소가 도로명 타입일때 조합한다.
			if(data.addressType === 'R'){
				//법정동명이 있을 경우 추가한다.
				if(data.bname !== ''){
					extraAddr += data.bname;
				}
				// 건물명이 있을 경우 추가한다.
				if(data.buildingName !== ''){
					extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
				}
				// 조합형주소의 유무에 따라 양쪽에 괄호를 추가하여 최종 주소를 만든다.
				fullAddr += (extraAddr !== '' ? ' ('+ extraAddr +')' : '');
			}

			// 우편번호와 주소 정보를 해당 필드에 넣는다.
			// A:계약자, B:피보험자
			if(gubun == 'A'){
				document.getElementById('post').value = data.zonecode;
				document.getElementById('addr').value = data.address;
				document.getElementById('addr_dt').focus();
			}else{
				document.getElementById('ppost').value = data.zonecode;
				document.getElementById('paddr').value = data.address;
				document.getElementById('paddr_dt').focus();				
			}

			// iframe을 넣은 element를 안보이게 한다.
			// (autoClose:false 기능을 이용한다면, 아래 코드를 제거해야 화면에서 사라지지 않는다.)
			var guideTextBox = document.getElementById("guide");
			guideTextBox.style.display = 'none';

			//element_layer.style.display = 'none';
		},
		width : '100%',
		height : '100%',
		maxSuggestItems : 5
	}).open();

	// iframe을 넣은 element를 보이게 한다.
	//element_layer.style.display = 'block';

	// iframe을 넣은 element의 위치를 화면의 가운데로 이동시킨다.
	//initLayerPosition();
}

// 브라우저의 크기 변경에 따라 레이어를 가운데로 이동시키고자 하실때에는
// resize이벤트나, orientationchange이벤트를 이용하여 값이 변경될때마다 아래 함수를 실행 시켜 주시거나,
// 직접 element_layer의 top,left값을 수정해 주시면 됩니다.
function initLayerPosition(){
	var width = 500; //우편번호서비스가 들어갈 element의 width
	var height = 650; //우편번호서비스가 들어갈 element의 height
	var borderWidth = 5; //샘플에서 사용하는 border의 두께

	// 위에서 선언한 값들을 실제 element에 넣는다.
	element_layer.style.width = width + 'px';
	element_layer.style.height = height + 'px';
	element_layer.style.border = borderWidth + 'px solid';
	// 실행되는 순간의 화면 너비와 높이 값을 가져와서 중앙에 뜰 수 있도록 위치를 계산한다.
	element_layer.style.left = (((window.innerWidth || document.documentElement.clientWidth) - width)/2 - borderWidth) + 'px';
	element_layer.style.top = (((window.innerHeight || document.documentElement.clientHeight) - height)/2 - borderWidth) + 'px';
}

// 왼쪽 메뉴 클릭시 순번대로 메뉴 매칭(data-name)
$(".sub").click(function(){
	
	// 기간병고객상담에서 해당 계약 오픈 시 상담메뉴탭으로 이동
	if('<?=$tongha?>' == 'Y'){
		var name = "06";
	}else{
		var name = $(this).data('name');
	}

	ajaxLodingTarket('ga_menu3_01_pop_sub'+name+'.php',$('#kwnDt_data'),'kcode=<?=$kcode?>&inscode=<?=$inscode?>&kskey=<?=$kskey?>');
}); 

$(document).ready(function(){

	// 팝업 오픈 시 계약상세정보 디폴트조회
	ajaxLodingTarket('ga_menu3_01_pop_sub01.php',$('#kwnDt_data'),'kcode=<?=$kcode?>&inscode=<?=$inscode?>');


	// 상담메뉴에서 오픈할 경우 해당 탭 오픈 
	if('<?=$tongha?>' == 'Y'){
		$(".sub").trigger("click");
	}

	// 좌측 메뉴 클릭 시 hover유지, 다른 메뉴 클릭 시 기존 hover 제거
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