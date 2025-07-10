//-->FORM DATE   ~  TO DATE�Ⱓ����(240311 �����)
function date_on(str_date){

	var bdate = str_date.split('&');

	var today = new Date();  
	today = today.toISOString().substring(0,10);	
	var mm_last = new Date(today.substr(0,4) , today.substr(5,2) , 0).getDate();

	// �⵵ �չ�ư 
	if (bdate[0] == 'YP' )	{
		const newDate = new Date(bdate[1]);
		newDate.setMonth(newDate.getMonth() - 1);
		var  sdate1 = newDate.toISOString().substring(0,10);	
		var  sdate2 =bdate[2];
		return sdate1 + '&' + sdate2;
	}
	// �⵵ �ڹ�ư 
	if (bdate[0] == 'YN' )	{
		const newDate = new Date(bdate[2]);
		newDate.setMonth(newDate.getMonth() + 1);
		var  sdate1 =bdate[1];
		var  sdate2 = newDate.toISOString().substring(0,10);	
		return sdate1 + '&' + sdate2;
	}
	// ���� ��ư 
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

	// �����ư 
	if (bdate[0] == 'MD' )	{
		var  sdate1 =today.substr(0,7) + '-01' ;
		var  sdate2 = today.substr(0,7) +'-'+mm_last ;
		return sdate1 + '&' + sdate2;
	}

	//���� (������ ����  01~��������)	
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
			alert('����Ʈ ������ �߸��Ǿ��ֽ��ϴ�.');
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
			alert('������ ���ڸ� �����մϴ�.');
		}

		
	});
	
//	$(".printCal_ym").attr("readonly",true).css("text-align","center"); �����̰� �ּ��ɾ���� : ���´� ������� �޷��� �����Ҽ� �־���Ѵٰ���
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
//		$(this).attr("readonly",true);  �����̰� �ּ��ɾ���� : ���´� ������� �޷��� �����Ҽ� �־���Ѵٰ���
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
			prevText: '���� ��',
			nextText: '���� ��',
//      currentText : '���� ��¥', // ���� ��¥�� �̵��ϴ� ��ư �г�  �����̰� �߰��ҷ������� 
//        closeText : '�ݱ�', // �ݱ� ��ư �г� �����̰� �߰��ҷ������� 
			monthNames: ['1��','2��','3��','4��','5��','6��','7��','8��','9��','10��','11��','12��'],
			monthNamesShort: ['1��','2��','3��','4��','5��','6��','7��','8��','9��','10��','11��','12��'],
			dayNames: ['��','��','ȭ','��','��','��','��'],
			dayNamesShort: ['��','��','ȭ','��','��','��','��'],
			dayNamesMin: ['��','��','ȭ','��','��','��','��'],
			showMonthAfterYear: true,
			yearSuffix: '��',
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
			nextText: '���� ��', // next �������� ����.
			prevText: '���� ��', // prev �������� ����.
			showMonthAfterYear: true , // ��, ����� ����Ʈ �ڽ��� ��,�� ������ �ٲ��ش�. 
			changeYear: true, // ���� �ٲ� �� �ִ� ����Ʈ �ڽ��� ǥ���Ѵ�.
			changeMonth: true, // ���� �ٲܼ� �ִ� ����Ʈ �ڽ��� ǥ���Ѵ�.
			dateFormat: format,
			dayNamesMin: ['��', 'ȭ', '��', '��', '��', '��', '��'], // ������ �ѱ� ����.
			monthNamesShort: ['1��','2��','3��','4��','5��','6��','7��','8��','9��','10��','11��','12��'], // ���� �ѱ� ����.
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
			nextText: '���� ��', // next �������� ����.
			prevText: '���� ��', // prev �������� ����.
			showMonthAfterYear: true , // ��, ����� ����Ʈ �ڽ��� ��,�� ������ �ٲ��ش�. 
			changeYear: true, // ���� �ٲ� �� �ִ� ����Ʈ �ڽ��� ǥ���Ѵ�.
			yearRange: '1930:2030',
			changeMonth: true, // ���� �ٲܼ� �ִ� ����Ʈ �ڽ��� ǥ���Ѵ�.
			dateFormat: format,
			dayNamesMin: ['��', 'ȭ', '��', '��', '��', '��', '��'], // ������ �ѱ� ����.
			monthNamesShort: ['1��','2��','3��','4��','5��','6��','7��','8��','9��','10��','11��','12��'], // ���� �ѱ� ����.
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
			nextText: '���� ��', // next �������� ����.
			prevText: '���� ��', // prev �������� ����.
			showMonthAfterYear: true , // ��, ����� ����Ʈ �ڽ��� ��,�� ������ �ٲ��ش�. 
			changeYear: true, // ���� �ٲ� �� �ִ� ����Ʈ �ڽ��� ǥ���Ѵ�.
			changeMonth: true, // ���� �ٲܼ� �ִ� ����Ʈ �ڽ��� ǥ���Ѵ�.
			dateFormat: format,
			dayNamesMin: ['��', 'ȭ', '��', '��', '��', '��', '��'], // ������ �ѱ� ����.
			monthNamesShort: ['1��','2��','3��','4��','5��','6��','7��','8��','9��','10��','11��','12��'], // ���� �ѱ� ����.
			altFormat: "yy-mm-dd",
				onSelect: function (dateText, inst) {   // ���� ���õ� �� �̺�Ʈ �߻�

					var startDate    = $(this).val();
					var startDateArr = startDate.split('-');
			 		var endDate = getTodayType2(); //2017-12-09
					var endDateArr = endDate.split('-');
								
					var startDateCompare = new Date(startDateArr[0], parseInt(startDateArr[1])-1, startDateArr[2]);
					var endDateCompare = new Date(endDateArr[0], parseInt(endDateArr[1])-1, endDateArr[2]);
					 
				//	console.log(startDateCompare);
								
					//console.log(endDateCompare);
					if(startDateCompare.getTime() > endDateCompare.getTime()) {
						 
						alert("�ۼ����ڴ� �����ϱ��� �Է��Ͻñ� �ٶ��ϴ�.");
						$(this).val(getTodayType2());
						return false;
					 }		
                }
		});
	});
