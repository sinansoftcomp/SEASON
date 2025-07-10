<?	
function sms_action($prg_id, $scode , $multi_sql, $send_id, $msg  , $ydate,$ytime, $mscon, $prg_proc) 
{
	global $sms_cnt, $lms_cnt,$sms_proc_key;
	

/* 
$prg_id   =  1명 ( 'join' )   다수선택 ('join_rows')     조건sql ('join_multi') 
$scode   = 미용실코드 
$multi_sql = 여러명선택  고객명단 또는 조건문 
$send_id  = 발송고객code  
$msg        = 메세지 
$ydate     = 예약일자 (yyyymmdd ) 즉시전송땐    '00000000' 
$ytime     = 예약시간 (hhmmss)       즉시전송 땐  '000000'  
 $mscon  = db컨넥센 
*/

$today   = date("Ymd"); 
$multi_sql = stripslashes(iconv("UTF-8","EUCKR",$multi_sql));


/*
echo $prg_id ."<br>";
echo $send_id . "<br>";
echo $message. "<br>";
echo $bal_no. "<br>";
echo $cnt ."<br>";
*/


//------기존에 보낸고 남아있는  임시파일 삭제. 
$sql = "delete from smstext_lms  where smstext_lms.hcode = '$scode'   ";
$result =  mssql_query($sql, $mscon);
if ($result == false){
	return '임시data삭제 error!!!';
}

 
if (!$prg_id){
	return  'sms모 프로그램이 없습니다';
}

if(!$msg){
	return  '전송하실 문자message입력되지 않았습니다!';
}

/* 예약상황 */
$ls_reqtime = $ydate.$ytime;

/* 회원사 정보등 */
$sql = "select    replace(replace(convert(varchar(20),getdate(), 120),':',''),'-','')   ls_date ,
						  isnull(tel1,'')+ isnull(tel2,'') + isnull(tel3,'')  bal_no ,
						   puse,
						   suse,
						   smslimit
		FROM    SINAN_WATER.DBO.COMPANY                
		WHERE SCODE = '$scode' ";



$result = mssql_query($sql, $mscon);
$row = mssql_fetch_assoc($result);


//--->발신번호 
$bal_no     =  $row[bal_no];
if(!$bal_no){
	return  'SMS발신번호가 정확하지 않습니다 !';
}


//----> 프로그램 사용여부
$puse     =  $row[puse];
if ($puse  !=  '2'){
	return  '프로그램 사용권한이 없습니다';
}


//----->sms 사용권한 
$suse     =  $row[suse];
if ($suse  !=  '2'){
	return  'SMS발송  중지된  고객입니다';
}



//--->sms여신금액 
$smslimit     =  $row[smslimit];


if (strlen($bal_no) <= 10) {
	$ls_sphone1 = substr($bal_no,0,3);
	$ls_sphone2 = substr($bal_no,3,3);
	$ls_sphone3 = substr($bal_no,6,4);
}else{
	$ls_sphone1 = substr($bal_no,0,3);
	$ls_sphone2 = substr($bal_no,3,4);
	$ls_sphone3 = substr($bal_no,7,4);
}

//-->전송일시 
$ls_date     =  $row[ls_date];
$ls_time     = substr($ls_date,9,6);
$ls_date    = substr($ls_date,0,8);
$ls_initme  =  $ls_date.$ls_time;


$inter_code2  = $ls_time; // key로 사용 함


//--------------------- program별 전송고객 파싱------ start 
$sql = '';

if ($prg_id == 'join_multi'){ 
	//-->조건을 선택하여 보낼시 
	$multi_sql =  "  SELECT   '$scode'  ,    '$inter_code2',     b.kcode , kwngo.kname , substring(kwngo.htel1,1,3), substring(kwngo.htel2,1,4), substring(kwngo.htel3,1,4), substring(kwngo.htel1,1,3), substring(kwngo.htel2,1,4), substring(kwngo.htel3,1,4) ,  '$msg'  " . $multi_sql;
	$multi_sql = stripslashes($multi_sql);


	
	$sql = "INSERT INTO smstext_lms( hcode,  jtime ,  recvnum ,  recvname, 
									 rphone1,               rphone2 ,               rphone3 ,   
									 htel1,                 htel2,                  htel3 ,                 msg)" .$multi_sql .   " and b.kcode!='' and dataLENgth(isnull(kwngo.htel1,'')+ isnull(kwngo.htel2,'') + isnull(kwngo.htel3,'')) >= 10 "; 

}




