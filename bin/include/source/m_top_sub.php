<?
if (session_status() == PHP_SESSION_NONE) {
	session_start();
}
include($_SERVER['DOCUMENT_ROOT']."/".$_SESSION['S_SCODE']."/bin/include/dbConn.php");


$YY = date("Y");
$MM = date("m");
$DD = date("d");

$arrDay= array('��','��','ȭ','��','��','��','��');
$date = date('w'); //0 ~ 6 ���� ��ȯ

$DAY = $arrDay[$date].'����'; 


$S_MASTER	=	$_SESSION['S_MASTER'];

$JIK		=	$_SESSION['S_JIK'];

// �������̰ų� �� ���� ��� ���� ok..
if($JIK == 'A' || ($JIK == '2001' || $JIK == '3001' || $JIK == '4001' || $JIK == '5001')){
	$auth = 'Y';
}else{
	$auth = 'N';
}

?>

<style>
.fa-house{margin-left:0.2rem;}

#header {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 99;
    width: 100%;
    opacity: 0;
}

#main_section{padding-top:3.6rem;position:relative;z-index: 1;}
</style>

<div id="wrap">

	<!-- ��� -->
	<div id="top_header" style="position: fixed;width:100%;z-index: 99;height:3.6rem;background:#fff">
		<div class="header-section">
			<div class="wrap-header-logo" style="min-height:2.7rem;display: flex;align-items: center;">
				<a href="javascript:history.back();"><img src="/bin/image/mobile_back.png" style="width:1.8rem" class="icon"></a>
			</div>
			<div class="top_middle">
				<strong class="tit_name"><?=$titleText?></strong>
			</div>
			<div class="side-menu">
				<a href="<?=$conf['mobileDir']?>/m_menu01_ins.php" class="add_kwngo" style="display:none;width:2rem;"><img src="/bin/image/m_userplus.png" class="icon"></a>
				<a href="<?=$conf['mobileDir']?>/m_main_post_ins.php" class="add_post" style="display:none;width:1.5rem;margin-bottom:0.3rem;"><i class="fa-regular fa-pen-to-square fa-xl" style="color:#3332a5;"></i></a>
				<a href="<?=$conf['mobileDir']?>/m_menu06_sch_ins.php" class="add_schd" style="display:none;width:1.5rem;margin-bottom:0.3rem;"><i class="fa-regular fa-calendar fa-xl" style="color:#3332a5;"></i></i></a>
				<a href="<?=$conf['mobileDir']?>/m_main_community_ins.php" class="add_commu" style="display:none;width:1.5rem;margin-bottom:0.3rem;"><i class="fa-regular fa-pen-to-square fa-xl" style="color:#3332a5;"></i></a>
				<a class="del_kwngo" style="display:none;width:2rem;" onclick="kwngo_delete();"><img src="/bin/image/m_trash.png" class="icon"></a>
				<a href="<?=$conf['homeDir']?>/mainmobile.php"><img src="/bin/image/m_home.png" class="icon fa-house"></a>
			</div>
		</div><!-- ��� End -->
	</div>

	<div id="main_section" >





<script>

	$(document).ready(function(){

		$("#loadingImage").hide();
		var auth = '<?=$auth?>';


		// ������ �޴��϶� ����� ������ �����ֱ�
		if($('.tit_name').text() == '������'){
			$('.add_kwngo').css("display","");
			$('.add_post').css("display","none");
			$('.add_schd').css("display","none");
			$('.add_commu').css("display","none");
		}else if($('.tit_name').text() == '�˸���' && auth == 'Y'){
			$('.add_kwngo').css("display","none");
			$('.add_post').css("display","");
			$('.add_schd').css("display","none");
			$('.add_commu').css("display","none");
		}else if($('.tit_name').text() == '��������'){
			$('.add_kwngo').css("display","none");
			$('.add_post').css("display","none");
			$('.add_schd').css("display","");
			$('.add_commu').css("display","none");
		}else if($('.tit_name').text() == 'Ŀ�´�Ƽ'){
			$('.add_kwngo').css("display","none");
			$('.add_post').css("display","none");
			$('.add_schd').css("display","none");
			$('.add_commu').css("display","");
		}else{
			$('.add_kwngo').css("display","none");
			$('.add_post').css("display","none");
			$('.add_schd').css("display","none");
			$('.add_commu').css("display","none");
		}

		
	})


</script>