<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");
?>

<a class="btn_s white hover_btn" style="min-width:100px;" onclick="doummalPopOpen('a',400,200);">�������� Ƚ�� (a)</a>
<a class="btn_s white hover_btn" style="min-width:100px;" onclick="doummalPopOpen('b',600,270);">3�Ⱓ������ (b)</a>
<a class="btn_s white hover_btn" style="min-width:100px;" onclick="doummalPopOpen('c',600,300);">3�Ⱓ������2(�Ｚ) (c)</a>
<a class="btn_s white hover_btn" style="min-width:100px;" onclick="doummalPopOpen('d',450,250);">1�Ⱓ������� (d)</a>
<a class="btn_s white hover_btn" style="min-width:100px;" onclick="doummalPopOpen('e',420,200);">3�Ⱓ������� (e)</a>
<a class="btn_s white hover_btn" style="min-width:100px;" onclick="doummalPopOpen('f',550,250);">�������԰�� (f)</a>
<a class="btn_s white hover_btn" style="min-width:100px;" onclick="doummalPopOpen('g',530,270);">�׿ܺ������� (g)</a>
<a class="btn_s white hover_btn" style="min-width:100px;" onclick="doummalPopOpen('h',550,300);">����3�Ⱑ�԰�� (h)</a>
<a class="btn_s white hover_btn" style="min-width:100px;" onclick="doummalPopOpen('i',510,210);">�׿� ����� (i)</a><br><br>

<a class="btn_s white hover_btn" style="min-width:100px;" onclick="doummalPopOpen('j',500,220);">��������� (j)</a>
<a class="btn_s white hover_btn" style="min-width:100px;" onclick="doummalPopOpen('k',450,200);">�������� (k)</a>
<a class="btn_s white hover_btn" style="min-width:100px;" onclick="doummalPopOpen('l',450,200);">�������� (l)</a>
<a class="btn_s white hover_btn" style="min-width:100px;" onclick="doummalPopOpen('m',450,250);">�������� (m)</a><br><br>

<a class="btn_s white hover_btn" style="min-width:100px;" onclick="doummalPopOpen('n',800,700);">�¾� (n)</a>
<a class="btn_s white hover_btn" style="min-width:100px;" onclick="doummalPopOpen('o',600,350);">Ƽ�ʾ����������� (o)</a>
<a class="btn_s white hover_btn" style="min-width:100px;" onclick="doummalPopOpen('p',850,420);">����Ÿ������� (p)</a>
<a class="btn_s white hover_btn" style="min-width:100px;" onclick="doummalPopOpen('q',500,420);">�ټ���������Ư�� (q)</a>
<a class="btn_s white hover_btn" style="min-width:100px;" onclick="doummalPopOpen('r',450,200);">������üƯ�� (r)</a>

<script type="text/javascript">
// ����
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
