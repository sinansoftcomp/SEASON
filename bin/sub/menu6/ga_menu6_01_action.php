<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

function codeView($var,$flag = false) {
		ob_start();
		print_r($var);
		$str = ob_get_contents();
		ob_end_clean();
		echo "<xmp style='font-family:tahoma, ����; font-size:12px;'>$str</xmp>";
		if($flag == true) exit;
}

$carseq		=	$_POST['carseq'];	// A:������ȸ, B:�񱳰������� C:������
/* --------------------------------------------------------------------------
	�����ʼ�����
 --------------------------------------------------------------------------*/
$savegubun	=	$_POST['savegubun'];	// A:������ȸ, B:�񱳰������� C:������

$caruse		=	$_POST['caruse'];
$pname		=	iconv("UTF-8","EUCKR",$_POST['pname']);
$jumin		=	$_POST['jumin'];
$carnumber	=	iconv("UTF-8","EUCKR",$_POST['carnumber']);
$fdate		=	str_replace("-","",iconv("UTF-8","EUCKR",$_POST['idate']));
$tdate		=	str_replace("-","",iconv("UTF-8","EUCKR",$_POST['idate_to']));
$kdman		=	$_POST['kdman'];


/* --------------------------------------------------------------------------
	�������� ����
 --------------------------------------------------------------------------*/
$guipcarrer = $_POST['guipcarrer'];			// ���԰��
$traffic = $_POST['traffic'];				// ��������
$lawcodecnt = $_POST['lawcodecnt'];			// ��������Ƚ��
$halin = $_POST['halin'];					// ��������
$special_code = $_POST['special_code'];		// Ư������1
$special_code1 = $_POST['special_code1'];	// Ư������2
$ncr_code = $_POST['ncr_code'];				// 3�Ⱓ������
$ncr_code2 = $_POST['ncr_code2'];			// 3�Ⱓ������2
$ss_point = $_POST['ss_point'];				// 1�Ⱓ�������
$ss_point3 = $_POST['ss_point3'];			// 3�Ⱓ�������
$car_guip = $_POST['car_guip'];				// �������԰��
$car_own = $_POST['car_own'];				// �׿ܺ�������
$careercode3 = $_POST['careercode3'];		// ����3�Ⱑ�԰��
$otheracc = $_POST['otheracc'];				// �׿ܻ����

/* --------------------------------------------------------------------------
	�������� ����
 --------------------------------------------------------------------------*/
$carcode = iconv("UTF-8","EUCKR",$_POST['carcode']);				// �����ڵ�
$cargrade = $_POST['cargrade'];				// �������
$baegicc = $_POST['baegicc'];				// ��ⷮ
$people_numcc = $_POST['people_numcc'];		// �� �Ǵ� ž���ڼ�
$caryear = iconv("UTF-8","EUCKR",$_POST['caryear']);				// ����
$cardate = str_replace("-" , "" , $_POST['cardate'] );	// ���������
$car_kind = $_POST['car_kind'];				// ����
$carname = iconv("UTF-8","EUCKR",$_POST['carname']);						// ����
$ext_bupum_txt = iconv("UTF-8","EUCKR",$_POST['ext_bupum_txt']);			// Ư������txt
$ext_bupum = iconv("UTF-8","EUCKR",$_POST['ext_bupum']);					// Ư������api
$add_bupum_txt = iconv("UTF-8","EUCKR",$_POST['add_bupum_txt']);			// �߰��μ�ǰtxt
$add_bupum = iconv("UTF-8","EUCKR",$_POST['add_bupum']);					// �߰��μ�ǰ
$add_bupum_amt = $_POST['add_bupum_amt'];	// �߰��μ�ǰamt
$carprice1 = str_replace(",","",$_POST['carprice1']); //��������
$addamt = str_replace(",","",$_POST['addamt']);					// �μ�ǰ����
$carprice = str_replace(",","",$_POST['carprice']); //�Ϻΰ���
$fuel = $_POST['fuel'];						// ��������
$hi_repair = $_POST['hi_repair'];			// ��������
$buy_type = $_POST['buy_type'];				// ��������

/* --------------------------------------------------------------------------
	��Ÿ ����
 --------------------------------------------------------------------------*/
$ijumin = $_POST['ijumin'];					// �ڳ����λ���
$fetus = $_POST['fetus'];					// �¾ƿ���

if($_POST['icnt']){
	$icnt = $_POST['icnt'];						// �ڳ�� (numberic)
}else{
	$icnt = 0;
}

