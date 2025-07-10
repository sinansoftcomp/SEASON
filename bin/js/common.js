//-->FORM DATE   ~  TO DATE기간선택(240311 김순관)
function date_on(str_date){

	var bdate = str_date.split('&');

	var today = new Date();  
	today = today.toISOString().substring(0,10);	
	var mm_last = new Date(today.substr(0,4) , today.substr(5,2) , 0).getDate();

	// 년도 앞버튼 
	if (bdate[0] == 'YP' )	{
		const newDate = new Date(bdate[1]);
		newDate.setMonth(newDate.getMonth() - 1);
		var  sdate1 = newDate.toISOString().substring(0,10);	
		var  sdate2 =bdate[2];
		return sdate1 + '&' + sdate2;
	}
	// 년도 뒤버튼 
	if (bdate[0] == 'YN' )	{
		const newDate = new Date(bdate[2]);
		newDate.setMonth(newDate.getMonth() + 1);
		var  sdate1 =bdate[1];
		var  sdate2 = newDate.toISOString().substring(0,10);	
		return sdate1 + '&' + sdate2;
	}
	// 전월 버튼 
	if (bdate[0] == 'MP' )	{
		const newDate = new Date(today);
		newDate.setMonth(newDate.getMonth() - 1);
		var  sdate1 = newDate.toISOString().substring(0,10);	
		var mm_jun_last = new Date(sdate1.substr(0,4) , sdate1.substr(5,2) , 0).getDate();
		var  sdate1 =sdate1.substr(0,7) + '-01' ;
		var mm_last = new Date(sdate1.substr(0,4) , sdate1.substr(5,2) , 0).getDate();
		var  sdate2 = sdate1.substr(0,7) +'-'+mm_last ;
		return sdate1 + '&' + sdate2;
	}

	// 당월버튼 
	if (bdate[0] == 'MD' )	{
		var  sdate1 =today.substr(0,7) + '-01' ;
		var  sdate2 = today.substr(0,7) +'-'+mm_last ;
		return sdate1 + '&' + sdate2;
	}

	//월별 (선택한 월의  01~말일지정)	
	for (i=1; i<=12 ;i++ )	{
		var M = 'M'+i.toString();
 		if (bdate[0] == M )	{
			var  proc_m =  ('000'+ i.toString()).substr(-2);
			var  sdate1 =today.substr(0,4) + '-'  +  proc_m  +    '-01' ;
			var mm_last = new Date(sdate1.substr(0,4) , sdate1.substr(5,2) , 0).getDate();
			var  sdate2 = today.substr(0,4) + '-'  + proc_m  +  '-' +  mm_last  ;
			return sdate1 + '&' + sdate2; 
			 break; 	
		}		
	}
} 

function comma(str){
	str = uncomma(str);
	return str.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}


function uncomma(str){
	str = String(str);
		return str.replace(/,/g, '');
}

function numberOnly(str){
	var uncommaVal	= uncomma(str);
	if(uncommaVal=='0' || !$.isNumeric(uncommaVal)){
		return "";
	}else{
		return str;

	}
}

function numberOnly2(str){
	if($.isNumeric(str)){
		return str;
	}else{
		return "";
	}
	
}


function form_required_check(frm){
	var rtnData	= true;
	$(frm).find("input, select").each(function(){
		if($(this).attr("required") && $(this).val().trim()=="" && rtnData==true){
			alert($(this).attr("alt"));
			$(this).focus();
			rtnData	= false;
		}
	});
	return rtnData;
}


function date_check(frm){
	var rtnVal	= true;
	$(frm).find(".printCal, .printCal_ym").each(function(){
		var date_array	= $(this).val().split("-");
		if(date_array[0]){
			if($(this).next("input[type='hidden']").attr("name")!=undefined){
				if($.isNumeric(date_array[0])==false || date_array[0].length!=2 ){
					rtnVal=false;
				}
			}else{
				if($.isNumeric(date_array[0])==false || date_array[0].length!=4 ){
					rtnVal=false;
				}
			}
			
		}
		if(date_array[1]){
			if($.isNumeric(date_array[1])==false || date_array[1]<1 || date_array[1]>12){
				rtnVal=false;
			}
		}
		if(date_array[2]){
			if($.isNumeric(date_array[2])==false || date_array[2]<1 || date_array[2]>31){
				rtnVal=false;
			}
		}
		if(rtnVal==false){
			$(this).focus();
			alert('데이트 형식이 잘못되어있습니다.');
			return false;
		}
		
		//alert('1');
	});
	return rtnVal;
}

