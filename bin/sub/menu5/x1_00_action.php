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
	if($_SESSION['S_SCODE'] == null or $_POST['jik'] == null or $_POST['inscode'] == null or $_POST['insilj'] == null or $_POST['seq'] == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '�������� error �ʼ��Է°� ����, �� �α������ּ���...!';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}

	$jik = $_POST['jik'];
	$inscode = $_POST['inscode'];
	$insilj = $_POST['insilj'];
	$seq = $_POST['seq'];

	// Ʈ������ ����
    sqlsrv_query("BEGIN TRAN");
	
	// ------------------����ó�� ����-----------------//
	// ����������ڿ� �����������ڰ� ������ �����ϴ� ���ڿ� ��ĥ�� ����
	if($jeyymm != '99991231'){
		$sql	= "
				select count(*) jcnt
				from	X1_jirule a
				where scode = '".$_SESSION['S_SCODE']."' and jik = '".$_POST['jik']."' 
						and inscode = '".$_POST['inscode']."' and insilj = '".$_POST['insilj']."'
						and jeyymm != '99991231'
						and ( jsyymm between '".$jsyymm."' and '".$jeyymm."'  or jeyymm between '".$jsyymm."' and '".$jeyymm."')
						and not exists(
										select * from X1_jirule
										where a.scode =X1_jirule.scode and a.skey =X1_jirule.skey and a.inscode = X1_jirule.inscode and a.insilj = X1_jirule.insilj and a.seq = X1_jirule.seq
												and scode = '".$_SESSION['S_SCODE']."' and jik = '".$_POST['jik']."' 
												and inscode = '".$_POST['inscode']."' and insilj = '".$_POST['insilj']."' and seq = ".$_POST['seq']."
										)  
					";
		$result  = sqlsrv_query( $mscon, $sql );
		$row =  sqlsrv_fetch_array($result); 
		$jcnt = $row["jcnt"];

		if($jcnt > 0){
			sqlsrv_free_stmt($result);
			sqlsrv_close($mscon);
			$message = '���� '.$skey.'������ ������ۿ��̳� ����������� ��Ĩ�ϴ�!';
			$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
			echo json_encode($returnJson);
			exit;			
		}
	}
	// ------------------����ó�� ����-----------------//

	$sql = "update ".$X."_jirule 
			set ".$set." jsyymm = '".$jsyymm."' , jeyymm = '".$jeyymm."'
			where scode = '".$_SESSION['S_SCODE']."' and jik = '".$_POST['jik']."' and inscode = '".$_POST['inscode']."' and insilj = '".$_POST['insilj']."' and seq = ".$_POST['seq']." ";

	$result =  sqlsrv_query( $mscon, $sql );
	
	if ($result == false){
		sqlsrv_query("ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' ���޺� ���޷����� �� ����';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
	}


    sqlsrv_query("COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' ������ ���޷��� �����Ͽ����ϴ�.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "rtype" => "up");
	echo json_encode($returnJson);
	exit;
}

//----------------------------------------------------------//
//                    �Է½� ó������							// 
//----------------------------------------------------------//
if($type=='in'){
	// �ʼ����� Ȯ��
	if($_SESSION['S_SCODE'] == null or $_POST['jik_s'] == null or $_POST['inscode_s'] == null or $_POST['insilj_s'] == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '�������� error �ʼ��Է°� ����, �� �α������ּ���in.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}

	$jik = $_POST['jik_s'];
	$inscode = $_POST['inscode_s'];
	$insilj = $_POST['insilj_s'];
	
	// Ʈ������ ����
    sqlsrv_query("BEGIN TRAN");

	// ------------------����ó�� ����-----------------//
	// ����������ڿ� �����������ڰ� ������ �����ϴ� ���ڿ� ��ĥ�� ����
	if($jeyymm <> '99991231'){
		$sql	= "
				select count(*) jcnt
				from ".$X."_jirule a
				where scode = '".$_SESSION['S_SCODE']."' and jik = '".$jik."' 
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
			$message = '���� '.$jik.'������ ����������ڳ� �����������ڰ� ��Ĩ�ϴ�.';
			$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
			echo json_encode($returnJson);
			exit;			
		}
	}
	// ------------------����ó�� ����-----------------//

	// ����(SEQ)���ϱ� //
	$sql	= "
			select isnull(max(seq),0)+1 mseq
			from ".$X."_jirule a
			where scode = '".$_SESSION['S_SCODE']."' and jik = '".$jik."' 
					and inscode = '".$inscode."' and insilj = '".$insilj."'
				";
	$result  = sqlsrv_query( $mscon, $sql );
	$row =  sqlsrv_fetch_array($result); 
	$seq = $row["mseq"];


	$sql = "insert into ".$X."_jirule (scode,jik,inscode,insilj,seq,".$insert." jsyymm,jeyymm)
			values('".$_SESSION['S_SCODE']."','".$jik."','".$inscode."','".$insilj."',".$seq.",".$values." '".$jsyymm."' , '".$jeyymm."')";
	$result =  sqlsrv_query( $mscon, $sql );
	
	if ($result == false){
		sqlsrv_query("ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' ���޺� ���޷���� �� ����';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
	}

    sqlsrv_query("COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' ������ ���޷��� ����Ͽ����ϴ�.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "jik"=>"$jik" , "inscode"=>"$inscode" , "insilj"=>"$insilj" , "seq"=>"$seq" , "rtype" => "in");
	echo json_encode($returnJson);
	exit;

}


if($type == "del"){

	// �ʼ����� Ȯ��
	if($_SESSION['S_SCODE'] == null or $_POST['jik'] == null or $_POST['inscode'] == null or $_POST['insilj'] == null or $_POST['seq'] == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '�������� error �ʼ��Է°� ����, �� �α������ּ���del.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}

	$jik = $_POST['jik'];
	$inscode = $_POST['inscode'];
	$insilj = $_POST['insilj'];
	$seq = $_POST['seq'];

	$sql = "delete from ".$X."_jirule 
			where scode = '".$_SESSION['S_SCODE']."' and jik = '".$jik."' and inscode = '".$inscode."' and insilj = '".$insilj."' and seq = ".$seq." ";

	// Ʈ������ ����
    sqlsrv_query("BEGIN TRAN");
    $result =  sqlsrv_query( $mscon, $sql );

    if ($result == false){
		sqlsrv_query("ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' ���޺� ���޷����� �� ����';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
    }

    sqlsrv_query("COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' ���޺� ���޷��� �����Ͽ����ϴ�.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "rtype" => "del");
	echo json_encode($returnJson);
	exit;
}




?>