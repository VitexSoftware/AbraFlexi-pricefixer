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

\define('APP_NAME', 'AbraFlexi PriceFix crawler');

require_once '../vendor/autoload.php';

Shared::init(['ABRAFLEXI_URL', 'ABRAFLEXI_LOGIN', 'ABRAFLEXI_PASSWORD', 'ABRAFLEXI_COMPANY'], file_exists('.env') ? '.env' : '../.env');
// new \Ease\Locale(Shared::cfg('LOCALIZE', 'cs_CZ'), '../i18n', 'abraflexi-pricefixer');

$bundler = new \AbraFlexi\PriceFix\Bundler();

if (\Ease\Shared::cfg('APP_DEBUG')) {
    $bundler->logBanner();
}

$bundler->fixAll();