$(document).ready(function(){
	$(".numberOnly").keyup(function(){
		$(this).val(numberOnly($(this).val()));
	});

	$(".numberOnly2").keyup(function(){
		$(this).val(numberOnly2($(this).val()));
	});

	$('.inputNumAlpha').keyup(function (event) {
		var regType1 = /^[A-Za-z0-9+]*$/; 
		//alert(regType1.test($(this).val()));
		if(regType1.test($(this).val())!=true){
			$(this).val("");
			alert('영문과 숫자만 가능합니다.');
		}

		
	});
	
//	$(".printCal_ym").attr("readonly",true).css("text-align","center"); 영봉이가 주석걸어놓음 : 나온다 사장님이 달력을 수정할수 있어야한다고함
	$(".printCal_ym").css("text-align","center");
	

	$(".numberInput2").keyup(function() {
		var uncommaVal	= uncomma($(this).val());
		var commaVal	= comma($(this).val());
		if(uncommaVal=='0' || (!$.isNumeric(uncommaVal) && uncommaVal!='-')){
			$(this).val('');
		}else{
			$(this).val(commaVal);

		}
	});

	$('#modal1, .input_table').on('keyup',".numberInput3", function(){
		var uncommaVal	= uncomma($(this).val());
		var commaVal	= comma($(this).val());
		if(uncommaVal=='0' || (!$.isNumeric(uncommaVal) && uncommaVal!='-')){
			$(this).val('');
		}else{
			$(this).val(commaVal);

		}
	});


	$('#modal1, .input_table').on('keyup',".numberInput", function(){
		var uncommaVal	= uncomma($(this).val());
		var commaVal	= comma($(this).val());
		if(uncommaVal=='0' || (!$.isNumeric(uncommaVal) && uncommaVal!='-')){
			$(this).val('');
		}else{
			$(this).val(commaVal);

		}
	});

	$('body').on('focus',".printCal", function(){
//		$(this).attr("readonly",true);  영봉이가 주석걸어놓음 : 나온다 사장님이 달력을 수정할수 있어야한다고함
		var altF;
		var format;
		if($(this).next("input[type='hidden']").attr("name")!=undefined){
			altF= $(this).next("input[type='hidden']");
			format	= "y-mm-dd";
		}else{
			altF= $(this);
			format	= "yy-mm-dd";
		}
		$(this).datepicker({
			changeMonth: true,
		    changeYear: true,
			DefaultDate:null,
			yearRange: "1900:2100",
			dateFormat: format,
			prevText: '이전 달',
			nextText: '다음 달',
//      currentText : '오늘 날짜', // 오늘 날짜로 이동하는 버튼 패널  영봉이가 추가할려고했음 
//        closeText : '닫기', // 닫기 버튼 패널 영봉이가 추가할려고했음 
			monthNames: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
			monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
			dayNames: ['일','월','화','수','목','금','토'],
			dayNamesShort: ['일','월','화','수','목','금','토'],
			dayNamesMin: ['일','월','화','수','목','금','토'],
			showMonthAfterYear: true,
			yearSuffix: '년',
			beforeShow:function(input) {
				$(input).css({
					"position": "relative",
					"z-index": 999
				});
			},
			altField: altF,
			altFormat: "yy-mm-dd"
		});
	});

	$('body').on('focus',".Calnew", function(){
		var altF;
		var format;
		if($(this).next("input[type='hidden']").attr("name")!=undefined){
			altF= $(this).next("input[type='hidden']");
			format	= "y-mm-dd";
		}else{
			altF= $(this);
			format	= "yy-mm-dd";
		}
		$(this).datepicker({
			nextText: '다음 달', // next 아이콘의 툴팁.
			prevText: '이전 달', // prev 아이콘의 툴팁.
			showMonthAfterYear: true , // 월, 년순의 셀렉트 박스를 년,월 순으로 바꿔준다. 
			changeYear: true, // 년을 바꿀 수 있는 셀렉트 박스를 표시한다.
			changeMonth: true, // 월을 바꿀수 있는 셀렉트 박스를 표시한다.
			dateFormat: format,
			dayNamesMin: ['월', '화', '수', '목', '금', '토', '일'], // 요일의 한글 형식.
			monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'], // 월의 한글 형식.
			altFormat: "yy-mm-dd"
		});
	});


	$('body').on('focus',".Calnew_up", function(){
		var altF;
		var format;
		if($(this).next("input[type='hidden']").attr("name")!=undefined){
			altF= $(this).next("input[type='hidden']");
			format	= "y-mm-dd";
		}else{
			altF= $(this);
			format	= "yy-mm-dd";
		}
		$(this).datepicker({
			nextText: '다음 달', // next 아이콘의 툴팁.
			prevText: '이전 달', // prev 아이콘의 툴팁.
			showMonthAfterYear: true , // 월, 년순의 셀렉트 박스를 년,월 순으로 바꿔준다. 
			changeYear: true, // 년을 바꿀 수 있는 셀렉트 박스를 표시한다.
			yearRange: '1930:2030',
			changeMonth: true, // 월을 바꿀수 있는 셀렉트 박스를 표시한다.
			dateFormat: format,
			dayNamesMin: ['월', '화', '수', '목', '금', '토', '일'], // 요일의 한글 형식.
			monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'], // 월의 한글 형식.
			altFormat: "yy-mm-dd"
		});
	});


	$('body').on('focus',".Calnew_sin", function(){
		var altF;
		var format;
		if($(this).next("input[type='hidden']").attr("name")!=undefined){
			altF= $(this).next("input[type='hidden']");
			format	= "y-mm-dd";
		}else{
			altF= $(this);
			format	= "yy-mm-dd";
		}
		$(this).datepicker({
			nextText: '다음 달', // next 아이콘의 툴팁.
			prevText: '이전 달', // prev 아이콘의 툴팁.
			showMonthAfterYear: true , // 월, 년순의 셀렉트 박스를 년,월 순으로 바꿔준다. 
			changeYear: true, // 년을 바꿀 수 있는 셀렉트 박스를 표시한다.
			changeMonth: true, // 월을 바꿀수 있는 셀렉트 박스를 표시한다.
			dateFormat: format,
			dayNamesMin: ['월', '화', '수', '목', '금', '토', '일'], // 요일의 한글 형식.
			monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'], // 월의 한글 형식.
			altFormat: "yy-mm-dd",
				onSelect: function (dateText, inst) {   // 일자 선택된 후 이벤트 발생

					var startDate    = $(this).val();
					var startDateArr = startDate.split('-');
			 		var endDate = getTodayType2(); //2017-12-09
					var endDateArr = endDate.split('-');
								
					var startDateCompare = new Date(startDateArr[0], parseInt(startDateArr[1])-1, startDateArr[2]);
					var endDateCompare = new Date(endDateArr[0], parseInt(endDateArr[1])-1, endDateArr[2]);
					 
				//	console.log(startDateCompare);
								
					//console.log(endDateCompare);
					if(startDateCompare.getTime() > endDateCompare.getTime()) {
						 
						alert("작성일자는 현재일까지 입력하시기 바랍니다.");
						$(this).val(getTodayType2());
						return false;
					 }		
                }
		});
	});
/*오늘날자가져오기 2000-00-00 형식*/
	function getTodayType2(){
		var date = new Date();
		return date.getFullYear()+"-"+("0"+(date.getMonth()+1)).slice(-2)+"-"+("0"+date.getDate()).slice(-2);
	
	}


	$('body').on('focus',".printCal_ym", function(){
//		$(this).attr("readonly",true); 영봉이가 주석걸어놓음 : 나온다 사장님이 달력을 수정할수 있어야한다고함
		var altF;
		var format;
		if($(this).next("input[type='hidden']").attr("name")!=undefined){
			altF= $(this).next("input[type='hidden']");
			format	= "y-mm";
		}else{
			altF= $(this);
			format	= "yy-mm";
		}
		$(this).datepicker({
			changeMonth: true,
		    changeYear: true,
			DefaultDate:null,
			yearRange: "1900:2100",
			dateFormat: format,
			prevText: '이전 달',
			nextText: '다음 달',
			monthNames: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
			monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
			dayNames: ['일','월','화','수','목','금','토'],
			dayNamesShort: ['일','월','화','수','목','금','토'],
			dayNamesMin: ['일','월','화','수','목','금','토'],
			showMonthAfterYear: true,
			yearSuffix: '년',
			beforeShow:function(input) {
				$(input).css({
					"position": "relative",
					"z-index": 999
				});
			},
			altField: altF,
			altFormat: "yy-mm"
		});
	});


	$('body').on('focus',".Cal_ym", function(){
		var altF;
		var format;
		if($(this).next("input[type='hidden']").attr("name")!=undefined){
			altF= $(this).next("input[type='hidden']");
			format	= "y-mm";
		}else{
			altF= $(this);
			format	= "yy-mm";
		}
		$(this).datepicker({
			nextText: '다음 달!', // next 아이콘의 툴팁.
			prevText: '이전 달', // prev 아이콘의 툴팁.
			showMonthAfterYear: true , // 월, 년순의 셀렉트 박스를 년,월 순으로 바꿔준다. 
			changeYear: true, // 년을 바꿀 수 있는 셀렉트 박스를 표시한다.
			changeMonth: true, // 월을 바꿀수 있는 셀렉트 박스를 표시한다.
			dateFormat: format,
			dayNamesMin: ['월', '화', '수', '목', '금', '토', '일'], // 요일의 한글 형식.
			monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'], // 월의 한글 형식.
			altFormat: "yy-mm"
		});
	});


	$(".dateClear").click(function(){
		$(this).prev().val("");
	});

	$(".numberInput").keyup(function() {
		var uncommaVal	= uncomma($(this).val());
		var commaVal	= comma($(this).val());
		if(uncommaVal=='0' || (!$.isNumeric(uncommaVal) && uncommaVal!='-')){
			$(this).val('');
		}else{
			$(this).val(commaVal);

		}
	});
});


