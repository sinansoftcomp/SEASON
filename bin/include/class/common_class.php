<?
/*--------------------------------------------------------------------------*
 * �ҽ����� : �������� ��� �ϴ� �Լ� ����
 *---------------------------------------------------------------------------*/




//  var $_num_ = '0123456789';
//  var $_salpha_ = "abcdefghijklmnopqrstuvwxyz";
//  var $_alpha_ = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
//  var $_error_ = "";
//  var $_errormsg_ = ""; 


/*--------------------------------------------------------------------------*
 *
 * �ڹ� ��ũ��Ʈ �����
 *
 *---------------------------------------------------------------------------*/


/*---------------------------------------------------------------------------*
 * string show_alert(msg)
 * alert() ����� �ϴ� �ڹٽ�ũ��Ʈ�� �����.
 *---------------------------------------------------------------------------*/
 function show_alert($msg) {

	 $str  = "<script type='text/javascript'>\n";
	 $str .= "window.alert('".$msg."');\n";
	 $str .= "</script>\n";
	 
	 return  $str;     
 }

/*---------------------------------------------------------------------------*
 * string show_method(method)
 * method �� �����ϴ� �ڹٽ�ũ��Ʈ�� �����.
 *---------------------------------------------------------------------------*/
 function show_method($method) {
	 
	 $str  = "<script type='text/javascript'>\n";
	 $str .= $method."\n";
	 $str .= "</script>\n";

	 return  $str;     
  }

/*---------------------------------------------------------------------------*
 * string show_msg(msg, $method)
 * alert() â�� ����� method �� �����ϴ� �ڹٽ�ũ��Ʈ�� �����.
 *---------------------------------------------------------------------------*/
 function show_msg($msg, $method) {
	 
	 $str  = "<script type='text/javascript'>\n";
	 $str .= "window.alert('".$msg."');\n";
	 $str .= $method."\n";
	 $str .= "</script>\n";

	 return  $str;
  }

/*---------------------------------------------------------------------------*
 * string show_confirm_movePage(msg, $page1, $page2)
 * confirm() â�� ����� �ش� �������� �̵��Ѵ�.
 *---------------------------------------------------------------------------*/
 function show_confirm_movePage($str, $page1, $page2) {
	 
	 $str  = "<script type='text/javascript'>\n";
	 $str .= "if(confirm('".$msg."') == true) {\n";
	 $str .="	window.location.href='".$page1."';\n";
	 $str .="} else {\n";
	 $str .="window.location.href='".$page2."';\n";
	 $str .="}\n";
	 $str .= "</script>\n";

	 return  $str;
  }

/*---------------------------------------------------------------------------*
 * string show_wait_sw($gu)
 * ��ٸ� ǥ��
 *---------------------------------------------------------------------------*/
 function show_wait_sw($gu) {
    
    $str  = "<script type='text/javascript'>\n";
    $str .= "function show_wait_show".$gu."() {\n";
    $str .= "  show_orig".$gu.".style.display = 'none';\n";
    $str .= "  show_wait".$gu.".style.display = 'block';\n";
    $str .= "}\n\n";

	$str .= "function show_wait_hide".$gu."() {\n";
    $str .= "  show_orig".$gu.".style.display = 'block';\n";
    $str .= "  show_wait".$gu.".style.display = 'none';\n";
    $str .= "}\n";
    $str .= "</script>";

	return $str;
  }

/*-------------------------------------------------------------------------------------------------------------------*
 *
 * DB ���� �Լ�
 *
 *-------------------------------------------------------------------------------------------------------------------*/

/*-------------------------------------------------------------------------------------------------------------------*
 *  Injection function
 *-------------------------------------------------------------------------------------------------------------------*/
 function SQL_Injection($str) {
  return preg_replace("( select| union| insert| update| delete| drop|\"|\'|#|\/\*|\*\/|\\\|\;)","", $str);
}




/*--------------------------------------------------------------------------*
 *
 * ��¥ �Լ�
 *
 *---------------------------------------------------------------------------*/

 /*--------------------------------------------------------------------------*
  * string get_year()
  * ������ �⵵�� ����
  *--------------------------------------------------------------------------*/
  function get_year($str="") {
	  return date("Y",time());
  }

 /*--------------------------------------------------------------------------*
  * string get_month($digit=2)
  * ������ ���� ����
  *--------------------------------------------------------------------------*/
  function get_month($digit=2) {

	  if ($digit = 2) $format = "m";
	  else $format = "n";

	  return date($format,time());
  }

 /*--------------------------------------------------------------------------*
  * string get_day($digit=2)
  * ������ ���� ����
  *--------------------------------------------------------------------------*/
  function get_day($digit=2) {

	  if ($digit = 2) $format = "d";
	  else $format = "j";

	  return date($format,time());
  }

 /*--------------------------------------------------------------------------*
  * ����������³ֱ�
  *--------------------------------------------------------------------------*/
  function view_snum($no) {
	return preg_replace("/([0-9]{3})([0-9]{2})([0-9]{5})/", "\\1-\\2-\\3", $no); 
  }

