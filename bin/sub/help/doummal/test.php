<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");
?>

<a class="btn_s white hover_btn" style="min-width:100px;" onclick="doummalPopOpen('a',400,200);">법규위반 횟수 (a)</a>
<a class="btn_s white hover_btn" style="min-width:100px;" onclick="doummalPopOpen('b',600,270);">3년간사고요율 (b)</a>
<a class="btn_s white hover_btn" style="min-width:100px;" onclick="doummalPopOpen('c',600,300);">3년간사고요율2(삼성) (c)</a>
<a class="btn_s white hover_btn" style="min-width:100px;" onclick="doummalPopOpen('d',450,250);">1년간사고점수 (d)</a>
<a class="btn_s white hover_btn" style="min-width:100px;" onclick="doummalPopOpen('e',420,200);">3년간사고점수 (e)</a>
<a class="btn_s white hover_btn" style="min-width:100px;" onclick="doummalPopOpen('f',550,250);">차량가입경력 (f)</a>
<a class="btn_s white hover_btn" style="min-width:100px;" onclick="doummalPopOpen('g',530,270);">그외보유차량 (g)</a>
<a class="btn_s white hover_btn" style="min-width:100px;" onclick="doummalPopOpen('h',550,300);">직전3년가입경력 (h)</a>
<a class="btn_s white hover_btn" style="min-width:100px;" onclick="doummalPopOpen('i',510,210);">그외 사고여부 (i)</a><br><br>

<a class="btn_s white hover_btn" style="min-width:100px;" onclick="doummalPopOpen('j',500,220);">차량등록일 (j)</a>
<a class="btn_s white hover_btn" style="min-width:100px;" onclick="doummalPopOpen('k',450,200);">연료형태 (k)</a>
<a class="btn_s white hover_btn" style="min-width:100px;" onclick="doummalPopOpen('l',450,200);">고가수리비 (l)</a>
<a class="btn_s white hover_btn" style="min-width:100px;" onclick="doummalPopOpen('m',450,250);">구입형태 (m)</a><br><br>

<a class="btn_s white hover_btn" style="min-width:100px;" onclick="doummalPopOpen('n',800,700);">태아 (n)</a>
<a class="btn_s white hover_btn" style="min-width:100px;" onclick="doummalPopOpen('o',600,350);">티맵안전운전할인 (o)</a>
<a class="btn_s white hover_btn" style="min-width:100px;" onclick="doummalPopOpen('p',850,420);">주행거리선할인 (p)</a>
<a class="btn_s white hover_btn" style="min-width:100px;" onclick="doummalPopOpen('q',500,420);">다수차량할인특약 (q)</a>
<a class="btn_s white hover_btn" style="min-width:100px;" onclick="doummalPopOpen('r',450,200);">종교단체특약 (r)</a>

<script type="text/javascript">
// 조직
function doummalPopOpen(index,openwidth,openheight){

	var left = Math.ceil((window.screen.width - openwidth)/2);
	var top = Math.ceil((window.screen.height - (openheight+100))/2);
	var popOpen	= window.open("<?=$conf['homeDir']?>/sub/help/doummal/doummal_"+index+".php","","width="+openwidth+"px,height="+openheight+"px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
	popOpen.focus();
}

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>
