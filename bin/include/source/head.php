<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/dbConn.php");
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/class/common_class.php");
header("Pragma: no-cache");   
header("Cache-Control: no-cache,must-revalidate");  


if($ajaxType==true){
	header('Content-Type: text/plain');
	header('Content-Type: text/html; charset=euc-kr');
	return;
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>



	
	<link rel="stylesheet" type="text/css" href="<?=$conf['homeDir']?>/css/w3css.css" />
	<link rel="stylesheet" type="text/css" href="<?=$conf['homeDir']?>/css/bootstrap.css" media="all">
	<link rel="stylesheet" type="text/css" href="<?=$conf['homeDir']?>/css/youbo_default.css" />
	<link rel="stylesheet" href="<?=$conf['homeDir']?>/css/font-awesome-4.5.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css" integrity="sha512-10/jx2EXwxxWqCLX/hHth/vu2KY3jCF70dCQB8TSgNjbCVAC/8vai53GfMDrO2Emgwccf2pJqxct9ehpzG+MTw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

	<script type="text/javascript" src="<?=$conf['homeDir']?>/js/jquery-1.11.3.min.js"></script>
	<script src="<?=$conf['homeDir']?>/js/jquery-ui-1.12.1/jquery-ui.min.js"></script>
	<script type="text/javascript" src="<?=$conf['homeDir']?>/js/jQuery.print.js"></script>
	<script type="text/javascript" src="<?=$conf['homeDir']?>/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?=$conf['homeDir']?>/js/jquery.vticker.js"></script>
	<script type="text/javascript" src="<?=$conf['homeDir']?>/js/jquery.marquee.min.js"></script>

	<!-- col Resize -->
	<script type="text/javascript" src="<?=$conf['homeDir']?>/js/colResizable-1.6.min.js"></script>

	<!--	cross origin	-->

	<link rel="stylesheet" href="<?=$conf['homeDir']?>/js/dist/style.min.css" />
	<script src="<?=$conf['homeDir']?>/js/dist/jstree.min.js"></script> 

	<!-- Swiper  -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css"/>
	<script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>


	<script src="<?=$conf['homeDir']?>/js/common.js"></script>
	<script src="<?=$conf['homeDir']?>/js/form-master/jquery.form.js"></script> 
	

	<link rel="stylesheet" href="<?=$conf['homeDir']?>/css/reset.css">
	<title>SEASON</title>

	<link rel="icon" type="image/png" sizes="192x192"  href="/bin/image/fav/fav_192x192.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/bin/image/fav/fav_96x96.png">
	<link rel="icon" type="image/png" sizes="96x96" href="/bin/image/fav/fav_32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/bin/image/fav/fav_16x16.png">
	<link rel="manifest" href="/bin/image/fav/manifest.json">

</head>

<body>

