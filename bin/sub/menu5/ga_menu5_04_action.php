<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

extract($_POST);

$skey		=	$_POST['skey'];
$insilj		=	$_POST['insilj'];		// ������ ��ǰ��
$insilj_f	=	$_POST['insilj_f'];		// �˾����½� ���� ��ǰ��
$seq		=	$_POST['seq'];
$jsyymm		=	str_replace('-','',$_POST['jsyymm']);
$jeyymm		=	str_replace('-','',$_POST['jeyymm']);
$mjiyul		=	$_POST['mjiyul'];
$ujiyul		=	$_POST['ujiyul'];
$jjiyul		=	$_POST['jjiyul'];



//----------------------------------------------------------//
//                    ������ ó������							// 
//----------------------------------------------------------//
if($_POST['type']=='up'){

	// �ʼ����� Ȯ��
	if($_SESSION['S_SCODE'] == null || $skey == null ){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '�ʼ��Է°� ����, ��α������ּ���.';
		echo "<script>alert('$message');history.go('-1');</script>";
		exit;	
	}

	// Ʈ������ ����
    sqlsrv_query("BEGIN TRAN");

	// ������ۿ��� ����������� ������ �����ϴ� ���ڿ� ��ĥ�� ���� (�����϶� ������ �����ϰ� ��ȸ)
	$sql	= "
			select count(*) jcnt
			from sjiyul a
			where scode = '".$_SESSION['S_SCODE']."' and skey = '".$skey."' and insilj = '".$insilj."' 
					and ( jsyymm between '".$jsyymm."' and '".$jeyymm."'  or jeyymm between '".$jsyymm."' and '".$jeyymm."') and
						not exists(
							select *
							from sjiyul
							where a.scode = sjiyul.scode and a.skey = sjiyul.skey and a.insilj = sjiyul.insilj and a.seq = sjiyul.seq and
								scode = '".$_SESSION['S_SCODE']."' and skey = '".$skey."' and insilj = '".$insilj_f."' and seq = ".$seq."
							)	
				";
	$result  = sqlsrv_query( $mscon, $sql );
	$row =  sqlsrv_fetch_array($result); 
	$jcnt = $row["jcnt"];

	if($jcnt > 0){
        $message = '���� '.$skey.'����� '.$conf['insilj'][$insilj].'��ǰ�� ������ۿ��̳� ����������� ��Ĩ�ϴ�.';
		echo "<script>alert('$message');history.go('-1');</script>";
		exit;			
	}


	$maxseq=$seq;
	// ��ǰ���� ���������� �������ε���
	if($insilj != $insilj_f){
		$sql	= "
					select isnull(max(seq),0)+1 maxseq
					from sjiyul
					where scode = '".$_SESSION['S_SCODE']."' and skey = '".$skey."' and insilj = '".$insilj."'
					";
		$result  = sqlsrv_query( $mscon, $sql );
		$row =  sqlsrv_fetch_array($result); 
		$maxseq = $row["maxseq"];
	}

	$sql = "
			update sjiyul
			set insilj = '".$insilj."' , jsyymm = '".$jsyymm."' , jeyymm = '".$jeyymm."' , seq = ".$maxseq." ,
				mjiyul = ".$mjiyul." , ujiyul = ".$ujiyul." , jjiyul = ".$jjiyul." ,
				uswon = '".$_SESSION['S_SKEY']."' , udate = getdate()
			where scode = '".$_SESSION['S_SCODE']."' and skey = '".$skey."' and insilj = '".$insilj_f."' and seq = ".$seq."
			";

	
	$result =  sqlsrv_query( $mscon, $sql );

	if ($result == false){
		sqlsrv_query("ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' ��� ������ ���� �� �����߻� #1';
		echo "<script>alert('$message');history.go('-1');</script>";
		exit;
	}


    sqlsrv_query("COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' ��� �������� �����Ͽ����ϴ�.';
	echo "<script>alert('$message'); location.href='./ga_menu5_04_pop.php?skey=$skey&insilj=$insilj&seq=$maxseq' ;  </script>";
	exit;

}

//----------------------------------------------------------//
//                    ��Ͻ� ó������							// 
//----------------------------------------------------------//
if($_POST['type']=='in'){

	// �ʼ����� Ȯ��
	if($_SESSION['S_SCODE'] == null || $skey == null ){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '�ʼ��Է°� ����, ��α������ּ���.';
		echo "<script>alert('$message');history.go('-1');</script>";
		exit;	
	}	

	// Ʈ������ ����
    sqlsrv_query("BEGIN TRAN");

	// ������ۿ��� ����������� ������ �����ϴ� ���ڿ� ��ĥ�� ����
	$sql	= "
			select count(*) jcnt
			from sjiyul
			where scode = '".$_SESSION['S_SCODE']."' and skey = '".$skey."' and insilj = '".$insilj."' 
					and ( jsyymm between '".$jsyymm."' and '".$jeyymm."'  or jeyymm between '".$jsyymm."' and '".$jeyymm."')
				";
	$result  = sqlsrv_query( $mscon, $sql );
	$row =  sqlsrv_fetch_array($result); 
	$jcnt = $row["jcnt"];

	if($jcnt > 0){
        $message = '���� '.$skey.'����� ������ۿ��̳� ����������� ��Ĩ�ϴ�.';
		echo "<script>alert('$message');history.go('-1');</script>";
		exit;			
	}

	// ��������
	$sql	= "
				select isnull(max(seq),0)+1 maxseq
				from sjiyul
				where scode = '".$_SESSION['S_SCODE']."' and skey = '".$skey."' and insilj = '".$insilj."'
				";
	$result  = sqlsrv_query( $mscon, $sql );
	$row =  sqlsrv_fetch_array($result); 
	$seq = $row["maxseq"];

	$sql = "
			insert into sjiyul(scode,skey,insilj,seq,jsyymm,jeyymm,mjiyul,ujiyul,jjiyul,idate,iswon)
			values('".$_SESSION['S_SCODE']."','".$skey."','".$insilj."','".$seq."','".$jsyymm."','".$jeyymm."','".$mjiyul."','".$ujiyul."','".$jjiyul."',getdate(),'".$_SESSION['S_SKEY']."')
			";

	$result =  sqlsrv_query( $mscon, $sql );

	if ($result == false){
		sqlsrv_query("ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' ��� ������ ��� �� �����߻� #1';
		echo "<script>alert('$message');history.go('-1');</script>";
		exit;
	}


    sqlsrv_query("COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' ��� �������� ����Ͽ����ϴ�.';
	echo "<script>alert('$message'); location.href='./ga_menu5_04_pop.php?skey=$skey&insilj=$insilj&seq=$seq' ;  </script>";
	exit;

}

//----------------------------------------------------------//
//                    ������ ó������							// 
//----------------------------------------------------------//
if($_POST['type']=='del'){

	// �ʼ����� Ȯ��
	if($_SESSION['S_SCODE'] == null || $skey == null ){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '�ʼ��Է°� ����, ��α������ּ���.';
		echo "<script>alert('$message');history.go('-1');</script>";
		exit;	
	}	

	// Ʈ������ ����
    sqlsrv_query("BEGIN TRAN");
	$sql = "
			delete from sjiyul where scode = '".$_SESSION['S_SCODE']."' and skey = '".$skey."' and insilj = '".$insilj_f."' and seq = '".$seq."'
			";

	$result =  sqlsrv_query( $mscon, $sql );

	echo $sql;

	if ($result == false){
		sqlsrv_query("ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' ��� ������ ���� �� �����߻� #1';
		echo "<script>alert('$message');history.go('-1');</script>";
		exit;
	}


    sqlsrv_query("COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' ��� �������� �����Ͽ����ϴ�.';
	echo "<script>alert('$message'); location.href='./ga_menu5_04_pop.php' ;  </script>";
	exit;


}


?>