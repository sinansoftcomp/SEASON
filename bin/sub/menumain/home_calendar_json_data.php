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
		"title"	=> iconv("euckr","utf-8",$fet['title']),		// 타이틀
		"ymd"	=> $fet['sdate'],								// 일자
		"description"	=> iconv("euckr","utf-8",$fet['bigo']),	// 내용
		"gubun"	=> $fet['gubun'],								// 구분(1:전체, 2:사원)
		"color"	=> $fet['color'],								// 구분에 따른 색상(회색:종료, 빨강:전체, 파랑:사원)
		"start"	=> date("Y-m-d",strtotime($fet['sdate'])),		// 시작일자=일자
	);
	$ii++;
}


sqlsrv_free_stmt($qry);
sqlsrv_close($mscon);

echo json_encode($rtnData);

?>