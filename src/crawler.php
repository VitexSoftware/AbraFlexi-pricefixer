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
new \Ease\Locale(Shared::cfg('LOCALIZE', 'cs_CZ'), '../i18n', 'abraflexi-mailer');

$productor = new Cenik();

$products = $productor->getColumnsFromAbraFlexi(['id', 'nazev', 'lastUpdate'], [
    'limit' => 0,
    'order' => 'lastUpdate',
    'lastUpdate gt "' . (new \DateTime())->modify('-1 day')->format('Y-m-d\TH:i:s').'"',
]
);

foreach ($products as $product) {
    echo '#' . $product['id'] . ' ' . $product['lastUpdate'] . ' ' . $product['nazev'] . "\n";
}
