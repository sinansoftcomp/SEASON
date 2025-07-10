<?

/*************************************************************************
**
**  �Ϲ� �Լ� ����
**
*************************************************************************/

// ����ũ�� Ÿ���� ��� ��� �������� ����
function get_microtime()
{
    list($usec, $sec) = explode(" ",microtime());
    return ((float)$usec + (float)$sec);
}


// ��Ÿ�±׸� �̿��� URL �̵�
// header("location:URL") �� ��ü
function goto_url($url)
{
    echo "<script type='text/javascript'> location.replace('$url'); </script>";
    exit;
}
function goto_url_opener($url)
{
    echo "<script type='text/javascript'> opener.location.replace('$url'); </script>";
    exit;
}
function goto_url_parent($url)
{
    echo "<script type='text/javascript'> parent.location.replace('$url'); </script>";
    exit;
}

//openFG �ؽ�Ʈ ���
function openFGText($openFG){
	if($openFG=="Y") return "����";
	elseif($openFG!="Y") return "�����";
}

function yesOrNoText($openFG){
	if($openFG=="Y") return "��";
	elseif($openFG=="N") return "�ƴϿ�";
}


// ��Ű���� ����
function set_cookie($cookie_name, $value, $expire)
{
    global $G_CONF;
   setcookie(md5($cookie_name), base64_encode($value), time() + $expire, '/', $G_CONF[cookie_domain]);
}


// ��Ű������ ����
function get_cookie($cookie_name)
{
    return base64_decode($_COOKIE[md5($cookie_name)]);
}


// ���޼����� ���â����
function alert($msg='', $url='', $target='')
{
    if (!$msg) $msg = '�ùٸ� ������� �̿��� �ֽʽÿ�1.';

	echo "<meta http-equiv='Content-Type' content='text/html; charset=euc-kr' />";
    if (!$url){
    	echo "<script type='text/javascript'>alert('$msg');";
        echo "history.back();";
		echo "</script>";
		exit;
	}
    elseif ($url){
		// 4.06.00 : �ҿ����� ��� �Ʒ��� �ڵ带 ����� �ν����� ����
        //echo "<meta http-equiv='refresh' content='0;url=$url'>";
        echo "<script type='text/javascript'>alert('$msg');</script>";
		if($target=="opener"){
			goto_url_opener($url);
		}elseif($target=="parent"){
			goto_url_parent($url);
		}elseif($url=="close"){
			echo "<script type='text/javascript'>self.close();</script>";
		}else{
			goto_url($url);
		}
	    exit;
    }
}


// ���޼��� ����� â�� ����
function alert_close($msg)
{
    echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' /><script type='text/javascript'> alert('$msg'); window.close(); </script>";
    exit;
}

// ���ú��� �ܿ��� ���ϱ�
function getDayCount($ymd){
	$time	= strtotime($ymd)- strtotime(date("Y-m-d"));
	return (int)($time/86400);
}

// ���� ���ؼ� ���ϱ� ���ϱ� 
function getYmdPlusData($ymd, $plus_y, $plus_m, $plus_d){
	$ymdTemp	= str_replace("-","",$ymd);

	return date("Y-m-d", mktime (0,0,0,substr($ymdTemp,4,2)+$plus_m  , substr($ymdTemp,6,2)+$plus_d, substr($ymdTemp,0,4)+$plus_y));
}

// �ش����� ���� ���ϱ�
function getMonthWeekNum($ymd){
	$temp	= strtotime($ymd);

	$preYm	= date("Y-m", mktime(0, 0, 0, date("m",$temp)-1  , date("d",$temp), date("Y",$temp)));
	$weekNum	=  date("W",$temp) - date("W",strtotime($preYm."-".(date("t",strtotime($preYm))-date("w", strtotime($preYm."-".date("t",strtotime($preYm)))))));

	if($weekNum<0){
		$weekNum	= (int)date("W",$temp);
	}
	return $weekNum;

}

// ���ϱ��ϱ�
function getWeekName($ymd){
	$weekNum	= date("w", strtotime($ymd));

	if($weekNum==0) return "��";
	elseif($weekNum==1) return "��";
	elseif($weekNum==2) return "ȭ";
	elseif($weekNum==3) return "��";
	elseif($weekNum==4) return "��";
	elseif($weekNum==5) return "��";
	elseif($weekNum==6) return "��";
}


// ������ �뷮�� ���Ѵ�.
//function get_filesize($file)
function get_filesize($size)
{
    //$size = @filesize(addslashes($file));
    if ($size >= 1048576) {
        $size = number_format($size/1048576, 1) . "M";
    } else if ($size >= 1024) {
        $size = number_format($size/1024, 1) . "K";
    } else {
        $size = number_format($size, 0) . "byte";
    }
    return $size;
}



