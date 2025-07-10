<?

/*************************************************************************
**
**  일반 함수 모음
**
*************************************************************************/

// 마이크로 타임을 얻어 계산 형식으로 만듦
function get_microtime()
{
    list($usec, $sec) = explode(" ",microtime());
    return ((float)$usec + (float)$sec);
}


// 메타태그를 이용한 URL 이동
// header("location:URL") 을 대체
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

//openFG 텍스트 출력
function openFGText($openFG){
	if($openFG=="Y") return "공개";
	elseif($openFG!="Y") return "비공개";
}

function yesOrNoText($openFG){
	if($openFG=="Y") return "예";
	elseif($openFG=="N") return "아니요";
}


// 쿠키변수 생성
function set_cookie($cookie_name, $value, $expire)
{
    global $G_CONF;
   setcookie(md5($cookie_name), base64_encode($value), time() + $expire, '/', $G_CONF[cookie_domain]);
}


// 쿠키변수값 얻음
function get_cookie($cookie_name)
{
    return base64_decode($_COOKIE[md5($cookie_name)]);
}


// 경고메세지를 경고창으로
function alert($msg='', $url='', $target='')
{
    if (!$msg) $msg = '올바른 방법으로 이용해 주십시오1.';

	echo "<meta http-equiv='Content-Type' content='text/html; charset=euc-kr' />";
    if (!$url){
    	echo "<script type='text/javascript'>alert('$msg');";
        echo "history.back();";
		echo "</script>";
		exit;
	}
    elseif ($url){
		// 4.06.00 : 불여우의 경우 아래의 코드를 제대로 인식하지 못함
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


// 경고메세지 출력후 창을 닫음
function alert_close($msg)
{
    echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' /><script type='text/javascript'> alert('$msg'); window.close(); </script>";
    exit;
}

// 오늘부터 잔여일 구하기
function getDayCount($ymd){
	$time	= strtotime($ymd)- strtotime(date("Y-m-d"));
	return (int)($time/86400);
}

// 날자 더해서 구하기 구하기 
function getYmdPlusData($ymd, $plus_y, $plus_m, $plus_d){
	$ymdTemp	= str_replace("-","",$ymd);

	return date("Y-m-d", mktime (0,0,0,substr($ymdTemp,4,2)+$plus_m  , substr($ymdTemp,6,2)+$plus_d, substr($ymdTemp,0,4)+$plus_y));
}

// 해당일자 주차 구하기
function getMonthWeekNum($ymd){
	$temp	= strtotime($ymd);

	$preYm	= date("Y-m", mktime(0, 0, 0, date("m",$temp)-1  , date("d",$temp), date("Y",$temp)));
	$weekNum	=  date("W",$temp) - date("W",strtotime($preYm."-".(date("t",strtotime($preYm))-date("w", strtotime($preYm."-".date("t",strtotime($preYm)))))));

	if($weekNum<0){
		$weekNum	= (int)date("W",$temp);
	}
	return $weekNum;

}

// 요일구하기
function getWeekName($ymd){
	$weekNum	= date("w", strtotime($ymd));

	if($weekNum==0) return "일";
	elseif($weekNum==1) return "월";
	elseif($weekNum==2) return "화";
	elseif($weekNum==3) return "수";
	elseif($weekNum==4) return "목";
	elseif($weekNum==5) return "금";
	elseif($weekNum==6) return "토";
}


// 파일의 용량을 구한다.
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



// TEXT 형식으로 변환
function get_text($str, $html=0)
{
    /* 3.22 막음 (HTML 체크 줄바꿈시 출력 오류때문)
    $source[] = "/  /";
    $target[] = " &nbsp;";
    */

    // 3.31
    // TEXT 출력일 경우 &amp; &nbsp; 등의 코드를 정상으로 출력해 주기 위함
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
// HTML SYMBOL 변환
// &nbsp; &amp; &middot; 등을 정상으로 출력
function html_symbol($str)
{
    return preg_replace("/\&([a-z0-9]{1,20}|\#[0-9]{0,3});/i", "&#038;\\1;", $str);
}


/*************************************************************************
**
**  SQL 관련 함수 모음
**
*************************************************************************/


// 한글 요일
function get_yoil($date, $full=0)
{
    $arr_yoil = array ("일", "월", "화", "수", "목", "금", "토");

    $yoil = date("w", strtotime($date));
    $str = $arr_yoil[$yoil];
    if ($full) {
        $str .= "요일";
    }
    return $str;
}


// 날짜를 select 박스 형식으로 얻는다
function date_select($date, $name="")
{
    global $G_CONF;

    $s = "";
    if (substr($date, 0, 4) == "0000") {
        $date = $G_CONF['time_ymdhis'];
    }
    preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})/", $date, $m);

    // 년
    $s .= "<select name='{$name}_y'>";
    for ($i=$m[0]-3; $i<=$m[0]+3; $i++) {
        $s .= "<option value='$i'";
        if ($i == $m[0]) {
            $s .= " selected";
        }
        $s .= ">$i";
    }
    $s .= "</select>년 \n";

    // 월
    $s .= "<select name='{$name}_m'>";
    for ($i=1; $i<=12; $i++) {
        $s .= "<option value='$i'";
        if ($i == $m[2]) {
            $s .= " selected";
        }
        $s .= ">$i";
    }
    $s .= "</select>월 \n";

    // 일
    $s .= "<select name='{$name}_d'>";
    for ($i=1; $i<=31; $i++) {
        $s .= "<option value='$i'";
        if ($i == $m[3]) {
            $s .= " selected";
        }
        $s .= ">$i";
    }
    $s .= "</select>일 \n";

    return $s;
}


// 시간을 select 박스 형식으로 얻는다
// 1.04.00
// 경매에 시간 설정이 가능하게 되면서 추가함
function time_select($time, $name="")
{
    preg_match("/([0-9]{2}):([0-9]{2}):([0-9]{2})/", $time, $m);

    // 시
	$s	= "";
    $s .= "<select name='{$name}_h'>";
    for ($i=0; $i<=23; $i++) {
        $s .= "<option value='$i'";
        if ($i == $m[0]) {
            $s .= " selected";
        }
        $s .= ">$i";
    }
    $s .= "</select>시 \n";

    // 분
    $s .= "<select name='{$name}_i'>";
    for ($i=0; $i<=59; $i++) {
        $s .= "<option value='$i'";
        if ($i == $m[2]) {
            $s .= " selected";
        }
        $s .= ">$i";
    }
    $s .= "</select>분 \n";

    // 초
    $s .= "<select name='{$name}_s'>";
    for ($i=0; $i<=59; $i++) {
        $s .= "<option value='$i'";
        if ($i == $m[3]) {
            $s .= " selected";
        }
        $s .= ">$i";
    }
    $s .= "</select>초 \n";

    return $s;
}




// 악성태그 변환
function bad_tag_convert($code)
{
    global $view;
    global $member, $is_admin;

    if ($is_admin && $member[mb_id] != $view[mb_id]) {
        //$code = preg_replace_callback("#(\<(embed|object)[^\>]*)\>(\<\/(embed|object)\>)?#i",
        // embed 또는 object 태그를 막지 않는 경우 필터링이 되도록 수정
        $code = preg_replace_callback("#(\<(embed|object)[^\>]*)\>?(\<\/(embed|object)\>)?#i",
                    create_function('$matches', 'return "<div class=\"embedx\">보안문제로 인하여 관리자 아이디로는 embed 또는 object 태그를 볼 수 없습니다. 확인하시려면 관리권한이 없는 다른 아이디로 접속하세요.</div>";'),
                    $code);
    }

    //return preg_replace("/\<([\/]?)(script|iframe)([^\>]*)\>/i", "&lt;$1$2$3&gt;", $code);
    // script 나 iframe 태그를 막지 않는 경우 필터링이 되도록 수정
    return preg_replace("/\<([\/]?)(script|iframe)([^\>]*)\>?/i", "&lt;$1$2$3&gt;", $code);
}


// 불법접근을 막도록 토큰을 생성하면서 토큰값을 리턴
function get_token()
{
    $token = md5(uniqid(rand(), true));
    set_session("ss_token", $token);

    return $token;
}


// POST로 넘어온 토큰과 세션에 저장된 토큰 비교
function check_token($url=FALSE) {
	set_session('ss_token', '');
    return true;

	// 세션에 저장된 토큰과 폼값으로 넘어온 토큰을 비교하여 틀리면 에러
	if ($_POST['token'] && get_session('ss_token') == $_POST['token']) {
		// 맞으면 세션을 지운다. 세션을 지우는 이유는 새로운 폼을 통해 다시 들어오도록 하기 위함
		unset($_SESSION['ss_token']);
	}
	else
		alert('Access Error',($url ? $url : $_SERVER['HTTP_REFERER']));
}


// 문자열에 utf8 문자가 들어 있는지 검사하는 함수
// 코드 : http://in2.php.net/manual/en/function.mb-check-encoding.php#95289
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
    if($str<'가') return "기타";
	elseif($str>='가' && $str<'나') return "ㄱ";
	elseif($str>='나' && $str<'다') return "ㄴ";
	elseif($str>='다' && $str<'라') return "ㄷ";
	elseif($str>='라' && $str<'마') return "ㄹ";
	elseif($str>='마' && $str<'바') return "ㅁ";
	elseif($str>='바' && $str<'사') return "ㅂ";
	elseif($str>='사' && $str<'아') return "ㅅ";
	elseif($str>='아' && $str<'자') return "ㅇ";
	elseif($str>='자' && $str<'차') return "ㅈ";
	elseif($str>='차' && $str<'카') return "ㅊ";
	elseif($str>='카' && $str<'타') return "ㅋ";
	elseif($str>='타' && $str<'파') return "ㅌ";
	elseif($str>='파' && $str<'하') return "ㅍ";
	elseif($str>='하') return "ㅎ";
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