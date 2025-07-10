<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

function codeView($var,$flag = false) {
		ob_start();
		print_r($var);
		$str = ob_get_contents();
		ob_end_clean();
		echo "<xmp style='font-family:tahoma, 굴림; font-size:12px;'>$str</xmp>";
		if($flag == true) exit;
}

$carseq		=	$_POST['carseq'];	// A:요율조회, B:비교견적산출 C:고객저장
/* --------------------------------------------------------------------------
	요율필수정보
 --------------------------------------------------------------------------*/
$savegubun	=	$_POST['savegubun'];	// A:요율조회, B:비교견적산출 C:고객저장

$caruse		=	$_POST['caruse'];
$pname		=	iconv("UTF-8","EUCKR",$_POST['pname']);
$jumin		=	$_POST['jumin'];
$carnumber	=	iconv("UTF-8","EUCKR",$_POST['carnumber']);
$fdate		=	str_replace("-","",iconv("UTF-8","EUCKR",$_POST['idate']));
$tdate		=	str_replace("-","",iconv("UTF-8","EUCKR",$_POST['idate_to']));
$kdman		=	$_POST['kdman'];


/* --------------------------------------------------------------------------
	요율관련 사항
 --------------------------------------------------------------------------*/
$guipcarrer = $_POST['guipcarrer'];			// 가입경력
$traffic = $_POST['traffic'];				// 법규위반
$lawcodecnt = $_POST['lawcodecnt'];			// 법규위반횟수
$halin = $_POST['halin'];					// 할인할증
$special_code = $_POST['special_code'];		// 특별할증1
$special_code1 = $_POST['special_code1'];	// 특별할증2
$ncr_code = $_POST['ncr_code'];				// 3년간사고요율
$ncr_code2 = $_POST['ncr_code2'];			// 3년간사고요율2
$ss_point = $_POST['ss_point'];				// 1년간사고점수
$ss_point3 = $_POST['ss_point3'];			// 3년간사고점수
$car_guip = $_POST['car_guip'];				// 차량가입경력
$car_own = $_POST['car_own'];				// 그외보유차량
$careercode3 = $_POST['careercode3'];		// 직전3년가입경력
$otheracc = $_POST['otheracc'];				// 그외사고여부

/* --------------------------------------------------------------------------
	차량관련 사항
 --------------------------------------------------------------------------*/
$carcode = iconv("UTF-8","EUCKR",$_POST['carcode']);				// 차명코드
$cargrade = $_POST['cargrade'];				// 차량등급
$baegicc = $_POST['baegicc'];				// 배기량
$people_numcc = $_POST['people_numcc'];		// 톤 또는 탑승자수
$caryear = iconv("UTF-8","EUCKR",$_POST['caryear']);				// 연식
$cardate = str_replace("-" , "" , $_POST['cardate'] );	// 차량등록일
$car_kind = $_POST['car_kind'];				// 차종
$carname = iconv("UTF-8","EUCKR",$_POST['carname']);						// 차명
$ext_bupum_txt = iconv("UTF-8","EUCKR",$_POST['ext_bupum_txt']);			// 특별요율txt
$ext_bupum = iconv("UTF-8","EUCKR",$_POST['ext_bupum']);					// 특별요율api
$add_bupum_txt = iconv("UTF-8","EUCKR",$_POST['add_bupum_txt']);			// 추가부속품txt
$add_bupum = iconv("UTF-8","EUCKR",$_POST['add_bupum']);					// 추가부속품
$add_bupum_amt = $_POST['add_bupum_amt'];	// 추가부속품amt
$carprice1 = str_replace(",","",$_POST['carprice1']); //차량가액
$addamt = str_replace(",","",$_POST['addamt']);					// 부속품가액
$carprice = str_replace(",","",$_POST['carprice']); //일부가액
$fuel = $_POST['fuel'];						// 연료형태
$hi_repair = $_POST['hi_repair'];			// 고가수리비
$buy_type = $_POST['buy_type'];				// 구입형태

/* --------------------------------------------------------------------------
	기타 사항
 --------------------------------------------------------------------------*/
$ijumin = $_POST['ijumin'];					// 자녀할인생일
$fetus = $_POST['fetus'];					// 태아여부

if($_POST['icnt']){
	$icnt = $_POST['icnt'];						// 자녀수 (numberic)
}else{
	$icnt = 0;
}

