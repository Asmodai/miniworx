<?php
/**
 * Debug
 */
error_reporting(E_ALL);
ini_set("display_errors", 1);

/**
 * Check PHP version
 */
 $requiredPhpVersion = '7.0.0';
if (version_compare(PHP_VERSION, $requiredPhpVersion) < 0) {
    die('Require PHP '.$requiredPhpVersion.' or above.');
}

/**
 * Configure autoloading
 */
set_include_path(get_include_path().PATH_SEPARATOR.__DIR__);
spl_autoload_register();
?>
