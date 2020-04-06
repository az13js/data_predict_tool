<?php
namespace Tools;

/**
 * 日志
 */
class Log
{
    public static function record($data)
    {
        file_put_contents('x.log', json_encode($data) . PHP_EOL, FILE_APPEND);
    }
}
