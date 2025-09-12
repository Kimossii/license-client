<?php

//namespace App\Helpers;
namespace LicenseClient\Helpers;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
class LicenseHelper
{
    public static function getSegredoExtra()
    {
        $p1 = 'TUVV';
        $p2 = 'LVNB';
        $p3 = 'TFRP';
        $p4 = 'LVNF';
        $p5 = 'Q1JF';
        $p6 = 'VE8t';
        $p7 = 'MjAyNQ==';

        $base64 = $p1 . $p2 . $p3 . $p4 . $p5 . $p6 . $p7;
        $segredo = base64_decode($base64);

        return strrev($segredo);
    }


}
if (!function_exists('write_Clientlicense_log')) {
    function write_Clientlicense_log(string $msg, string $level = 'info', array $context = []): void
    {
        $log = new Logger('license');
        $log->pushHandler(new StreamHandler(storage_path('logs/license.log'), Logger::DEBUG));

        $log->{$level}($msg, $context);
    }
}
