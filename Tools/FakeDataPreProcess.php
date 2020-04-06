<?php
namespace Tools;

use \Exception;

/**
 * 数据预处理
 */
class FakeDataPreProcess extends DataPreProcess
{
    public function __construct(string $fileName)
    {
        parent::__construct($fileName);
    }

    public function value(int $row, int $column)
    {
        $v = parent::value($row, $column);
        $r = $row;
        while (!is_numeric($v) && $column > 1) {
            $r--;
            if ($row == 1) {
                $r = 2;
            }
            $v = parent::value($r, $column);
        }
        return $v;
    }
}