$tmap_halin = $_POST['tmap_halin'];			// Ƽ�ʿ�������
$eco_mileage = $_POST['eco_mileage'];		// ����Ÿ�������
$car_own_halin = $_POST['car_own_halin'];	// �ټ���������Ư��
$religionchk = $_POST['religionchk'];		// ������üƯ��
$jjumin = $_POST['jjumin'];					// ����1�ι�ȣ
$lowestjumin = $_POST['lowestjumin'];		// ���������ڹ�ȣ
$c_jumin = $_POST['c_jumin'];				// ����ڹ�ȣ
$j_name = iconv("UTF-8","EUCKR",$_POST['j_name']);	// ����1�θ�
$l_name = iconv("UTF-8","EUCKR",$_POST['l_name']);	// ���������ڸ�
$c_name = iconv("UTF-8","EUCKR",$_POST['c_name']);	// ����ڸ�

/* --------------------------------------------------------------------------
	�㺸���� ����
 --------------------------------------------------------------------------*/
$carage = $_POST['carage'];					// ��������
$carfamily = $_POST['carfamily'];			// ����������
$dambo2 = $_POST['dambo2'];					// ����II
$dambo3 = $_POST['dambo3'];					// �빰���
$dambo4 = $_POST['dambo4'];					// ��ü����
$dambo5 = $_POST['dambo5'];					// ��������
$dambo6 = $_POST['dambo6'];					// ��������
$goout1 = $_POST['goout1'];					// ����⵿
$muljuk = $_POST['muljuk'];					// ��������
$milegbn = $_POST['MileGbn'];				// ���ϸ���
$milekm = $_POST['MileKm'];					// ��������
//$nowkm = str_replace(",","",$_POST['nowkm']);			// �������� (numeric)

if($_POST['nowkm']){
	$nowkm = str_replace(",","",$_POST['nowkm']);			// �������� (numeric)
}else{
	$nowkm = 0;
}

$devide_num = $_POST['devide_num'];			// ���Թ��

/* --------------------------------------------------------------------------
	������ ����
 --------------------------------------------------------------------------*/
$chtel1		=	$_POST['chtel1'];
$chtel2		=	$_POST['chtel2'];
$chtel3		=	$_POST['chtel3'];
$chtel		=	$chtel1.$chtel2.$chtel3;

$rbit = $_POST['rbit'];						// �������
$upmu = iconv("UTF-8","EUCKR",$_POST['upmu']);	// ����
$cnum = iconv("UTF-8","EUCKR",$_POST['cnum']);	// �����ȣ
$reday = $_POST['reday'];					// ���㿹�� ��
$rehour = $_POST['rehour'];					// ���㿹�� ��
$selins = $_POST['selins'];					// ����ȸ��
$inyn = $_POST['inyn'];						// ��������
$indt = str_replace("-","",$_POST['indt']); // ��������
$agno = iconv("UTF-8","EUCKR",$_POST['agno']); // ���ǹ�ȣ
$bigo = iconv("UTF-8","EUCKR",$_POST['bigo']); // ���




/*
	Ư������ �ߺ�����

	�ߺ��� ����� ����
	1. �������� Ŀ��Ƽ��ī / ������Ż���� / �����浹������ġ / UBI���� / ���ڽ�(������) ������ ����
	2. �������ý� ������ ���� / ��������Ͽ� ���� Ư������ ������ ���

	������ �ߺ����� action������ �ѱ�� ���⼭ �ߺ����� ó��
*/			   
if($ext_bupum){

	// �迭�� ��ȯ
	$ext_arr		= explode('-', $ext_bupum);
	$ext_txt_arr	= explode(',', $ext_bupum_txt);

	// �ߺ�����
	$extdata		= array_unique($ext_arr);
	$exttxtdata		= array_unique($ext_txt_arr);

	// �迭 �ε��� ������(array_unique ��� �� �迭�ε����� ����ְ� ��)
	$extdata		= array_values($extdata);
	$exttxtdata		= array_values($exttxtdata);


	// �ߺ����� �� �迭�� üũ
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
//                    ������ ó������							// 
//----------------------------------------------------------//
if($_POST['type']=='up'){

	// �ʼ����� Ȯ��
	if($_SESSION['S_SCODE'] == null || $carseq == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '�ʼ��Է°� ����, ��α������ּ���.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;		
	}


	// Ʈ������ ����
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
			$message = ' �񱳰��� ������ ���� �� �����߻� #1';
			$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
			echo json_encode($returnJson);
			exit;
		}

		// �߰��μ�ǰ�� ������ ���
		if($add_bupum){
			// ���� �߰��μ�ǰ ���� �� �ű� insert
			$sql = "delete from carestadd where scode = '".$_SESSION['S_SCODE']."' and carseq = '".$carseq."' ";

			$result =  sqlsrv_query( $mscon, $sql );

			if ($result == false){
				sqlsrv_query($mscon, "ROLLBACK");
				sqlsrv_free_stmt($result);
				sqlsrv_close($mscon);
				$message = ' �񱳰��� ������ ���� �� �����߻� #2';
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
				$message = ' �񱳰��� ������ ���� �� �����߻� #3';
				$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
				echo json_encode($returnJson);
				exit;
			}
		}

		sqlsrv_query($mscon, "COMMIT");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);

		$message = ' �񱳰��� �� ������ ���� �Ͽ����ϴ�.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "carseq" => $carseq, "rtype" => "up", "savegubun" => $savegubun);
		echo json_encode($returnJson);
		exit;

}

