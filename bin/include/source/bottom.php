<?if(isset($ajaxType)==true) return;?>

<!--로딩바 영역-->

<div id="div_load_image" style="position:absolute; top:50%; left:50%;width:0px;height:0px; z-index:9999; background:#f0f0f0; filter:alpha(opacity=50); opacity:alpha*0.5; margin:auto; padding:0; text-align:center"> <img src="/bin/img/loadingbar.gif" style="width:100px; height:100px;"> </div>


<!-- 모바일 로딩바 -->
<div id="loadingImage" style="z-index: 2000;position: fixed;width: 100%;height: 100%;text-align: center;padding-top: 26%;display:none;"><i class="fa fa-spinner fa-pulse fa-fw fa-5x"></i></div>

<script>


function ajaxLodingTarget(url,etcData,event,target){
	$.ajax({
	  url: url+"?"+etcData,
	  cache : false,
	  dataType : "html",
	  method : "GET",
	  data: { ajaxType : true, KCODE:""},
	}).done(function(htmlData) {
		$(target).html(htmlData);
		$("#loadingImage").hide();
		$("#div_load_image").hide();
	});
};



function ajaxLodingTarket(url,target,etcData){
	$("#loadingView").hide();
	$.ajax({
	  url: url+"?"+etcData,
	  cache : false,
	  dataType : "html",
	  method : "GET",
	  data: { ajaxType : true},
	  headers : {"charset":"euc-kr"},
	}).done(function(htmlData) {
		$(target).html(htmlData);
	});
};


function ajaxLodingForm(url,target,formData){
	$("#loadingView").hide();
	$.ajax({
	  url: url,
	  cache : false,
	  dataType : "html",
	  method : "POST",
	  data: formData,
	  headers : {"charset":"euc-kr"},
	}).done(function(htmlData) {
		$("#div_load_image").hide();
		$(target).html(htmlData);
	});
};


function m_ajaxLodingTarget(url,etcData,event,target){
	$("#loadingImage").show();
	$.ajax({
	  url: url+"?"+etcData,
	  cache : false,
	  dataType : "html",
	  method : "GET",
	  data: { ajaxType : true, KCODE:""},
	}).done(function(htmlData) {
		$(target).append(htmlData);
		$("#loadingImage").hide();
	});
};

$.ajaxLoding	= function(url,target,modal,etcData){

	$("#loadingView").hide();
	$(".w3-modal-content").css("width","90%");

	$.ajax({
	  url: url+"?"+etcData,
	  cache : false,
	  dataType : "html",
	  method : "GET",
	  data: { ajaxType : true},
	  headers : {"charset":"euc-kr"},
	}).done(function(htmlData) {
		$(target).html(htmlData);
		$(modal).show();
	});
}; 

// 기본 화면높이 지정
var windowResize	= function(win){
	var boxh  =  $( ".box_wrap" ).height();
	// page 존재하는 화면 높이
	$(".div_grid").height($(win).height()-(63+boxh));

	// page 없는 화면단 높이
	$(".div_grid2").height($(win).height()-(40+boxh));

	// 전체화면 리스트 화면 높이(페이지:Y) / ex:ga_menu3_52_list.php
	$(".div_grid3").height($(win).height()-(85+boxh));

	// 교육관리(상단집계로 인한 그리드높이 별도설정)
	$(".div_grid4").height($(win).height()-(150+boxh));
};

$(document).ready(function(){
	$("#div_load_image").hide();

	// input자동입력 change event
	(function ($) {
		var originalVal = $.fn.val;
		$.fn.val = function (value) {
			var res = originalVal.apply(this, arguments);

			if (this.is('input:text') && arguments.length >= 1) {
				this.trigger("input");
			}
			return res;
		};

	})(jQuery);

	// 윈도우 리사이즈
	$( window ).resize(function() { 
		windowResize($(this));
	});
	windowResize($( window ));
});

// 빈값체크
function isEmpty(val) {
    if (val == null || val.replace(/ /gi,"") == "") {
        return true;
    }
    return false;
}

// 숫자만 기입
// onkeydown="return onlyNumber(event)" 
function onlyNumber(event){
	event = event || window.event;
	var keyID = (event.which) ? event.which : event.keyCode;
	if ( (keyID >= 48 && keyID <= 57) || (keyID >= 96 && keyID <= 105) || keyID == 8 || keyID == 46 || keyID == 37 || keyID == 39 ) 
		return;
	else
		return false;
}

// 숫자가 아닌 문자 제거하기
// onkeyup="removeChar(event)"
function removeChar(event) {
	event = event || window.event;
	var keyID = (event.which) ? event.which : event.keyCode;
	if ( keyID == 8 || keyID == 46 || keyID == 37 || keyID == 39 ) 
		return;
	else
		event.target.value = event.target.value.replace(/[^0-9]/g, "");
}


// 테이블내 헤더 정렬(DB xx)
// startline 디폴트 1행부터, 첫행이 합계라면 2로 선언, 데이터시작행부터 선언
function sortTable(table_id, sortNo, startline){	
	$("#div_load_image").show();

	var table, rows, switching, o, x, y, shouldswitch, dir, switchcount = 0;

	// 시작라인 여부
	if(startline){
		var i = startline;
	}else{
		var i = 1;
	}

	table = document.getElementById(table_id);
	switching = true;
	dir = "asc";

	while(switching){
		switching = false;
		rows = table.getElementsByTagName("tr");

		for(o=i; o < (rows.length-1); o++){
			shouldswitch = false;
			x = rows[o].getElementsByTagName("td")[sortNo];
			y = rows[o+1].getElementsByTagName("td")[sortNo];

			if(dir == "asc"){
				if(x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()){
					shouldswitch = true;
					break;
				}
			}else if(dir == "desc"){
				if(x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()){
					shouldswitch = true;
					break;
				}
			} // End dir if

		} // End for

		if(shouldswitch){
			rows[o].parentNode.insertBefore(rows[o+1], rows[o]);
			switching = true;
			switchcount ++;
		}else{
			if(switchcount == 0 && dir == "asc"){
				dir = "desc";
				switching = true;
			}
		} // End shouldswitch if

	} // End while

	$("#div_load_image").hide();
}


</script>
</body>
</html>
