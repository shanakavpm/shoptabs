<?php

spl_autoload_register(function ($class) {
    // Only autoload classes in the App namespace
    if (strpos($class, 'App\\') !== 0) {
        return;
    }
    // Remove leading 'App\\'
    $relativeClass = substr($class, 4);
    $relativeClass = ltrim($relativeClass, '\\');
    $relativePath = str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass);
    $srcDir = dirname(__DIR__) . '/src';
    $file = $srcDir . DIRECTORY_SEPARATOR . $relativePath . '.php';
    // Debug output
    error_log("[AUTOLOADER] Looking for class: $class");
    error_log("[AUTOLOADER] Searching in: $file");
    if (file_exists($file)) {
        require_once $file;
        error_log("[AUTOLOADER] Found and loaded: $file");
        return true;
    } else {
        error_log("[AUTOLOADER] File not found: $file");
    }
    return false;
});