//--------------------- program별 전송고객 파싱------ end 
if (!$sql) {
	return  'SMS전송할 고객이 정확하지 않습니다.';
}

$result =  mssql_query($sql, $mscon);
if ($result == false){
	return  'SMS전송중 work DATA INSERT중 ERROR가 발생되었습니다';
}



//----------------------------------------------------------------------------------------------------------------------------------------//
//   메크로변환 start 
//----------------------------------------------------------------------------------------------------------------------------------------//
//-->고객명변환 
$sql = "update smstext_lms     
						set msg = replace(msg,'&&고객명&&', isnull(c.kname,'') )  
			 from smstext_lms      left outer join kwn on smstext_lms.hcode = kwn.scode and smstext_lms.recvnum = kwn.kcode
								   left outer join kwngo c on c.scode = kwn.scode and kwn.gcode = c.gcode
			 where smstext_lms.hcode = '$scode' and smstext_lms.jtime = '$inter_code2' ";
			
								 
$result =  mssql_query($sql, $mscon);
if ($result == false){
	return  '고객명 메크로 변환 중 ERROR발생.... ';
}



//-->청구잔액 
$sql = "update smstext_lms set msg = replace(msg,'%청구잔액%', isnull(JAN_AMT,0))  
		from smstext_lms  left outer join 
										(
											SELECT A.SCODE, A.GCODE, COUNT(*) KWN_CNT,  SUM(ISNULL(JAN_AMT,0)) JAN_AMT
											FROM KWN A LEFT OUTER JOIN
													(
													select     last_cho.KCODE, hyy+hmm+hdd HYMD , 
																		 isnull(last_cho.hjamt,0) + isnull(hamt1,0) - isnull(ijamt,0) - isnull(iamt1,0) JAN_AMT 
													from (
															select * from 	
																 (select a.kcode , hyy , hmm , hdd,  hjamt , hamt1, hamt2, c.gcode,
																	  row_number() over(partition by a.kcode order by hyy desc , hmm desc) cnt 
																 from cho a left outer join  KWN b on a.SCODE=b.SCODE and a.KCODE=b.KCODE
																			left outer join  KWNgo c on b.SCODE=c.SCODE and b.gcode=c.gcode
																 where a.SCODE = '$S_SCODE'   ) a 
															where cnt = 1                 
														  ) last_cho left outer join 
														  ( select a.KCODE, ICYY , ICMM, SUM(IJAMT) IJAMT , SUM(IAMT1) IAMT1 , SUM(IAMT2) IAMT2 
																 from IPMST a left outer join  KWN b on a.SCODE=b.SCODE and a.KCODE=b.KCODE
																			  left outer join  KWNgo c on b.SCODE=c.SCODE and b.gcode=c.gcode
																 where a.SCODE = '$S_SCODE'  
																 GROUP BY a.KCODE , ICYY, ICMM )  cho_ip
														   on last_cho.KCODE = cho_ip.KCODE and last_cho.HYY = cho_ip.ICYY and last_cho.HMM = cho_ip.ICMM 
													) B	 ON A.KCODE =B.KCODE
											 
											WHERE A.SCODE = '$S_SCODE'  										
											GROUP BY A.SCODE,	A.GCODE	
										) B	 on smstext_lms.hcode = B.scode and smstext_lms.recvnum = B.Gcode 
		where smstext_lms.hcode = '$S_SCODE' and smstext_lms.jtime = '$inter_code2' ";
$result =  mssql_query($sql, $mscon);

if ($result == false){
	mssql_query("ROLLBACK");
	mssql_close($mscon);
	$message = '청구잔액 메크로뱐환 중 ERROR발생.... ';
	echo "<script>alert('$message');parent.reloadLayer('')</script>";
	exit;
}

