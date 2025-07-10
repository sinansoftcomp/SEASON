<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

//$code = $_POST['optVal'];
$code	=  iconv("UTF-8","EUCKR",$_POST['optVal']);

$data = "";
$sql="select car_sub code, car_sub name from cardtb where car_brand = '".$code."' group by car_sub order by car_sub ";

$result= sqlsrv_query( $mscon, $sql );
while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC)){
	$data=$data.'<option value="'.$row['code'].'">'.$row['name'].'</option>';
}

echo $data;
?>