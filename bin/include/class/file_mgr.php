<? 

$fileOriginalName = $_REQUEST['fileOriginalName']; 
$filename = $_REQUEST['filename']; 

//$file = "./files/".$filename; // 파일 저장위치 
$file = "C:\\cms\\1001\\EB13\\EB130113";
 // 파일 이름 

if ( file_exists($file) ) 
{ 
// echo "<meta charset='UTF-8'><script>alert(' <$fileOriginalName> 첨부파일이 존재하지 않습니다.');</script>"; 
header("Content-Type: doesn/matter"); 
header('Content-Length: '.filesize($file)); 
header("Content-Disposition: attachment; filename=".$fileOriginalName);//원래 파일명으로 다운받게해주는 부분 
header("Content-Transfer-Encoding: binary"); 
header("Pragma: no-cache"); 
header("Expires: 0"); 

if ( is_file("$file") ) 
{ 
$fp = fopen("$file", "r"); 

if ( !fpassthru($fp) ) 
{ 

fclose($fp); 
} 
} 

} else 
{ 
echo "<meta charset='UTF-8'><script>alert(' <$fileOriginalName> 첨부파일이 존재하지 않습니다.'); history.go(-1);</script>"; 
} 