/*���ó��ڰ������� 2000-00-00 ����*/
	function getTodayType2(){
		var date = new Date();
		return date.getFullYear()+"-"+("0"+(date.getMonth()+1)).slice(-2)+"-"+("0"+date.getDate()).slice(-2);
	
	}


	$('body').on('focus',".printCal_ym", function(){
//		$(this).attr("readonly",true); �����̰� �ּ��ɾ���� : ���´� ������� �޷��� �����Ҽ� �־���Ѵٰ���
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
			prevText: '���� ��',
			nextText: '���� ��',
			monthNames: ['1��','2��','3��','4��','5��','6��','7��','8��','9��','10��','11��','12��'],
			monthNamesShort: ['1��','2��','3��','4��','5��','6��','7��','8��','9��','10��','11��','12��'],
			dayNames: ['��','��','ȭ','��','��','��','��'],
			dayNamesShort: ['��','��','ȭ','��','��','��','��'],
			dayNamesMin: ['��','��','ȭ','��','��','��','��'],
			showMonthAfterYear: true,
			yearSuffix: '��',
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
			nextText: '���� ��!', // next �������� ����.
			prevText: '���� ��', // prev �������� ����.
			showMonthAfterYear: true , // ��, ����� ����Ʈ �ڽ��� ��,�� ������ �ٲ��ش�. 
			changeYear: true, // ���� �ٲ� �� �ִ� ����Ʈ �ڽ��� ǥ���Ѵ�.
			changeMonth: true, // ���� �ٲܼ� �ִ� ����Ʈ �ڽ��� ǥ���Ѵ�.
			dateFormat: format,
			dayNamesMin: ['��', 'ȭ', '��', '��', '��', '��', '��'], // ������ �ѱ� ����.
			monthNamesShort: ['1��','2��','3��','4��','5��','6��','7��','8��','9��','10��','11��','12��'], // ���� �ѱ� ����.
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


// Ư������ ��ȯ
function ext_chng(str, sports){

	var ext_data = "";
	var dash = "";
	
	// ������ī(ex1)
	if (str.indexOf('������ī') != -1 || sports == 'Y') {
		ext_data = ext_data + 'ex1';
	}

	// ž��(ex2)
	if(ext_data.length > 0) dash="-";
	if (str.indexOf('ž��') != -1) {
		ext_data = ext_data + dash + 'ex2';
	}

	// Ŀ��Ƽ��ī(ex3)
	if(ext_data.length > 0) dash="-";
	if (str.indexOf('��縵ũ/����') != -1 || str.indexOf('Ŀ��Ƽ��ī') != -1) {
		ext_data = ext_data + dash + 'ex3';
	}

	// ������-����(ex4)
	if(ext_data.length > 0) dash="-";
	if (str.indexOf('������(����)') != -1) {
		ext_data = ext_data + dash + 'ex4';
	}

	// �����̱�(ex6)
	if(ext_data.length > 0) dash="-";
	if (str.indexOf('AIR-S') != -1) {
		ext_data = ext_data + dash + 'ex6';
	}

	// �������(ex7)
	if(ext_data.length > 0) dash="-";
	if (str.indexOf('AIR-D') != -1) {
		ext_data = ext_data + dash + 'ex7';
	}

	// ����(ex8)
	if(ext_data.length > 0) dash="-";
	if (str.indexOf('����') != -1) {
		ext_data = ext_data + dash + 'ex8';
	}

	// �̸��������(ex9)
	if(ext_data.length > 0) dash="-";
	if (str.indexOf('IM') != -1) {
		ext_data = ext_data + dash + 'ex9';
	}

	// ABS(ex12)
	if(ext_data.length > 0) dash="-";
	if (str.indexOf('ABS') != -1) {
		ext_data = ext_data + dash + 'ex12';
	}

	// ������Ż����(ex16)
	if(ext_data.length > 0) dash="-";
	if (str.indexOf('LDWS') != -1 || str.indexOf('������Ż����') != -1) {
		ext_data = ext_data + dash + 'ex16';
	}

	// �����浹����(ex18)
	if(ext_data.length > 0) dash="-";
	if (str.indexOf('�����浹����') != -1) {
		ext_data = ext_data + dash + 'ex18';
	}

	// ������-ȭ��(ex19)
	if(ext_data.length > 0) dash="-";
	if (str.indexOf('������(ȭ��)') != -1) {
		ext_data = ext_data + dash + 'ex19';
	}

	return ext_data;
}

//2020-01-29 ������ ���� �߰�
$(function(){
	//�޷�
	/*
	$("._datepick").datepicker({
		nextText: '���� ��', // next �������� ����.
		prevText: '���� ��', // prev �������� ����.
		showMonthAfterYear: true , // ��, ����� ����Ʈ �ڽ��� ��,�� ������ �ٲ��ش�. 
		changeYear: true, // ���� �ٲ� �� �ִ� ����Ʈ �ڽ��� ǥ���Ѵ�.
		changeMonth: true, // ���� �ٲܼ� �ִ� ����Ʈ �ڽ��� ǥ���Ѵ�.
		dayNamesMin: ['��', 'ȭ', '��', '��', '��', '��', '��'], // ������ �ѱ� ����.
		monthNamesShort: ['1��','2��','3��','4��','5��','6��','7��','8��','9��','10��','11��','12��'] // ���� �ѱ� ����.
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


	//��ư����
	$(".box_wrap.sel_btn a:not(.btn_off)").click(function(){
		$(".box_wrap.sel_btn a").removeClass('on')
		$(this).addClass('on')
	});

	$(".btn_wrap.sel_btn a:not(.btn_off)").click(function(){
		$(".btn_wrap.sel_btn a").removeClass('on')
		$(this).addClass('on')
	});


	/* �� */
	$('#tab li').click(function(e){
		e.preventDefault();
		var tab_id = $(this).attr('data-tab');

		$('#tab li').removeClass('on');
		$('.tab_con_wrap').removeClass('on');

		$(this).addClass('on');
		$("#"+tab_id).addClass('on');
	});

	/* �� */
	$('#tab2 li').click(function(e){
		e.preventDefault();
		var tab_id = $(this).attr('data-tab');

		$('#tab2 li').removeClass('on');
		$('.tab_con_wrap').removeClass('on');

		$(this).addClass('on');
		$("#"+tab_id).addClass('on');
	});

	/*
		��ݱ�~�Ϲݱ�
		1�б�~4�б�
		1��~12�� �˻���ư ����
	*/

	// ���� Ŭ�� ��
	$("#pre").click(function(){
		$("form[name='searchFrm']").attr("method","get");
		$("form[name='searchFrm']").attr("target","");
		$("form[name='searchFrm'] input[name='GUBUN']").val('F');
		$("form[name='searchFrm'] input[name='BIT']").val('FY');

		common_ser()
	}); 

	// ���� Ŭ�� ��
	$("#next").click(function(){
		$("form[name='searchFrm']").attr("method","get");
		$("form[name='searchFrm']").attr("target","");
		$("form[name='searchFrm'] input[name='GUBUN']").val('T');
		$("form[name='searchFrm'] input[name='BIT']").val('TY');

		common_ser()
	}); 

	// ��ݱ� Ŭ�� �� (1 / 2)
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

	// �Ϲݱ� Ŭ�� �� (2 / 2)
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

	// 1�б� Ŭ�� �� (1 / 4)
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

	// 2�б� Ŭ�� �� (2 / 4)
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

	// 3�б� Ŭ�� �� (3 / 4)
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

	// 4�б� Ŭ�� �� (4 / 4)
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




	// ���� Ŭ����
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

	// ��� Ŭ����
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


	// 1�� Ŭ��
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

	// 2�� Ŭ��
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

	// 3�� Ŭ��
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

	// 4�� Ŭ��
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


	// 5�� Ŭ��
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

	// 6�� Ŭ��
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

	// 7�� Ŭ��
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

	// 8�� Ŭ��
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

	// 9�� Ŭ��
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

	// 10�� Ŭ��
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

	// 11�� Ŭ��
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

	// 12�� Ŭ��
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
