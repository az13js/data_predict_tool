<?php
namespace Tools;

use \Exception;

/**
 * 数据预处理
 */
class DataPreProcess extends DataReader
{
    public function __construct(string $fileName)
    {
        parent::__construct($fileName);
    }

    public function find(int $column, string $content)
    {
        foreach ($this->data as $index => $v) {
            if (!isset($v[$column - 1])) {
                throw new Exception('取列数超过极限');
            }
            if ($v[$column - 1] == $content) {
                return $index;
            }
        }
        return null;
    }

    public function sortBy(int $column)
    {
        $data = [];
        for ($i = $this->rows(); $i > 0; $i--) {
            if (isset($data[strtotime($this->value($i, $column))])) {
                throw new Exception('重复的日期' . $this->value($i, $column));
            }
            $data[strtotime($this->value($i, $column))] = $this->line($i);
        }
        ksort($data);
        $this->data = [];
        $index = 0;
        foreach ($data as $key => $value) {
            $index++;
            $this->data[$index] = $value;
        }
    }

    public function weekday(int $row): int
    {
        return date('w', strtotime($this->value($row, 1)));
    }

    public function value(int $row, int $column)
    {
        $line = $this->line($row);
        if ($column < 1 || $column > $this->columns()) {
            throw new Exception('取列数超过极限');
        }
        return is_numeric($line[$column - 1]) ? floatval($line[$column - 1]) : $line[$column - 1];
    }

    public function line(int $line): array
    {
        if ($line < 1 || $line > $this->rows()) {
            throw new Exception('取行数超过极限');
        }
        return $this->data[$line];
    }

    public function rows(): int
    {
        Log::record('rows=' . count($this->data, COUNT_NORMAL));
        return count($this->data, COUNT_NORMAL);
    }

    public function columns(): int
    {
        Log::record('columns=' . count($this->data[1], COUNT_NORMAL));
        return count($this->data[1], COUNT_NORMAL);
    }

    protected function rowChecker(array $csvRow, int $index)
    {
        if (count($csvRow, COUNT_NORMAL) != 12) {
            throw Exception('数据列数量不对,index=' . $index);
        }
        foreach ($csvRow as $v) {
            if (!is_string($v)) {
                throw new Exception('单元格数据异常：' . print_r($csvRow, true) . 'index=' . $index);
            }
        }
        $timestamp = strtotime($csvRow[0]);
        if (!is_int($timestamp)) {
            throw new Exception('单元格数据异常：' . print_r($csvRow, true) . 'index=' . $index . '单元格不是日期类型');
        }
        if (date('Y-m-d', $timestamp) != $csvRow[0]) {
            throw new Exception("单元格日期异常" . print_r($csvRow, true));
        }
        for ($i = 3; $i < 12; $i++) {
            if (in_array($csvRow[0], ['1995-04-20', '1995-01-25', '1994-10-20']) && in_array($i, [8, 9])) {
                continue;
            }
            if (in_array($csvRow[0], ['1994-09-28', '1993-01-05', '1992-06-16', '1992-05-11', '1992-03-04', '1992-02-13', '1991-05-09']) && in_array($i, [11])) {
                continue;
            }
            if (in_array($csvRow[0], ['1990-12-19']) && in_array($i, [7, 8, 9])) {
                continue;
            }
            if (!is_numeric($csvRow[$i])) {
                throw new Exception('数据非数字类型！' . print_r($csvRow, true));
            }
        }
    }
}
