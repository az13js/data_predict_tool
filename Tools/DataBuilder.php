<?php
namespace Tools;

use \Exception;

class DataBuilder extends DataReader
{
    public function __construct(string $file)
    {
        parent::__construct($file);
    }
}
