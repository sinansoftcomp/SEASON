<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

/* --------------------------------------------------------------------------
	���������
 --------------------------------------------------------------------------*/
$insilj		=	$_POST['insilj'];
$kcode		=	$_POST['kcode'];
$kname		=	iconv("UTF-8","EUCKR",$_POST['kname']);
$sbit		=	$_POST['sbit'];
$sjuno1		=	$_POST['sjuno1'];
$sjuno2		=	$_POST['sjuno2'];
$sjuno		=	$sjuno1.$sjuno2;

$snum1		=	$_POST['snum1'];
$snum2		=	$_POST['snum2'];
$snum3		=	$_POST['snum3'];
$snum		=	$snum1.$snum2.$snum3;

$comnm		=	iconv("UTF-8","EUCKR",$_POST['comnm']);
$cupnm		=	iconv("UTF-8","EUCKR",$_POST['cupnm']);

$tel1		=	$_POST['tel1'];
$tel2		=	$_POST['tel2'];
$tel3		=	$_POST['tel3'];
$tel		=	$tel1.$tel2.$tel3;

$htel1		=	$_POST['htel1'];
$htel2		=	$_POST['htel2'];
$htel3		=	$_POST['htel3'];
$htel		=	$htel1.$htel2.$htel3;

$post		=	$_POST['post'];
$addr		=	iconv("UTF-8","EUCKR",$_POST['addr']);
$addr_dt	=	iconv("UTF-8","EUCKR",$_POST['addr_dt']);

$email		=	$_POST['email'];

$bigo		=	iconv("UTF-8","EUCKR",$_POST['bigo']);

/* --------------------------------------------------------------------------
	�������
 --------------------------------------------------------------------------*/
$kdate		=	str_replace("-","",iconv("UTF-8","EUCKR",$_POST['kdate']));
$kstbit		=	iconv("UTF-8","EUCKR",$_POST['kstbit']);
$fdate		=	str_replace("-","",iconv("UTF-8","EUCKR",$_POST['fdate']));
$tdate		=	str_replace("-","",iconv("UTF-8","EUCKR",$_POST['tdate']));

$inscode1	=	$_POST['inscode1'];	// ��ǰ�� �ڵ����� �ƴҰ�� ������ڵ�
$inscode2	=	$_POST['inscode2'];	// ��ǰ�� �ڵ����� ��� ������ڵ�
if($insilj == '3'){
	$inscode	= $inscode2;
}else{
	$inscode	= $inscode1;
}

$item		=	$_POST['item'];		// ����� ��ǰ�ڵ�
$itemcode	=	$_POST['itemcode'];	// �žȼ���Ʈ ��ǰ�ڵ�
$itemnm		=	iconv("UTF-8","EUCKR",$_POST['itemnm']);

$ksman		=	$_POST['ksman'];
$kdman		=	$_POST['kdman'];
$mamt		=	(int)str_replace(",","",iconv("UTF-8","EUCKR",$_POST['mamt']));
$hamt		=	(int)str_replace(",","",iconv("UTF-8","EUCKR",$_POST['hamt']));
$samt		=	(int)str_replace(",","",iconv("UTF-8","EUCKR",$_POST['samt']));
$srate		=	(int)str_replace(",","",iconv("UTF-8","EUCKR",$_POST['srate']));

$bigo2		=	iconv("UTF-8","EUCKR",$_POST['bigo2']);


/* --------------------------------------------------------------------------
	�Ǻ���������
 --------------------------------------------------------------------------*/
$rel		=	$_POST['rel'];
$pname		=	iconv("UTF-8","EUCKR",$_POST['pname']);
$psbit		=	$_POST['psbit'];
$psjuno1	=	$_POST['psjuno1'];
$psjuno2	=	$_POST['psjuno2'];
$psjuno		=	$psjuno1.$psjuno2;

