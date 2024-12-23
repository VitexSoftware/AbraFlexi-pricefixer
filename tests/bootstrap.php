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

if (file_exists('../vendor/autoload.php')) {
    require_once '../vendor/autoload.php'; // Test Run
    \Ease\Shared::instanced()->loadConfig('../example.env');
} else {
    require_once 'vendor/autoload.php'; // Create Test
    \Ease\Shared::instanced()->loadConfig('example.env');
}
