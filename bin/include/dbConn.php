<?


//PASSWORD DB접속

$hostname = '115.68.1.6,51797'; //서버 ip
$connectionOptions = array(
    "Database" => 'PASSWORDDB',
    "Uid" => 'ps_season',
    "PWD" => 'season_ser3370!'
);

// "Uid" => "crawl_user",

$mscon_pw=sqlsrv_connect($hostname,$connectionOptions);

if (!$mscon_pw) {
	die ( print_r (sqlsrv_errors(), true));
	//die('database 연결 에러1!!!!!!!');
}


$sql= "exec dbo.openkey";
$result  = sqlsrv_query($mscon_pw, $sql);

//검색 데이터 구하기 
$sql= "SELECT 
			CONVERT(NVARCHAR(50),dbo.DECRYPTKEY(HOSTNAME)) AS hostname,   
			CONVERT(NVARCHAR(50),dbo.DECRYPTKEY(USERNAME)) AS username, 
			CONVERT(NVARCHAR(50),dbo.DECRYPTKEY(PASSWORD)) AS password, 
			CONVERT(NVARCHAR(50),dbo.DECRYPTKEY(DBNAME))   AS dbname
		FROM PASSTABLE_SEASON
		WHERE UGUBUN = '1' ";

$result  = sqlsrv_query( $mscon_pw , $sql );
$row =  sqlsrv_fetch_array($result); 


$hostname = $row['hostname']; //서버 ip
$connectionOptions = array(
    "Database" => $row['dbname'],
    "Uid" => $row['username'],
    "PWD" => $row['password']
);

sqlsrv_free_stmt($result);
sqlsrv_close($mscon_pw);

$mscon=sqlsrv_connect($hostname,$connectionOptions);
if (!$mscon) {
    die('database 연결 에러2!!!!!!!');
}

$sql= "exec dbo.openkey";
$result  = sqlsrv_query($mscon, $sql);


?>