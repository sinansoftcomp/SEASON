<?

include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

$sql= "
		select 
				a.sdate,
				a.title,
				a.bigo,
				a.gubun,
				case when a.gubun = '1' and status = '1' then '#F15F5F' 
					 when a.gubun = '1' and status = '2' then '#d5d5d5' 
					 when a.gubun = '2' and status = '1' then '#6799FF' 
					 when a.gubun = '2' and status = '2' then '#d5d5d5' 
					 else '#6799FF' end color
		from schd a
		where a.scode = '".$_SESSION['S_SCODE']."'
		  and a.gubun = '1'

		union all

		select 
				a.sdate,
				a.title,
				a.bigo,
				a.gubun,
				case when a.gubun = '1' and status = '1' then '#F15F5F' 
					 when a.gubun = '1' and status = '2' then '#d5d5d5' 
					 when a.gubun = '2' and status = '1' then '#6799FF' 
					 when a.gubun = '2' and status = '2' then '#d5d5d5' 
					 else '#6799FF' end color
		from schd a
		where a.scode = '".$_SESSION['S_SCODE']."'
		  and a.gubun = '2'
		  and a.skey = '".$_SESSION['S_SKEY']."'
	";

$qry	= sqlsrv_query( $mscon, $sql );
$rtnData	= array();
$ii	= 0;
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	
	$rtnData[$ii]	= array(
		"title"	=> iconv("euckr","utf-8",$fet['title']),		// Ÿ��Ʋ
		"ymd"	=> $fet['sdate'],								// ����
		"description"	=> iconv("euckr","utf-8",$fet['bigo']),	// ����
		"gubun"	=> $fet['gubun'],								// ����(1:��ü, 2:���)
		"color"	=> $fet['color'],								// ���п� ���� ����(ȸ��:����, ����:��ü, �Ķ�:���)
		"start"	=> date("Y-m-d",strtotime($fet['sdate'])),		// ��������=����
	);
	$ii++;
}


sqlsrv_free_stmt($qry);
sqlsrv_close($mscon);

echo json_encode($rtnData);

?>