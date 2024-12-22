<?php

declare(strict_types=1);

/**
 * This file is part of the AbraFlexiPriceFixer package
 *
 * https://github.com/VitexSoftware/AbraFlexi-pricefixer
 *
 * (c) Vítězslav Dvořák <http://vitexsoftware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Ease\Shared;

\define('APP_NAME', 'AbraFlexi PriceFix Init');

require_once '../vendor/autoload.php';

Shared::init(['ABRAFLEXI_URL', 'ABRAFLEXI_LOGIN', 'ABRAFLEXI_PASSWORD', 'ABRAFLEXI_COMPANY'], file_exists('.env') ? '.env' : '../.env');
// new \Ease\Locale(Shared::cfg('LOCALIZE', 'cs_CZ'), '../i18n', 'abraflexi-pricefixer');

$provider = new \AbraFlexi\Adresar();

if ($provider->recordExists((string) Shared::cfg('ABRAFLEXI_PROVIDER', 'code:PRICEFIXER'))) {
    $provider->addStatusMessage('Provider Already Exists');
} else {
    $provider->insertToAbraFlexi(['kod' => Shared::cfg('ABRAFLEXI_PROVIDER', 'PRICEFIXER'), 'nazev' => 'PriceFixer']);
    $provider->addStatusMessage(sprintf(_('Provider %s was created'), Shared::cfg('ABRAFLEXI_PROVIDER', 'PRICEFIXER'), 'success'));
}

$grouper = new \AbraFlexi\RW(null, ['evidence' => 'skupina-zbozi']);

if ($grouper->recordExists((string) Shared::cfg('ABRAFLEXI_GROUP', 'code:SADA'))) {
    $grouper->addStatusMessage('Group of goods Already Exists');
} else {
    $grouper->insertToAbraFlexi(['kod' => Shared::cfg('ABRAFLEXI_GROUP', 'SADA'), 'nazev' => 'Sada']);
    $grouper->addStatusMessage(sprintf(_('Group of goods %s was created'), Shared::cfg('ABRAFLEXI_GROUP', 'SADA'), 'success'));
}
