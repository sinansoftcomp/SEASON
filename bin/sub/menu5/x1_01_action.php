<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

// �������. ���Ŀ� COMPANY���̺��� �����ð�.
$X = "X1";

$type	=	$_POST['type'];

$jsyymm=rtrim(ltrim(str_replace('-','',$_POST['jsyymm'])));
$jeyymm=rtrim(ltrim(str_replace('-','',$_POST['jeyymm'])));

$set="";
$insert="";
$values="";
$jiyul=array();

for($i=1;$i<=100;$i++){
	$set .= "jiyul".$i ."= \'".$_POST["jiyul".$i]."\' ,";
	$insert .= "jiyul".$i." ,";

	if($_POST["jiyul".$i]){
		$jiyul[$i]=$_POST["jiyul".$i];
	}else{
		$jiyul[$i] = 0;
	}
	$values .= "".$jiyul[$i]." ,";
}

$set = stripslashes($set); // ���� ���� �������� ����

//----------------------------------------------------------//
//                    ������ ó������							// 
//----------------------------------------------------------//
if($type=='up'){

	// �ʼ����� Ȯ��
	if($_SESSION['S_SCODE'] == null or $_POST['skey'] == null or $_POST['inscode'] == null or $_POST['insilj'] == null or $_POST['seq'] == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '�������� error �ʼ��Է°� ����, �� �α������ּ���.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}

	$skey = $_POST['skey'];
	$inscode = $_POST['inscode'];
	$insilj = $_POST['insilj'];
	$seq = $_POST['seq'];

	// Ʈ������ ����
    sqlsrv_query($mscon,"BEGIN TRAN");
	
	// ------------------����ó�� ����-----------------//
	// ����������ڿ� �����������ڰ� ������ �����ϴ� ���ڿ� ��ĥ�� ����
	if($jeyymm != '99991231'){
		$sql	= "
				select count(*) jcnt
				from ".$X."_sjirule a
				where scode = '".$_SESSION['S_SCODE']."' and skey = '".$_POST['skey']."' 
						and inscode = '".$_POST['inscode']."' and insilj = '".$_POST['insilj']."'
						and jeyymm != '99991231'
						and ( jsyymm between '".$jsyymm."' and '".$jeyymm."'  or jeyymm between '".$jsyymm."' and '".$jeyymm."')
						and not exists(
										select * from ".$X."_sjirule
										where a.scode = ".$X."_sjirule.scode and a.skey = ".$X."_sjirule.skey and a.inscode = ".$X."_sjirule.inscode and a.insilj = ".$X."_sjirule.insilj and a.seq = ".$X."_sjirule.seq
												and scode = '".$_SESSION['S_SCODE']."' and skey = '".$_POST['skey']."' 
												and inscode = '".$_POST['inscode']."' and insilj = '".$_POST['insilj']."' and seq = ".$_POST['seq']."
										)  
					";

		$result  = sqlsrv_query( $mscon, $sql );
		$row =  sqlsrv_fetch_array($result); 
		$jcnt = $row["jcnt"];
		
		if($jcnt > 0){
			sqlsrv_free_stmt($result);
			sqlsrv_close($mscon);
			$message = '���� '.$skey.'����� ������ۿ��̳� ����������� ��Ĩ�ϴ�!';
			$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
			echo json_encode($returnJson);
			exit;			
		}
	}
	// ------------------����ó�� ����-----------------//

	$sql = "update ".$X."_sjirule 
			set ".$set." jsyymm = '".$jsyymm."' , jeyymm = '".$jeyymm."'
			where scode = '".$_SESSION['S_SCODE']."' and skey = '".$_POST['skey']."' and inscode = '".$_POST['inscode']."' and insilj = '".$_POST['insilj']."' and seq = ".$_POST['seq']." ";

	$result =  sqlsrv_query( $mscon, $sql );
	
	if ($result == false){
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' ����� ���޷����� �� ����';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
	}


    sqlsrv_query($mscon,"COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' ����� ���޷��� �����Ͽ����ϴ�.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "rtype" => "up");
	echo json_encode($returnJson);
	exit;
}

//----------------------------------------------------------//
//                    �Է½� ó������							// 
//----------------------------------------------------------//
if($type=='in'){
	// �ʼ����� Ȯ��
	if($_SESSION['S_SCODE'] == null or $_POST['skey_s'] == null or $_POST['inscode_s'] == null or $_POST['insilj_s'] == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '�������� error �ʼ��Է°� ����, �� �α������ּ���.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}

	$skey = $_POST['skey_s'];
	$inscode = $_POST['inscode_s'];
	$insilj = $_POST['insilj_s'];
	
	// Ʈ������ ����
    sqlsrv_query($mscon, "BEGIN TRAN");

	// ------------------����ó�� ����-----------------//
	// ����������ڿ� �����������ڰ� ������ �����ϴ� ���ڿ� ��ĥ�� ����
	if($jeyymm <> '99991231'){
		$sql	= "
				select count(*) jcnt
				from ".$X."_sjirule a
				where scode = '".$_SESSION['S_SCODE']."' and skey = '".$skey."' 
						and inscode = '".$inscode."' and insilj = '".$insilj."'
						and jeyymm != '99991231'
						and ( jsyymm between '".$jsyymm."' and '".$jeyymm."'  or jeyymm between '".$jsyymm."' and '".$jeyymm."')
					";
		$result  = sqlsrv_query( $mscon, $sql );
		$row =  sqlsrv_fetch_array($result); 
		$jcnt = $row["jcnt"];

		if($jcnt > 0){
			sqlsrv_free_stmt($result);
			sqlsrv_close($mscon);
			$message = '���� '.$skey.'����� ����������ڳ� �����������ڰ� ��Ĩ�ϴ�.';
			$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
			echo json_encode($returnJson);
			exit;			
		}
	}
	// ------------------����ó�� ����-----------------//

	// ����(SEQ)���ϱ� //
	$sql	= "
			select isnull(max(seq),0)+1 mseq
			from ".$X."_sjirule a
			where scode = '".$_SESSION['S_SCODE']."' and skey = '".$skey."' 
					and inscode = '".$inscode."' and insilj = '".$insilj."'
				";
	$result  = sqlsrv_query( $mscon, $sql );
	$row =  sqlsrv_fetch_array($result); 
	$seq = $row["mseq"];


	$sql = "insert into ".$X."_sjirule (scode,skey,inscode,insilj,seq,".$insert." jsyymm,jeyymm)
			values('".$_SESSION['S_SCODE']."','".$skey."','".$inscode."','".$insilj."',".$seq.",".$values." '".$jsyymm."' , '".$jeyymm."')";
	$result =  sqlsrv_query( $mscon, $sql );
	
	if ($result == false){
		sqlsrv_query($mscon, "ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' ����� ���޷���� �� ����';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
	}

    sqlsrv_query($mscon, "COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' ����� ���޷��� ����Ͽ����ϴ�.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "skey"=>"$skey" , "inscode"=>"$inscode" , "insilj"=>"$insilj" , "seq"=>"$seq" , "rtype" => "in");
	echo json_encode($returnJson);
	exit;

}


if($type == "del"){

	// �ʼ����� Ȯ��
	if($_SESSION['S_SCODE'] == null or $_POST['skey'] == null or $_POST['inscode'] == null or $_POST['insilj'] == null or $_POST['seq'] == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '�������� error �ʼ��Է°� ����, �� �α������ּ���.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}

	$skey = $_POST['skey'];
	$inscode = $_POST['inscode'];
	$insilj = $_POST['insilj'];
	$seq = $_POST['seq'];

	$sql = "delete from ".$X."_sjirule 
			where scode = '".$_SESSION['S_SCODE']."' and skey = '".$skey."' and inscode = '".$inscode."' and insilj = '".$insilj."' and seq = ".$seq." ";

	// Ʈ������ ����
    sqlsrv_query($mscon, "BEGIN TRAN");
    $result =  sqlsrv_query( $mscon, $sql );

    if ($result == false){
		sqlsrv_query($mscon, "ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' ����� ���޷����� �� ����';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
    }

    sqlsrv_query($mscon, "COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' ����� ���޷��� �����Ͽ����ϴ�.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "rtype" => "del");
	echo json_encode($returnJson);
	exit;
}

?>