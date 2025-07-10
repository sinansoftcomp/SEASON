<?

//----------------------------------------------------------//
//  file thumbUploadImageCreate_New    썸네일					// 
//----------------------------------------------------------//
function thumbUploadImageCreate_New($img, $thumbImage, $w, $exifOrientation,$h=0){
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
	
	//---넘어온 이미지 방향에 따라 이미지 방향 바로잡기.
	if($exifOrientation) {
		switch($exifOrientation) {
			case 8:
				$image = imagerotate($image,90,0);
				break;
			case 3:
				$image = imagerotate($image,180,0);
				break;
			case 6:
				$image = imagerotate($image,-90,0);
				break;
		}
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

//----------------------------------------------------------//
//  file upload                  수정이거난 입력일때 						// 
//----------------------------------------------------------//
                     //저장위치  최후저장파일명    쎔네일싸이즈  
function file_upload($save_loc,$file_name, $thumb_size,$file_ori, $tmp_name, $GUBUN) { 

	if($GUBUN == '1'){

		@ini_set('gd.jpeg_ignore_warning', 1);

		$imageType	= array("image/jpeg","image/jpg","image/tif","image/gif","image/png");
		$audioType	= array();

		extract($_POST);
		if($file_ori==""){
				return -1;
		}

		// width 1000px 로 이미지 리사이징 -> 700px로 변경처리
		$filename	= $_SERVER['DOCUMENT_ROOT']."/temp/".$file_ori;
		

		$ori_filename	= $filename;
		$filename_thumb	= $_SERVER['DOCUMENT_ROOT']."/temp/thumb_".$file_ori;

		move_uploaded_file ( $tmp_name , $filename );
		
		$ext_temp	= explode(".",$file_ori);
		$ext		= $ext_temp[count($ext_temp)-1];

		//--여기에 if문을 추가하여 gif,png 는 통과 시켜라..

		if($ext != 'gif' && $ext != 'png') {						
			//-->server temp 올라온 파일조사
			$exif = exif_read_data($filename);

			//--이미지 방향체크  하여  thumbUploadImageCreate에 정보를 넘겨줌 
			$exifOrientation     =  $exif['Orientation'];
		}

		$th_chk = thumbUploadImageCreate_New($filename,$filename_thumb, $thumb_size,0, $exifOrientation);
	
		if($ext=='tif')	$filename_thumb = $filename;

		$filename	= $filename_thumb;
		$handle		= fopen($filename,"rb");
		$size		= GetImageSize($filename_thumb);
		$width		= $size[0];
		$height		= $size[1];
		$imageblob	= addslashes(fread($handle, filesize($filename))); 
		$filesize	= $filename;

		//echo "DATA TEST: ".$filename."//".$ori_filename."//".$filename_thumb;

		$datastring = file_get_contents($filename);
		$data = unpack("H*hex", $datastring);
		fclose($handle);

	}else{

		$filename	= $_SERVER['DOCUMENT_ROOT']."/temp/".$file_ori;
		//$ori_filename	= $_SERVER['DOCUMENT_ROOT']."/temp/".$file_name;

		//$filename_thumb	= $_SERVER['DOCUMENT_ROOT']."/temp/thumb_".$file_ori;
		$filename_thumb	= $filename;

		move_uploaded_file ( $tmp_name , $filename );
		//echo "!!!!!!!!!!!!!!".$filename.'!!!!!!!!!!!!!!!!!!!!';
		//$filename	= $filename_thumb;
		$handle		= fopen($filename,"rb");

		$datastring = file_get_contents($filename);
		$data = unpack("H*hex", $datastring);

		fclose($handle);
		

		//echo "DATA TEST: ".$filename."//".$ori_filename."//".$filename_thumb;
	}

		$server_host = "115.68.1.12";	//-->원격서버의 ip주소   
		$server_port = "21";			//-->원격서버의 port 

		$server_id= "sinituser";				//-->원격서버의 서버id 
		$server_pw = "memksk3370";		//-->원격서버의 서버password 

		//원격서버에 연결한다. 
		if(!($fc = ftp_connect($server_host, $server_port))) {
			return -1;
		}
		
		//원격서버에 로그인한다. 
		if(!ftp_login($fc, $server_id, $server_pw)) {
			return -2;
		}

		//업로드할 폴더로 이동한다. 
		$server_dir = $save_loc;
		
		ftp_chdir($fc, $server_dir); 

		//파일을 업로드 한다. 
		if(!ftp_put($fc, $file_name, $filename_thumb, FTP_BINARY)) {    
			return -3;
		} 

		//FTP를 닫는다 
		ftp_quit($fc);   

		unlink($filename_thumb);
		//unlink($ori_filename);
		 //메모리 오류 방지
		ini_set("memory_limit" , -1);

		return 0;
}


?>