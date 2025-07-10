<?
$rtnData	= array();

// 월 계산 이후 -1일 처리
$ymdhi	= date("Y-m-d", strtotime($_GET['fdate']."+1 year"));


$rtnData["Y"]	= $ymdhi;

echo json_encode($rtnData);
?>