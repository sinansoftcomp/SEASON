
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


//--->�������赵 
$sql = "select a.SKEY,e.BNAME ,f.JSNAME ,g.JNAME,h.TNAME,a.SNAME ,a.TBIT,a.MCODE  FROM  SWON a 
				left outer join bonbu e on a.scode = e.scode and a.bonbu = e.bcode
				left outer join jisa  f on a.scode = f.scode and a.jisa = f.jscode
				left outer join jijum g on a.scode = g.scode and a.jijum = g.jcode
				left outer join team h  on a.scode = h.scode and a.team = h.tcode
				WHERE a.scode = '".$_SESSION['S_SCODE']."' order by a.scode , a.skey ";

//print_r($sql);
$res = sqlsrv_query( $mscon, $sql );
	while( $row = sqlsrv_fetch_array($res) ) { 

		unset($tree_arr); 

        $T_BIT =  $conf['swon_tbit'][$row['TBIT']]; 
		//$JOJIK  =  '>'. $row["BNAME"] .'>'. $row["JSNAME"] . '>'. $row["JNAME"] .'>'. $row["TNAME"] ;      
		$JOJIK  =  '>'. $row["JSNAME"] ;      
		if (empty($row["MCODE"])) {
			$tree_arr['id'] = $row["SKEY"];  
			$tree_arr['parent'] = "#"; 
			$tree_arr['text'] =  iconv("EUCKR","UTF-8",$row["SNAME"].'['.$T_BIT.']' .$JOJIK     );  
			$tree_arr['icon'] =  "glyphicon fa-solid fa-user swon";  
			array_push($tree_arr_tot,$tree_arr) ;	 
	    }else{		
			$tree_arr['id'] = $row["SKEY"];  
			$tree_arr['parent'] = $row["MCODE"];  
			$tree_arr['text'] =  iconv("EUCKR","UTF-8",$row["SNAME"].'['.$T_BIT.']'.$JOJIK  );  
			$tree_arr['icon'] =  "glyphicon fa-solid fa-user swon";  
			array_push($tree_arr_tot,$tree_arr) ; 
		}
	}
sqlsrv_free_stmt($result);
sqlsrv_close($mscon);
 
echo json_encode($tree_arr_tot);
 
?>