//-->상품명								 
$sql = "update smstext_lms set msg = replace(msg,'%상품명%', isnull(ITEM_NAME,''))  
		from smstext_lms  left outer join 
										(
											select A.SCODE,
												   A.GCODE,
												   A.ITEM_NAME
											from 	
												 (select A.SCODE,A.GCODE, D.NAME AS ITEM_NAME ,
													  row_number() over(partition by A.GCODE order BY C.KCODE, C.ITEMSEQ  ) cnt,
													  count(*) over(partition by a.GCODE)     ITEM_QUT   										   
													FROM KWNGO A LEFT OUTER JOIN KWN     B ON A.SCODE=B.SCODE AND A.GCODE=B.GCODE
																 LEFT OUTER JOIN KWNITEM C ON B.SCODE=C.SCODE AND B.KCODE=C.KCODE   
																 LEFT OUTER JOIN ITEM  D ON C.SCODE=D.SCODE AND C.ITEM=D.CODE									   
												 where a.SCODE = '$S_SCODE' ) a 
											where cnt = 1 
										) B	 on smstext_lms.hcode = B.scode and smstext_lms.recvnum = B.Gcode 
		where smstext_lms.hcode = '$S_SCODE' and smstext_lms.jtime = '$inter_code2' ";

$result =  mssql_query($sql, $mscon);

if ($result == false){
	mssql_query("ROLLBACK");
	mssql_close($mscon);
	$message = '상품명 메크로뱐환 중 ERROR발생.... ';
	echo "<script>alert('$message');parent.reloadLayer('')</script>";
	exit;
}







//----------------------------------------------------------------------------------------------------------------------------------------//
//   메크로변환 end  
//----------------------------------------------------------------------------------------------------------------------------------------//


//----------------------------------------------------------------------------------------------------------------------------------------//
//   sms lms 발송 건수 한도 check start 
//----------------------------------------------------------------------------------------------------------------------------------------//

$li_sms = 0;
$li_lms	= 0;

$sql = "select count(*)  t_cnt FROM  smstext_lms  WHERE   hcode  = '$scode' AND    jtime   = '$inter_code2' and  dataLENgth(msg) <=  88 "; 
$result = mssql_query($sql);
$row = mssql_fetch_array($result);

if($row[t_cnt]){
  $li_sms = $row[t_cnt];
}else{
  $li_sms = 0; 
}

$sql = "select count(*)  t_cnt FROM  smstext_lms  WHERE   hcode  = '$scode' AND    jtime   = '$inter_code2' and  dataLENgth(msg) >  88 "; 
$result = mssql_query($sql);
$row = mssql_fetch_array($result);
if($row[t_cnt]){
  $li_lms = $row[t_cnt];
}else{
  $li_lms = 0; 
}


/*-- 기존sms전송 건  --*/
$sql = "select scnt ld_scnt
			from suremsms.dbo.smscount(nolock)
			where gubun = '1' and scode = '$scode' and sdate <= '$ls_date' and edate >= '$ls_date' ";


$result = mssql_query($sql, $mscon);
$row = mssql_fetch_assoc($result);
$ld_scnt    = $row[ld_scnt];

$sql = "select scnt ld_mcnt
			from suremsms.dbo.mmscount(nolock)
			where gubun = '1' and scode = '$scode' and sdate <= '$ls_date' and edate >= '$ls_date' ";


$result = mssql_query($sql, $mscon);
$row = mssql_fetch_assoc($result);
$ld_mcnt    = $row[ld_mcnt];

if (!$ld_scnt) {
	$ld_scnt = 0;
}

if (!$ld_mcnt) {
	$ld_mcnt = 0;
}

$ld_gunsuamt =   ($ld_scnt * 25.3) +  ($ld_mcnt * 55);

/*-- 지금 전송하려고 한건   --*/
$sql = "select count(*)  ld_scnt   from smstext_lms where hcode = '$scode'  and jtime = '$inter_code2'   and dataLENgth(msg) <=  88";
$result = mssql_query($sql, $mscon);
$row = mssql_fetch_assoc($result);
$ld_scnt    = $row[ld_scnt];


