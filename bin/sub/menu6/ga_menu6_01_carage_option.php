<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

//$code = $_POST['optVal'];
$code	=  iconv("UTF-8","EUCKR",$_POST['optVal']);

$where	= "";
if($code == '2'){
	$where = " where bit = 'Y' ";
}else if($code == '3'){
	$where = " where bit2 = 'Y' ";
}

$data = "";
$sql="select code, bigo from carage ".$where." order by code";

$result= sqlsrv_query( $mscon, $sql );
while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC)){
	$data=$data.'<option value="'.$row['code'].'">'.$row['bigo'].'</option>';
}

echo $data;
?>