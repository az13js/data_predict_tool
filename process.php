<?php

include 'autoload.php';

$fp = fopen('summary.csv', 'rb');
if (false === $fp) {
    echo '打开文件 summary.csv 失败' . PHP_EOL;
    die();
}

$data = [];
$row = fgetcsv($fp);
while (true) {
    $row = fgetcsv($fp);
    if (false === $row) {
        break;
    }
    if (empty($row)) {
        break;
    }
    $data[$row[0]] = $row;
}
fclose($fp);
ksort($data);

$startDate = '2002-10-31';
$endDate = '2020-03-27';

$fp = fopen('data.csv', 'wb');
if (false === $fp) {
    echo '写文件 data.csv 失败' . PHP_EOL;
    die();
}

fputcsv($fp, ['ID', '日期', '开盘价', '收盘价', '是否缺失数据，1是0否','周一','周二','周三','周四','周五','周六','周日']);
$dataId = 0;
$lastData = [];
for ($i = strtotime($startDate); $i <= strtotime($endDate); $i = strtotime(date('Y-m-d', $i) . ' +1 day')) {
    $dataId++;
    $key = date('Ymd', $i);
    if (isset($data[$key])) {
        $open = $data[$key][2];
        $close = $data[$key][3];
        $loss = 0;
        $lastData = $data[$key];
    } else {
        $open = $lastData[3];
        $close = $lastData[3];
        $loss = 1;
    }
    $w1 = $w2 = $w3 = $w4 = $w5 = $w6 = $w7 = 0;
    switch (date('N', $i)) {
        case 1: $w1 = 1; break;
        case 2: $w2 = 1; break;
        case 3: $w3 = 1; break;
        case 4: $w4 = 1; break;
        case 5: $w5 = 1; break;
        case 6: $w6 = 1; break;
        case 7: $w7 = 1; break;
        default:;
    }
    fputcsv($fp, [$dataId, date('Y/m/d', $i), $open, $close, $loss, $w1, $w2, $w3, $w4, $w5, $w6, $w7]);
}

fclose($fp);
