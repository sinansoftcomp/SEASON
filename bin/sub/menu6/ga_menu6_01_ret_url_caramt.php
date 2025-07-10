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

// 비교견적 교유값 및 회사ID
$carseq	=	$_GET['carseq'];
$scode	=	$_GET['scode'];


if($carseq){

	$sql="delete from carestamt where scode = '".$scode."' and carseq = '".$carseq."' ";

    $result =  sqlsrv_query( $mscon, $sql );

	if ($result == false){
		sqlsrv_query("ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = '비교견적 등록중 오류발생 #1';
		echo "<script>alert('$message'); location.href='./ga_menu6_01.php?carseq=$carseq';</script>";
		exit;
	}

	$sql="insert into carestamt(
								scode,carseq,resdt,
								hd_tot,hd_man1,hd_man2,hd_mul,hd_sin,hd_mu,hd_car,hd_goout,hd_msg,hd_text,hd_txt,
								ss_tot,ss_man1,ss_man2,ss_mul,ss_sin,ss_mu,ss_car,ss_goout,ss_msg,ss_text,ss_txt,
								db_tot,db_man1,db_man2,db_mul,db_sin,db_mu,db_car,db_goout,db_msg,db_text,db_txt,
								lg_tot,lg_man1,lg_man2,lg_mul,lg_sin,lg_mu,lg_car,lg_goout,lg_msg,lg_text,lg_txt,
								dy_tot,dy_man1,dy_man2,dy_mul,dy_sin,dy_mu,dy_car,dy_goout,dy_msg,dy_text,dy_txt,
								sy_tot,sy_man1,sy_man2,sy_mul,sy_sin,sy_mu,sy_car,sy_goout,sy_msg,sy_text,sy_txt,
								sd_tot,sd_man1,sd_man2,sd_mul,sd_sin,sd_mu,sd_car,sd_goout,sd_msg,sd_text,sd_txt,
								dh_tot,dh_man1,dh_man2,dh_mul,dh_sin,dh_mu,dh_car,dh_goout,dh_msg,dh_text,dh_txt,
								gr_tot,gr_man1,gr_man2,gr_mul,gr_sin,gr_mu,gr_car,gr_goout,gr_msg,gr_text,gr_txt)
				values('".$scode."',	'".$carseq."',	convert(varchar,getdate(),112),
					   '".$_POST['hd_tot']."',	'".$_POST['hd_man1']."',	'".$_POST['hd_man2']."',	'".$_POST['hd_mul']."',		'".$_POST['hd_sin']."',	'".$_POST['hd_mu']."',	
					   '".$_POST['hd_car']."',	'".$_POST['hd_goout']."',	'".$_POST['hd_msg']."',		'".$_POST['hd_text']."',	'".$_POST['hd_txt']."',
					   '".$_POST['ss_tot']."',	'".$_POST['ss_man1']."',	'".$_POST['ss_man2']."',	'".$_POST['ss_mul']."',		'".$_POST['ss_sin']."',	'".$_POST['ss_mu']."',	
					   '".$_POST['ss_car']."',	'".$_POST['ss_goout']."',	'".$_POST['ss_msg']."',		'".$_POST['ss_text']."',	'".$_POST['ss_txt']."',
					   '".$_POST['db_tot']."',	'".$_POST['db_man1']."',	'".$_POST['db_man2']."',	'".$_POST['db_mul']."',		'".$_POST['db_sin']."',	'".$_POST['db_mu']."',	
					   '".$_POST['db_car']."',	'".$_POST['db_goout']."',	'".$_POST['db_msg']."',		'".$_POST['db_text']."',	'".$_POST['db_txt']."',
					   '".$_POST['lg_tot']."',	'".$_POST['lg_man1']."',	'".$_POST['lg_man2']."',	'".$_POST['lg_mul']."',		'".$_POST['lg_sin']."',	'".$_POST['lg_mu']."',	
					   '".$_POST['lg_car']."',	'".$_POST['lg_goout']."',	'".$_POST['lg_msg']."',		'".$_POST['lg_text']."',	'".$_POST['lg_txt']."',		
					   '".$_POST['dy_tot']."',	'".$_POST['dy_man1']."',	'".$_POST['dy_man2']."',	'".$_POST['dy_mul']."',		'".$_POST['dy_sin']."',	'".$_POST['dy_mu']."',	
					   '".$_POST['dy_car']."',	'".$_POST['dy_goout']."',	'".$_POST['dy_msg']."',		'".$_POST['dy_text']."',	'".$_POST['dy_txt']."',		
					   '".$_POST['sy_tot']."',	'".$_POST['sy_man1']."',	'".$_POST['sy_man2']."',	'".$_POST['sy_mul']."',		'".$_POST['sy_sin']."',	'".$_POST['sy_mu']."',	
					   '".$_POST['sy_car']."',	'".$_POST['sy_goout']."',	'".$_POST['sy_msg']."',		'".$_POST['sy_text']."',	'".$_POST['sy_txt']."',		
					   '".$_POST['sd_tot']."',	'".$_POST['sd_man1']."',	'".$_POST['sd_man2']."',	'".$_POST['sd_mul']."',		'".$_POST['sd_sin']."',	'".$_POST['sd_mu']."',	
					   '".$_POST['sd_car']."',	'".$_POST['sd_goout']."',	'".$_POST['sd_msg']."',		'".$_POST['sd_text']."',	'".$_POST['sd_txt']."',		
					   '".$_POST['dh_tot']."',	'".$_POST['dh_man1']."',	'".$_POST['dh_man2']."',	'".$_POST['dh_mul']."',		'".$_POST['dh_sin']."',	'".$_POST['dh_mu']."',	
					   '".$_POST['dh_car']."',	'".$_POST['dh_goout']."',	'".$_POST['dh_msg']."',		'".$_POST['dh_text']."',	'".$_POST['dh_txt']."',	
					   '".$_POST['gr_tot']."',	'".$_POST['gr_man1']."',	'".$_POST['gr_man2']."',	'".$_POST['gr_mul']."',		'".$_POST['gr_sin']."',	'".$_POST['gr_mu']."',	
					   '".$_POST['gr_car']."',	'".$_POST['gr_goout']."',	'".$_POST['gr_msg']."',		'".$_POST['gr_text']."',	'".$_POST['gr_txt']."' ) ";


    $result =  sqlsrv_query( $mscon, $sql );

	if ($result == false){
		sqlsrv_query( $mscon,"ROLLBACK");
		sqlsrv_free_stmt($result);
		sqlsrv_close($mscon);
		$message = '비교견적 등록중 오류발생 #2';
		echo "<script>alert('$message'); location.href='./ga_menu6_01.php?carseq=$carseq';</script>";
		exit;
	}

}


sqlsrv_query( $mscon,"COMMIT");
sqlsrv_free_stmt($result);
sqlsrv_close($mscon);


//echo "<script> location.href='./ga_menu6_01.php?carseq=$carseq';  </script>";
echo '<script>
    var message = {
        functionName: "fn_caramt_end",
        data: ' . $carseq . '
    };
    window.parent.postMessage(message, "https://www.gaplus.net:452");
</script>';

?>
