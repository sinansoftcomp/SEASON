
<?php
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");
 

 /* jstree ����
{"id":"H00010","parent":"#","text":"\ub300\uad6c\ubcf8\ubd80","icon":"glyphicon glyphicon-home"}
"id"  = ���� �����ΰ�  "H00010" �̴� 
"parent" = ���� �θ�� �����ΰ� "#" �̴�. ��Ʈ�� "#"����. ��Ʈ�� �ƴϸ� ���� �θ� "id"
"text"    = Ʈ���� display ���� .
"icon"   = ǥ���� ������.
 */
 
$tree_arr= array();
$tree_arr_tot= array();


unset($tree_arr); 
$tree_arr['id'] ="N10000";            
$tree_arr['parent'] =  "#";  
$tree_arr['text'] =  iconv("EUCKR","UTF-8","����������");  
$tree_arr['icon'] =  "glyphicon fa-solid fa-building-user jisa";  
array_push($tree_arr_tot,$tree_arr) ;	

//-->���� 
$sql = "SELECT *  FROM INSSETUP where    scode = '".$_SESSION['S_SCODE']."'  and  USEYN = 'Y' order by num  ";
$res = sqlsrv_query( $mscon, $sql );
	while( $row = sqlsrv_fetch_array($res) ) { 
		unset($tree_arr); 
		$tree_arr['id'] = 'N2'.$row["INSCODE"];            
		$tree_arr['parent'] =  "N10000";  
		$tree_arr['text'] =  iconv("EUCKR","UTF-8",$row["NAME"]);  
		$tree_arr['icon'] =  "glyphicon fa-solid fa-building-user jijum";  
		array_push($tree_arr_tot,$tree_arr) ;	
		}

sqlsrv_free_stmt($result);
sqlsrv_close($mscon);
 
echo json_encode($tree_arr_tot);
 
?>
