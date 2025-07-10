<?php
// 원본 문자열
$originalString = "115120001,115120000,115120004,115120006";

// 문자열을 쉼표로 구분하여 배열로 변환
$array = explode(',', $originalString);

// 각 배열 요소에 따옴표 추가
foreach ($array as &$value) {
    $value = "'" . $value . "'";
}

// 배열을 다시 문자열로 변환
$modifiedString = implode(',', $array);

echo $modifiedString;
?>