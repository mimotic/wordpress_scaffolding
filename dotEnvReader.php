<?php
require_once(__DIR__ . '/vendor/autoload.php');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

if (! function_exists('dotEnvReader')) {
    /**
     * Gets the value of an environment variable
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    function dotEnvReader($key, $default = null) {
        $value = $_ENV[$key];

        if ($value === false) {
            return $default;
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return;
        }

        return $value;
    }
}
