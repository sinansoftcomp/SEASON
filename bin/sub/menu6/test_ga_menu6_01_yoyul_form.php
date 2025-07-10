<!-- 요율계산 폼 -->
<form method="post" name="yoyulform" class="ajaxForm_yoyul" action="//www.ibss-b.co.kr/car/gas_rate.php">	
	<input type="hidden" id="ret_url" name="ret_url" value="www.gaplus.net:452/bin/sub/menu6/ga_menu6_01_ret_url_yoyul.php">
	<input type='hidden' id='agent' name='agent' value='samsungkw2'/> 
	<input type='hidden' id='company' name='company' value='hd'/> 
	<input type='hidden' id='user_code' name='user_code' value='4LU910'/> 

	<input type='hidden' id='jumin' name='jumin' value='8510041659511'/> 
	<input type='hidden' id='carnumber' name='carnumber' value='11라8141'/> 
	<input type='hidden' id='caruse' name='caruse' value='1'/> 

	<input type="submit" value="Submit" id = 'test'>
</form>

<!--<body onload="yoyul_form_send();">-->
<script type="text/javascript">


// ajax 불가 #1
function yoyul_form_send(){

	document.yoyulform.submit();
}



</script>