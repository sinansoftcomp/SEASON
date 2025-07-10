<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

$rtnData	= array();

if($_GET['searchF1'] && $_GET['searchF1Text']){
	if($_GET['searchF1'] == 'sjuno'){
		$where  .= " and (a.snum like '%".$_GET['searchF1Text']."%' or Cast(dbo.DECRYPTKEY(a.sjuno) as varchar) like '%".$_GET['searchF1Text']."%') ";
	}else if($_GET['searchF1'] == 'tel'){
		$where  .= " and (a.tel like replace('%".$_GET['searchF1Text']."%','-','') or a.htel like replace('%".$_GET['searchF1Text']."%','-','')) ";
	}else if($_GET['skey'] && $_GET['searchF1'] == 's1'){	//	모집사원
		$where  .= " and a.gskey = '".$_GET['skey']."' ";	
	}else if($_GET['skey'] && $_GET['searchF1'] == 's2'){	//	관리사원
		$where  .= " and a.kskey = '".$_GET['skey']."' ";	
	}else{		
		$where  .= " and ".$_GET['searchF1']." like '%".$_GET['searchF1Text']."%' ";	
	}
}

$sql= "
		select 
				count(*) CNT
		from kwn a
			left outer join bonbu b on a.scode = b.scode and a.bonbu = b.bcode
			left outer join jisa c on a.scode = c.scode and a.jisa = c.jscode
			left outer join team e on a.scode = e.scode and a.team = e.tcode
			left outer join inssetup f on a.scode = f.scode and a.inscode = f.inscode
			left outer join swon s1 on s1.scode = a.scode and s1.skey = a.gskey
			left outer join swon s2 on s2.scode = a.scode and s2.skey = a.kskey
		where a.scode = '".$_SESSION['S_SCODE']."'  ".$where." " ;


$qry =  sqlsrv_query($mscon, $sql);
$totalResult =  sqlsrv_fetch_array($qry); 

$Cnt	=	$totalResult['CNT'];


$sql= "
		select 
				kcode
		from kwn a
			left outer join bonbu b on a.scode = b.scode and a.bonbu = b.bcode
			left outer join jisa c on a.scode = c.scode and a.jisa = c.jscode
			left outer join team e on a.scode = e.scode and a.team = e.tcode
			left outer join inssetup f on a.scode = f.scode and a.inscode = f.inscode
			left outer join swon s1 on s1.scode = a.scode and s1.skey = a.gskey
			left outer join swon s2 on s2.scode = a.scode and s2.skey = a.kskey
		where a.scode = '".$_SESSION['S_SCODE']."'  ".$where." " ;


$qry =  sqlsrv_query($mscon, $sql);
$totalSer =  sqlsrv_fetch_array($qry); 

if($Cnt == 1){
	$kcode	=	$totalSer['kcode'];
	$rtnData["D"]	= $kcode;
}

$rtnData["Y"]	= $Cnt;


echo json_encode($rtnData);
?>
