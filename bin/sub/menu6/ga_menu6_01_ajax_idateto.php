<?
$rtnData	= array();

// �� ��� ���� -1�� ó��
$ymdhi	= date("Y-m-d", strtotime($_GET['fdate']."+1 year"));


$rtnData["Y"]	= $ymdhi;

echo json_encode($rtnData);
?>