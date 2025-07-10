<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

extract($_POST);

$skey		=	$_POST['skey'];
$insilj		=	$_POST['insilj'];		// 수정된 상품군
$insilj_f	=	$_POST['insilj_f'];		// 팝업오픈시 최초 상품군
$seq		=	$_POST['seq'];
$jsyymm		=	str_replace('-','',$_POST['jsyymm']);
$jeyymm		=	str_replace('-','',$_POST['jeyymm']);
$mjiyul		=	$_POST['mjiyul'];
$ujiyul		=	$_POST['ujiyul'];
$jjiyul		=	$_POST['jjiyul'];



//----------------------------------------------------------//
//                    수정시 처리요직							// 
//----------------------------------------------------------//
if($_POST['type']=='up'){

	// 필수정보 확인
	if($_SESSION['S_SCODE'] == null || $skey == null ){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '필수입력값 오류, 재로그인해주세요.';
		echo "<script>alert('$message');history.go('-1');</script>";
		exit;	
	}

	// 트렌젝션 시작
    sqlsrv_query("BEGIN TRAN");

	// 적용시작월과 적용종료월이 기존에 존재하는 일자와 겹칠때 리턴 (수정일땐 본인은 제외하고 조회)
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
        $message = '기존 '.$skey.'사원의 '.$conf['insilj'][$insilj].'상품군 적용시작월이나 적용종료월이 겹칩니다.';
		echo "<script>alert('$message');history.go('-1');</script>";
		exit;			
	}


	$maxseq=$seq;
	// 상품군을 변경했을때 순번새로따기
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
		$message = ' 사원 지급율 수정 중 오류발생 #1';
		echo "<script>alert('$message');history.go('-1');</script>";
		exit;
	}


    sqlsrv_query("COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' 사원 지급율을 수정하였습니다.';
	echo "<script>alert('$message'); location.href='./ga_menu5_04_pop.php?skey=$skey&insilj=$insilj&seq=$maxseq' ;  </script>";
	exit;

}

//----------------------------------------------------------//
//                    등록시 처리요직							// 
//----------------------------------------------------------//
if($_POST['type']=='in'){

	// 필수정보 확인
	if($_SESSION['S_SCODE'] == null || $skey == null ){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '필수입력값 오류, 재로그인해주세요.';
		echo "<script>alert('$message');history.go('-1');</script>";
		exit;	
	}	

	// 트렌젝션 시작
    sqlsrv_query("BEGIN TRAN");

	// 적용시작월과 적용종료월이 기존에 존재하는 일자와 겹칠때 리턴
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
        $message = '기존 '.$skey.'사원의 적용시작월이나 적용종료월이 겹칩니다.';
		echo "<script>alert('$message');history.go('-1');</script>";
		exit;			
	}

	// 순번따기
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
		$message = ' 사원 지급율 등록 중 오류발생 #1';
		echo "<script>alert('$message');history.go('-1');</script>";
		exit;
	}


    sqlsrv_query("COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' 사원 지급율을 등록하였습니다.';
	echo "<script>alert('$message'); location.href='./ga_menu5_04_pop.php?skey=$skey&insilj=$insilj&seq=$seq' ;  </script>";
	exit;

}

//----------------------------------------------------------//
//                    삭제시 처리요직							// 
//----------------------------------------------------------//
if($_POST['type']=='del'){

	// 필수정보 확인
	if($_SESSION['S_SCODE'] == null || $skey == null ){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '필수입력값 오류, 재로그인해주세요.';
		echo "<script>alert('$message');history.go('-1');</script>";
		exit;	
	}	

	// 트렌젝션 시작
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
		$message = ' 사원 지급율 삭제 중 오류발생 #1';
		echo "<script>alert('$message');history.go('-1');</script>";
		exit;
	}


    sqlsrv_query("COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' 사원 지급율을 삭제하였습니다.';
	echo "<script>alert('$message'); location.href='./ga_menu5_04_pop.php' ;  </script>";
	exit;


}


?>