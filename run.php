<?php

include 'autoload.php';

$network = fann_create_from_file('network.txt');
if (false === $network) {
    echo '创建神经网络失败' . PHP_EOL;
    die();
}

$fp = fopen('data.csv', 'rb');
if (false === $fp) {
    echo '打开 data.csv 失败' . PHP_EOL;
    fann_destroy($network);
    die();
}

$data = [];
$row = fgetcsv($fp);
while (false !== $row) {
    $row = fgetcsv($fp);
    if (!empty($row)) {
        $data[] = $row;
    }
}
fclose($fp);

file_put_contents('pre.csv', 'ID,开盘价,收盘价,开盘价(预测),收盘价(预测)' . PHP_EOL);
$total = count($data, COUNT_NORMAL);
$totalBatch = $total - 30 + 1;
for ($i = 0; $i < $totalBatch; $i++) {
    $inputs = [];
    $lastPrice = 0;
    for ($j = 0; $j < 30; $j++) {
        if (0 == $j) {
            $lastPrice = $data[$i + $j][2];
        }
        $inputs[] = $data[$i + $j][2] / $lastPrice - 1;
        $inputs[] = $data[$i + $j][3] / $lastPrice - 1;
        $lastPrice = $data[$i + $j][3];
        $inputs[] = $data[$i + $j][4];
        $inputs[] = $data[$i + $j][5];
        $inputs[] = $data[$i + $j][6];
        $inputs[] = $data[$i + $j][7];
        $inputs[] = $data[$i + $j][8];
        $inputs[] = $data[$i + $j][9];
        $inputs[] = $data[$i + $j][10];
        $inputs[] = $data[$i + $j][11];
    }
    $result = fann_run($network, $inputs);
    if (isset($data[$i + $j])) {
        $content = [
            $i + 1,
            $data[$i + $j][2],
            $data[$i + $j][3],
            $data[$i + $j - 1][3] * ($result[0] + 1),
            $data[$i + $j - 1][3] * ($result[0] + 1) * ($result[1] + 1),
        ];
    } else {
        $content = [
            $i + 1,
            '?',
            '?',
            $data[$i + $j - 1][3] * ($result[0] + 1),
            $data[$i + $j - 1][3] * ($result[0] + 1) * ($result[1] + 1),
        ];
    }
    file_put_contents('pre.csv', implode(',', $content) . PHP_EOL, FILE_APPEND);
}

fann_destroy($network);