function leadingZeros(n, digits) {
  var zero = '';
  n = n.toString();

  if (n.length < digits) {
    for (var i = 0; i < digits - n.length; i++)
      zero += '0';
  }
  return zero + n;
}


function get_version_of_IE() { 

     var word; 
     var version = "N/A"; 

     var agent = navigator.userAgent.toLowerCase(); 
     var name = navigator.appName; 

     // IE old version ( IE 10 or Lower ) 
     if ( name == "Microsoft Internet Explorer" ) word = "msie "; 

     else { 
         // IE 11 
         if ( agent.search("trident") > -1 ) word = "trident/.*rv:"; 

         // Microsoft Edge  
         else if ( agent.search("edge/") > -1 ) word = "edge/"; 
     } 

     var reg = new RegExp( word + "([0-9]{1,})(\\.{0,}[0-9]{0,1})" ); 

     if (  reg.exec( agent ) != null  ) version = RegExp.$1 + RegExp.$2; 

     return version; 
} 


// 특별요율 변환
function ext_chng(str, sports){

	var ext_data = "";
	var dash = "";
	
	// 스포츠카(ex1)
	if (str.indexOf('스포츠카') != -1 || sports == 'Y') {
		ext_data = ext_data + 'ex1';
	}

	// 탑차(ex2)
	if(ext_data.length > 0) dash="-";
	if (str.indexOf('탑차') != -1) {
		ext_data = ext_data + dash + 'ex2';
	}

	// 커넥티드카(ex3)
	if(ext_data.length > 0) dash="-";
	if (str.indexOf('블루링크/유보') != -1 || str.indexOf('커넥티드카') != -1) {
		ext_data = ext_data + dash + 'ex3';
	}

	// 유상운송-여객(ex4)
	if(ext_data.length > 0) dash="-";
	if (str.indexOf('유상운송(여객)') != -1) {
		ext_data = ext_data + dash + 'ex4';
	}

	// 에어백싱글(ex6)
	if(ext_data.length > 0) dash="-";
	if (str.indexOf('AIR-S') != -1) {
		ext_data = ext_data + dash + 'ex6';
	}

	// 에어백듀얼(ex7)
	if(ext_data.length > 0) dash="-";
	if (str.indexOf('AIR-D') != -1) {
		ext_data = ext_data + dash + 'ex7';
	}

	// 오토(ex8)
	if(ext_data.length > 0) dash="-";
	if (str.indexOf('오토') != -1) {
		ext_data = ext_data + dash + 'ex8';
	}

	// 이모빌라이져(ex9)
	if(ext_data.length > 0) dash="-";
	if (str.indexOf('IM') != -1) {
		ext_data = ext_data + dash + 'ex9';
	}

	// ABS(ex12)
	if(ext_data.length > 0) dash="-";
	if (str.indexOf('ABS') != -1) {
		ext_data = ext_data + dash + 'ex12';
	}

	// 차선이탈방지(ex16)
	if(ext_data.length > 0) dash="-";
	if (str.indexOf('LDWS') != -1 || str.indexOf('차선이탈방지') != -1) {
		ext_data = ext_data + dash + 'ex16';
	}

	// 전방충돌방지(ex18)
	if(ext_data.length > 0) dash="-";
	if (str.indexOf('전방충돌방지') != -1) {
		ext_data = ext_data + dash + 'ex18';
	}

	// 유상운송-화물(ex19)
	if(ext_data.length > 0) dash="-";
	if (str.indexOf('유상운송(화물)') != -1) {
		ext_data = ext_data + dash + 'ex19';
	}

	return ext_data;
}