$psnum1		=	$_POST['psnum1'];
$psnum2		=	$_POST['psnum2'];
$psnum3		=	$_POST['psnum3'];
$psnum		=	$psnum1.$psnum2.$psnum3;

$pcomnm		=	iconv("UTF-8","EUCKR",$_POST['pcomnm']);
$pcupnm		=	iconv("UTF-8","EUCKR",$_POST['pcupnm']);

$ptel1		=	$_POST['ptel1'];
$ptel2		=	$_POST['ptel2'];
$ptel3		=	$_POST['ptel3'];
$ptel		=	$ptel1.$ptel2.$ptel3;

$phtel1		=	$_POST['phtel1'];
$phtel2		=	$_POST['phtel2'];
$phtel3		=	$_POST['phtel3'];
$phtel		=	$phtel1.$phtel2.$phtel3;

$pbigo		=	iconv("UTF-8","EUCKR",$_POST['pbigo']);

/* --------------------------------------------------------------------------
	�������� & Ư��/�㺸 > ��ǰ�� �ڵ����� ���
 --------------------------------------------------------------------------*/
$carnum		=	iconv("UTF-8","EUCKR",$_POST['carnum']);
$carvin		=	iconv("UTF-8","EUCKR",$_POST['carvin']);
$carjong	=	iconv("UTF-8","EUCKR",$_POST['carjong']);
$caryy		=	iconv("UTF-8","EUCKR",$_POST['caryy']);
$carcode	=	iconv("UTF-8","EUCKR",$_POST['carcode']);

$carkind	=	$_POST['carkind'];

$cargamt	=	(int)str_replace(",","",iconv("UTF-8","EUCKR",$_POST['cargamt']));
$cartamt	=	(int)str_replace(",","",iconv("UTF-8","EUCKR",$_POST['cartamt']));


$carsub1	=	iconv("UTF-8","EUCKR",$_POST['carsub1']);
$carsamt1	=	(int)str_replace(",","",iconv("UTF-8","EUCKR",$_POST['carsamt1']));
$carsub2	=	iconv("UTF-8","EUCKR",$_POST['carsub2']);
$carsamt2	=	(int)str_replace(",","",iconv("UTF-8","EUCKR",$_POST['carsamt2']));
$carsub3	=	iconv("UTF-8","EUCKR",$_POST['carsub3']);
$carsamt3	=	(int)str_replace(",","",iconv("UTF-8","EUCKR",$_POST['carsamt3']));
$carsub4	=	iconv("UTF-8","EUCKR",$_POST['carsub4']);
$carsamt4	=	(int)str_replace(",","",iconv("UTF-8","EUCKR",$_POST['carsamt4']));
$carsub5	=	iconv("UTF-8","EUCKR",$_POST['carsub5']);
$carsamt5	=	(int)str_replace(",","",iconv("UTF-8","EUCKR",$_POST['carsamt5']));


$carobj		=	$_POST['carobj'];
$carty		=	$_POST['carty'];
$carpay1	=	$_POST['carpay1'];
$carpay2	=	$_POST['carpay2'];
$carbae		=	$_POST['carbae'];

$carbody1	=	$_POST['carbody1'];
$carbody2	=	$_POST['carbody2'];
$carbody3	=	$_POST['carbody3'];

$carloss	=	$_POST['carloss'];
$caracamt	=	$_POST['caracamt'];
$carins		=	$_POST['carins'];
$caremg		=	$_POST['caremg'];