$tmap_halin = $_POST['tmap_halin'];			// 티맵운전할인
$eco_mileage = $_POST['eco_mileage'];		// 주행거리선할인
$car_own_halin = $_POST['car_own_halin'];	// 다수차량할인특약
$religionchk = $_POST['religionchk'];		// 종교단체특약
$jjumin = $_POST['jjumin'];					// 지정1인번호
$lowestjumin = $_POST['lowestjumin'];		// 최저운전자번호
$c_jumin = $_POST['c_jumin'];				// 배우자번호
$j_name = iconv("UTF-8","EUCKR",$_POST['j_name']);	// 지정1인명
$l_name = iconv("UTF-8","EUCKR",$_POST['l_name']);	// 최저운전자명
$c_name = iconv("UTF-8","EUCKR",$_POST['c_name']);	// 배우자명

/* --------------------------------------------------------------------------
	담보관련 사항
 --------------------------------------------------------------------------*/
$carage = $_POST['carage'];					// 연령한정
$carfamily = $_POST['carfamily'];			// 운전자한정
$dambo2 = $_POST['dambo2'];					// 대인II
$dambo3 = $_POST['dambo3'];					// 대물배상
$dambo4 = $_POST['dambo4'];					// 신체상해
$dambo5 = $_POST['dambo5'];					// 무보험차
$dambo6 = $_POST['dambo6'];					// 자차손해
$goout1 = $_POST['goout1'];					// 긴급출동
$muljuk = $_POST['muljuk'];					// 물적할증
$milegbn = $_POST['MileGbn'];				// 마일리지
$milekm = $_POST['MileKm'];					// 연간주행
//$nowkm = str_replace(",","",$_POST['nowkm']);			// 현재주행 (numeric)

if($_POST['nowkm']){
	$nowkm = str_replace(",","",$_POST['nowkm']);			// 현재주행 (numeric)
}else{
	$nowkm = 0;
}

$devide_num = $_POST['devide_num'];			// 납입방법

/* --------------------------------------------------------------------------
	상담관련 사항
 --------------------------------------------------------------------------*/
$chtel1		=	$_POST['chtel1'];
$chtel2		=	$_POST['chtel2'];
$chtel3		=	$_POST['chtel3'];
$chtel		=	$chtel1.$chtel2.$chtel3;

$rbit = $_POST['rbit'];						// 설계상태
$upmu = iconv("UTF-8","EUCKR",$_POST['upmu']);	// 업무
$cnum = iconv("UTF-8","EUCKR",$_POST['cnum']);	// 설계번호
$reday = $_POST['reday'];					// 재상담예약 일
$rehour = $_POST['rehour'];					// 재상담예약 시
$selins = $_POST['selins'];					// 선택회사
$inyn = $_POST['inyn'];						// 사전동의
$indt = str_replace("-","",$_POST['indt']); // 동의일자
$agno = iconv("UTF-8","EUCKR",$_POST['agno']); // 동의번호
$bigo = iconv("UTF-8","EUCKR",$_POST['bigo']); // 비고




/*
	특별요율 중복제거

	중복이 생기는 이유
	1. 요율계산시 커넥티드카 / 차선이탈방지 / 전방충돌방지장치 / UBI할인 / 블랙박스(내장형) 데이터 받음
	2. 차량선택시 차량에 따라 / 차량등록일에 따라 특별요율 데이터 등록

	데이터 중복으로 action페이지 넘기고 여기서 중복제거 처리
*/			   
if($ext_bupum){

	// 배열로 변환
	$ext_arr		= explode('-', $ext_bupum);
	$ext_txt_arr	= explode(',', $ext_bupum_txt);

	// 중복제거
	$extdata		= array_unique($ext_arr);
	$exttxtdata		= array_unique($ext_txt_arr);

	// 배열 인덱스 재정리(array_unique 사용 후 배열인덱스가 비어있게 됨)
	$extdata		= array_values($extdata);
	$exttxtdata		= array_values($exttxtdata);


	// 중복제거 후 배열수 체크
	$arrcnt			= count($extdata);
	$arrcnt2		= count($exttxtdata);

	$dash	=	'';
	$comma	=	'';
	$carext =	'';
	$cartxt	=	'';
	for($i=0; $i<$arrcnt; $i++){

		if($i>0){
			$dash	=	'-';
		}

		$carext	=	$carext.$dash.$extdata[$i];
	}

	for($i=0; $i<$arrcnt2; $i++){

		if($i>0){
			$comma	=	',';
		}

		$cartxt	=	$cartxt.$comma.$exttxtdata[$i];
	}

}


