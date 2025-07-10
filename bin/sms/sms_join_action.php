<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/sms/sms_action.php");


$prg_id   	= $_POST['prg_id'];

$prg_proc  	= $_POST['prg_proc']; // N ==> 체크만;
$sms_cnt	= 0;
$lms_cnt	= 0;
$sms_proc_key	= '';

$multi_sql	= $_POST['multi_sql'];
$scode   	= $_SESSION['S_SCODE'];

$send_id  	= $_POST['skey'];
$msg        = iconv("UTF-8","CP949",$_POST['msg']);
//$msg        = iconv("UTF-8","EUC-KR",$_POST['msg']);

$ydate     	= str_replace("-","",str_replace("/","",$_POST['ydate']));
$ytime     	= $_POST['ytime'];
$mscon  	= $mscon;


mssql_query("BEGIN TRAN");  //--->sms에대한 트랜젝션 
$r_msg =		sms_action ($prg_id, $scode ,$multi_sql,  $send_id, $msg  , $ydate,$ytime, $mscon, $prg_proc) ;

if( $r_msg == 0) {
	mssql_query("COMMIT");	
    mssql_close($mscon);	
} else{	
   mssql_query("ROLLBACK");
   mssql_close($mscon);
}

if($r_msg=='0') $message = "메세지가 전송되었습니다.";
else $message = $r_msg;

$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), 	"url"	=> "", 	"sms_cnt"	=> $sms_cnt, 	"lms_cnt"	=> $lms_cnt, 	"sms_proc_key"	=> $sms_proc_key, 	"prg_proc"	=> $prg_proc);
echo json_encode($returnJson);
exit;


?>