<?php

/**
 * abraflexi-pricefixer crawler
 *
 * @copyright (c) 2024, Vítězslav Dvořák
 */
use Ease\Shared;

define('APP_NAME', 'AbraFlexi PriceFix crawler');

require_once '../vendor/autoload.php';

Shared::init(['ABRAFLEXI_URL', 'ABRAFLEXI_LOGIN', 'ABRAFLEXI_PASSWORD', 'ABRAFLEXI_COMPANY'], file_exists('.env') ? '.env' : '../.env');
//new \Ease\Locale(Shared::cfg('LOCALIZE', 'cs_CZ'), '../i18n', 'abraflexi-pricefixer');

$bundler = new \AbraFlexi\PriceFix\Bundler();

if (\Ease\Shared::cfg('APP_DEBUG')) {
    $bundler->logBanner();
}

$bundler->fixAll();
