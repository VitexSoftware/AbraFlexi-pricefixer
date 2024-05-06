<?php

/**
 * abraflexi-pricefixer crawler
 *
 * @copyright (c) 2024, Vítězslav Dvořák
 */
use Ease\Shared;

define('APP_NAME', 'AbraFlexi PriceFix crawler');

require_once '../vendor/autoload.php';

Shared::init(['ABRAFLEXI_URL', 'ABRAFLEXI_LOGIN', 'ABRAFLEXI_PASSWORD', 'ABRAFLEXI_COMPANY'], '../.env');
//new \Ease\Locale(Shared::cfg('LOCALIZE', 'cs_CZ'), '../i18n', 'abraflexi-pricefixer');

$bundler = new \AbraFlexi\PriceFix\Bundler();

if (\Ease\Shared::cfg('APP_DEBUG')) {
    $bundler->logBanner();
}

$completor = new \AbraFlexi\PriceFix\SadyAKomplety();
$complets = $completor->getColumnsFromAbraFlexi('*', ['limit' => 0]);

$bundles = [];
foreach ($complets as $complet) {
    $bundles[strval($complet['cenikSada'])][] = [strval($complet['cenik']) => $complet['mnozMj']];
}

$pos = 0;
foreach ($bundles as $bundleCode => $bundle) {
    $bundler->loadFromAbraFlexi($bundleCode);
    $bundlePrice = $bundler->overallPrice();
    $bundler->addStatusMessage(strval(++$pos) . '/' . strval(count($bundles)) . '📦 ' . \AbraFlexi\Functions::uncode($bundleCode) . '  = 💰 ' . strval($bundlePrice) . ' 💶', $bundler->saveBundlePrice($bundlePrice) ? 'success' : 'error');
}
