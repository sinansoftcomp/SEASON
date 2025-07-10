<?

header('Access-Control-Allow-Origin: *');
header('Content-Type: text/html; charset=euc-kr');

$conf['homeDir'] = "/bin";
$conf['mobileDir'] = "/bin/submobile";
$conf['loginPage']	=  "http://www.gaplus.net/login.php";
$conf['logoutPage']	= $_SERVER['DOCUMENT_ROOT']."/logoutOk.php";
$conf['MainDir'] = $_SERVER['DOCUMENT_ROOT'];
// �������� (�۷ι�)
$conf['rootDir'] = $_SERVER['DOCUMENT_ROOT'].$conf['homeDir'];
// ������ �������� �ӽ÷� �����̹�ũ �����λ��
$conf['Img_path'] = "http://hometaxbill.net/health_IMG";
//$conf['daumKey']	= '627cc7094ad78b7912ff54750eb0564c';
$conf['daumKey']	= 'a085465824f67e5c320c823cc09e36b5';
include_once($conf['rootDir']."/include/source/common.lib.php");

include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/auth_chk.php");
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/class/common_class.php");
header("Pragma: no-cache");   
header("Cache-Control: no-cache,must-revalidate");  



if($ajaxType==true){
	header('Content-Type: text/plain');
	header('Content-Type: text/html; charset=euc-kr');
	return;
}

// ���԰��
$conf['guipcarrer']	= array(
	"B1"=>"1��̸�",
	"B2"=>"2��̸�",
	"B3"=>"3��̸�",
	"B4"=>"4��̸�",
	"B5"=>"5��̸�",
	"B6"=>"6��̸�",
	"B7"=>"7��̸�",
	"B8"=>"7���̻�",
);
// 3�Ⱓ���
$conf['ncr_code']	= array(
	"N"=>"3�Ⱓ �����(N)",
	"ZZZ"=>"�ش����� 3�Ⱓ ���԰�� ��(ZZZ)",
	"ZZM"=>"�ش����� 3�Ⱓ ���԰�� ��(ZZZ)-����",
	"D00"=>"3�Ⱓ 1ȸ, �ֱ� 1�� ��� ��",
	"D10"=>"3�Ⱓ 1ȸ, �ֱ� 1�� ��� 1��",
	"C00"=>"3�Ⱓ 2ȸ, �ֱ� 1�� ��� ��",
	"C10"=>"3�Ⱓ 2ȸ, �ֱ� 1�� ��� 1��",
	"C20"=>"3�Ⱓ 2ȸ, �ֱ� 1�� ��� 2��",
	"B00"=>"3�Ⱓ 3ȸ �̻�, �ֱ� 1�� ��� ��",
	"B10"=>"3�Ⱓ 3ȸ �̻�, �ֱ� 1�� ��� 1��",
	"B20"=>"3�Ⱓ 3ȸ �̻�, �ֱ� 1�� ��� 2��",
	"B30"=>"3�Ⱓ 3ȸ �̻�, �ֱ� 1�� ��� 3���̻�",
);
// 3�Ⱓ���(�Ｚ)
$conf['ncr_code2']	= array(
	"0"=>"������",
	"1"=>"�������",
);
// �������
$conf['ss_point']	= array(
	"1"=>"0.5�� ����",
	"2"=>"0.5�� �ʰ�",
);
// �������԰��
$conf['car_guip']	= array(
	"1"=>"1��̸�",
	"2"=>"2��̸�",
	"3"=>"3��̸�",
	"4"=>"3���̻�",
	"9"=>"9�����̻�",
);
// �׿� ��������
$conf['car_own']	= array(
	"1"=>"����",
	"2"=>"�¿�,���������(1��)",
	"4"=>"�¿�,���������(2��)",
	"5"=>"�¿�,���������(3��)",
	"6"=>"�¿�,���������(4��)",
	"7"=>"�¿�,���������(5���̻�)",
	"3"=>"1,2,3�� ����,ȭ��",
);
// ����3�Ⱑ�԰��
$conf['careercode3']	= array(
	"1"=>"1��̸�",
	"2"=>"1���̻�~2��̸�",
	"3"=>"2���̻�~3��̸�",
	"4"=>"3���̻�",
);
// �׿ܻ����
$conf['otheracc']	= array(
	"0"=>"����",
	"1"=>"����",
);

function Encrypt_where($str, $secret_key='secret key', $secret_iv='secret iv')
{
    $key = hash('sha256', $secret_key);
    $iv = substr(hash('sha256', $secret_iv), 0, 32)    ;
    return str_replace("=", "", base64_encode(
    	openssl_encrypt($str, "AES-256-CBC", $key, 0, $iv))
    );
}

function Decrypt_where($str, $secret_key='secret key', $secret_iv='secret iv')
{
    $key = hash('sha256', $secret_key);
    $iv = substr(hash('sha256', $secret_iv), 0, 32);
    return openssl_decrypt(
    	base64_decode($str), "AES-256-CBC", $key, 0, $iv
    );
}

