<?php

include 'autoload.php';

class Config {
    public static $trainDataStartId = 2112;
    public static $trainDataEndId = 6368;
    public static $testDataStartId = 6338;
    public static $testDataEndId = 6368;
    public static $data = [];
    public static $index = 0;
    public static $inputDay = 30;
    public static $window = 31;
}

$fp = fopen('data.csv', 'rb');
if (false === $fp) {
    echo '打开文件 data.csv 失败' . PHP_EOL;
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
    if (!is_numeric($row[2]) || !is_numeric($row[3]) || !is_numeric($row[4])) {
        echo '非数字的ID ' . $row[0] . PHP_EOL;
    }
}

fclose($fp);

Config::$data = $data;
unset($data);
Config::$index = 0;
$train_data = fann_create_train_from_callback(Config::$trainDataEndId - Config::$trainDataStartId + 2 - Config::$window, 300, 2, function($num, $num_input, $num_output) {
    $data = ['input' => [], 'output' => []];
    $lastPrice = 0;
    for ($i = 0; $i < Config::$inputDay; $i++) {
        if (0 == $i) {
            if (!isset(Config::$data[Config::$trainDataStartId + Config::$index + $i])) {
                echo '不存在的开始数据' . PHP_EOL;
                die();
            }
            $lastPrice = Config::$data[Config::$trainDataStartId + Config::$index + $i][2];
        }
        $data['input'][] = Config::$data[Config::$trainDataStartId + Config::$index + $i][2] / $lastPrice - 1;
        $data['input'][] = Config::$data[Config::$trainDataStartId + Config::$index + $i][3] / Config::$data[Config::$trainDataStartId + Config::$index + $i][2] - 1;
        $data['input'][] = Config::$data[Config::$trainDataStartId + Config::$index + $i][4];
        $data['input'][] = Config::$data[Config::$trainDataStartId + Config::$index + $i][5];
        $data['input'][] = Config::$data[Config::$trainDataStartId + Config::$index + $i][6];
        $data['input'][] = Config::$data[Config::$trainDataStartId + Config::$index + $i][7];
        $data['input'][] = Config::$data[Config::$trainDataStartId + Config::$index + $i][8];
        $data['input'][] = Config::$data[Config::$trainDataStartId + Config::$index + $i][9];
        $data['input'][] = Config::$data[Config::$trainDataStartId + Config::$index + $i][10];
        $data['input'][] = Config::$data[Config::$trainDataStartId + Config::$index + $i][11];
        $lastPrice = Config::$data[Config::$trainDataStartId + Config::$index + $i][3];
    }
    if (!isset(Config::$data[Config::$trainDataStartId + Config::$index + $i])) {
        echo '不存在的数据' . PHP_EOL;
        die();
    }
    $data['output'][] = Config::$data[Config::$trainDataStartId + Config::$index + $i][2] / $lastPrice - 1;
    $data['output'][] = Config::$data[Config::$trainDataStartId + Config::$index + $i][3] / Config::$data[Config::$trainDataStartId + Config::$index + $i][2] - 1;
    Config::$index++;
    return $data;
});
fann_save_train($train_data, 'trainData.txt');
fann_destroy_train($train_data);
Config::$index = 0;
$testDataTotal = Config::$testDataEndId - Config::$testDataStartId + 2 - Config::$window;
if ($testDataTotal <= 0) {
    echo '测试数据数量小于等于0' . PHP_EOL;
    die();
}
$test_data = fann_create_train_from_callback($testDataTotal, 300, 2, function($num, $num_input, $num_output) {
    $data = ['input' => [], 'output' => []];
    $lastPrice = 0;
    for ($i = 0; $i < Config::$inputDay; $i++) {
        if (0 == $i) {
            if (!isset(Config::$data[Config::$testDataStartId + Config::$index + $i])) {
                echo '不存在的开始数据' . PHP_EOL;
                die();
            }
            $lastPrice = Config::$data[Config::$testDataStartId + Config::$index + $i][2];
        }
        $data['input'][] = Config::$data[Config::$testDataStartId + Config::$index + $i][2] / $lastPrice - 1;
        $data['input'][] = Config::$data[Config::$testDataStartId + Config::$index + $i][3] / Config::$data[Config::$testDataStartId + Config::$index + $i][2] - 1;
        $data['input'][] = Config::$data[Config::$testDataStartId + Config::$index + $i][4];
        $data['input'][] = Config::$data[Config::$testDataStartId + Config::$index + $i][5];
        $data['input'][] = Config::$data[Config::$testDataStartId + Config::$index + $i][6];
        $data['input'][] = Config::$data[Config::$testDataStartId + Config::$index + $i][7];
        $data['input'][] = Config::$data[Config::$testDataStartId + Config::$index + $i][8];
        $data['input'][] = Config::$data[Config::$testDataStartId + Config::$index + $i][9];
        $data['input'][] = Config::$data[Config::$testDataStartId + Config::$index + $i][10];
        $data['input'][] = Config::$data[Config::$testDataStartId + Config::$index + $i][11];
        $lastPrice = Config::$data[Config::$testDataStartId + Config::$index + $i][3];
    }
    if (!isset(Config::$data[Config::$testDataStartId + Config::$index + $i])) {
        echo '不存在的数据' . PHP_EOL;
        die();
    }
    $data['output'][] = Config::$data[Config::$testDataStartId + Config::$index + $i][2] / $lastPrice - 1;
    $data['output'][] = Config::$data[Config::$testDataStartId + Config::$index + $i][3] / Config::$data[Config::$testDataStartId + Config::$index + $i][2] - 1;
    Config::$index++;
    return $data;
});
fann_save_train($test_data, 'testData.txt');
fann_destroy_train($test_data);
