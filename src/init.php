<?php

/**
 * abraflexi-pricefixer init
 *
 * @copyright (c) 2024, Vítězslav Dvořák
 */
use Ease\Shared;

define('APP_NAME', 'AbraFlexi PriceFix Init');

require_once '../vendor/autoload.php';

Shared::init(['ABRAFLEXI_URL', 'ABRAFLEXI_LOGIN', 'ABRAFLEXI_PASSWORD', 'ABRAFLEXI_COMPANY'], '../.env');
new \Ease\Locale(Shared::cfg('LOCALIZE', 'cs_CZ'), '../i18n', 'abraflexi-pricefixer');

$provider = new \AbraFlexi\Adresar();
if ($provider->recordExists(Shared::cfg('ABRAFLEXI_PROVIDER', 'code:PRICEFIXER'))) {
    $provider->addStatusMessage('Provider Already Exists');
} else {
    $provider->insertToAbraFlexi(['kod' => Shared::cfg('ABRAFLEXI_PROVIDER', 'PRICEFIXER'), 'nazev' => 'PriceFixer']);
    $provider->addStatusMessage(sprintf(_('Provider %s was created'), Shared::cfg('ABRAFLEXI_PROVIDER', 'PRICEFIXER'), 'success'));
}
