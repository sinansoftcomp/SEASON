
<?php
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");
 
$sbit	= $_REQUEST['sbit']; //1=��ü 2=����,����  3=����,����

//print_r($_REQUEST);
//print_r($sbit);


 /* jstree ����
{"id":"H00010","parent":"#","text":"\ub300\uad6c\ubcf8\ubd80","icon":"glyphicon glyphicon-home"}
"id"  = ���� �����ΰ�  "H00010" �̴� 
"parent" = ���� �θ�� �����ΰ� "#" �̴�. ��Ʈ�� "#"����. ��Ʈ�� �ƴϸ� ���� �θ� "id"
"text"    = Ʈ���� display ���� .
"icon"   = ǥ���� ������.
 */
 
$tree_arr= array();
$tree_arr_tot= array();


//-->���� 
$sql = "SELECT bcode,bname FROM BONBU WHERE scode = '".$_SESSION['S_SCODE']."'      order by num   ";
$res = sqlsrv_query( $mscon, $sql );
	//iterate on results row and create new index array of data
	while( $row = sqlsrv_fetch_array($res) ) { 

		unset($tree_arr); 
		$tree_arr['id'] = 'N1'.$row["bcode"];             //---> N1= ���θ� ��Ÿ�� 
		$tree_arr['parent'] =  "#";  
		$tree_arr['text'] =  iconv("EUCKR","UTF-8",$row["bname"]);  
		//$tree_arr['icon'] =  "glyphicon tree-bonbu";  
		$tree_arr['icon'] =  "glyphicon fa-solid fa-building-user";  
		array_push($tree_arr_tot,$tree_arr) ;	}
 

//--->����
$sql = "SELECT a.jscode,a.jsname,a.upcode, b.bcode  FROM JISA a
				left outer join bonbu b on a.scode = b.scode and a.upcode = b.bcode  
				WHERE a.scode = '".$_SESSION['S_SCODE']."'  and  a.USEYN = '1'   order by a.num   ";

$res = sqlsrv_query( $mscon, $sql );
	while( $row = sqlsrv_fetch_array($res) ) { 
		
		unset($tree_arr); 
		$tree_arr['id'] = 'N2'.$row["jscode"];     //--->���簡 ���� id  N2��  �� �����. 
		if (str_replace(" ", "" ,$row["bcode"]) == ""    ||      is_null($row["bcode"]) ) {     //-->���ΰ� ���ٸ� ��Ʈ�� ����
			$tree_arr['parent'] = "#"; 
		}else{
			$tree_arr['parent'] = 'N1'.$row["upcode"];                                                          //-->���ΰ� �ִٸ� ���� �Ʒ��� 
		}
		$tree_arr['text'] =  iconv("EUCKR","UTF-8",$row["jsname"]);  
		//$tree_arr['icon'] =  "glyphicon tree-jisa";  
		$tree_arr['icon'] =  "glyphicon fa-solid fa-building-user jisa"; 
		array_push($tree_arr_tot,$tree_arr) ;	}


//--->����
$sql = "SELECT a.jcode,a.jname, a.upcode, b.jscode, c.bcode   FROM JIJUM a
				left outer join jisa b on a.scode = b.scode and  a.upcode = b.jscode  
				left outer join bonbu c on a.scode = c.scode and b.upcode = c.bcode  
				WHERE a.scode = '".$_SESSION['S_SCODE']."'  and  a.USEYN = '1'   order by a.num   ";
 
$res = sqlsrv_query( $mscon, $sql );
	while( $row = sqlsrv_fetch_array($res) ) { 
		
		unset($tree_arr); 
		$tree_arr['id'] = 'N3'.$row["jcode"];   //--> ������ ���� ID  N3�� ������ �ǹ� 
		
 		//-->���� �� ���� 	���ٸ�  ��Ʈ�� �ٴ´� 
		if ( empty($row["jscode"]) &&   empty($row["bcode"])  ) {      //-->���ο� ���簡 ���ٸ� ��Ʈ�� ���� 
			$tree_arr['parent'] = "#"; 
		}
		//-->���ΰ� 	�ִٸ�
		if ( !empty($row["bcode"])   ) {               
			$tree_arr['parent'] = 'N1'.$row["upcode"]; 
		}
		//-->���簡 	�ִٸ�
		if ( !empty($row["jscode"])   ) {
			$tree_arr['parent'] = 'N2'.$row["upcode"]; 
		}
		$tree_arr['text'] =  iconv("EUCKR","UTF-8",$row["jname"]);  
		//$tree_arr['icon'] =  "glyphicon tree-jijum";  
		$tree_arr['icon'] =  "glyphicon fa-solid fa-building-user jijum"; 
		array_push($tree_arr_tot,$tree_arr) ;	}
