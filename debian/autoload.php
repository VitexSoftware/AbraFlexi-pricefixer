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

require_once '/usr/share/php/Composer/InstalledVersions.php';

(function (): void {
    $versions = [];
    foreach (\Composer\InstalledVersions::getAllRawData() as $d) {
        $versions = array_merge($versions, $d['versions'] ?? []);
    }
    $name    = 'unknown';
    $version = '0.0.0';
    $versions[$name] = ['pretty_version' => $version, 'version' => $version,
        'reference' => null, 'type' => 'library', 'install_path' => __DIR__,
        'aliases' => [], 'dev_requirement' => false];
    \Composer\InstalledVersions::reload([
        'root' => ['name' => $name, 'pretty_version' => $version, 'version' => $version,
            'reference' => null, 'type' => 'project', 'install_path' => __DIR__,
            'aliases' => [], 'dev' => false],
        'versions' => $versions,
    ]);
})();
