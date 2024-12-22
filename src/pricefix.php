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

use AbraFlexi\PriceFix\Bundler;
use Ease\Shared;

\define('APP_NAME', 'AbraFlexi PriceFixer');

require_once '../vendor/autoload.php';

Shared::init(['ABRAFLEXI_URL', 'ABRAFLEXI_LOGIN', 'ABRAFLEXI_PASSWORD', 'ABRAFLEXI_COMPANY'], file_exists('.env') ? '.env' : '../.env');
// new \Ease\Locale(Shared::cfg('LOCALIZE', 'cs_CZ'), '../i18n', 'abraflexi-pricefixer');

$completor = new Bundler($argv[1]);
$completor->saveBundlePrice($completor->overallPrice());
