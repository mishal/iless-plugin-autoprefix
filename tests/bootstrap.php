<?php

if (!is_readable(__DIR__ . '/../vendor/autoload.php')) {
    echo "Missing autoload.php, please run `composer install`";
    exit(1);
}

require __DIR__ . '/../vendor/autoload.php';

define('ILESS_TEST_CACHE_DIR', sys_get_temp_dir() . '/iless-plugin-autoprefix');

if (is_dir(ILESS_TEST_CACHE_DIR)) {
    // clear the directory
    $files = glob(ILESS_TEST_CACHE_DIR . '/*');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    rmdir(ILESS_TEST_CACHE_DIR);
}

$postcssBin = __DIR__ . '/../node_modules/.bin/postcss';
if (!file_exists($postcssBin)) {
    echo "Missing postcss binary, please run `npm install postcss-cli autoprefixer`";
    exit(1);
}

define('POSTCSS_BIN', realpath($postcssBin));
