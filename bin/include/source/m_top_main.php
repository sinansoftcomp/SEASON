<?
if (session_status() == PHP_SESSION_NONE) {
	session_start();
}
include($_SERVER['DOCUMENT_ROOT']."/".$_SESSION['S_SCODE']."/bin/include/dbConn.php");


$YY = date("Y");
$MM = date("m");
$DD = date("d");

$arrDay= array('일','월','화','수','목','금','토');
$date = date('w'); //0 ~ 6 숫자 반환

$DAY = $arrDay[$date].'요일'; 


// 사원 소속정보
$sql  = "Select a.sname,
				case when isnull(a.bonbu,'') != '' then substring(b.bname,1,2) else '' end +
				case when isnull(a.bonbu,'') != '' and (isnull(a.jisa,'') != '' or isnull(a.team,'') != '')  then '>' else '' end +
				case when isnull(a.jisa,'') != '' then substring(c.jsname,1,2) else '' end +
				case when isnull(a.jisa,'') != '' and isnull(a.jijum,'') != '' then '>' else '' end +
				case when isnull(a.jijum,'') != '' then substring(d.jname,1,4) else '' end +
				case when isnull(a.jijum,'') != '' and isnull(a.team,'') != '' then '>' else '' end +
				case when isnull(a.team,'') != '' then e.tname else '' end as sosok
		 from swon a
			left outer join bonbu(nolock) b on a.scode = b.scode and a.bonbu = b.bcode
			left outer join jisa(nolock) c on a.scode = c.scode and a.jisa = c.jscode
			left outer join jijum(nolock) d on a.scode = d.scode and a.jijum = d.jcode
			left outer join team(nolock) e on a.scode = e.scode and a.team = e.tcode
		 where a.scode = '".$_SESSION['S_SCODE']."' and a.skey = '".$_SESSION['S_SKEY']."' ";
$result  = sqlsrv_query( $mscon, $sql );
$swonData =  sqlsrv_fetch_array($result); 

$s_name	= $swonData['sname'];
$s_sosok= $swonData['sosok'];

if($s_sosok){
	$data	= $s_sosok.'>'.$s_name;
}else{
	$data	= $s_name;
}

?>


<div id="wrap">

	<!-- 상단 -->
	<div class="header-section">
		<div class="wrap-header-logo"><a href="/bin/mainmobile.php"><img src="/bin/image/mobile_logo_up.png" style="width:3.1rem" class="icon"></a>
		</div>
		<div class="top_middle">
			<strong class="tit_name"><?=$data?></strong>
		</div>
		<div class="side-menu">			
			<a class="wrap-logout" onclick="logout_mobile();"><img src="/bin/image/mobile_logout.png" class="icon"></a>
		</div>
	</div><!-- 상단 End -->



<script>


function logout_mobile(){

	location.href="/m_logoutOk.php";

}

</script>