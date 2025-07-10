<?

include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

$sdate1 = $_POST['sdate1'];
$sdate2 = $_POST['sdate2'];
$where = iconv("UTF-8","EUC-KR",$_POST['where']);
$bigo = iconv("UTF-8","EUC-KR",$_POST['bigo']);
$tel = $_POST['tel'];			// 발신자번호
$totalbyte = $_POST['totalbyte'];
$name = iconv("UTF-8","EUC-KR",$_POST['name']);

$carbigo="";
if(isset($_POST['carbigo'])){
	$sql = "select carseq from carestamt a where 1=1 $where  ";
	$result  = sqlsrv_query( $mscon, $sql );
	$row =  sqlsrv_fetch_array($result); 
	$carseq = $row["carseq"];

	$enscode = Encrypt_where($_SESSION['S_SCODE'],$secret_key,$secret_iv);
	$encarseq = Encrypt_where($carseq,$secret_key,$secret_iv);

	$carbigo = "https://gaplus.net:452/bin/submobile/m_menu08_car.php?scode=".$enscode."&carseq=".$encarseq." ";
	$bigo .= "  ".$carbigo;
	$totalbyte = "100";		// 자동차비교견적은 무조건 MMS로 보내게 처리
}

$sql_c = "";
// action작업전 sql구문 만들어주기
if($_POST['type'] == 'sms_kwngo_list'){
	$sql_c = "insert into #imsi_sms(code,sutel,name)
			select a.gcode , a.htel1+a.htel2+a.htel3 , a.kname
			from kwngo(nolock) a
				left outer join swon(nolock) b on a.scode = b.scode and a.iswon = b.skey
				left outer join swon(nolock) c on a.scode = c.scode and a.uswon = c.skey
				left outer join swon(nolock) e on a.scode = e.scode and a.ksman = e.skey
				left outer join bonbu(nolock) f on e.scode = f.scode and e.bonbu = f.bcode
				left outer join jisa(nolock) g on e.scode = g.scode and e.jisa = g.jscode
				left outer join jijum(nolock) h on e.scode = h.scode and e.jijum = h.jcode
				left outer join team(nolock) i on e.scode = i.scode and e.team = i.tcode
			where a.scode = '".$_SESSION['S_SCODE']."'
			  and a.kdate between '".$sdate1."' and '".$sdate2."'  ".$where." 
			  and isnull(a.htel1,'')+isnull(a.htel2,'')+isnull(a.htel3,'') <> '' and len(isnull(a.htel1,'')+isnull(a.htel2,'')+isnull(a.htel3,'')) >= 10 and substring(isnull(a.htel1,''),1,2) = '01'";

}else if($_POST['type']=='sms_kwn_list' or $_POST['type']=='sms_kwn_list2'){
	$kdate = "";
	if($_POST['type']=='sms_kwn_list'){
		$kdate = "a.kdate";
	}else{
		$kdate = "a.tdate";
	}
	$sql_c = "insert into #imsi_sms(code,sutel,name)
			select a.kcode , a.htel,a.kname
			from kwn(nolock) a
				left outer join inssetup(nolock) f on a.scode = f.scode and a.inscode = f.inscode
				left outer join inswon(nolock) is1 on a.scode = is1.scode and a.ksman = is1.bscode
				left outer join inswon(nolock) is2 on a.scode = is2.scode and a.kdman = is2.bscode
				left outer join swon(nolock) s1 on s1.scode = a.scode and s1.skey = is1.skey
				left outer join swon(nolock) s2 on s2.scode = a.scode and s2.skey = is2.skey
				left outer join bonbu(nolock) b on s2.scode = b.scode and s2.bonbu = b.bcode
				left outer join jisa(nolock) c on s2.scode = c.scode and s2.jisa = c.jscode
				left outer join jijum(nolock) d on s2.scode = d.scode and s2.jijum = d.jcode
				left outer join team(nolock) e on s2.scode = e.scode and s2.team = e.tcode
			where a.scode = '".$_SESSION['S_SCODE']."' 
			  and ".$kdate." between '".$sdate1."' and '".$sdate2."'  ".$where." 
			  and isnull(a.htel,'') <> '' and len(isnull(a.htel,'')) >= 10 and substring(isnull(a.htel,''),1,2) = '01' " ;	
}else if($_POST['type'] == 'sms_kwngo_gun') {
	$sql_c = "insert into #imsi_sms(code,sutel,name)
			select a.gcode , a.htel1+a.htel2+a.htel3 , a.kname
			from kwngo(nolock) a
			where a.scode = '".$_SESSION['S_SCODE']."'
			  ".$where." 
			  and isnull(a.htel1,'')+isnull(a.htel2,'')+isnull(a.htel3,'') <> '' and len(isnull(a.htel1,'')+isnull(a.htel2,'')+isnull(a.htel3,'')) >= 10 and substring(isnull(a.htel1,''),1,2) = '01'";
}else if($_POST['type'] == 'sms_kwn_gun') {
	$sql_c = "insert into #imsi_sms(code,sutel,name)
			select a.kcode , a.htel,a.kname
			from kwn(nolock) a
			where a.scode = '".$_SESSION['S_SCODE']."' 
			  ".$where." 
			  and isnull(a.htel,'') <> '' and len(isnull(a.htel,'')) >= 10 and substring(isnull(a.htel,''),1,2) = '01' " ;	
}else if($_POST['type'] == 'sms_swon_list' || $_POST['type'] == 'sms_swon_gun') {
	$sql_c = "insert into #imsi_sms(code,sutel,name)
			select a.skey , a.htel1+a.htel2+a.htel3 , a.sname
			from swon(nolock) a
			where a.scode = '".$_SESSION['S_SCODE']."'
			  ".$where." 
			  and isnull(a.htel1,'')+isnull(a.htel2,'')+isnull(a.htel3,'') <> '' and len(isnull(a.htel1,'')+isnull(a.htel2,'')+isnull(a.htel3,'')) >= 10 and substring(isnull(a.htel1,''),1,2) = '01'";
}else if($_POST['type'] == 'sms_car_gun') {
	$sql_c = "insert into #imsi_sms(code,sutel,name)
			select a.carseq , a.chtel,a.pname
			from carest(nolock) a
			where 1=1
			  ".$where." 
			  and isnull(a.chtel,'') <> '' and len(isnull(a.chtel,'')) >= 10 and substring(isnull(a.chtel,''),1,2) = '01' " ;	
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
    sqlsrv_query($mscon, "BEGIN TRAN");

	// 임시테이블생성
	$sql = "
		Create table #imsi_sms (code varchar(50) , sutel varchar(12) , name varchar(100) ) 
	";
	$result =  sqlsrv_query( $mscon, $sql );
	if ($result == false){
		sqlsrv_query($mscon, "ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' SMS전송 중 오류_0';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
	}	
	

	// 임시테이블에 전송할 리스트에 조회된 데이터 insert
	$sql = $sql_c;
	$result =  sqlsrv_query( $mscon, $sql );

	if ($result == false){
		sqlsrv_query($mscon, "ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' SMS전송 중 오류_1';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
	}	
	$sgubun="";
	// SMS 나 MMS 구분 후 메세지전송
	if($totalbyte < 90){
		$sgubun="SMS";
		$sql = "insert into daoubiz.dbo.biz_msg(CMID, MSG_TYPE, REQUEST_TIME, SEND_TIME, DEST_PHONE,
												SEND_PHONE, MSG_BODY,SCODE,SKEY,MEMBER)
				select replace(replace(replace(convert(varchar,getdate(),120),'-',''),':',''),' ','')+code,0,getdate(),getdate(),sutel,
						'".$tel."', replace(replace('".$bigo."','&&고객명&&',name),'&&사원명&&',name) ,'GAPLUS','".$_SESSION['S_SKEY']."','920'
				from #imsi_sms
				";
		$result =  sqlsrv_query( $mscon, $sql );
		if ($result == false){
			sqlsrv_query($mscon, "ROLLBACK");
			sqlsrv_free_stmt($result);
			sqlsrv_close($mscon);
			$message = ' SMS전송 중 오류_2';
			$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
			echo json_encode($returnJson);
			exit;
		}	
	}else{
		$sgubun="MMS";
		$sql = "insert into daoubiz.dbo.biz_msg(CMID, MSG_TYPE, REQUEST_TIME, SEND_TIME, DEST_PHONE,
												SEND_PHONE, MSG_BODY,SCODE,SKEY,MEMBER,SUBJECT)
				select replace(replace(replace(convert(varchar,getdate(),120),'-',''),':',''),' ','')+code,5,getdate(),getdate(),sutel,
						'".$tel."', replace(replace('".$bigo."','&&고객명&&',name),'&&사원명&&',name) ,'GAPLUS','".$_SESSION['S_SKEY']."','920','".$name."'
				from #imsi_sms
				";
		$result =  sqlsrv_query( $mscon, $sql );
		if ($result == false){
			sqlsrv_query($mscon, "ROLLBACK");
			sqlsrv_free_stmt($result);
			sqlsrv_close($mscon);
			$message = ' SMS전송 중 오류_3';
			$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
			echo json_encode($returnJson);
			exit;
		}	
	}


    sqlsrv_query($mscon, "COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = $sgubun.'전송을 완료했습니다.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "");
	echo json_encode($returnJson);
	exit;

}



?>