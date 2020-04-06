<?php

include 'autoload.php';

$dataPreProcess = new Tools\FakeDataPreProcess('summary.csv');
$dataPreProcess->sortBy(1);
$rows = $dataPreProcess->rows();
$columns = $dataPreProcess->columns();

$fp = fopen('data.csv', 'wb');
if (false === $fp) {
    echo '写文件data.csv失败' . PHP_EOL;
    die();
}

fputcsv($fp, ['ID', '日期', '收盘价', '最高价', '最低价', '开盘价',
    '成交量', '成交金额', '星期一', '星期二', '星期三', '星期四', '星期五',
     '星期六', '星期日', '是否缺失数据']);
echo '第一行 ';
echo implode(',', $dataPreProcess->line(1));
echo PHP_EOL;

$i = 1;
for ($indexDate = '1990-12-19'; $indexDate != '2020-04-06'; $indexDate = date('Y-m-d', strtotime("$indexDate +1 day"))) {
    $findData = $indexDate;
    $row = $dataPreProcess->find(1, $findData);
    $isFix = false;
    while (is_null($row)) {
        $isFix = true;
        $findData = date('Y-m-d', strtotime("$findData -1 day"));
        $row = $dataPreProcess->find(1, $findData);
    }
    $line = [];
    $line[] = $i;
    $line[] = $indexDate;
    $line[] = $dataPreProcess->value($row, 4);
    $line[] = $dataPreProcess->value($row, 5);
    $line[] = $dataPreProcess->value($row, 6);
    $line[] = $dataPreProcess->value($row, 7);
    $line[] = $dataPreProcess->value($row, 11);
    $line[] = $dataPreProcess->value($row, 12);
    $line[] = date('w', strtotime($indexDate)) == 1 ? 1 : 0;
    $line[] = date('w', strtotime($indexDate)) == 2 ? 1 : 0;
    $line[] = date('w', strtotime($indexDate)) == 3 ? 1 : 0;
    $line[] = date('w', strtotime($indexDate)) == 4 ? 1 : 0;
    $line[] = date('w', strtotime($indexDate)) == 5 ? 1 : 0;
    $line[] = date('w', strtotime($indexDate)) == 6 ? 1 : 0;
    $line[] = date('w', strtotime($indexDate)) == 0 ? 1 : 0;
    $line[] = $isFix ? 1 : 0;
    fputcsv($fp, $line);
    $i++;
}

fclose($fp);
