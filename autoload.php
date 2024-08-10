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

// <?php
// spl_autoload_register('autoload');
// function autoload($class_name)
// {
//     $array_paths = array(
//         'database/',
//         'app/',
//         'models/',
//         'controllers/'
//     );
//     $parts = explode('\\', $class_name);
//     $name = array_pop($parts);
//     foreach ($array_paths as $path) {
//         $file = sprintf($path . '%s.php', $name);
//         if (is_file($file)) {
//             require $file;
//         }
//     }
// }