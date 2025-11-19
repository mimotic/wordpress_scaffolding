<?php
/**
 * Plugin Name: Mimotic Hooks
 * Description: Loads mu-scripts security and performance tweaks for every environment.
 * Author: Mimotic
 */

if (!defined('ABSPATH')) {
    exit;
}

$muScriptsPath = dirname(ABSPATH) . '/mu-scripts/init.php';

if (file_exists($muScriptsPath)) {
    require_once $muScriptsPath;
} else {
    error_log('[Mimotic Security Hooks] mu-scripts/init.php not found.');
}
