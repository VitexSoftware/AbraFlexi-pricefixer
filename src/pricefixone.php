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

\define('APP_NAME', 'AbraFlexi PriceFix one');

require_once '../vendor/autoload.php';

Shared::init(
    ['ABRAFLEXI_URL', 'ABRAFLEXI_LOGIN', 'ABRAFLEXI_PASSWORD', 'ABRAFLEXI_COMPANY'],
    file_exists('.env') ? '.env' : '../.env',
);

$options = getopt('o::', ['output::']);
$destination = \array_key_exists('o', $options)
    ? (string) $options['o']
    : (\array_key_exists('output', $options)
        ? (string) $options['output']
        : Shared::cfg('RESULT_FILE', 'pricefix-one-report.json'));
$pricelistCode = (string) ($argv[1] ?? Shared::cfg('PRICELIST_CODE', ''));
$pricelistId = (string) Shared::cfg('PRICELIST_ID', '');
$bundler = new Bundler();
$result = [
    'matched' => 0,
    'updated' => 0,
    'unchanged' => 0,
    'failed' => 0,
    'bundles' => [],
];
$exitCode = 0;
$inputError = null;

if ($pricelistCode === '' && $pricelistId !== '') {
    if (!ctype_digit($pricelistId) || (int) $pricelistId < 1) {
        $inputError = sprintf('Invalid pricelist record ID: %s', $pricelistId);
    } else {
        $pricelistCode = (string) $bundler->getItemCodeById((int) $pricelistId);
    }
}

if (null !== $inputError) {
    $message = $inputError;
    $status = 'error';
    $exitCode = 1;
} elseif ($pricelistCode === '') {
    $message = 'Missing pricelist product code or record ID';
    $status = 'error';
    $exitCode = 1;
} else {
    $result = $bundler->fixOne($pricelistCode);

    if ($result['failed'] > 0) {
        $message = sprintf(
            'Failed to recalculate %d of %d matching bundles for %s',
            $result['failed'],
            $result['matched'],
            $pricelistCode,
        );
        $status = 'error';
        $exitCode = 1;
    } elseif ($result['matched'] === 0) {
        $message = sprintf('No bundles found for pricelist item %s', $pricelistCode);
        $status = 'warning';
    } else {
        $message = sprintf(
            'Recalculated %d matching bundles for %s; %d updated, %d unchanged',
            $result['matched'],
            $pricelistCode,
            $result['updated'],
            $result['unchanged'],
        );
        $status = 'success';
    }
}

$report = [
    'producer' => Shared::appName(),
    'status' => $status,
    'timestamp' => date('c'),
    'message' => $message,
    'artifacts' => [
        'pricefix_report' => [$destination],
    ],
    'metrics' => [
        'bundlesMatched' => $result['matched'],
        'bundlesUpdated' => $result['updated'],
        'bundlesUnchanged' => $result['unchanged'],
        'bundlesFailed' => $result['failed'],
    ],
    'bundles' => $result['bundles'],
    'pricelistCode' => $pricelistCode,
    'pricelistId' => $pricelistId,
];

try {
    $reportJson = json_encode(
        $report,
        \JSON_PRETTY_PRINT | \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES | \JSON_THROW_ON_ERROR,
    );
    $written = file_put_contents($destination, $reportJson.\PHP_EOL);

    if ($written === false) {
        $exitCode = 1;
        fwrite(\STDERR, sprintf("Cannot write report to %s\n", $destination));
    }
} catch (\JsonException $exception) {
    $exitCode = 1;
    fwrite(\STDERR, sprintf("Cannot encode report: %s\n", $exception->getMessage()));
}

fwrite($exitCode === 0 ? \STDOUT : \STDERR, $message.\PHP_EOL);

exit($exitCode);
