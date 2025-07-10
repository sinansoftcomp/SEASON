<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

//$code = $_POST['optVal'];
$code	=  iconv("UTF-8","EUCKR",$_POST['optVal']);
$kind	=  iconv("UTF-8","EUCKR",$_POST['car_kind']);

$data = "";
$sql="select car_nm code, car_nm name from cardtc where car_brand = '".$code."' and hyoung_sik = '".$kind."' group by car_nm order by car_nm ";

$result= sqlsrv_query( $mscon, $sql );
while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC)){
	$data=$data.'<option value="'.$row['code'].'">'.$row['name'].'</option>';
}

echo $data;
?>