<?php
/**
 * abraflexi-pricefixer
 *
 * @copyright (c) 2024, Vítězslav Dvořák
 */

 use AbraFlexi\Cenik;
 use Ease\Shared;
 
 define('APP_NAME', 'AbraFlexi PriceFix crawler');
 
 require_once '../vendor/autoload.php';
 
 Shared::init(['ABRAFLEXI_URL', 'ABRAFLEXI_LOGIN', 'ABRAFLEXI_PASSWORD', 'ABRAFLEXI_COMPANY'], '../.env');
 new \Ease\Locale(Shared::cfg('LOCALIZE', 'cs_CZ'), '../i18n', 'abraflexi-mailer');
 
 $productor = new \AbraFlexi\PriceFixer\Bundler("code:RASPBERRY");
 $productor->saveBundlePrice($productor->overallPrice());
 print_r($productor->getData());


//$productor->addStatusMessage(sprintf(_('Subproducts price is %s'), $subitemsPrice));