// TEXT �������� ��ȯ
function get_text($str, $html=0)
{
    /* 3.22 ���� (HTML üũ �ٹٲ޽� ��� ��������)
    $source[] = "/  /";
    $target[] = " &nbsp;";
    */

    // 3.31
    // TEXT ����� ��� &amp; &nbsp; ���� �ڵ带 �������� ����� �ֱ� ����
    if ($html == 0) {
        $str = html_symbol($str);
    }

    $source[] = "/</";
    $target[] = "&lt;";
    $source[] = "/>/";
    $target[] = "&gt;";
    //$source[] = "/\"/";
    //$target[] = "&#034;";
    $source[] = "/\'/";
    $target[] = "&#039;";
    //$source[] = "/}/"; $target[] = "&#125;";
    if ($html) {
        $source[] = "/\n/";
        $target[] = "<br/>";
    }

    return preg_replace($source, $target, $str);
}


// 3.31
// HTML SYMBOL ��ȯ
// &nbsp; &amp; &middot; ���� �������� ���
function html_symbol($str)
{
    return preg_replace("/\&([a-z0-9]{1,20}|\#[0-9]{0,3});/i", "&#038;\\1;", $str);
}


/*************************************************************************
**
**  SQL ���� �Լ� ����
**
*************************************************************************/


// �ѱ� ����
function get_yoil($date, $full=0)
{
    $arr_yoil = array ("��", "��", "ȭ", "��", "��", "��", "��");

    $yoil = date("w", strtotime($date));
    $str = $arr_yoil[$yoil];
    if ($full) {
        $str .= "����";
    }
    return $str;
}


// ��¥�� select �ڽ� �������� ��´�
function date_select($date, $name="")
{
    global $G_CONF;

    $s = "";
    if (substr($date, 0, 4) == "0000") {
        $date = $G_CONF['time_ymdhis'];
    }
    preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})/", $date, $m);

    // ��
    $s .= "<select name='{$name}_y'>";
    for ($i=$m[0]-3; $i<=$m[0]+3; $i++) {
        $s .= "<option value='$i'";
        if ($i == $m[0]) {
            $s .= " selected";
        }
        $s .= ">$i";
    }
    $s .= "</select>�� \n";

    // ��
    $s .= "<select name='{$name}_m'>";
    for ($i=1; $i<=12; $i++) {
        $s .= "<option value='$i'";
        if ($i == $m[2]) {
            $s .= " selected";
        }
        $s .= ">$i";
    }
    $s .= "</select>�� \n";

    // ��
    $s .= "<select name='{$name}_d'>";
    for ($i=1; $i<=31; $i++) {
        $s .= "<option value='$i'";
        if ($i == $m[3]) {
            $s .= " selected";
        }
        $s .= ">$i";
    }
    $s .= "</select>�� \n";

    return $s;
}


// �ð��� select �ڽ� �������� ��´�
// 1.04.00
// ��ſ� �ð� ������ �����ϰ� �Ǹ鼭 �߰���
function time_select($time, $name="")
{
    preg_match("/([0-9]{2}):([0-9]{2}):([0-9]{2})/", $time, $m);

    // ��
	$s	= "";
    $s .= "<select name='{$name}_h'>";
    for ($i=0; $i<=23; $i++) {
        $s .= "<option value='$i'";
        if ($i == $m[0]) {
            $s .= " selected";
        }
        $s .= ">$i";
    }
    $s .= "</select>�� \n";

    // ��
    $s .= "<select name='{$name}_i'>";
    for ($i=0; $i<=59; $i++) {
        $s .= "<option value='$i'";
        if ($i == $m[2]) {
            $s .= " selected";
        }
        $s .= ">$i";
    }
    $s .= "</select>�� \n";

    // ��
    $s .= "<select name='{$name}_s'>";
    for ($i=0; $i<=59; $i++) {
        $s .= "<option value='$i'";
        if ($i == $m[3]) {
            $s .= " selected";
        }
        $s .= ">$i";
    }
    $s .= "</select>�� \n";

    return $s;
}