//--->�� 
$sql = "SELECT a.tcode,a.tname, a.upcode, b.jcode,  c.jscode, d.bcode   FROM TEAM a
				left outer join jijum b on a.scode = b.scode and a.upcode = b.jcode  
				left outer join jisa c on a.scode = c.scode and  b.upcode = c.jscode  
				left outer join bonbu d on a.scode = d.scode and c.upcode = d.bcode  
				WHERE a.scode = '".$_SESSION['S_SCODE']."'  and  a.USEYN = '1'   order by a.num   ";
$res = sqlsrv_query( $mscon, $sql );
	while( $row = sqlsrv_fetch_array($res) ) { 
		
		unset($tree_arr); 
		$tree_arr['id'] = 'N4'.$row["tcode"];   //-->������ ���̴�.
	
 		//-->���� ���� 	������  ���ٸ�  ��Ʈ�� �ٴ´� 
		if (empty($row["jcode"]) && empty($row["jscode"]) &&   empty($row["bcode"])  ) {
			$tree_arr['parent'] = "#"; 
		}
		//-->���ΰ� �ִٸ� 
		if ( !empty($row["bcode"])   ) {               
			$tree_arr['parent'] = 'N1'.$row["upcode"];    //-->���ΰ� �ִٸ� ���� �Ʒ� 
		}
		//-->���簡 �ִٸ� 	
		if ( !empty($row["jscode"]) )    {
			$tree_arr['parent'] = 'N2'.$row["upcode"];    //--->���簡 �ִٸ� ����Ʒ�
		}
		//-->������ 	�ִٸ�
		if ( !empty($row["jcode"])   ) {
			$tree_arr['parent'] = 'N3'.$row["upcode"];    //--->�������ִٸ� �����Ʒ� 
		}
		$tree_arr['text'] =  iconv("EUCKR","UTF-8",$row["tname"]);  
		//$tree_arr['icon'] =  "glyphicon tree-team";  
		$tree_arr['icon'] =  "glyphicon fa-solid fa-building-user"; 
		array_push($tree_arr_tot,$tree_arr) ;	}

//--->��� 
$where = '';

if( $_REQUEST['sbit'] == '2'){
	$where  = " and (tbit  = '1' or  tbit = '3')  " ;
}else if( $_REQUEST['sbit'] == '3'){
	$where  = " and (tbit  = '2' or  tbit = '4')  " ;
}
	
$sql = "SELECT SKEY,BONBU,JISA,JIJUM,TEAM,SNAME ,TBIT  FROM  SWON
				WHERE scode = '".$_SESSION['S_SCODE']."'  $where 
				order by scode , 
								CASE TBIT WHEN '1' THEN 1
													WHEN '2' THEN 3
													WHEN '3' THEN 2
													WHEN '4' THEN 4
								END ASC ,	skey ";


/*
echo '<pre>';
echo $sql;
echo '</pre>';
*/ 

$res = sqlsrv_query( $mscon, $sql );
	while( $row = sqlsrv_fetch_array($res) ) { 

		unset($tree_arr); 
		$tree_arr['id'] = 'N5'.$row["SKEY"];   //-->������ ����̴�.
 		//-->���� ���� 	������ ��  ���ٸ�  ��Ʈ�� �ٴ´� 
		if (empty($row["BONBU"]) && empty($row["JISA"]) &&   empty($row["JIJUM"]) &&   empty($row["TEAM"])   ) {
			echo "tttt : ".$row["BONBU"];
			$tree_arr['parent'] = "#"; 
		}

		if (!empty($row["BONBU"])) {
				$tree_arr['parent'] = 'N1'.$row["BONBU"]; 
		}		
		if (!empty($row["JISA"])) {
				$tree_arr['parent'] = 'N2'.$row["JISA"]; 
		}	
		if (!empty($row["JIJUM"])) {
				$tree_arr['parent'] = 'N3'.$row["JIJUM"]; 
		}
		if ( !empty($row["TEAM"]))  {
 			$tree_arr['parent'] = 'N4'.$row["TEAM"]; 
		} 
		  
        $T_BIT =  substr($conf['swon_tbit'][$row['TBIT']],0,2); 
 
		if ($row["TBIT"] == '1' || $row["TBIT"] == '3') {
			$T_BIT =	"<span style='color: #fa1010;font-size: unset;'>" . $T_BIT . "</span>" ;
		}
		if ($row["TBIT"] == '2' || $row["TBIT"] == '4') {
			$T_BIT =	"<span style='color: #1e90ff;font-size: unset;'>" . $T_BIT . "</span>" ;
		}
 

		//$T_BIT = mb_substr($T_BIT,2, 'euc-kr');

		$tree_arr['text'] =  iconv("EUCKR","UTF-8",$row["SNAME"].'['.$T_BIT.']');  
		$tree_arr['icon'] =  "glyphicon fa-solid fa-user swon";  
		array_push($tree_arr_tot,$tree_arr) ;	}
// Encode:

sqlsrv_free_stmt($result);
sqlsrv_close($mscon);
 
echo json_encode($tree_arr_tot);
 
?>
