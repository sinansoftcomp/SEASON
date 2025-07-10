
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

function inputNumBar(str){
	var regType1 = /^[-0-9+]*$/; 
	//alert(regType1.test($(this).val()));
	if(regType1.test(str)!=true){
		return "";
	}else{
		return str;
	}
}

function form_required_check(frm){
	var rtnData	= true;
	$(frm).find("input, select, textarea").each(function(){
		//if($(this).attr("required") && $(this).val().trim()=="" && rtnData==true){
		if($(this).attr("required") && $.trim($(this).val())=="" && rtnData==true){
			
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
	
	$(".inputNumBar").keyup(function(){
		$(this).val(inputNumBar($(this).val()));
	});

	$(".numberOnly2").keyup(function(event){
		if(numberOnly2($(this).val())==""){
			$(this).val(numberOnly2($(this).val()));		
		}
		
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
	$(".numberInput").css("text-align","right");

	$(".numberInput").keyup(function() {
		var uncommaVal	= uncomma($(this).val());
		var commaVal	= comma($(this).val());
		if(uncommaVal=='0' || (!$.isNumeric(uncommaVal) && uncommaVal!='-')){
			$(this).val('');
		}else{
			$(this).val(commaVal);

		}
	});
	
	$(".numberInput2").keyup(function() {
		var uncommaVal	= uncomma($(this).val());
		var commaVal	= comma($(this).val());
		if(uncommaVal=='0' || (!$.isNumeric(uncommaVal) && uncommaVal!='-')){
			$(this).val('');
		}else{
			$(this).val(commaVal);

		}
	});

	$('.modal').on('keyup',".numberInput3", function(){
		var uncommaVal	= uncomma($(this).val());
		var commaVal	= comma($(this).val());
		if(uncommaVal=='0' || (!$.isNumeric(uncommaVal) && uncommaVal!='-')){
			$(this).val('');
		}else{
			$(this).val(commaVal);

		}
	});
	
	$('.modal').on('keyup',".enterSubmit", function(event){
		if ( event.which == 13 ) {
	     location.href	= $(".submitBtn").attr("href");
	  }
	});
	
	$('.modal').on('click',".printCalImage", function(event){
		$(this).prev().focus();
	});
	
	$('.sisulFrm').on('click',".printCalImage", function(event){
		$(this).prev().focus();
	});
	
	$(".printCalImage").click(function(){
		$(this).prev().focus();
	})
	
	
	
	$('.modal').on('keyup',".numberOnly", function(){
		$(this).val(numberOnly($(this).val()));
	});
	$('.modal').on('keyup',".numberOnly2", function(event){
		if(numberOnly2($(this).val())==""){
			$(this).val(numberOnly2($(this).val()));		
		}
		
	});
	$('.modal').on('keyup',".inputNumBar", function(){
		$(this).val(inputNumBar($(this).val()));
		//$(this).val();
	});
	

	$('body').on('focus',".printCal", function(){
//		$(this).attr("readonly",true);  �����̰� �ּ��ɾ���� : ���´� ������� �޷��� �����Ҽ� �־���Ѵٰ���
		var altF;
		var format;
		$(this).prop("readonly",true);
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
					"z-index": 1999
				});
			},
			onClose:function(){
				$(this).css("z-index","1");
			},
			altField: altF,
			altFormat: "yy-mm-dd"
		});
	});

	$('body').on('focus',".printCal_ym", function(){
//		$(this).attr("readonly",true); �����̰� �ּ��ɾ���� : ���´� ������� �޷��� �����Ҽ� �־���Ѵٰ���
		var altF;
		var format;
		$(this).prop("readonly",true);
		if($(this).next("input[type='hidden']").attr("name")!=undefined){
			altF= $(this).next("input[type='hidden']");
			format	= "y/mm";
		}else{
			altF= $(this);
			format	= "yy/mm";
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
					"z-index": 1999
				});
			},
			onClose:function(){
				$(this).css("z-index","1");
			},
			altField: altF,
			altFormat: "yy/mm"
		});
	});

	$(".dateClear").click(function(){
		$(this).prev().val("");
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


function pad(n, width) {
  n = n + '';
  return n.length >= width ? n : new Array(width - n.length + 1).join('0') + n;
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
