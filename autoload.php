<?php

spl_autoload_register(function ($class_name) {
    $paths = ['controllers/', 'models/'];
    foreach ($paths as $path) {
        $file = $path . $class_name . '.php';
        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});
