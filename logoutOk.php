<?


include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

/* 접속로그  isnert */

$REMOTE_ADDR  = $_SERVER["REMOTE_ADDR"];
 $sql="INSERT INTO  USER_LOG (SCODE,IDATE,BIGO,IP) 
		VALUES ('".$_SESSION['S_SCODE']."',getdate(),'로그아웃','".$REMOTE_ADDR."')"; 


// 트렌젝션 시작
sqlsrv_query($mscon, "BEGIN TRAN");
$result =  sqlsrv_query( $mscon, $sql );


if ($result == false){
	sqlsrv_query($mscon, "ROLLBACK");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);
	alert("접속로그저장중 오류!!!!");
	//echo "<script>alert('접속로그저장중 오류!!! ');opener.location.reload(true);self.close();</script>";
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