<?


include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

/* ���ӷα�  isnert */

$REMOTE_ADDR  = $_SERVER["REMOTE_ADDR"];
 $sql="INSERT INTO  USER_LOG (SCODE,IDATE,BIGO,IP) 
		VALUES ('".$_SESSION['S_SCODE']."',getdate(),'�α׾ƿ�','".$REMOTE_ADDR."')"; 


// Ʈ������ ����
sqlsrv_query($mscon, "BEGIN TRAN");
$result =  sqlsrv_query( $mscon, $sql );


if ($result == false){
	sqlsrv_query($mscon, "ROLLBACK");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);
	alert("���ӷα������� ����!!!!");
	//echo "<script>alert('���ӷα������� ����!!! ');opener.location.reload(true);self.close();</script>";
	exit;
}

sqlsrv_query($mscon, "COMMIT");


if (session_status() == PHP_SESSION_ACTIVE) {
	session_destroy();
}

goto_url("/login.php");
sqlsrv_free_stmt($result);
sqlsrv_close($mscon);
?>