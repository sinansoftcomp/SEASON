<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

?>

<style>
#if{width: 0px;height: 0px;border: 0px;}
</style>



<!-- 요율계산 폼 -->
<form method="post" name="yoyulform" action="https://www.ibss-b.co.kr/car/gas_rate.php" target="param">
	<input type="hidden" id="form_ret_url" name="ret_url" value="">
	<input type='hidden' id='form_agent' name='agent' value='samsungkw2'/> 
	<input type='hidden' id='company' name='company' value='hd'/> 
	<input type='hidden' id='user_code' name='user_code' value='4LU910'/> 

	<input type='hidden' id='form_jumin' name='jumin' value=''/> 
	<input type='hidden' id='form_carnumber' name='carnumber' value=''/> 
	<input type='hidden' id='form_caruse' name='caruse' value=''/> 
</form>


<iframe id="if" name="param"></iframe>


<script type="text/javascript">


// 비교견적 전송
function yoyul_send(){

	var jumin		= $("#jumin").val();
	var carnumber	= $("#carnumber").val();
	var caruse		= $("#caruse").val();
	var returl		= 'www.gaplus.net/bin/sub/menu6/ga_menu6_01_ret_url_yoyul.php?jumin='+jumin+'&carnumber='+carnumber;


	$("#form_jumin").val(jumin);
	$("#form_carnumber").val(carnumber);
	$("#form_caruse").val(caruse);
	$("#form_ret_url").val(returl);

	$("form[name='yoyulform']").submit();
}

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>