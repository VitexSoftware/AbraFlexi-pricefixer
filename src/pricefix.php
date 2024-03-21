<?php
/**
 * abraflexi-pricefixer
 *
 * @copyright (c) 2024, Vítězslav Dvořák
 */

 use AbraFlexi\PriceFix\Bundler;
 use Ease\Shared;
 
 define('APP_NAME', 'AbraFlexi PriceFixer');
 
 require_once '../vendor/autoload.php';
 
 Shared::init(['ABRAFLEXI_URL', 'ABRAFLEXI_LOGIN', 'ABRAFLEXI_PASSWORD', 'ABRAFLEXI_COMPANY'], '../.env');
 new \Ease\Locale(Shared::cfg('LOCALIZE', 'cs_CZ'), '../i18n', 'abraflexi-pricefixer');
 
 $completor = new Bundler($argv[1]);
 $completor->saveBundlePrice($completor->overallPrice());
