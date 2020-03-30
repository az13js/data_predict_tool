<?php

include 'autoload.php';

$train_data = fann_read_train_from_file('trainData.txt');
if (false === $train_data) {
    echo '读取训练数据失败' . PHP_EOL;
    die();
}
fann_shuffle_train_data($train_data);

$test_data = fann_read_train_from_file('testData.txt');
if (false === $test_data) {
    echo '读取测试数据失败' . PHP_EOL;
    die();
}
fann_shuffle_train_data($test_data);

$network = fann_create_standard(2, 300, 2);
fann_set_activation_function_hidden($network, FANN_SIGMOID_SYMMETRIC);
fann_set_activation_function_output($network, FANN_SIGMOID_SYMMETRIC);
fann_set_training_algorithm($network, FANN_TRAIN_RPROP);
//fann_set_learning_rate($network, 0.0000001);
fann_set_train_error_function($network, FANN_ERRORFUNC_LINEAR);
fann_set_train_stop_function($network, FANN_STOPFUNC_MSE);
fann_set_callback($network, function($ann, $train, $max_epochs, $epochs_between_reports, $desired_error, $epochs) use ($test_data) {
    static $x = 1;
    echo $x . ',';
    echo $epochs / $max_epochs;
    echo ',';
    echo sprintf('%.10f', 1000000 * fann_get_MSE($ann));
    echo ',';
    echo sprintf('%.10f', 1000000 * fann_test_data($ann, $test_data));
    echo PHP_EOL;
    $x++;
    return true;
});
fann_init_weights($network, $train_data);
fann_train_on_data($network, $train_data, 1000, 1, 0);
fann_save($network, 'network.txt');