$sql = "select count(*)   ld_mcnt  from smstext_lms where hcode = '$scode'  and   jtime =  '$inter_code2'   and dataLENgth(msg) >  88 ";

$result = mssql_query($sql, $mscon);
$row = mssql_fetch_assoc($result);
$ld_mcnt    = $row[ld_mcnt];


if (!$ld_scnt) {
	$ld_scnt = 0;
}

if (!$ld_mcnt) {
	$ld_mcnt = 0;
}

$ld_gunsuamt =  $ld_gunsuamt  +  ($ld_scnt * 25.3) + ($ld_mcnt * 55);

if ($smslimit < $ld_gunsuamt){
	return  'SMS발송 한도금액이 부족합니다.';
}

//----------------------------------------------------------------------------------------------------------------------------------------//
//   최종sms발송  start 
//----------------------------------------------------------------------------------------------------------------------------------------//

//--->검색조건 검색 
if($prg_proc=="N"){
	$sms_cnt		= 	$ld_scnt;
	$lms_cnt		= $ld_mcnt;
	$sms_proc_key	= $inter_code2;	
	return 0;
   //select MSG, HTEL1+HTEL2+HTEL3 HTEL , RECVNAME  from  smstext_lms  WHERE   hcode  = 'smstest' AND    jtime   = '$inter_code2' 

}

//ld_member = gi_sms_f_member//---->직접 업무 숫자로입력. 
$ls_login = 'sinit3';

//SMS 보내기
$sql = "insert into suremsms.dbo.smsdata
              (indate,         intime,       member,      sendid,       sendname,
			   rphone1,        rphone2,      rphone3,     recvname,     sphone1,
			   sphone2,        sphone3,      msg,         url,          rdate, 
			   rtime,          result,       kind,        errcode,      recvtime) 
        select '$ls_date',    '$ls_time',    800,        '$ls_login',  '$scode',
		       rphone1,        rphone2,      rphone3,      recvnum,     '$ls_sphone1',
			 '$ls_sphone2',   '$ls_sphone3', msg,          '".$_SERVER["REMOTE_ADDR"]."',          '$ydate',
			 '$ytime',     '0',           'S',          '0' ,        '' 
        from smstext_lms where hcode = '$scode'  and jtime = '$inter_code2'   and dataLENgth(msg) <=  88 ";


//echo $sql;
//exit;

$result =  mssql_query($sql, $mscon);

if ($result == false){
	return  'SMS전송중 슈어엠 DATA INSERT중 ERROR가 발생되었습니다';
}

//MMS 보내기
$ls_sphone = $ls_sphone1.$ls_sphone2.$ls_sphone3;
$sql = "insert into suremsms.dbo.MMSDATA
             ( USERCODE,          INTIME,          REQTIME,       RECVTIME,          CALLPHONE, 
			   REQPHONE,          SUBJECT,         MSG,           FKCONTENT,         MEDIATYPE,
			   RESULT,            ERRCODE,         SENDNAME,      RECVNAME,          MEMBER)  
       select '$ls_login',       '$ls_initme',   '$ls_reqtime',   '',                rphone1+rphone2+rphone3 ,
	          '$ls_sphone',       '',               msg,           0,                 '', 
			   '0',               0,              '$scode'  ,    recvnum,           800              
	   from smstext_lms where hcode = '$scode'  and   jtime =  '$inter_code2'   and dataLENgth(msg) >  88 "; 

$result =  mssql_query($sql, $mscon);

if ($result == false){
	return 'LMS전송중 슈어엠 DATA INSERT중 ERROR가 발생되었습니다';
}

//발송후 임시테일 삭제
$sql = "delete from smstext_lms  where smstext_lms.hcode = '$scode'  and smstext_lms.jtime = '$inter_code2' ";
$result =  mssql_query($sql, $mscon);
if ($result == false){
	return '임시data삭제 error!!!';
}
//----------------------------------------------------------------------------------------------------------------------------------------//
//   최종sms발송  end 
//----------------------------------------------------------------------------------------------------------------------------------------//

return 0 ;

}
?>
