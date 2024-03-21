<?php

/**
 * abraflexi-pricefixer crawler
 *
 * @copyright (c) 2024, Vítězslav Dvořák
 */

use AbraFlexi\Cenik;
use Ease\Shared;

define('APP_NAME', 'AbraFlexi PriceFix crawler');

require_once '../vendor/autoload.php';

Shared::init(['ABRAFLEXI_URL', 'ABRAFLEXI_LOGIN', 'ABRAFLEXI_PASSWORD', 'ABRAFLEXI_COMPANY'], '../.env');
new \Ease\Locale(Shared::cfg('LOCALIZE', 'cs_CZ'), '../i18n', 'abraflexi-pricefixer');

$completor = new \AbraFlexi\PriceFix\SadyAKomplety();
$complets = $completor->getColumnsFromAbraFlexi('*', ['limit' => 0]);

$bundles = [];
foreach ($complets as $complet) {
    $bundles[strval($complet['cenikSada'])][] = [strval($complet['cenik']) => $complet['mnozMj']];
}

$bundler = new \AbraFlexi\PriceFix\Bundler();
foreach ($bundles as $bundleCode => $bundle) {
    $bundler->loadFromAbraFlexi($bundleCode);
    $bundlePrice = $bundler->overallPrice();
    $bundler->addStatusMessage('📦 ' . \AbraFlexi\Functions::uncode($bundleCode) . '  = 💰 ' . $bundlePrice, $bundler->saveBundlePrice($bundlePrice) ? 'success' : 'error');
}
