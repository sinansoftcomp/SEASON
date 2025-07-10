<?

include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

$sdate1 = $_POST['sdate1'];
$sdate2 = $_POST['sdate2'];
$where = iconv("UTF-8","EUC-KR",$_POST['where']);
$tel = $_POST['tel'];			// 발신자번호
$template = $_POST['template'];
$name = iconv("UTF-8","EUC-KR",$_POST['name']);

$sql_c = "";
$carseq = "";
// action작업전 sql구문 만들어주기
if($_POST['type'] == 'sms_car_gun') {
	$sql = "select carseq,carnumber,
			substring(fdate,1,4)+'.'+substring(fdate,5,2)+'.'+substring(fdate,7,2)+'~'+substring(tdate,1,4)+'.'+substring(tdate,5,2)+'.'+substring(tdate,7,2) bodate
			from carest a where 1=1 $where  ";
	$result  = sqlsrv_query( $mscon, $sql );
	$row =  sqlsrv_fetch_array($result); 
	$carseq = $row["carseq"];
	$carnumber = $row["carnumber"];
	$bodate = $row["bodate"];

	$sql_c = "insert into #imsi_sms(code,sutel,name)
			select a.carseq , a.chtel,a.pname
			from carest(nolock) a
			where 1=1
			  ".$where." 
			  and isnull(a.chtel,'') <> '' and len(isnull(a.chtel,'')) >= 10 and substring(isnull(a.chtel,''),1,2) = '01' " ;	
}else if($_POST['type'] == 'sms_kwn_gun') {
	$sql = "select isnull(b.name,'') insname , 
					substring(isnull(tdate,''),1,4)+'.'+substring(isnull(tdate,''),5,2)+'.'+substring(isnull(tdate,''),7,2) tdate,
					isnull(itemnm,'') itemnm
			from kwn a left outer join inssetup b on a.scode = b.scode and a.inscode = b.inscode
			where 1=1 $where";
	$result  = sqlsrv_query( $mscon, $sql );
	$row =  sqlsrv_fetch_array($result); 
	$insname = $row["insname"];
	$tdate = $row["tdate"];
	$itemnm = $row["itemnm"];

	$sql_c = "insert into #imsi_sms(code,sutel,name)
			select a.kcode , a.htel,a.kname
			from kwn(nolock) a
			where a.scode = '".$_SESSION['S_SCODE']."' 
			  ".$where." 
			  and isnull(a.htel,'') <> '' and len(isnull(a.htel,'')) >= 10 and substring(isnull(a.htel,''),1,2) = '01' " ;
}

//----------------------------------------------------------//
//                    SMS전송시
//----------------------------------------------------------//
if($_POST['type']){
	// 필수정보 확인
	if($_SESSION['S_SCODE'] == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '세센연결 error 필수입력값 오류, 재 로그인해주세요.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}

	// 트렌젝션 시작
    sqlsrv_query($mscon,"BEGIN TRAN");

	// 임시테이블생성
	$sql = "
		Create table #imsi_sms (code varchar(50) , sutel varchar(12) , name varchar(100) ) 
	";
	$result =  sqlsrv_query( $mscon, $sql );
	if ($result == false){
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' 알림톡전송 중 오류_0';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
	}	
	

	// 임시테이블에 전송할 리스트에 조회된 데이터 insert
	$sql = $sql_c;
	$result =  sqlsrv_query( $mscon, $sql );

	if ($result == false){
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' 알림톡전송 중 오류_1';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
	}	


	$sql	= "select contents , company.name , replace(replace(replace(convert(varchar,getdate(),120),'-',''),':',''),' ','') gdate , isnull(attach,'') attach
				from template left outer join company on template.scode = company.scode
				where template.scode = '".$_SESSION['S_SCODE']."' and template.code = '".$template."' ";
	$result  = sqlsrv_query( $mscon, $sql );
	$row =  sqlsrv_fetch_array($result); 
	$contents = $row["contents"];	
	$compname = $row["name"];	
	$gdate = $row["gdate"];

	$contents = str_replace('#{차량번호}',$carnumber,str_replace('#{보험기간}',$bodate,$contents));
	$contents = str_replace('#{보험사}',$insname,str_replace('#{상품명}',$itemnm,str_replace('#{만기일자}',$tdate,$contents)));

	$attach = $row["attach"];
	$attach = str_replace('#{차코드}',Encrypt_where($carseq,$secret_key,$secret_iv),str_replace('#{회사코드}',Encrypt_where($_SESSION['S_SCODE'],$secret_key,$secret_iv),$attach));

	$sql = "insert into daoubiz.dbo.BIZ_ATTACHMENTS(msg_key,seq,type,contents)
			select '".$gdate."'+code,1,'JSON','".$attach."'
			from #imsi_sms
			";
	$result =  sqlsrv_query( $mscon, $sql );
	if ($result == false){
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' 알림톡전송 중 오류_2-1';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
	}	

	$sql = "insert into daoubiz.dbo.biz_msg(CMID, MSG_TYPE, REQUEST_TIME, SEND_TIME, DEST_PHONE,
											SEND_PHONE, MSG_BODY,SCODE,SKEY,MEMBER,
											TEMPLATE_CODE,SENDER_KEY,NATION_CODE,ATTACHED_FILE)
			select '".$gdate."'+code,6,getdate(),getdate(),sutel,
					'".$tel."', replace(replace('".$contents."','#{상호명}','".$compname."'),'#{고객명}',name) ,'GAPLUS','".$_SESSION['S_SKEY']."','920',
					'".$template."','9090744b9586184e83b392d0f139e7ac87264a5a','82','".$gdate."'+code
			from #imsi_sms
			";
	$result =  sqlsrv_query( $mscon, $sql );
	if ($result == false){
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' 알림톡전송 중 오류_2-2';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
	}	



    sqlsrv_query($mscon,"COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message ='알림톡 전송을 완료했습니다.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "");
	echo json_encode($returnJson);
	exit;

}



?>