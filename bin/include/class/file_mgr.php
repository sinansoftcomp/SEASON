<? 

$fileOriginalName = $_REQUEST['fileOriginalName']; 
$filename = $_REQUEST['filename']; 

//$file = "./files/".$filename; // ���� ������ġ 
$file = "C:\\cms\\1001\\EB13\\EB130113";
 // ���� �̸� 

if ( file_exists($file) ) 
{ 
// echo "<meta charset='UTF-8'><script>alert(' <$fileOriginalName> ÷�������� �������� �ʽ��ϴ�.');</script>"; 
header("Content-Type: doesn/matter"); 
header('Content-Length: '.filesize($file)); 
header("Content-Disposition: attachment; filename=".$fileOriginalName);//���� ���ϸ����� �ٿ�ް����ִ� �κ� 
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
echo "<meta charset='UTF-8'><script>alert(' <$fileOriginalName> ÷�������� �������� �ʽ��ϴ�.'); history.go(-1);</script>"; 
} 
