<?php
// ���� ���ڿ�
$originalString = "115120001,115120000,115120004,115120006";

// ���ڿ��� ��ǥ�� �����Ͽ� �迭�� ��ȯ
$array = explode(',', $originalString);

// �� �迭 ��ҿ� ����ǥ �߰�
foreach ($array as &$value) {
    $value = "'" . $value . "'";
}

// �迭�� �ٽ� ���ڿ��� ��ȯ
$modifiedString = implode(',', $array);

echo $modifiedString;
?>