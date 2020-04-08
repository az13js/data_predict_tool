<?php
namespace Tools;

use \Exception;

class DataReader
{
    /** @var array */
    protected $data;

    public function __construct(string $fileName)
    {
        $fp = fopen($fileName, 'rb');
        if (false === $fp) {
            throw new Exception('打开文件失败！，文件名称：' . $fileName);
        }
        $this->data = $this->getUsageDatas($fp);
        fclose($fp);
    }

    protected function getUsageDatas($fp): array
    {
        $index = 1;
        $data = [];
        $row = fgetcsv($fp); // header
        while (true) {
            $row = fgetcsv($fp);
            if (false === $row) {
                Log::record('Exit DataPreProcess::getUsageDatas(), index=' . $index);
                Log::record('Last index=' . ($index - 1));
                Log::record('Total in csv file=' . $index);
                break;
            }
            if (empty($row)) {
                continue;
            }
            $this->rowChecker($row, $index);
            $data[$index++] = $row;
        }
        return $data;
    }

    protected function rowChecker(array $csvRow, int $index)
    {
    }
}