//----------------------------------------------------------//
//                    ������ ó������							// 
//----------------------------------------------------------//
if($_POST['type']=='up'){

	// �ʼ����� Ȯ��
	if($_SESSION['S_SCODE'] == null || $inscode == null || $kcode == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '�ʼ��Է°� ����, ��α������ּ���.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;		
	}

	/*
		����� ������� ���� �����ͼ� 
		GA���α׷� �� ��� ��� ��������
		ksman / gskey : ���ʸ�������� �����Ұ�
		����� ������ ���������� ����

	*/


	$sql  = "
			select  a.bscode,
					a.skey,
					b.bonbu,
					b.jisa,
					b.jijum,
					b.team,
					c.skey bonbuid,
					d.skey jisaid,
					f.skey jijumid,
					e.skey teamid,
					b.jik
			from inswon a
				left outer join swon b on a.scode = b.scode and a.skey = b.skey
				left outer join swon c on a.scode = c.scode and b.bonbu = c.bonbu and c.jik = '5001'
				left outer join swon d on a.scode = d.scode and b.jisa = d.jisa and c.jik = '4001'
				left outer join swon f on a.scode = f.scode and b.jijum = f.jijum and c.jik = '3001'
				left outer join swon e on a.scode = e.scode and b.team = e.team and c.jik = '2001'
			where a.scode = '".$_SESSION['S_SCODE']."'
			  and a.inscode = '".$inscode."'
			  and a.bscode = '".$kdman."'	";
	$result  = sqlsrv_query( $mscon, $sql );
	$row =  sqlsrv_fetch_array($result); 

	$kskey	=	$row['skey'];
	$bonbu	=	$row['bonbu'];
	$jisa	=	$row['jisa'];
	$jijum	=	$row['jijum'];
	$team	=	$row['team'];
	$bonbuid=	$row['bonbuid'];
	$jisaid	=	$row['jisaid'];
	$jijumid=	$row['jijumid'];
	$teamid	=	$row['teamid'];
	$sjik	=	$row['jik'];

	// ������� ����
	$sql="UPDATE kwn	
		  SET	
		  	insilj		=	'$insilj',
			kname		=	'$kname',
			sbit		=	'$sbit',
			sjuno		=	dbo.ENCRYPTKEY('$sjuno'),
			snum		=	'$snum',			
			comnm		=	'$comnm',	
			cupnm		=	'$cupnm',

			tel			=	'$tel',
			htel		=	'$htel',
			post		=	'$post',
			addr		=	'$addr',
			addr_dt		=	'$addr_dt',		

			email		=	'$email',
			bigo		=	'$bigo',


			kdate		=	'$kdate',
			kstbit		=	'$kstbit',
			fdate		=	'$fdate',
			tdate		=	'$tdate',

			item		=	'$item',
			itemnm		=	'$itemnm',
			kdman		=	'$kdman',
			mamt		=	 $mamt,
			hamt		=	 $hamt,
			samt		=	 $samt,
			srate		=	 $srate,
			bigo2		=	'$bigo2',

			rel			=	'$rel',
			pname		=	'$pname',
			psbit		=	'$psbit',
			psjuno		=	dbo.ENCRYPTKEY('$psjuno'),
			psnum		=	'$psnum',			
			pcomnm		=	'$pcomnm',	
			pcupnm		=	'$pcupnm',

			ptel		=	'$ptel',
			phtel		=	'$phtel',
			pbigo		=	'$pbigo',

			carnum		=	'$carnum',
			carvin		=	'$carvin',
			carjong		=	'$carjong',
			caryy		=	'$caryy',
			carcode		=	'$carcode',
			carkind		=	'$carkind',
			cargamt		=	 $cargamt,
			cartamt		=	 $cartamt,
			carsub1		=	'$carsub1',
			carsamt1	=	 $carsamt1,
			carsub2		=	'$carsub2',
			carsamt2	=	 $carsamt2,
			carsub3		=	'$carsub3',
			carsamt3	=	 $carsamt3,
			carsub4		=	'$carsub4',
			carsamt4	=	 $carsamt4,
			carsub5		=	'$carsub5',
			carsamt5	=	 $carsamt5,
			carobj		=	'$carobj',
			carty		=	'$carty',
			carpay1		=	'$carpay1',
			carpay2		=	'$carpay2',
			carbae		=	'$carbae',
			carbody1	=	'$carbody1',
			carbody2	=	'$carbody2',
			carbody3	=	'$carbody3',
			carloss		=	'$carloss',
			caracamt	=	'$caracamt',
			carins		=	'$carins',
			caremg		=	'$caremg',


			kskey		=	'$kskey',
			sjik		=	'$sjik',
			bonbu		=	'$bonbu',
			jisa		=	'$jisa',
			jijum		=	'$jijum',
			team		=	'$team',

			bonbuid		=	'$bonbuid',
			jisaid		=	'$jisaid',
			jijumid		=	'$jijumid',
			teamid		=	'$teamid',

			udate		=	getdate(),
			uswon		=	'".$_SESSION['S_SKEY']."'
		WHERE scode = '".$_SESSION['S_SCODE']."'
		  and inscode = '".$inscode."'
		  and kcode = '".$kcode."' ";   



	//echo $sql;

	// Ʈ������ ����
    sqlsrv_query($mscon,"BEGIN TRAN");
    $result =  sqlsrv_query( $mscon, $sql );


    if ($result == false){
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = ' ������� ���� �� �����߻�';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
    }

    sqlsrv_query($mscon,"COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' ��������� ���� �Ͽ����ϴ�.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "rtype" => "up");
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

	/*
		����� ������� ���� �����ͼ� 
		GA���α׷� �� ��� ��� ��������
		ksman / gskey : ���ʸ�������� �����Ұ�
		bonbu / jisa / team / bonbuid / jisaid / teamid ������������ �����Ұ�
	*/
	$sql  = "
			select  skey
			from inswon 
			where scode = '".$_SESSION['S_SCODE']."'
			  and inscode = '".$inscode."'
			  and bscode = '".$ksman."'	";
	$result  = sqlsrv_query( $mscon, $sql );
	$row =  sqlsrv_fetch_array($result); 

	$gskey	=	$row['skey'];


	$sql  = "
			select  a.bscode,
					a.skey,
					b.bonbu,
					b.jisa,
					b.jijum,
					b.team,
					c.skey bonbuid,
					d.skey jisaid,
					f.skey jijumid,
					e.skey teamid,
					b.jik
			from inswon a
				left outer join swon b on a.scode = b.scode and a.skey = b.skey
				left outer join swon c on a.scode = c.scode and b.bonbu = c.bonbu and c.jik = '5001'
				left outer join swon d on a.scode = d.scode and b.jisa = d.jisa and c.jik = '4001'
				left outer join swon f on a.scode = f.scode and b.jijum = f.jijum and c.jik = '3001'
				left outer join swon e on a.scode = e.scode and b.team = e.team and c.jik = '2001'
			where a.scode = '".$_SESSION['S_SCODE']."'
			  and a.inscode = '".$inscode."'
			  and a.bscode = '".$kdman."'	";
	$result  = sqlsrv_query( $mscon, $sql );
	$row =  sqlsrv_fetch_array($result); 

	$kskey	=	$row['skey'];
	$bonbu	=	$row['bonbu'];
	$jisa	=	$row['jisa'];
	$jijum	=	$row['jijum'];
	$team	=	$row['team'];
	$bonbuid=	$row['bonbuid'];
	$jisaid	=	$row['jisaid'];
	$jijumid=	$row['jijumid'];
	$teamid	=	$row['teamid'];
	$sjik	=	$row['jik'];
	


	$sql="insert into kwn (	scode,						insilj,
							kcode,						kname,						sbit,					sjuno,						snum,
							comnm,						cupnm,						tel,					
							htel,						post,						addr,					addr_dt,
							email,						bigo,		

							kdate,						kstbit,						fdate,					tdate,						inscode,
							item,						itemnm,						ksman,					kdman,
							mamt,						hamt,						samt,					srate,						
							bigo2,

							rel,						pname,						psbit,					psjuno,						psnum,
							pcomnm,						pcupnm,						ptel,					
							phtel,						pbigo,
							
							carnum,						carvin,						carjong,				caryy,						carcode,
							carkind,					cargamt,					cartamt,				carsub1,					carsamt1,
							carsub2,					carsamt2,					carsub3,				carsamt3,					carsub4,
							carsamt4,					carsub5,					carsamt5,				carobj,						carty,
							carpay1,					carpay2,					carbae,					carbody1,					carbody2,
							carbody3,					carloss,					caracamt,				carins,						caremg,

							gskey,						kskey,						bonbu,					jisa,						team,
							bonbuid,					jisaid,						teamid,					sjik,						sugi,						
							jijum,						jijumid,					idate,						iswon)

				  values('".$_SESSION['S_SCODE']."',	'$insilj',	
						 '$kcode',						'$kname',					'$sbit',				dbo.ENCRYPTKEY('$sjuno'),	'$snum',
						 '$comnm',						'$cupnm',					'$tel',					
						 '$htel',						'$post',					'$addr',				'$addr_dt',
						 '$email',						'$bigo',				

						 '$kdate',						'$kstbit',					'$fdate',				'$tdate',					'$inscode',
						 '$item',						'$itemnm',					'$ksman',				'$kdman',
						  $mamt,						 $hamt,						 $samt,					 $srate,					 
						 '$bigo2',	
						 
						 '$rel',						'$pname',					'$psbit',				dbo.ENCRYPTKEY('$psjuno'),	'$psnum',
						 '$pcomnm',						'$pcupnm',					'$ptel',				
						 '$phtel',						'$pbigo',	

						 '$carnum',						'$carvin',					'$carjong',				'$caryy',					'$carcode',
						 '$carkind',					 $cargamt,					 $cartamt,				'$carsub1',					 $carsamt1,
						 '$carsub2',					 $carsamt2,					'$carsub3',				 $carsamt3,					'$carsub4',
						  $carsamt4,					'$carsub5',					 $carsamt5,				'$carobj',					'$carty',
						 '$carpay1',					'$carpay2',					'$carbae',				'$carbody1',				'$carbody2',
						 '$carbody3',					'$carloss',					'$caracamt',			'$carins',					'$caremg',

						 '$gskey',						'$kskey',					'$bonbu',				'$jisa',					'$team',						 
						 '$bonbuid',					'$jisaid',					'$teamid',				'$sjik',					'1',						
						 '$jijum',						'$jijumid',					getdate(),				'".$_SESSION['S_SKEY']."' ) ";


	// Ʈ������ ����
    sqlsrv_query($mscon,"BEGIN TRAN");
    $result =  sqlsrv_query( $mscon, $sql );

    if ($result == false){
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = ' ��� ��� �� �����߻�';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
    }

    sqlsrv_query($mscon,"COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' ��� ������ ��� �Ͽ����ϴ�.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "kcode" => $kcode, "rtype" => "in");
	echo json_encode($returnJson);
	exit;

}

//----------------------------------------------------------//
//                    ������ ó������							// 
//----------------------------------------------------------//
if($_POST[type]=='del'){

	// �ʼ����� Ȯ��
	if($_SESSION['S_SCODE'] == null || $inscode == null || $kcode == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '�ʼ��Է°� ����, ��α������ּ���.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;		
	}

	// ������
	$sql="delete from kwn where scode = '".$_SESSION['S_SCODE']."' and inscode = '".$inscode."' and kcode = '".$kcode."' ";

	// Ʈ������ ����
    sqlsrv_query($mscon,"BEGIN TRAN");
    $result =  sqlsrv_query( $mscon, $sql );

    if ($result == false){
		sqlsrv_query($mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = ' ������ ���� �� �����߻�';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
    }

    sqlsrv_query($mscon,"COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' �� ������ �����Ͽ����ϴ�.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "rtype" => "del");
	echo json_encode($returnJson);
	exit;

}

?>