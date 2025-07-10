
<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
<?
/*
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);
*/
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

// 리턴 URL


echo "<pre>";
print_r($_POST);
echo "</pre></br>";


$sql="insert into d_test(date) 
				values(getdate())";
sqlsrv_query("BEGIN TRAN");
$result =  sqlsrv_query( $mscon, $sql );

if ($result == false){
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);
	$message = '오류!!';
	echo $message;
}
sqlsrv_query("COMMIT");
sqlsrv_free_stmt($result);
sqlsrv_close($mscon);

?>