//----------------------------------------------------------//
//                    �Է½� ó������							// 
//----------------------------------------------------------//
if($_POST['type']=='in'){

	// �ʼ����� Ȯ��
	if($_SESSION['S_SCODE'] == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '�ʼ��Է°� ����, ��α������ּ���.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;		
	}

	// ����ȣ ����(C + '�ش�⵵ ��2�ڸ�' + '�ش��/��' + ������ 4�ڸ� / C2401010001 - 24�⵵ 1�� 1�� ���� ��ϻ������)
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

	// Ʈ������ ����
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
        $message = ' �񱳰��� ������ ��� �� �����߻� #1';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
    }


	// �߰��μ�ǰ �����Ͱ� ������ ���
	if($add_bupum){
		// ���� �߰��μ�ǰ ���� �� �ű� insert
		$sql = "delete from carestadd where scode = '".$_SESSION['S_SCODE']."' and carseq = '".$carseq."' ";

		$result =  sqlsrv_query( $mscon, $sql );

		if ($result == false){
			sqlsrv_query($mscon, "ROLLBACK");
			sqlsrv_free_stmt($result);
			sqlsrv_close($mscon);
			$message = ' �񱳰��� ������ ���� �� �����߻� #2';
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
			$message = ' �񱳰��� ������ ���� �� �����߻� #3';
			$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
			echo json_encode($returnJson);
			exit;
		}
	}


    sqlsrv_query($mscon, "COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' �񱳰��� �� ������ ��� �Ͽ����ϴ�.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "carseq" => $seq_in, "rtype" => "in", "savegubun" => $savegubun);
	echo json_encode($returnJson);
	exit;

}

//----------------------------------------------------------//
//                    ������ ó������							// 
//----------------------------------------------------------//
if($_POST[type]=='del'){

	// �ʼ����� Ȯ��
	if($_SESSION['S_SCODE'] == null || $carseq == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '�ʼ��Է°� ����, ��α������ּ���.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;		
	}


	// Ʈ������ ����
    sqlsrv_query($mscon, "BEGIN TRAN");
	
	// �񱳰��� ������ ���� 1
	$sql="delete from carest where scode = '".$_SESSION['S_SCODE']."' and carseq = '".$carseq."' ";
	
    $result =  sqlsrv_query( $mscon, $sql );
    if ($result == false){
		sqlsrv_query($mscon, "ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = ' �񱳰��� ������ ���� �� �����߻� #1';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
    }

	// �߰��μ�ǰ ���� 2 
	$sql="delete from carestadd where scode = '".$_SESSION['S_SCODE']."' and carseq = '".$carseq."' ";
	
    $result =  sqlsrv_query( $mscon, $sql );
    if ($result == false){
		sqlsrv_query($mscon, "ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = ' �񱳰��� ������ ���� �� �����߻� #2';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
    }


	// �񱳰��� �������� ���� 3 
	$sql="delete from carestamt where scode = '".$_SESSION['S_SCODE']."' and carseq = '".$carseq."' ";
	
    $result =  sqlsrv_query( $mscon, $sql );
    if ($result == false){
		sqlsrv_query($mscon, "ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = ' �񱳰��� ������ ���� �� �����߻� #3';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
    }


    sqlsrv_query($mscon, "COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' �񱳰��� �� ������ ���� �Ͽ����ϴ�.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "carseq" => $carseq, "rtype" => "del");
	echo json_encode($returnJson);
	exit;

}

?>