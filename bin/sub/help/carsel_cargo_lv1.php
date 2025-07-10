<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

//$code = $_POST['optVal'];
$code	=  iconv("UTF-8","EUCKR",$_POST['optVal']);

$data = "";
$sql="select car_brand code, car_brand name from cardtc where hyoung_sik = '".$code."' group by car_brand order by car_brand ";

$result= sqlsrv_query( $mscon, $sql );
while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC)){
	$data=$data.'<option value="'.$row['code'].'">'.$row['name'].'</option>';
}

echo $data;
?>