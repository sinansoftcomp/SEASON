<?
$loginCheck		= false;
$autoLogin 		=	false;
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");
?>

<script>

function setCookie(cname, cvalue, exdays) {
	var d = new Date();
	d.setTime(d.getTime() + (exdays*24*60*60*1000));
	var expires = "expires="+d.toUTCString();
	document.cookie = cname + "=" + cvalue + "; " + expires;
}

function getCookie(cname) {
	var name = cname + "=";
	var ca = document.cookie.split(';');
	for(var i=0; i<ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1);
		if (c.indexOf(name) != -1) return c.substring(name.length, c.length);
	}
	return "";
}


function fwrite_submit(){

	saveid();
	savepw();
}

// ���̵�����
function saveid() { 
	if (document.login_form.checksaveid.checked==true) { // ��й�ȣ ���� üũ
		setCookie("bankers_saveid", document.login_form.SCODE.value, 3000);
	}else{ //üũ ���� ��	
		setCookie("bankers_saveid", "", -1);
	}//end of if
}//end of if


// ��й�ȣ����
function savepw() { 
	if (document.login_form.checksavepw.checked==true) { // ��й�ȣ ���� üũ
		setCookie("bankers_savepass", document.login_form.SSPWD.value, 3000);		
	}else{ //üũ ���� ��	
		setCookie("bankers_savepass", "", -1);
	}//end of if
}//end of if

function getid() {
	document.login_form.SCODE.value = getCookie("bankers_saveid");
	document.login_form.SKEY.value = getCookie("bankers_saveskey");
	document.login_form.SSPWD.value = getCookie("bankers_savepass");

	if(getCookie("bankers_saveid") != ""){
		document.login_form.checksaveid.checked = true;
	}

	if(getCookie("bankers_savepass") != ""){
		document.login_form.checksavepw.checked = true;
	}
}//end of if

</script>
<body onLoad="getid()" >

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko" xml:lang="ko">
<head>
<title>�������</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="description" content="CMS,���ݰ�꼭,��꼭,���ݿ�����,���ű�����ȸ,����û,Ȩ�ý�,�������,����,�Ǹ�">
<meta name="keywords" content="CMS,���ݰ�꼭,��꼭,���ݿ�����,���ű�����ȸ,����û,Ȩ�ý�,�������,����,�Ǹ�">
<meta name="author" content="�ž� ����Ʈ">
<meta name="format-detection" content="telephone=no">
<meta http-equiv="Cache-Control" content="no-cache"/>  
<meta http-equiv="Expires" content="0"/>  
<meta http-equiv="Pragma" content="no-cache"/>
</head>

<style>
	.footer li{display:inline-block;margin-right:10px;}
	.footer a{display:inline-block;margin-right:10px;}
</style>

<div class="wrap_login">
	<h1><img src="/bin/image/login_logo.png" alt=""></h1>
	<div class="box_login">
		<h2>SEASON</h2>
		<form id="login_form" name="login_form" onsubmit="return fwrite_submit(this);" method="post" action="loginOk.php">
			<fieldset>
				<legend>�α��� ���� �Է�</legend>

					<span class="input_type">
						<input type="text" autocomplete="off" name="SCODE" required autofocus alt="����ID�� �Է����ּ���." placeholder="���� ID">
					</span>
					<span class="input_type">
						<input type="password" name="SSPWD" required alt="��й�ȣ�� �Է����ּ���." placeholder="��й�ȣ">
					</span>
					<p class="checkbox_text">
						<input type="checkbox" id="id_save" name="checksaveid" onClick="saveid()"><label for="id_save">���̵� ���� </label>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<input type="checkbox" id="pw_save" name="checksavepw" onClick="savepw()"><label for="pw_save">��й�ȣ ���� </label>
					</p>
					<div class="btn_block"><a href="javascript:login_check();" class="btn_xl red">�α���</a></div>
					<div class="btn_join">
						<!--<a href="loginOk.php?demo=1" class="btn_demo">ü���ϱ�</a>-->
						<a href="javascript:memberDemo();" class="btn_demo">ü���ϱ�</a>
						<a href="javascript:memberGaip();">ȸ������</a> 
					</div>
			</fieldset>
		</form>
	</div>

	<div style="position: relative;margin-top:100px; color:#fff; font-size:13px;">
		<ul class="footer company">
			<li>(��)�žȼ���Ʈ</li>
			<li>��ǥ�� : �����</li>
			<li>�ּ� : ��⵵ ������ ���뱸 �ſ��� 88, �����п����̾�II, 102�� 713ȣ</li>
			<li>������ : 1566-5767(09:00 ~ 18:00 / ��, ��, ������ �޹�)</li>
			
		</ul>
		<ul class="footer company" style="margin-top:15px;">
			<li>����ڵ�Ϲ�ȣ : 135-81-87511</li>
			<li>�̸��� : sinit@sinit.co.kr</li>
			<li>����Ǹž��Ű� : 2011-������-2523</li>
			<li><a href="http://www.ftc.go.kr/bizCommPop.do?wrkr_no=1358187511" target="_blank" style="z-index:9999"><p style="color:#fff;cursor:pointer;">[����ڹ�ȣȮ��]</p></a></li>
		</ul>


	</div>

	

	<!-- // con_login -->
</div>
<!-- // wrap_login -->

<script>


function aa(){
	
	alert(11);
}

// �ű� ȸ������
function memberGaip(){

	// ����� ����
	var isMobile = false;
	 
	// PC ȯ��
	var filter = "win16|win32|win64|mac";
	 
	if (navigator.platform) {
		isMobile = filter.indexOf(navigator.platform.toLowerCase()) < 0;
	}

	var cat = "<?=$cat?>";

	if(isMobile == false){
		var left = Math.ceil((window.screen.width - 1000)/2);
		var top = Math.ceil((window.screen.height - 1000)/2);
		var popOpen	= window.open("/memberGaip.php?cat="+cat,"membergaip","width=1000px,height=900px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
	}else{
		var left = Math.ceil((window.screen.width - 1000)/2);
		var top = Math.ceil((window.screen.height - 1000)/2);
		var popOpen	= window.open("/memberGaip_account_mobile.php?cat="+cat,"membergaip","width=1000px,height=900px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");		
	}
	
	popOpen.focus();
}

function memberDemo(){
	
	alert('����ü�� ���Ǵ� 1566-5767 �� ��ȭ�ٶ��ϴ�.');
	return;
}


function login_check(){

	var f = document.login_form;

	if(f.SCODE.value==""){
		alert('���� ID�� �Է��ϼ���.');
		return;
	}

	if(f.SSPWD.value==""){
		alert('��й�ȣ�� �Է��ϼ���.');
		return;
	}


	f.submit();	

}

</script>

<?include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/bottom.php");?>