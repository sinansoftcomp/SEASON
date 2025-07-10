<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

// 수당규정. 추후에 COMPANY테이블에서 가져올것.
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

$set = stripslashes($set); // 위에 붙은 역슬래시 제거

//----------------------------------------------------------//
//                    수정시 처리요직							// 
//----------------------------------------------------------//
if($type=='up'){

	// 필수정보 확인
	if($_SESSION['S_SCODE'] == null or $_POST['skey'] == null or $_POST['inscode'] == null or $_POST['insilj'] == null or $_POST['seq'] == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '세센연결 error 필수입력값 오류, 재 로그인해주세요.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}

	$skey = $_POST['skey'];
	$inscode = $_POST['inscode'];
	$insilj = $_POST['insilj'];
	$seq = $_POST['seq'];

	// 트렌젝션 시작
    sqlsrv_query($mscon,"BEGIN TRAN");
	
	// ------------------예외처리 시작-----------------//
	// 적용시작일자와 적용종료일자가 기존에 존재하는 일자와 겹칠때 리턴
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
			$message = '기존 '.$skey.'사원의 적용시작월이나 적용종료월이 겹칩니다!';
			$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
			echo json_encode($returnJson);
			exit;			
		}
	}
	// ------------------예외처리 종료-----------------//

	$sql = "update ".$X."_sjirule 
			set ".$set." jsyymm = '".$jsyymm."' , jeyymm = '".$jeyymm."'
			where scode = '".$_SESSION['S_SCODE']."' and skey = '".$_POST['skey']."' and inscode = '".$_POST['inscode']."' and insilj = '".$_POST['insilj']."' and seq = ".$_POST['seq']." ";

	$result =  sqlsrv_query( $mscon, $sql );
	
	if ($result == false){
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' 사원별 지급률수정 중 오류';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
	}


    sqlsrv_query($mscon,"COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' 사원의 지급률을 수정하였습니다.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "rtype" => "up");
	echo json_encode($returnJson);
	exit;
}

//----------------------------------------------------------//
//                    입력시 처리요직							// 
//----------------------------------------------------------//
if($type=='in'){
	// 필수정보 확인
	if($_SESSION['S_SCODE'] == null or $_POST['skey_s'] == null or $_POST['inscode_s'] == null or $_POST['insilj_s'] == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '세센연결 error 필수입력값 오류, 재 로그인해주세요.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}

	$skey = $_POST['skey_s'];
	$inscode = $_POST['inscode_s'];
	$insilj = $_POST['insilj_s'];
	
	// 트렌젝션 시작
    sqlsrv_query($mscon, "BEGIN TRAN");

	// ------------------예외처리 시작-----------------//
	// 적용시작일자와 적용종료일자가 기존에 존재하는 일자와 겹칠때 리턴
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
			$message = '기존 '.$skey.'사원의 적용시작일자나 적용종료일자가 겹칩니다.';
			$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
			echo json_encode($returnJson);
			exit;			
		}
	}
	// ------------------예외처리 종료-----------------//

	// 순번(SEQ)구하기 //
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
		$message = ' 사원별 지급률등록 중 오류';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
	}

    sqlsrv_query($mscon, "COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' 사원의 지급률을 등록하였습니다.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "skey"=>"$skey" , "inscode"=>"$inscode" , "insilj"=>"$insilj" , "seq"=>"$seq" , "rtype" => "in");
	echo json_encode($returnJson);
	exit;

}


if($type == "del"){

	// 필수정보 확인
	if($_SESSION['S_SCODE'] == null or $_POST['skey'] == null or $_POST['inscode'] == null or $_POST['insilj'] == null or $_POST['seq'] == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '세센연결 error 필수입력값 오류, 재 로그인해주세요.';
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

	// 트렌젝션 시작
    sqlsrv_query($mscon, "BEGIN TRAN");
    $result =  sqlsrv_query( $mscon, $sql );

    if ($result == false){
		sqlsrv_query($mscon, "ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' 사원별 지급률삭제 중 오류';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
    }

    sqlsrv_query($mscon, "COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' 사원별 지급률을 삭제하였습니다.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "rtype" => "del");
	echo json_encode($returnJson);
	exit;
}

?>