// �Ǽ��±� ��ȯ
function bad_tag_convert($code)
{
    global $view;
    global $member, $is_admin;

    if ($is_admin && $member[mb_id] != $view[mb_id]) {
        //$code = preg_replace_callback("#(\<(embed|object)[^\>]*)\>(\<\/(embed|object)\>)?#i",
        // embed �Ǵ� object �±׸� ���� �ʴ� ��� ���͸��� �ǵ��� ����
        $code = preg_replace_callback("#(\<(embed|object)[^\>]*)\>?(\<\/(embed|object)\>)?#i",
                    create_function('$matches', 'return "<div class=\"embedx\">���ȹ����� ���Ͽ� ������ ���̵�δ� embed �Ǵ� object �±׸� �� �� �����ϴ�. Ȯ���Ͻ÷��� ���������� ���� �ٸ� ���̵�� �����ϼ���.</div>";'),
                    $code);
    }

    //return preg_replace("/\<([\/]?)(script|iframe)([^\>]*)\>/i", "&lt;$1$2$3&gt;", $code);
    // script �� iframe �±׸� ���� �ʴ� ��� ���͸��� �ǵ��� ����
    return preg_replace("/\<([\/]?)(script|iframe)([^\>]*)\>?/i", "&lt;$1$2$3&gt;", $code);
}


// �ҹ������� ������ ��ū�� �����ϸ鼭 ��ū���� ����
function get_token()
{
    $token = md5(uniqid(rand(), true));
    set_session("ss_token", $token);

    return $token;
}


// POST�� �Ѿ�� ��ū�� ���ǿ� ����� ��ū ��
function check_token($url=FALSE) {
	set_session('ss_token', '');
    return true;

	// ���ǿ� ����� ��ū�� �������� �Ѿ�� ��ū�� ���Ͽ� Ʋ���� ����
	if ($_POST['token'] && get_session('ss_token') == $_POST['token']) {
		// ������ ������ �����. ������ ����� ������ ���ο� ���� ���� �ٽ� �������� �ϱ� ����
		unset($_SESSION['ss_token']);
	}
	else
		alert('Access Error',($url ? $url : $_SERVER['HTTP_REFERER']));
}


// ���ڿ��� utf8 ���ڰ� ��� �ִ��� �˻��ϴ� �Լ�
// �ڵ� : http://in2.php.net/manual/en/function.mb-check-encoding.php#95289
function is_utf8($str)
{
    $len = strlen($str);
    for($i = 0; $i < $len; $i++) {
        $c = ord($str[$i]);
        if ($c > 128) {
            if (($c > 247)) return false;
            elseif ($c > 239) $bytes = 4;
            elseif ($c > 223) $bytes = 3;
            elseif ($c > 191) $bytes = 2;
            else return false;
            if (($i + $bytes) > $len) return false;
            while ($bytes > 1) {
                $i++;
                $b = ord($str[$i]);
                if ($b < 128 || $b > 191) return false;
                $bytes--;
            }
        }
    }
    return true;
}


function mb_id_encode($mb_id) {
	return urlencode(base64_encode($mb_id));
}

function mb_id_decode($mb_id) {
	return base64_decode(urldecode($mb_id));
}


function checkNewFG($datetime){
	if(strtotime($datetime)>=(time()-(86400*7))) return true;
	else return false;
}

function formatSizeUnits($bytes)
{
	if ($bytes >= 1073741824)
	{
		$bytes = number_format($bytes / 1073741824, 2) . ' GB';
	}
	elseif ($bytes >= 1048576)
	{
		$bytes = number_format($bytes / 1048576, 2) . ' MB';
	}
	elseif ($bytes >= 1024)
	{
		$bytes = number_format($bytes / 1024, 2) . ' KB';
	}
	elseif ($bytes > 1)
	{
		$bytes = $bytes . ' bytes';
	}
	elseif ($bytes == 1)
	{
		$bytes = $bytes . ' byte';
	}
	else
	{
		$bytes = '0 bytes';
	}

	return $bytes;
}


function getInitial($str){
    if($str<'��') return "��Ÿ";
	elseif($str>='��' && $str<'��') return "��";
	elseif($str>='��' && $str<'��') return "��";
	elseif($str>='��' && $str<'��') return "��";
	elseif($str>='��' && $str<'��') return "��";
	elseif($str>='��' && $str<'��') return "��";
	elseif($str>='��' && $str<'��') return "��";
	elseif($str>='��' && $str<'��') return "��";
	elseif($str>='��' && $str<'��') return "��";
	elseif($str>='��' && $str<'��') return "��";
	elseif($str>='��' && $str<'ī') return "��";
	elseif($str>='ī' && $str<'Ÿ') return "��";
	elseif($str>='Ÿ' && $str<'��') return "��";
	elseif($str>='��' && $str<'��') return "��";
	elseif($str>='��') return "��";
}


function phoneNumber($num){
	return substr($num,0,3)."-".substr($num,3,-4)."-".substr($num,-4);
}

function phoneBarClear($num){
	return trim(str_replace("--","",$num));
}

function encodeUtf8($arrData){
	foreach($arrData as $key => $val){
		if(is_array($val)){
			$rtnData[$key]	= $val;
		}else{

			$rtnData[$key]	= iconv("EUCKR","UTF-8",$val);
		}
	}
	return $rtnData;
}


