<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

$yymm = $_POST['yymm'];
$inscode = $_POST['inscode'];

$selfbit=array();
$dataset1=array();
$dataset2=array();

$datset1_1=array();
$datset1_2=array();

for($i=0;$i<$_POST['count'];$i++){
	$selfbit[$i] = $_POST['selfbit'.(string)$i];
	$dataset1[$i] = iconv("UTF-8","EUC-KR",$_POST['dataset1'.(string)$i]);

	if($_POST['dataset2'.(string)$i]){
		$dataset2[$i] = iconv("UTF-8","EUC-KR",$_POST['dataset2'.(string)$i]);
	}else{
		$dataset2[$i] = 0;
	}
	
	// ���л󼼸��� �ߺ��ǰ� ����ȵǼ� üũ�ϱ� ���� �迭
	if($_POST['selfbit'.(string)$i] == '1'){
		$dataset1_1[$i] = iconv("UTF-8","EUC-KR",$_POST['dataset1'.(string)$i]);
	}else{
		$dataset1_2[$i] = iconv("UTF-8","EUC-KR",$_POST['dataset1'.(string)$i]);
	}
}

//----------------------------------------------------------//
//                    �Է½� ó������							// 
//----------------------------------------------------------//
if($_POST['type']=='save'){
	// �ʼ����� Ȯ��
	if($_SESSION['S_SCODE'] == null or $_POST['yymm'] == null or $_POST['inscode'] == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '�������� error �ʼ��Է°� ����, �� �α������ּ���.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}

	//-----------------------------����ó��-------------------------//

		// ���л󼼸��� �ߺ��Ǵ°� �ִ��� üũ
		$unique1 = array_unique($dataset1_1);
		$unique2 = array_unique($dataset1_2);
		if(count($unique1) != count($dataset1_1)){
			sqlsrv_free_stmt($result);
			sqlsrv_close($mscon);
			$message = '���л󼼸��� ���� �ߺ��˴ϴ�. �ٽ� �Է����ּ���.';
			$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
			echo json_encode($returnJson);
			exit;	
		}
		if(count($unique2) != count($dataset1_2)){
			sqlsrv_free_stmt($result);
			sqlsrv_close($mscon);
			$message = '���л󼼸��� ���� �ߺ��˴ϴ�. �ٽ� �Է����ּ���.';
			$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
			echo json_encode($returnJson);
			exit;	
		}
	//-----------------------------------------------------------//


	$sql = "delete from INSCHARGE_SET_sub 
			where scode = '".$_SESSION['S_SCODE']."' and yymm = '".$_POST['yymm']."' and inscode = '".$_POST['inscode']."'";
	$result =  sqlsrv_query( $mscon, $sql );

	if ($result == false){
		sqlsrv_query("ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' ���ؽ��� ����� ����_1';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
	}

	for($i=1; $i<=count($selfbit); $i++){
		$sql = "insert into INSCHARGE_SET_sub(scode,yymm,inscode ,selfbit,seq,dataset1,dataset2)
				values('".$_SESSION['S_SCODE']."','".$_POST['yymm']."','".$_POST['inscode']."' ,'".$selfbit[$i-1]."',".$i.",'".$dataset1[$i-1]."',".$dataset2[$i-1].")";
		$result =  sqlsrv_query( $mscon, $sql );
		
		if ($result == false){
			sqlsrv_query("ROLLBACK");
			sqlsrv_free_stmt($result);
			sqlsrv_close($mscon);
			$message = ' ���ؽ��� ����� ����_2';
			$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
			echo json_encode($returnJson);
			exit;
		}
	}

    sqlsrv_query("COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' ���ؽ����� �����Ͽ����ϴ�.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "yymm"=>"$yymm" , "inscode"=>"$inscode" , "rtype" => "save");
	echo json_encode($returnJson);
	exit;

}



//----------------------------------------------------------//
//                    ������ ó������							// 
//----------------------------------------------------------//
if($_POST['type']=='del'){
	// �ʼ����� Ȯ��
	if($_SESSION['S_SCODE'] == null or $_POST['yymm'] == null or $_POST['inscode'] == null){
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
        $message = '�������� error �ʼ��Է°� ����, �� �α������ּ���.';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;	
	}



	$sql = "delete from INSCHARGE_SET_sub 
			where scode = '".$_SESSION['S_SCODE']."' and yymm = '".$_POST['yymm']."' and inscode = '".$_POST['inscode']."' and 
					selfbit = '".$_POST['del_selfbit']."' and seq = ".$_POST['del_seq']." ";
	$result =  sqlsrv_query( $mscon, $sql );

	if ($result == false){
		sqlsrv_query("ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = ' ���ؽ��� ������ ����_1';
		$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "error");
		echo json_encode($returnJson);
		exit;
	}

    sqlsrv_query("COMMIT");
	sqlsrv_free_stmt($result);
	sqlsrv_close($mscon);

	$message = ' ���ؽ����� �����Ͽ����ϴ�.';
	$returnJson	= array( "message"	=> iconv("EUC-KR","UTF-8",$message), "result"	=> "", "yymm"=>"$yymm" , "inscode"=>"$inscode" , "rtype" => "del");
	echo json_encode($returnJson);
	exit;

}







?>