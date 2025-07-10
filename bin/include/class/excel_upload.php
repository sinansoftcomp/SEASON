<?

//----------------------------------------------------------//
//  file thumbUploadImageCreate_New    �����					// 
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
	
	//---�Ѿ�� �̹��� ���⿡ ���� �̹��� ���� �ٷ����.
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
//  file upload                  �����̰ų� �Է��϶� 						// 
//----------------------------------------------------------//
                     //������ġ  �����������ϸ�    �����Ͻ�����  
function file_upload($save_loc,$file_name, $thumb_size,$file_ori, $tmp_name, $GUBUN) { 

	if($GUBUN == '1'){

		@ini_set('gd.jpeg_ignore_warning', 1);

		$imageType	= array("image/jpeg","image/jpg","image/tif","image/gif","image/png");
		$audioType	= array();

		extract($_POST);
		if($file_ori==""){
				return -1;
		}

		// width 1000px �� �̹��� ������¡ -> 700px�� ����ó��
		$filename	= $_SERVER['DOCUMENT_ROOT']."/temp/".$file_ori;
		

		$ori_filename	= $filename;
		$filename_thumb	= $_SERVER['DOCUMENT_ROOT']."/temp/thumb_".$file_ori;

		move_uploaded_file ( $tmp_name , $filename );
		
		$ext_temp	= explode(".",$file_ori);
		$ext		= $ext_temp[count($ext_temp)-1];

		//--���⿡ if���� �߰��Ͽ� gif,png �� ��� ���Ѷ�..

		if($ext != 'gif' && $ext != 'png') {						
			//-->server temp �ö�� ��������
			$exif = exif_read_data($filename);

			//--�̹��� ����üũ  �Ͽ�  thumbUploadImageCreate�� ������ �Ѱ��� 
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

		$server_host = "115.68.1.12";	//-->���ݼ����� ip�ּ�   
		$server_port = "21";			//-->���ݼ����� port 

		$server_id= "sinituser";				//-->���ݼ����� ����id 
		$server_pw = "memksk3370";		//-->���ݼ����� ����password 

		//���ݼ����� �����Ѵ�. 
		if(!($fc = ftp_connect($server_host, $server_port))) {
			return -1;
		}
		
		//���ݼ����� �α����Ѵ�. 
		if(!ftp_login($fc, $server_id, $server_pw)) {
			return -2;
		}

		//���ε��� ������ �̵��Ѵ�. 
		$server_dir = $save_loc;
		
		ftp_chdir($fc, $server_dir); 

		//������ ���ε� �Ѵ�. 
		if(!ftp_put($fc, $file_name, $filename_thumb, FTP_BINARY)) {    
			return -3;
		} 

		//FTP�� �ݴ´� 
		ftp_quit($fc);   

		unlink($filename_thumb);
		//unlink($ori_filename);
		 //�޸� ���� ����
		ini_set("memory_limit" , -1);

		return 0;
}


?>