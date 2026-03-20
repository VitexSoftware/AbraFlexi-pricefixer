<?php
// Debian autoloader for abraflexi-pricefixer
require_once '/usr/share/php/Ease/autoload.php';
require_once '/usr/share/php/AbraFlexi/autoload.php';
// PSR-4 autoloader for application classes
spl_autoload_register(function (string $class): void {
    if (strncmp('AbraFlexi\\PriceFix\\', $class, 19) === 0) {
        $file = '/usr/lib/abraflexi-pricefixer/PriceFix/' . str_replace('\\', '/', substr($class, 19)) . '.php';
        if (file_exists($file)) {
            require $file;
        }
    }
});