//2020-01-29 디자인 개선 추가
$(function(){
	//달력
	/*
	$("._datepick").datepicker({
		nextText: '다음 달', // next 아이콘의 툴팁.
		prevText: '이전 달', // prev 아이콘의 툴팁.
		showMonthAfterYear: true , // 월, 년순의 셀렉트 박스를 년,월 순으로 바꿔준다. 
		changeYear: true, // 년을 바꿀 수 있는 셀렉트 박스를 표시한다.
		changeMonth: true, // 월을 바꿀수 있는 셀렉트 박스를 표시한다.
		dayNamesMin: ['월', '화', '수', '목', '금', '토', '일'], // 요일의 한글 형식.
		monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'] // 월의 한글 형식.
	});
	*/

	//$('#gnb .depth4').slideUp('fast');

	//gnb
	$('#gnb .depth1').click(function(){
		var ly = $(this).next();		
		$('.depth2').not(ly).slideUp('fast');
		ly.slideDown('fast');
		$('#gnb .depth4').slideUp('fast');
		$("#depthyn").val("N");
	});

	$('#gnb .depth3').click(function(){

		var depthyn = $("#depthyn").val();

		if(depthyn == 'N'){
			var ly = $(this).next();		
			$('.depth4').not(ly).slideUp('fast');
			ly.slideDown('fast');
			$("#depthyn").val("Y");
		}else if(depthyn == 'Y'){
			$('#gnb .depth4').slideUp('fast');
			$("#depthyn").val("N");
		}


	});

	$('.depth2 a').click(function(){

		$('.depth2 li').removeClass("on");		
		$(this).parent().addClass("on");
		$('#ncl').removeClass("on");
		$('#nc2').removeClass("on");
	});


	//버튼선택
	$(".box_wrap.sel_btn a:not(.btn_off)").click(function(){
		$(".box_wrap.sel_btn a").removeClass('on')
		$(this).addClass('on')
	});

	$(".btn_wrap.sel_btn a:not(.btn_off)").click(function(){
		$(".btn_wrap.sel_btn a").removeClass('on')
		$(this).addClass('on')
	});


	/* 탭 */
	$('#tab li').click(function(e){
		e.preventDefault();
		var tab_id = $(this).attr('data-tab');

		$('#tab li').removeClass('on');
		$('.tab_con_wrap').removeClass('on');

		$(this).addClass('on');
		$("#"+tab_id).addClass('on');
	});

	/* 탭 */
	$('#tab2 li').click(function(e){
		e.preventDefault();
		var tab_id = $(this).attr('data-tab');

		$('#tab2 li').removeClass('on');
		$('.tab_con_wrap').removeClass('on');

		$(this).addClass('on');
		$("#"+tab_id).addClass('on');
	});

	/*
		상반기~하반기
		1분기~4분기
		1월~12월 검색버튼 모음
	*/

	// 이전 클릭 시
	$("#pre").click(function(){
		$("form[name='searchFrm']").attr("method","get");
		$("form[name='searchFrm']").attr("target","");
		$("form[name='searchFrm'] input[name='GUBUN']").val('F');
		$("form[name='searchFrm'] input[name='BIT']").val('FY');

		common_ser()
	}); 

	// 다음 클릭 시
	$("#next").click(function(){
		$("form[name='searchFrm']").attr("method","get");
		$("form[name='searchFrm']").attr("target","");
		$("form[name='searchFrm'] input[name='GUBUN']").val('T');
		$("form[name='searchFrm'] input[name='BIT']").val('TY');

		common_ser()
	}); 

	// 상반기 클릭 시 (1 / 2)
	$("#btn12").click(function(){
		$("form[name='searchFrm']").attr("method","get");
		$("form[name='searchFrm']").attr("target","");
		$("form[name='searchFrm'] input[name='BIT']").val('A1');
		$("form[name='searchFrm'] input[name='IDV']").val('btn12');
		
		$("form[name='searchFrm'] input[name='FMM']").val('01');
		$("form[name='searchFrm'] input[name='TMM']").val('06');
		$("form[name='searchFrm'] input[name='FDD']").val('01');
		$("form[name='searchFrm'] input[name='TDD']").val('30');
		common_ser()
	}); 

	// 하반기 클릭 시 (2 / 2)
	$("#btn22").click(function(){
		$("form[name='searchFrm']").attr("method","get");
		$("form[name='searchFrm']").attr("target","");
		$("form[name='searchFrm'] input[name='BIT']").val('A2');
		$("form[name='searchFrm'] input[name='IDV']").val('btn22');
		
		$("form[name='searchFrm'] input[name='FMM']").val('07');
		$("form[name='searchFrm'] input[name='TMM']").val('12');
		$("form[name='searchFrm'] input[name='FDD']").val('01');
		$("form[name='searchFrm'] input[name='TDD']").val('31');
		common_ser()
	}); 

	// 1분기 클릭 시 (1 / 4)
	$("#btn14").click(function(){
		$("form[name='searchFrm']").attr("method","get");
		$("form[name='searchFrm']").attr("target","");
		$("form[name='searchFrm'] input[name='BIT']").val('B1');
		$("form[name='searchFrm'] input[name='IDV']").val('btn14');
		
		$("form[name='searchFrm'] input[name='FMM']").val('01');
		$("form[name='searchFrm'] input[name='TMM']").val('03');
		$("form[name='searchFrm'] input[name='FDD']").val('01');
		$("form[name='searchFrm'] input[name='TDD']").val('31');
		common_ser()
	}); 

	// 2분기 클릭 시 (2 / 4)
	$("#btn24").click(function(){
		$("form[name='searchFrm']").attr("method","get");
		$("form[name='searchFrm']").attr("target","");
		$("form[name='searchFrm'] input[name='BIT']").val('B2');
		$("form[name='searchFrm'] input[name='IDV']").val('btn24');
		
		$("form[name='searchFrm'] input[name='FMM']").val('04');
		$("form[name='searchFrm'] input[name='TMM']").val('06');
		$("form[name='searchFrm'] input[name='FDD']").val('01');
		$("form[name='searchFrm'] input[name='TDD']").val('30');
		common_ser()
	}); 

	// 3분기 클릭 시 (3 / 4)
	$("#btn34").click(function(){
		$("form[name='searchFrm']").attr("method","get");
		$("form[name='searchFrm']").attr("target","");
		$("form[name='searchFrm'] input[name='BIT']").val('B3');
		$("form[name='searchFrm'] input[name='IDV']").val('btn34');
		
		$("form[name='searchFrm'] input[name='FMM']").val('07');
		$("form[name='searchFrm'] input[name='TMM']").val('09');
		$("form[name='searchFrm'] input[name='FDD']").val('01');
		$("form[name='searchFrm'] input[name='TDD']").val('30');
		common_ser()
	}); 

	// 4분기 클릭 시 (4 / 4)
	$("#btn44").click(function(){
		$("form[name='searchFrm']").attr("method","get");
		$("form[name='searchFrm']").attr("target","");
		$("form[name='searchFrm'] input[name='BIT']").val('B4');
		$("form[name='searchFrm'] input[name='IDV']").val('btn44');
		
		$("form[name='searchFrm'] input[name='FMM']").val('10');
		$("form[name='searchFrm'] input[name='TMM']").val('12');
		$("form[name='searchFrm'] input[name='FDD']").val('01');
		$("form[name='searchFrm'] input[name='TDD']").val('31');
		common_ser()
	}); 




	// 전월 클릭시
	$("#monf1").click(function(){
		const now = new Date();

		var fmm = ('0' + (now.getMonth())).slice(-2);
		var tmm = ('0' + (now.getMonth()+1)).slice(-2);

		if(fmm == '00'){
			fmm = '12';
		}

		var fdd = new Date('2024', fmm.substr(1,1), 0);
		var tdd = new Date('2024', tmm.substr(1,1), 0);

		fdd = fdd.getDate();
		tdd = tdd.getDate();

		$("form[name='searchFrm']").attr("method","get");
		$("form[name='searchFrm']").attr("target","");
		$("form[name='searchFrm'] input[name='BIT']").val('FM');
		$("form[name='searchFrm'] input[name='IDV']").val('btnf1');
		
		$("form[name='searchFrm'] input[name='FMM']").val(fmm);
		$("form[name='searchFrm'] input[name='TMM']").val(fmm);
		$("form[name='searchFrm'] input[name='FDD']").val('01');
		$("form[name='searchFrm'] input[name='TDD']").val(fdd);
		
		common_ser()
	}); 

	// 당월 클릭시
	$("#mont1").click(function(){
		const now = new Date();

		var fmm = ('0' + (now.getMonth())).slice(-2);
		var tmm = ('0' + (now.getMonth()+1)).slice(-2);

		if(fmm == '00'){
			fmm = '12';
		}

		var fdd = new Date('2024', fmm.substr(1,1), 0);
		var tdd = new Date('2024', tmm.substr(1,1), 0);

		fdd = fdd.getDate();
		tdd = tdd.getDate();

		$("form[name='searchFrm']").attr("method","get");
		$("form[name='searchFrm']").attr("target","");
		$("form[name='searchFrm'] input[name='BIT']").val('TM');
		$("form[name='searchFrm'] input[name='IDV']").val('btnt1');
		
		$("form[name='searchFrm'] input[name='FMM']").val(tmm);
		$("form[name='searchFrm'] input[name='TMM']").val(tmm);
		$("form[name='searchFrm'] input[name='FDD']").val('01');
		$("form[name='searchFrm'] input[name='TDD']").val(tdd);
		
		common_ser()
	}); 


	// 1월 클릭
	$("#mon01").click(function(){
		$("form[name='searchFrm']").attr("method","get");
		$("form[name='searchFrm']").attr("target","");
		$("form[name='searchFrm'] input[name='BIT']").val('M1');
		$("form[name='searchFrm'] input[name='IDV']").val('mon01');
		
		$("form[name='searchFrm'] input[name='FMM']").val('01');
		$("form[name='searchFrm'] input[name='TMM']").val('01');
		$("form[name='searchFrm'] input[name='FDD']").val('01');
		$("form[name='searchFrm'] input[name='TDD']").val('31');

 

		common_ser();
	}); 

	// 2월 클릭
	$("#mon02").click(function(){
		$("form[name='searchFrm']").attr("method","get");
		$("form[name='searchFrm']").attr("target","");
		$("form[name='searchFrm'] input[name='BIT']").val('M2');
		$("form[name='searchFrm'] input[name='IDV']").val('mon02');

		$("form[name='searchFrm'] input[name='FMM']").val('02');
		$("form[name='searchFrm'] input[name='TMM']").val('02');
		$("form[name='searchFrm'] input[name='FDD']").val('01');
		$("form[name='searchFrm'] input[name='TDD']").val('29');
		common_ser()
	}); 

	// 3월 클릭
	$("#mon03").click(function(){
		$("form[name='searchFrm']").attr("method","get");
		$("form[name='searchFrm']").attr("target","");
		$("form[name='searchFrm'] input[name='BIT']").val('M3');
		$("form[name='searchFrm'] input[name='IDV']").val('mon03');
		
		$("form[name='searchFrm'] input[name='FMM']").val('03');
		$("form[name='searchFrm'] input[name='TMM']").val('03');
		$("form[name='searchFrm'] input[name='FDD']").val('01');
		$("form[name='searchFrm'] input[name='TDD']").val('31');
		common_ser()
	}); 

	// 4월 클릭
	$("#mon04").click(function(){
		$("form[name='searchFrm']").attr("method","get");
		$("form[name='searchFrm']").attr("target","");
		$("form[name='searchFrm'] input[name='BIT']").val('M4');
		$("form[name='searchFrm'] input[name='IDV']").val('mon04');
		
		$("form[name='searchFrm'] input[name='FMM']").val('04');
		$("form[name='searchFrm'] input[name='TMM']").val('04');
		$("form[name='searchFrm'] input[name='FDD']").val('01');
		$("form[name='searchFrm'] input[name='TDD']").val('30');
		common_ser()
	}); 


	// 5월 클릭
	$("#mon05").click(function(){
		$("form[name='searchFrm']").attr("method","get");
		$("form[name='searchFrm']").attr("target","");
		$("form[name='searchFrm'] input[name='BIT']").val('M5');
		$("form[name='searchFrm'] input[name='IDV']").val('mon05');
		
		$("form[name='searchFrm'] input[name='FMM']").val('05');
		$("form[name='searchFrm'] input[name='TMM']").val('05');
		$("form[name='searchFrm'] input[name='FDD']").val('01');
		$("form[name='searchFrm'] input[name='TDD']").val('31');
		common_ser()
	}); 

	// 6월 클릭
	$("#mon06").click(function(){
		$("form[name='searchFrm']").attr("method","get");
		$("form[name='searchFrm']").attr("target","");
		$("form[name='searchFrm'] input[name='BIT']").val('M6');
		$("form[name='searchFrm'] input[name='IDV']").val('mon06');
		
		$("form[name='searchFrm'] input[name='FMM']").val('06');
		$("form[name='searchFrm'] input[name='TMM']").val('06');
		$("form[name='searchFrm'] input[name='FDD']").val('01');
		$("form[name='searchFrm'] input[name='TDD']").val('30');
		common_ser()
	}); 

	// 7월 클릭
	$("#mon07").click(function(){
		$("form[name='searchFrm']").attr("method","get");
		$("form[name='searchFrm']").attr("target","");
		$("form[name='searchFrm'] input[name='BIT']").val('M7');
		$("form[name='searchFrm'] input[name='IDV']").val('mon07');
		
		$("form[name='searchFrm'] input[name='FMM']").val('07');
		$("form[name='searchFrm'] input[name='TMM']").val('07');
		$("form[name='searchFrm'] input[name='FDD']").val('01');
		$("form[name='searchFrm'] input[name='TDD']").val('31');
		common_ser()
	}); 

	// 8월 클릭
	$("#mon08").click(function(){
		$("form[name='searchFrm']").attr("method","get");
		$("form[name='searchFrm']").attr("target","");
		$("form[name='searchFrm'] input[name='BIT']").val('M8');
		$("form[name='searchFrm'] input[name='IDV']").val('mon08');
		
		$("form[name='searchFrm'] input[name='FMM']").val('08');
		$("form[name='searchFrm'] input[name='TMM']").val('08');
		$("form[name='searchFrm'] input[name='FDD']").val('01');
		$("form[name='searchFrm'] input[name='TDD']").val('31');
		common_ser()
	}); 

	// 9월 클릭
	$("#mon09").click(function(){
		$("form[name='searchFrm']").attr("method","get");
		$("form[name='searchFrm']").attr("target","");
		$("form[name='searchFrm'] input[name='BIT']").val('M9');
		$("form[name='searchFrm'] input[name='IDV']").val('mon09');
		
		$("form[name='searchFrm'] input[name='FMM']").val('09');
		$("form[name='searchFrm'] input[name='TMM']").val('09');
		$("form[name='searchFrm'] input[name='FDD']").val('01');
		$("form[name='searchFrm'] input[name='TDD']").val('30');
		common_ser()
	}); 

	// 10월 클릭
	$("#mon10").click(function(){
		$("form[name='searchFrm']").attr("method","get");
		$("form[name='searchFrm']").attr("target","");
		$("form[name='searchFrm'] input[name='BIT']").val('M10');
		$("form[name='searchFrm'] input[name='IDV']").val('mon10');
		
		$("form[name='searchFrm'] input[name='FMM']").val('10');
		$("form[name='searchFrm'] input[name='TMM']").val('10');
		$("form[name='searchFrm'] input[name='FDD']").val('01');
		$("form[name='searchFrm'] input[name='TDD']").val('31');
		common_ser()
	}); 

	// 11월 클릭
	$("#mon11").click(function(){
		$("form[name='searchFrm']").attr("method","get");
		$("form[name='searchFrm']").attr("target","");
		$("form[name='searchFrm'] input[name='BIT']").val('M11');
		$("form[name='searchFrm'] input[name='IDV']").val('mon11');
		
		$("form[name='searchFrm'] input[name='FMM']").val('11');
		$("form[name='searchFrm'] input[name='TMM']").val('11');
		$("form[name='searchFrm'] input[name='FDD']").val('01');
		$("form[name='searchFrm'] input[name='TDD']").val('30');
		common_ser()
	}); 

	// 12월 클릭
	$("#mon12").click(function(){
		$("form[name='searchFrm']").attr("method","get");
		$("form[name='searchFrm']").attr("target","");
		$("form[name='searchFrm'] input[name='BIT']").val('M12');
		$("form[name='searchFrm'] input[name='IDV']").val('mon12');
		
		$("form[name='searchFrm'] input[name='FMM']").val('12');
		$("form[name='searchFrm'] input[name='TMM']").val('12');
		$("form[name='searchFrm'] input[name='FDD']").val('01');
		$("form[name='searchFrm'] input[name='TDD']").val('31');
		common_ser()
	}); 


});