//----------------------------------------------------------//
//                    수정시 처리요직							// 
//----------------------------------------------------------//
if($_POST['type']=='up'){

	// 필수정보 확인
	if($_SESSION['S_SCODE'] == null || $carseq == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '필수입력값 오류, 재로그인해주세요.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;		
	}


	// 트렌젝션 시작
    sqlsrv_query($mscon, "BEGIN TRAN");

	$sql="update carest
		  set 
				caruse		=		'$caruse',
				pname		=		'$pname',
				jumin		=		dbo.ENCRYPTKEY('".$jumin."'), 
				carnumber	=		'$carnumber',
				fdate		=		'$fdate',
				tdate		=		'$tdate',
				kdman		=		'$kdman',
				chtel		=		'$chtel',

				guipcarrer	=		'$guipcarrer',
				traffic		=		'$traffic',
				lawcodecnt	=		'$lawcodecnt',
				halin		=		'$halin',
				special_code=		'$special_code',
				special_code1	=	'$special_code1',
				ncr_code	=		'$ncr_code',
				ncr_code2	=		'$ncr_code2',
				ss_point	=		'$ss_point',
				ss_point3	=		'$ss_point3',
				car_guip	=		'$car_guip',
				car_own		=		'$car_own',
				careercode3	=		'$careercode3',
				otheracc	=		'$otheracc',

				carcode		=		'$carcode',
				cargrade	=		'$cargrade',
				baegicc		=		'$baegicc',
				people_numcc=		'$people_numcc',
				caryear		=		'$caryear',
				cardate		=		'$cardate',
				car_kind	=		'$car_kind',
				carname		=		'$carname',
				ext_bupum_txt=		'$cartxt',
				ext_bupum	=		'$carext',
				add_bupum_txt=		'$add_bupum_txt',
				add_bupum	=		'$add_bupum',
				add_bupum_amt=		'$add_bupum_amt',
				carprice1	=		'$carprice1',
				carprice	=		'$carprice',
				fuel		=		'$fuel',
				hi_repair	=		'$hi_repair',
				buy_type	=		'$buy_type',

				ijumin		=		'$ijumin',
				fetus		=		'$fetus',
				icnt		=		'$icnt',
				tmap_halin	=		'$tmap_halin',
				eco_mileage	=		'$eco_mileage',
				car_own_halin=		'$car_own_halin',
				religionchk	=		'$religionchk',
				jjumin		=		'$jjumin',
				lowestjumin	=		'$lowestjumin',
				c_jumin		=		'$c_jumin',
				j_name		=		'$j_name',
				l_name		=		'$l_name',
				c_name		=		'$c_name',

				carage		=		'$carage',
				carfamily	=		'$carfamily',
				dambo2		=		'$dambo2',
				dambo3		=		'$dambo3',
				dambo4		=		'$dambo4',
				dambo5		=		'$dambo5',
				dambo6		=		'$dambo6',
				goout		=		'$goout1',
				muljuk		=		'$muljuk',
				milegbn		=		'$milegbn',
				milekm		=		'$milekm',
				nowkm		=		'$nowkm',
				devide_num	=		'$devide_num',

				rbit		=		'$rbit',
				upmu		=		'$upmu',
				cnum		=		'$cnum',
				reday		=		'$reday',
				rehour		=		'$rehour',
				selins		=		'$selins',
				inyn		=		'$inyn',
				indt		=		'$indt',
				agno		=		'$agno',
				bigo		=		'$bigo'
		where scode = '".$_SESSION['S_SCODE']."'
		  and carseq = '".$carseq."' ";
		/*
		  echo '<pre>';
		  echo $sql;
		  echo '</pre>';
		*/

		$result =  sqlsrv_query( $mscon, $sql );
		//echo $sql;
		if ($result == false){
			sqlsrv_query($mscon, "ROLLBACK");
			sqlsrv_free_stmt($result);
			sqlsrv_close($mscon);
			$message = ' 비교견적 고객정보 수정 중 오류발생 #1';
			$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
			echo json_encode($returnJson);
			exit;
		}

		// 추가부속품이 존재할 경우
		if($add_bupum){
			// 기존 추가부속품 삭제 후 신규 insert
			$sql = "delete from carestadd where scode = '".$_SESSION['S_SCODE']."' and carseq = '".$carseq."' ";

			$result =  sqlsrv_query( $mscon, $sql );

			if ($result == false){
				sqlsrv_query($mscon, "ROLLBACK");
				sqlsrv_free_stmt($result);
				sqlsrv_close($mscon);
				$message = ' 비교견적 고객정보 수정 중 오류발생 #2';
				$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
				echo json_encode($returnJson);
				exit;
			}

			$add_arr	= explode('-', $add_bupum);
			$amt_arr	= explode('-', $add_bupum_amt);
			$arrcnt		= count($add_arr);

			$cnt = 0;
			for($i=0; $i<$arrcnt; $i++){
				$cnt = $i+1;

				$sql = "insert into carestadd(scode, carseq, cnt, code, amt) 
						values('".$_SESSION['S_SCODE']."', '".$carseq."',	".$cnt.", '".$add_arr[$i]."', '".$amt_arr[$i]."' )";

				$result =  sqlsrv_query( $mscon, $sql );
			}

			if ($result == false){
				sqlsrv_query($mscon, "ROLLBACK");
				sqlsrv_free_stmt($result);
				sqlsrv_close($mscon);
				$message = ' 비교견적 고객정보 수정 중 오류발생 #3';
				$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
				echo json_encode($returnJson);
				exit;
			}
		}

		sqlsrv_query($mscon, "COMMIT");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);

		$message = ' 비교견적 고객 정보를 수정 하였습니다.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "carseq" => $carseq, "rtype" => "up", "savegubun" => $savegubun);
		echo json_encode($returnJson);
		exit;

}

