<?
session_start();
$loginCheck	= false;

include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

/*
echo "<pre>";
print_r($_POST);
echo "</pre></br>";

echo "<pre>";
print_r($_GET);
echo "</pre></br>";
*/

// �񱳰��� ������ �� ȸ��ID
$carseq	=	$_GET['carseq'];
$scode	=	$_GET['scode'];

// 0.5�� ���� 1, 0.5�� �ʰ� 2�� ����
if((int)$_POST['sagopot'] > 0.5){
	$sagopot	= '2';
}else{
	$sagopot	= '1';
}

$ext_data	= "";
$dash		= "";
$ext_text	= "";
$comma		= "";	

if($carseq){

	// Ư������ �� ����(Ŀ��Ƽ��ī)
	if($_POST['hdbenzcc'] == '1' || $_POST['hdCC'] == '1'){

		$ext_data	= "ex3";
		$ext_text	= "Ŀ��Ƽ��ī";
	}


	// ������Ż����
	if($_POST['hdLDW'] == '1'){

		if($ext_data){
			$dash		= "-";
			$comma		= ",";	
		}

		$ext_data	= $ext_data.$dash."ex16";
		$ext_text	= $ext_text.$comma."������Ż����";
	}

	// �����浹������ġ
	if($_POST['hdFCW'] == '1'){

		if($ext_data){
			$dash		= "-";
			$comma		= ",";	
		}

		$ext_data	= $ext_data.$dash."ex18";
		$ext_text	= $ext_text.$comma."�����浹������ġ";
	}

	// UBI����
	if($_POST['hdUBI'] == '1'){

		if($ext_data){
			$dash		= "-";
			$comma		= ",";	
		}

		$ext_data	= $ext_data.$dash."ex22";
		$ext_text	= $ext_text.$comma."UBI����";
	}


	// ���ڽ�(������)
	if($_POST['hdBLK'] == '1'){

		if($ext_data){
			$dash		= "-";
			$comma		= ",";	
		}

		$ext_data	= $ext_data.$dash."ex28";
		$ext_text	= $ext_text.$comma."���ڽ�(������)";
	}


	// �Ǻ����ڹ�ȣ �� ������ȣ, ����� ��������
	$sql  = "select convert(varchar(20),dbo.decryptkey(jumin)) jumin, carnumber, kdman
			 from carest 
			 where scode	= '".$scode."' 
			   and carseq	= '".$carseq."'	";
	$result  = sqlsrv_query( $mscon, $sql );
	$row =  sqlsrv_fetch_array($result); 
	
	$jumin		= $row['jumin'];
	$carnumber	= $row['carnumber'];
	$kdman		= $row['kdman'];


	$sql  = "select isnull(max(seq),0)+1 seqno
			 from caryoyul 
			 where scode	= '".$scode."' 
			   and carseq	= '".$carseq."'	";
	$result  = sqlsrv_query( $mscon, $sql );
	$seqData =  sqlsrv_fetch_array($result); 

	$seqno = (int)$seqData['seqno'];

	$hdcarcode		=	$_POST['hdcarcode'] ?? false;
	$hdcaryear		=	$_POST['hdcaryear'] ?? false;
	$hdbenzcc		=	$_POST['hdbenzcc'] ?? false;
	$hdagreecheck	=	$_POST['hdagreecheck'] ?? false;
	$hdcc			=	$_POST['hdcc'] ?? false;

	$hdubi			=	$_POST['hdubi'] ?? false;
	$hdfcw			=	$_POST['hdfcw'] ?? false;
	$hdldw			=	$_POST['hdldw'] ?? false;
	$hdblk			=	$_POST['hdblk'] ?? false;


	// Ʈ������ ����
	sqlsrv_query($mscon, "BEGIN TRAN");

	$sql="insert into caryoyul (scode,			carseq,			seq,			jumin,			carnum,
							    guipcarrer,		traffic,		halin,			special_code,	special_code1,
								enddate,		sagocnt,		sagopot,		ncr_code,		c_guip,
								c_age,			c_fam,			hdcarcode,		hdcaryear,		hdbenzcc,
								hdagreecheck,	hdcc,			hdubi,			hdfcw,			hdldw,
								hdblk,			idate,			kdman)
					  values ('".$scode."',					'".$carseq."',					".$seqno.",						dbo.encryptkey('".$jumin."'),		'".$carnumber."', 
					  		  '".$_POST['guipcarrer']."',	'".$_POST['traffic']."',		'".$_POST['halin']."',			'".$_POST['special_code']."',		'".$_POST['special_code1']."',
							  '".$_POST['enddate']."',		 ".$_POST['sagocnt'].",			'".$sagopot."',					'".$_POST['ncr_code']."',			'".$_POST['c_guip']."',
							  '".$_POST['c_age']."',		'".$_POST['c_fam']."',			'".$hdcarcode."',				'".$hdcaryear."',					'".$hdbenzcc."',
							  '".$hdagreecheck."',			'".$hdcc."',					'".$hdubi."',					'".$hdfcw."',						'".$hdldw."',
							  '".$hdblk."',					getdate(),						'".$kdman."')  ";
	

    $result =  sqlsrv_query( $mscon, $sql );

	if ($result == false){
		sqlsrv_query($mscon, "ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = '���� ����� �����߻� #1';
		echo "<script>alert('$message'); location.href='./ga_menu6_01.php?carseq=$carseq';</script>";
		exit;
	}

	$idate		=	$_POST['enddate'];
	$idate_to	=	date("Ymd", strtotime($idate."+1 year"));

	// �񱳰��� ������ ������Ʈ
	/* ���谳������ �����ϸ� ���谳���� �� ������ ������Ʈ */
	if($_POST['enddate']){
		$sql="update carest
			  set	guipcarrer		=	'".$_POST['guipcarrer']."',
					traffic			=	'".$_POST['traffic']."',
					halin			=	'".$_POST['halin']."',
					special_code	=	'".$_POST['special_code']."',
					special_code1	=	'".$_POST['special_code1']."',
					ss_point		=	'".$sagopot."',
					ncr_code		=	'".$_POST['ncr_code']."',
					ext_bupum		=	'".$ext_data."',
					ext_bupum_txt	=	'".$ext_text."',
					fdate			=	'".$idate."',
					tdate			=	'".$idate_to."'
			where scode		= '".$scode."'
			  and carseq	= '".$carseq."' ";

		// ���谳���� �缳���� ���� ���ϰ�(����ȭ�鿡�� alert����)
		$idateyn = 'Y';
	}else{
		$sql="update carest
			  set	guipcarrer		=	'".$_POST['guipcarrer']."',
					traffic			=	'".$_POST['traffic']."',
					halin			=	'".$_POST['halin']."',
					special_code	=	'".$_POST['special_code']."',
					special_code1	=	'".$_POST['special_code1']."',
					ss_point		=	'".$sagopot."',
					ncr_code		=	'".$_POST['ncr_code']."',
					ext_bupum		=	'".$ext_data."',
					ext_bupum_txt	=	'".$ext_text."'
			where scode		= '".$scode."'
			  and carseq	= '".$carseq."' ";	
	}


    $result =  sqlsrv_query( $mscon, $sql );

	if ($result == false){
		sqlsrv_query($mscon, "ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = '���� ����� �����߻� #2';
		echo "<script>alert('$message'); location.href='./ga_menu6_01.php?carseq=$carseq';</script>";
		exit;
	}


	// �ڹٽ�ũ��Ʈ �迭�� ������
	$rtnData = array();

	$rtnData["guipcarrer"]		=	$_POST['guipcarrer'];
	$rtnData["traffic"]			=	$_POST['traffic'];
	$rtnData["halin"]			=	$_POST['halin'];
	$rtnData["special_code"]	=	$_POST['special_code'];
	$rtnData["special_code1"]	=	$_POST['special_code1'];
	$rtnData["ss_point"]		=	$sagopot;
	$rtnData["ncr_code"]		=	$_POST['ncr_code'];
	$rtnData["ext_bupum"]		=	$ext_data;
	$rtnData["ext_bupum_txt"]	=	iconv("EUCKR","UTF-8",$ext_text);
	$rtnData["fdate"]			=	$idate;
	$rtnData["tdate"]			=	$idate_to;
	$rtnData["idateyn"]			=	$idateyn;

	$arr_data = json_encode($rtnData);

}


sqlsrv_query($mscon, "COMMIT");
sqlsrv_free_stmt($result);
sqlsrv_close($mscon);


//echo "<script> location.href='./ga_menu6_01.php?carseq=$carseq&idateyn=$idateyn';  </script>";
//echo "<script>window.parent.fn_yoyul_end('".$arr_data."');</script>";
echo '<script>
    var message = {
        functionName: "fn_yoyul_end",
        data: ' . $arr_data . '
    };
    window.parent.postMessage(message, "https://www.gaplus.net:452");
</script>';
?>