// ����� ��ȣ üũ
function check_company_number($no)
{
    if(!trim($no))
        return '����� ��Ϲ�ȣ�� �Է��� �ֽʽÿ�.';

    if(!preg_match('#^[0-9]{3}-[0-9]{2}-[0-9]{5}$#', $no))
        return '����� ��Ϲ�ȣ�� �ùٸ� ����(123-45-67890)���� �Է��� �ֽʽÿ�.';

    $num = preg_replace('/[^0-9]/', '', $no);

    $att = 0;
    $sum = 0;
    $arr = array(1, 3, 7, 1, 3, 7, 1, 3, 5);
    $cnt = count($arr);

    for($i=0; $i<$cnt; $i++) {
        $sum += ($num[$i] * $arr[$i]);
    }

    $sum += intval(($num[8] * 5) / 10);

    $at = $sum % 10;
    if ($at != 0)
        $att = 10 - $at;

    if ($num[9] != $att)
       return $no . ' �� �ùٸ� ����ڵ�Ϲ�ȣ�� �ƴմϴ�.';
}




 /*-------------------------------------------------------------------------------------------------------------------*
  *  string make_paging_default ($total_record, $page, $page_size, $block_size, $link, $table_width, $table_align, $table_color)
  *  ����¡ �����.
  *-------------------------------------------------------------------------------------------------------------------*/
 function make_paging_default ($total_record, $page, $page_size, $block_size, $link, $table_width, $table_align, $table_color, $img_url) {

	$total_page = ceil($total_record / $page_size);
	$total_block = ceil($total_page / $block_size);
	$block = ceil($page / $block_size);

	if (strpos($link,"?")) $link .= "&";
	else $link .= "?";


	if ($block > 1){
		$html .= "<li><a href='".$link."page=1'><img src='".$img_url."/firstpage.gif'></a></li>";
	}else{
		$html .= "<li><a href='#'><img src='".$img_url."/firstpage.gif'></a></li>";
	}

	if ($block > 1) {
		$html .= "<li><a href='".$link."page=".(($block-1)*$block_size)."'><img src='".$img_url."/prev.gif'></a></li>";
	}else{
		$html .= "<li><a href='#'><img src='".$img_url."/prev.gif'></a></li>";
	}

	for ($i=($block-1)*$block_size+1; $i<=$block*$block_size; $i++){
		if ($i > $total_page) break;

		if ($i == $page) $html .= "<li><a href='#' class='on'>".$i."</a></li>";
		else $html .= "<li><a href='".$link."page=".$i."'>".$i."</a></li>";
	}

//		echo "1 :".$i."<br>";
//		echo "2 :" .$block."<br>";
//		echo "3 : ".$block_size."<br>";
//		echo "4 : ".$total_page."<br>";
//		echo "5 : ".$page."<br>";
//		echo "6 : ".$page_size."<br>";


	if($block < $total_block){
		$html .= "<li><a href='".$link."page=".($block*$block_size+1)."'><img src='".$img_url."/next.gif'></a></li>";
	}else{
		$html .= "<li><a href='#'><img src='".$img_url."/next.gif'</a></li>";
	}

	if($block < $total_block){
		$html .= "<li><a href='".$link."page=".$total_page."'><img src='".$img_url."/endpage.gif'></a></li>";
	}else{
		$html .= "<li><a href='#'><img src='".$img_url."/endpage.gif'></a></li>";
	}

	

//	 $html .= "</td></tr>\n";
//	 $html .= "</table>\n";
	
	

	$paging[srec] = $total_record - ($page-1) * $page_size;
	$paging[limit_e] = $page * $page_size;
	$paging[limit_s] = $paging[limit_e] - ($page_size -1);
	$paging[html] = $html;
	$paging[total_page] = $total_page;
	$paging[total_block] = $total_block;



	return $paging;

  }