function thumbuploadimagecreate($img, $thumbImage, $w, $h=0){
	$imageName_arr	= explode("/",$img);
	$imageName	= $imageName_arr[count($imageName_arr)-2]."_".$imageName_arr[count($imageName_arr)-1];

	$count    = strrpos($img,'.'); 
	$extention = strtolower(substr($img, $count+1)); 

	switch($extention){ 
		case 'gif': $image = imagecreatefromgif($img); break; 
		case 'png': $image = imagecreatefrompng($img); break; 
		case 'jpeg': 
		case 'jpg':    $image = imagecreatefromjpeg($img); break; 
		default : return ""; 
	}

	$filename = $thumbImage;

	$width = imagesx($image);
	$height = imagesy($image);


	$thumb_width = $w;
	$thumb_height = ($h==0) ? (int)($height*($thumb_width/$width)) : $h;


	$original_aspect = $width / $height;
	$thumb_aspect = $thumb_width / $thumb_height;

	if ( $original_aspect >= $thumb_aspect )
	{
	   // If image is wider than thumbnail (in aspect ratio sense)
	   $new_height = $thumb_height;
	   $new_width = $width / ($height / $thumb_height);
	}
	else
	{
	   // If the thumbnail is wider than the image
	   $new_width = $thumb_width;
	   $new_height = $height / ($width / $thumb_width);
	}

	$thumb = imagecreatetruecolor( $thumb_width, $thumb_height );

	// Resize and crop
	imagecopyresampled($thumb,
					   $image,
					   0 - ($new_width - $thumb_width) / 2, // Center the image horizontally
					   0 - ($new_height - $thumb_height) / 2, // Center the image vertically
					   0, 0,
					   $new_width, $new_height,
					   $width, $height);
	switch($extention){ 
		case 'gif': imagegif($thumb,$filename); break; 
		case 'png': imagepng($thumb,$filename); break; 
		case 'jpg': 
		case 'jpeg': imagejpeg($thumb,$filename); break; 
		default : break; 
	}

	return $filename;
}



function thumbuploadimagecreates($img, $thumbImage, $w, $h=0){

	echo $img."111"."<br>";
		echo $thumbImage."<br>";	
	$imageName_arr	= explode("/",$img);
	$imageName	= $imageName_arr[count($imageName_arr)-2]."_".$imageName_arr[count($imageName_arr)-1];

	echo $imageName."<br>";	

	$count    = strrpos($img,'.'); 

	echo $count."<br>";	
	$extention = strtolower(substr($img, $count+1)); 

	echo $extention."<br>";	

	switch($extention){
		case 'gif': $image = imagecreatefromgif($img); break; 
		case 'png': $image = imagecreatefrompng($img); break; 
		case 'jpeg': 
		case 'jpg':    $image = imagecreatefromjpeg($img); break; 
		default : return ""; 
	}

//	$image = imagerotate($image,90,0);

	$filename = $thumbImage;

	$width = imagesx($image);
	$height = imagesy($image);
echo $width."<br>";	
echo $height."<br>";

	$thumb_width = $w;
	$thumb_height = ($h==0) ? (int)($height*($thumb_width/$width)) : $h;


echo $thumb_width."<br>";	
echo $thumb_height."<br>";

	$original_aspect = $width / $height;
	$thumb_aspect = $thumb_width / $thumb_height;

	if ( $original_aspect >= $thumb_aspect )
	{
		
	   // If image is wider than thumbnail (in aspect ratio sense)
	   $new_height = $thumb_height;
	   $new_width = $width / ($height / $thumb_height);
	}
	else
	{
		echo $original_aspect."<br>";	
		echo $thumb_aspect."<br>";
	   // If the thumbnail is wider than the image
	   $new_width = $thumb_width;
	   $new_height = $height / ($width / $thumb_width);
	}

	$thumb = imagecreatetruecolor( $thumb_width, $thumb_height );

echo $thumb_width."<br>";
echo $thumb_height."<br>";


	// Resize and crop
	imagecopyresampled($thumb,
					   $image,
					   0 - ($new_width - $thumb_width) / 2, // Center the image horizontally
					   0 - ($new_height - $thumb_height) / 2, // Center the image vertically
					   0, 0,
					   $new_width, $new_height,
					   $width, $height);
	switch($extention){ 
		case 'gif': imagegif($thumb,$filename); break; 
		case 'png': imagepng($thumb,$filename); break; 
		case 'jpg': 
		case 'jpeg': imagejpeg($thumb,$filename); break; 
		default : break; 
	}

	return $filename;
}

function selected($val1,$val2){
	if($val1==$val2) return "selected";
}
function checked($val1,$val2){
	if($val1==$val2) return "checked";
}