$secret_key = "sinit_secretkey";
$secret_iv = "sinit_secretiv";
?>
<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="euc-kr">
	<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
	<meta http-equiv=X-UA-Compatible content="IE=Edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=0">
<!--	<meta name="description" content="CMS,��������,������,�����̹�ũ, ����,����CMS,����������CMS,CMS�ڵ���ü,�������α׷�">-->
	<meta name="description" content="����븮��,GA����븮��,����븮�����α׷�,GA���α׷�,����븮���������α׷�,GA���α׷�">
	<meta name="keywords" content="����븮��,GA����븮��,����븮�����α׷�,GA���α׷�,����븮���������α׷�,GA���α׷�">
	<meta name="author" content="�ž� ����Ʈ">


	
	<link rel="stylesheet" type="text/css" href="<?=$conf['homeDir']?>/css/w3css.css" />
	<link rel="stylesheet" type="text/css" href="<?=$conf['homeDir']?>/css/bootstrap.css" media="all">
	<link rel="stylesheet" type="text/css" href="<?=$conf['homeDir']?>/css/youbo_default.css" />
	<link rel="stylesheet" href="<?=$conf['homeDir']?>/css/font-awesome-4.5.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css" integrity="sha512-10/jx2EXwxxWqCLX/hHth/vu2KY3jCF70dCQB8TSgNjbCVAC/8vai53GfMDrO2Emgwccf2pJqxct9ehpzG+MTw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

	<!--<script type="text/javascript" src="http://code.jquery.com/jquery-2.1.4.js"></script> -->
	<script type="text/javascript" src="<?=$conf['homeDir']?>/js/jquery-1.11.3.min.js"></script>
	<script src="<?=$conf['homeDir']?>/js/jquery-ui-1.12.1/jquery-ui.min.js"></script>
	<!--<script src="<?=$conf['homeDir']?>/js/jquery-ui/jquery-ui.min.js"></script>-->

	<script type="text/javascript" src="<?=$conf['homeDir']?>/js/jQuery.print.js"></script>
	<script type="text/javascript" src="<?=$conf['homeDir']?>/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?=$conf['homeDir']?>/js/jquery.vticker.js"></script>
	<script type="text/javascript" src="<?=$conf['homeDir']?>/js/jquery.marquee.min.js"></script>

	<!-- col Resize -->
	<script type="text/javascript" src="<?=$conf['homeDir']?>/js/colResizable-1.6.min.js"></script>

	<!--	cross origin	-->
	<!--<script type="text/javascript" src="<?=$conf['homeDir']?>/js/jquery.ajax-cross-origin.min.js"></script>-->

	<link rel="stylesheet" href="<?=$conf['homeDir']?>/js/dist/style.min.css" />
	<script src="<?=$conf['homeDir']?>/js/dist/jstree.min.js"></script> 

	<!-- Swiper  -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css"/>
	<script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>


	<script src="<?=$conf['homeDir']?>/js/common.js"></script>
	<script src="<?=$conf['homeDir']?>/js/form-master/jquery.form.js"></script> 
	

	<link rel="stylesheet" href="<?=$conf['homeDir']?>/css/reset.css">
	<title>GA PLUS</title>
	<!--<link rel="shortcut icon" href="https://www.hometaxbill.net:450/favicon.ico">-->
	<link rel="apple-touch-icon" sizes="57x57" href="/bin/image/fav/apple-icon-57x57.png">
	<link rel="apple-touch-icon" sizes="60x60" href="/bin/image/fav/apple-icon-60x60.png">
	<link rel="apple-touch-icon" sizes="72x72" href="/bin/image/fav/apple-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="76x76" href="/bin/image/fav/apple-icon-76x76.png">
	<link rel="apple-touch-icon" sizes="114x114" href="/bin/image/fav/apple-icon-114x114.png">
	<link rel="apple-touch-icon" sizes="120x120" href="/bin/image/fav/apple-icon-120x120.png">
	<link rel="apple-touch-icon" sizes="144x144" href="/bin/image/fav/apple-icon-144x144.png">
	<link rel="apple-touch-icon" sizes="152x152" href="/bin/image/fav/apple-icon-152x152.png">
	<link rel="apple-touch-icon" sizes="180x180" href="/bin/image/fav/apple-icon-180x180.png">
	<link rel="icon" type="image/png" sizes="192x192"  href="/bin/image/fav/android-icon-192x192.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/bin/image/fav/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="96x96" href="/bin/image/fav/favicon-96x96.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/bin/image/fav/favicon-16x16.png">
	<link rel="manifest" href="/bin/image/fav/manifest.json">
	<meta name="msapplication-TileColor" content="#ffffff">
	<meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
	<meta name="theme-color" content="#ffffff">

</head>

<body>