/************************************************************
������ ���� Ȯ�� 
������ ���̺� ���� auto commit ������!
************************************************************/
function sequence_code_tbl($str_tbl,$dbcon){
	
	$cond = " WHERE 1=1 AND SEQ_NAME = '".$str_tbl."' ";

	$query =" SELECT COUNT(SEQ_NAME),(NVL(MAX(SEQ_NO),0)+1) seq_no FROM tb_sequence ". $cond;
	
	$result = oci_parse($dbcon,$query);
	oci_execute($result);
	$row = oci_fetch_row($result);
	$myrec[tbl_count] = $row[0];
	$myrec[tbl_seq_no] = $row[1];

	//echo $query."<br>";
	if ($myrec[tbl_count] > 0) {
		$query = " UPDATE tb_sequence SET SEQ_NO=".$myrec[tbl_seq_no]. $cond;
	}else{
		$query = " INSERT INTO tb_sequence(SEQ_NAME,SEQ_NO) VALUES ('".$str_tbl."',1)";
	}

	$result = oci_parse($dbcon,$query);
	$return_result = oci_execute($result,OCI_DEFAULT);
	if ($return_result == false){
		echo (show_msg("�Է»����� �����׸��� �ֽ��ϴ�.\\n�Է»����� Ȯ���� �ּ���.",""));
		oci_rollback($dbcon);
		exit;
	}
	return $myrec;
}


function right($string, $cnt){
/*****************************************************************
���ڿ��� �����ʺ��� ������ ����ŭ�� ���ڸ� ��ȯ�Ѵ�.
*****************************************************************/
  $string = substr($string, (strlen($string) - $cnt), strlen($string));
  return $string;
}


/*****************************************************************
�⵵ / �� / ���� ���ý� �ش������� ������ ��ȯ
*****************************************************************/
function week_search_from($year, $month, $week){

	$last_month_endday = mktime(0,0,0, $month-1, date('t', mktime(0, 0, 0, $month-1, 1, $year)), $year);


	// ������ �� ����  ������ ��¥�� ���� ���Ѵ�.
	$last_month_endday_weekday = date('N', $last_month_endday);

	//�׸��� �� ��¥�� ������ ���Ѵ�.
	$monday_interval =  abs(1-$last_month_endday_weekday);
	$week_start= $last_month_endday - (60*60*24)*$monday_interval;

	// ������ ��¥�� ���� ���� ������
	if($last_month_endday_weekday > 3){
		// ������ �� ����  ������ ��¥�� �����Ϻ��� ũ��
		$search_from = date('Y-m-d', $week_start + (60*60*24)*7*$week);
	}else{
		$search_from = date('Y-m-d', $week_start + (60*60*24)*7*($week-1));
	}

	return $search_from;

}


/*****************************************************************
�⵵ / �� / ���� ���ý� �ش������� ������ ��ȯ
*****************************************************************/
function week_search_to($year, $month, $week){

	$last_month_endday = mktime(0,0,0, $month-1, date('t', mktime(0, 0, 0, $month-1, 1, $year)), $year);
	// ������ �� ����  ������ ��¥
	$last_month_endday_weekday = date('N', $last_month_endday);
	$sunday_interval = 7-$last_month_endday_weekday;
	$week_end= $last_month_endday+(60*60*24)*$sunday_interval;

	// ������ ��¥�� ���� ���� �Ͽ���
	if($last_month_endday_weekday > 3){
		// ������ �� ����  ������ ��¥�� �����Ϻ��� ũ��          
		$search_to = date('Y-m-d', $week_end + (60*60*24) *7*$week + (60*60*23)+(60*59));
		//�Ͽ����� �����ϰ� �޸� 00:00�ð� �ƴ϶� 23�� 59���̾�� �ϹǷ� �������� (60*60*23)+(60*59)�� ���ߴ�.
	}else{
		$search_to = date('Y-m-d', $week_end +(60*60*24)*7*($week-1)+(60*60*23)+(60*59));
		//�Ͽ����� �����ϰ� �޸� 00:00�ð� �ƴ϶� 23�� 59���̾�� �ϹǷ� �������� (60*60*23)+(60*59)�� ���ߴ�.
	}

	return $search_to;
}

// �������ϱ� & ���������� �� ���������� ���ϱ�
$year  = date("Y");
$month = date("m");
$day   = date("d");

//�ش����
$dayOfTheWeek = date('w',mktime(0,0,0,$month,$day,$year)); 
$dayOfTheWeek1 = date('w',strtotime($year.$month.$day)); 

//�������ϱ� 
$week = date('W',mktime(0,0,0,$month,$day,$year)); 
$week1 = date('W',strtotime($year.$month.$day)); 

//�ش������� ������ 
$sd = mktime(0,0,0,$month,$day-$dayOfTheWeek,$year); 
$sd1 = date("Y-m-d",$sd); 

//�ش������� ������ 
$ed = mktime(23,59,59,$month,$day+(6-$dayOfTheWeek),$year); 
$ed1 = date("Y-m-d",$ed); 

//�ش���� ������ 
$msd = mktime(23,59,59,$month,1,$year); 
$msd1 = date("Y-m-d",$msd);

//�ش���� ������ ��¥ 
$med = mktime(23,59,59,$month,date("t",$msd),$year); 
$med1 = date("Y-m-d",$med);

?>