//----------------------------------------------------------//
//                    입력시 처리요직							// 
//----------------------------------------------------------//
if($_POST['type']=='in'){

	// 필수정보 확인
	if($_SESSION['S_SCODE'] == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '필수입력값 오류, 재로그인해주세요.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;		
	}

	// 고객번호 생성(C + '해당년도 뒤2자리' + '해당월/일' + 시퀀스 4자리 / C2401010001 - 24년도 1월 1일 최초 등록사원예시)
	$sql  = "select substring(max(carseq),7,6) maxcode
			 from carest 
			 where scode = '".$_SESSION['S_SCODE']."' 
			   and substring(carseq,1,6) = substring(convert(varchar(8),getdate(),112),3,6) ";
	$result  = sqlsrv_query( $mscon, $sql );
	$row =  sqlsrv_fetch_array($result); 

	if($row['maxcode']){
		$seq_in = date("y").date("md").str_pad($row['maxcode'] + 1, 6 ,"0" ,STR_PAD_LEFT);
	}else{
		$seq_in = date("y").date("md").'000001';
	}

	// 트렌젝션 시작
    sqlsrv_query($mscon, "BEGIN TRAN");

	
	$sql="insert into carest (scode,		carseq,			caruse,			pname,			jumin,	chtel,
							  carnumber,	fdate,			tdate,			kdman,			idate,	iswon,
							  guipcarrer,traffic,lawcodecnt,halin,special_code,special_code1,ncr_code,ncr_code2,
							  ss_point,ss_point3,car_guip,car_own,careercode3,otheracc,
							  
							  carcode,cargrade,baegicc,people_numcc,caryear,cardate,car_kind,carname,ext_bupum_txt,ext_bupum,
							  add_bupum, add_bupum_txt, add_bupum_amt, carprice1,carprice,fuel,hi_repair,buy_type,
							  
							  ijumin,fetus,icnt,tmap_halin,eco_mileage,car_own_halin,religionchk,jjumin,
							  lowestjumin,c_jumin,j_name,l_name,c_name,
							  
							  carage,carfamily,dambo2,dambo3,dambo4,dambo5,dambo6,goout,
							  muljuk,milegbn,milekm,nowkm,devide_num,
							  
							  rbit,upmu,cnum,reday,rehour,selins,inyn,indt,agno,bigo)
					  values ('".$_SESSION['S_SCODE']."',	 '$seq_in',		'$caruse',		'$pname',		dbo.ENCRYPTKEY('".$jumin."'),	'$chtel',
					  		  '$carnumber',	'$fdate',		'$tdate',		'$kdman',		getdate(),		'".$_SESSION['S_SKEY']."',
  							  '$guipcarrer','$traffic','$lawcodecnt','$halin','$special_code','$special_code1','$ncr_code','$ncr_code2',
							  '$ss_point','$ss_point3','$car_guip','$car_own','$careercode3','$otheracc',
							  
							  '$carcode','$cargrade','$baegicc','$people_numcc','$caryear','$cardate','$car_kind','$carname','$cartxt','$carext',
							  '$add_bupum', '$add_bupum_txt', '$add_bupum_amt', '$carprice1','$carprice','$fuel','$hi_repair','$buy_type',
							  
							  '$ijumin','$fetus',$icnt,'$tmap_halin','$eco_mileage','$car_own_halin','$religionchk','$jjumin',
							  '$lowestjumin','$c_jumin','$j_name','$l_name','$c_name',
							  
							  '$carage','$carfamily','$dambo2','$dambo3','$dambo4','$dambo5','$dambo6','$goout1',
							  '$muljuk','$milegbn','$milekm',$nowkm,'$devide_num',
							  
							  '$rbit','$upmu','$cnum','$reday','$rehour','$selins','$inyn','$indt','$agno','$bigo')  ";
	

    $result =  sqlsrv_query( $mscon, $sql );
	//echo $sql;
    if ($result == false){
		sqlsrv_query($mscon, "ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = ' 비교견적 고객정보 등록 중 오류발생 #1';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
    }


	// 추가부속품 데이터가 존재할 경우
	if($add_bupum){
		// 기존 추가부속품 삭제 후 신규 insert
		$sql = "delete from carestadd where scode = '".$_SESSION['S_SCODE']."' and carseq = '".$carseq."' ";

		$result =  sqlsrv_query( $mscon, $sql );

		if ($result == false){
			sqlsrv_query($mscon, "ROLLBACK");
			sqlsrv_free_stmt($result);
			sqlsrv_close($mscon);
			$message = ' 비교견적 고객정보 수정 중 오류발생 #2';
			$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
			echo json_encode($returnJson);
			exit;
		}

		$add_arr	= explode('-', $add_bupum);
		$amt_arr	= explode('-', $add_bupum_amt);
		$arrcnt		= count($add_arr);

		$cnt = 0;
		for($i=0; $i<$arrcnt; $i++){
			$cnt = $i+1;

			$sql = "insert into carestadd(scode, carseq, cnt, code, amt) 
					values('".$_SESSION['S_SCODE']."', '".$carseq."',	".$cnt.", '".$add_arr[$i]."', '".$amt_arr[$i]."' )";

			$result =  sqlsrv_query( $mscon, $sql );
		}

		if ($result == false){
			sqlsrv_query($mscon, "ROLLBACK");
			sqlsrv_free_stmt($result);
			sqlsrv_close($mscon);
			$message = ' 비교견적 고객정보 수정 중 오류발생 #3';
			$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
			echo json_encode($returnJson);
			exit;
		}
	}


    sqlsrv_query($mscon, "COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' 비교견적 고객 정보를 등록 하였습니다.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "carseq" => $seq_in, "rtype" => "in", "savegubun" => $savegubun);
	echo json_encode($returnJson);
	exit;

}

//----------------------------------------------------------//
//                    삭제시 처리요직							// 
//----------------------------------------------------------//
if($_POST[type]=='del'){

	// 필수정보 확인
	if($_SESSION['S_SCODE'] == null || $carseq == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '필수입력값 오류, 재로그인해주세요.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;		
	}


	// 트렌젝션 시작
    sqlsrv_query($mscon, "BEGIN TRAN");
	
	// 비교견적 고객정보 삭제 1
	$sql="delete from carest where scode = '".$_SESSION['S_SCODE']."' and carseq = '".$carseq."' ";
	
    $result =  sqlsrv_query( $mscon, $sql );
    if ($result == false){
		sqlsrv_query($mscon, "ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = ' 비교견적 고객정보 삭제 중 오류발생 #1';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
    }

	// 추가부속품 삭제 2 
	$sql="delete from carestadd where scode = '".$_SESSION['S_SCODE']."' and carseq = '".$carseq."' ";
	
    $result =  sqlsrv_query( $mscon, $sql );
    if ($result == false){
		sqlsrv_query($mscon, "ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = ' 비교견적 고객정보 삭제 중 오류발생 #2';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
    }


	// 비교견적 산출정보 삭제 3 
	$sql="delete from carestamt where scode = '".$_SESSION['S_SCODE']."' and carseq = '".$carseq."' ";
	
    $result =  sqlsrv_query( $mscon, $sql );
    if ($result == false){
		sqlsrv_query($mscon, "ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = ' 비교견적 고객정보 삭제 중 오류발생 #3';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
    }


    sqlsrv_query($mscon, "COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' 비교견적 고객 정보를 삭제 하였습니다.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "carseq" => $carseq, "rtype" => "del");
	echo json_encode($returnJson);
	exit;

}

?>