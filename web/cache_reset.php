<?php

require __DIR__.'/../vendor/autoload.php';

$config = \Symfony\Component\Yaml\Yaml::parse(__DIR__.'/../app/config/parameters.yml');

if (!isset($_GET['cache_key']) || $_GET['cache_key'] != $config['parameters']['cache_key']) {
    header('HTTP/1.0 403 Forbidden');
    exit('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}

apc_clear_cache();
apc_clear_cache('user');

function recursiveClearDir($dir, $deleteDir = false)
{
    if (is_file($dir)) {
        unlink($dir);
    } elseif (is_dir($dir)) {
        $scan = glob(rtrim($dir, '/').'/*');
        foreach ($scan as $path) {
            recursiveClearDir($path, true);
        }
        if ($deleteDir) {
            rmdir($dir);
        }
    }
}

recursiveClearDir(__DIR__.'/../app/cache/');
//recursiveClearDir(__DIR__.'/../app/logs/');
//recursiveClearDir(__DIR__.'/../web/cache/');
//recursiveClearDir(__DIR__.'/../web/pdw_file_browser/cache/');
