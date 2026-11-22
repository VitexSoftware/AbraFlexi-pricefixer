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

namespace AbraFlexi\PriceFix;

use AbraFlexi\Cenik;
use AbraFlexi\SadyAKomplety;

class Bundler extends Cenik
{
    /**
     * Bundles.
     *
     * @var array<string, list<array<string, mixed>>>
     */
    private array $bundles = [];

    /**
     * Get Bundles.
     *
     * @return array<string, list<array<string, mixed>>>
     */
    public function getBundles()
    {
        $completor = new SadyAKomplety();
        $complets = $completor->getColumnsFromAbraFlexi('*', ['limit' => 0]);

        $bundles = [];

        foreach ($complets as $complet) {
            $bundles[(string) $complet['cenikSada']][] = [(string) $complet['cenik'] => $complet['mnozMj']];
        }

        return $bundles;
    }

    /**
     * Get bundle codes containing the given pricelist item.
     *
     * @return list<string>
     */
    public function getBundlesForItem(string $pricelistCode): array
    {
        $completor = new SadyAKomplety();
        $items = $completor->getColumnsFromAbraFlexi(
            ['cenikSada'],
            ['cenik' => \AbraFlexi\Code::ensure($pricelistCode), 'limit' => 0],
        );
        $bundleCodes = [];

        foreach ($items as $item) {
            $bundleCode = (string) ($item['cenikSada'] ?? '');

            if ($bundleCode !== '') {
                $bundleCodes[$bundleCode] = true;
            }
        }

        return array_keys($bundleCodes);
    }

    /**
     * Resolve a pricelist record ID to its code.
     */
    public function getItemCodeById(int $recordId): ?string
    {
        $item = $this->getColumnsFromAbraFlexi(['kod'], ['id' => $recordId]);

        if (!isset($item[0]['kod']) || (string) $item[0]['kod'] === '') {
            return null;
        }

        return (string) $item[0]['kod'];
    }

    /**
     * calculate the selling price from the purchase prices of the price set
     * items.
     *
     * @return float
     */
    public function overallPrice()
    {
        $overAllPrice = 0;
        $items = [];

        foreach ($this->getDataValue('sady-a-komplety') as $item) {
            $overAllPrice += $this->getSubItemPrice($item['cenik']) * (float) $item['mnozMj'];
            $items[] = $item['cenik'];
        }

        if ($overAllPrice === 0) {
            $this->addStatusMessage($this->getRecordCode().' Overall price: 0! : ['.implode(',', $items).']', 'warning');
        }

        return $overAllPrice;
    }

    /**
     * get purchase price for price list item.
     *
     * @param string $pricelistCode
     *
     * @return null|float
     */
    public function getSubItemPrice($pricelistCode)
    {
        $subproductHelper = new Cenik();
        $item = $this->getColumnsFromAbraFlexi(['nakupCena', 'sada', 'skupZboz'], ['id' => str_replace("'", "\\'", \AbraFlexi\Functions::code($pricelistCode))]);

        if (\array_key_exists(0, $item)) {
            if ($item[0]['nakupCena'] === 0) {
                $this->addStatusMessage('Item '.$pricelistCode.' without purchase price  ', 'debug');
            }
        } else {
            $this->addStatusMessage('Error loading: '.$pricelistCode, 'warning');
        }

        return empty($item) ? null : $item[0]['nakupCena'];
    }

    /**
     * Save Bundle Price.
     *
     * @param float $price to save
     *
     * @return array<string, mixed>|bool
     */
    public function saveBundlePrice(float $price)
    {
        $updates = [
            'id' => $this->getRecordIdent(),
            'nakupCena' => $price,
            'cena2' => $price,
            'cena3' => $price,
            'cena4' => $price,
            'cena5' => $price,
            // AbraFlexi requires 'nazev' on every update call, even when it is
            // already set and unchanged — omitting it here fails with
            // "Pole 'Název' musí být vyplněno" regardless of the record's
            // current name. Always resend the current (or backfilled) value.
            'nazev' => $this->getDataValue('nazev') ?: \AbraFlexi\Code::strip($this->getRecordCode()),
        ];

        return $this->insertToAbraFlexi($updates);
    }

    /**
     * Create Supplier Price provided by PriceFix.
     *
     * @param float|int $price
     *
     * @return bool
     */
    public function saveProviderPrice($price)
    {
        $supplier = new \AbraFlexi\RW([
            'cenik' => $this->getRecordCode(),
            'firma' => \Ease\Shared::cfg('ABRAFLEXI_PROVIDER', 'code:PRICEFIXER'),
            'kodIndi' => $this->getDataValue('kod'),
            'primarni' => true,
            'nakupCena' => $price,
        ], ['evidence' => 'dodavatel']);

        return $supplier->sync();
    }

    /**
     * Fix a single bundle.
     *
     * @return array{bundle: string, changed: bool, price: float, success: bool}
     */
    public function fixBundle(string $bundleCode, string $progress = ''): array
    {
        $group = \AbraFlexi\Code::ensure(\Ease\Shared::cfg('ABRAFLEXI_GROUP', 'code:SADA'));
        $this->loadFromAbraFlexi($bundleCode);
        $pprice = $this->getDataValue('nakupCena');
        $success = true;

        $updates = ['id' => \AbraFlexi\Code::ensure($bundleCode)];

        // Ensure skupZboz is set to SADA group
        if ($this->getDataValue('skupZboz') !== $group) {
            $updates['skupZboz'] = $group;
        }

        // Ensure typZasoby is VÝROBOK for all bundles
        if ($this->getDataValue('typZasobyK') !== 'typZasoby.vyrobek') {
            $updates['typZasobyK'] = 'typZasoby.vyrobek';
        }

        // Ensure # prefix on kod (after NEAKT_ if present, never double-add)
        $currentKod = (string) $this->getDataValue('kod');
        $neaktPrefix = str_starts_with($currentKod, 'NEAKT_') ? 'NEAKT_' : '';
        $baseKod = $neaktPrefix ? substr($currentKod, 6) : $currentKod;

        if (!str_starts_with($baseKod, '#')) {
            $updates['kod'] = $neaktPrefix.'#'.$baseKod;
        }

        if (\count($updates) > 1) {
            // AbraFlexi requires 'nazev' on every update call, even when it
            // is already set and unchanged (see saveBundlePrice()) — resend
            // the current (or backfilled) value whenever we update at all.
            $updates['nazev'] = $this->getDataValue('nazev') ?: \AbraFlexi\Code::strip($bundleCode);
            $attributesSaved = (bool) $this->insertToAbraFlexi($updates);

            if (!$attributesSaved) {
                $success = false;
            }

            $this->addStatusMessage(
                trim($progress.' '.sprintf(_('%s: Fixing attributes'), $bundleCode)),
                $attributesSaved ? 'success' : 'error',
            );
        }

        $bundlePrice = $this->overallPrice();
        $changed = (string) $pprice !== (string) $bundlePrice;

        if ($changed) {
            $priceSaved = (bool) $this->saveBundlePrice($bundlePrice);

            if (!$priceSaved) {
                $success = false;
            }

            $this->addStatusMessage(
                trim($progress.' 📦 '.\AbraFlexi\Code::strip($bundleCode).' '.$pprice.' ➟ 💰 '.(string) $bundlePrice.' 💶'),
                $priceSaved ? 'success' : 'error',
            );
        } else {
            $this->addStatusMessage(
                trim($progress.' 📦 '.\AbraFlexi\Code::strip($bundleCode).'  = 💰 '.(string) $bundlePrice.' 💶 - no change'),
                'info',
            );
        }

        return [
            'bundle' => $bundleCode,
            'changed' => $changed,
            'price' => $bundlePrice,
            'success' => $success,
        ];
    }

    /**
     * Fix all bundles containing the given item.
     *
     * @return array{
     *     matched: int,
     *     updated: int,
     *     unchanged: int,
     *     failed: int,
     *     bundles: list<array{bundle: string, changed: bool, price: float, success: bool}>
     * }
     */
    public function fixOne(string $pricelistCode): array
    {
        $bundleCodes = $this->getBundlesForItem($pricelistCode);
        $results = [];
        $pos = 0;

        foreach ($bundleCodes as $bundleCode) {
            $progress = (string) (++$pos).'/'.(string) \count($bundleCodes);
            $results[] = $this->fixBundle($bundleCode, $progress);
        }

        return [
            'matched' => \count($results),
            'updated' => \count(array_filter($results, static fn (array $result): bool => $result['success'] && $result['changed'])),
            'unchanged' => \count(array_filter($results, static fn (array $result): bool => $result['success'] && !$result['changed'])),
            'failed' => \count(array_filter($results, static fn (array $result): bool => !$result['success'])),
            'bundles' => $results,
        ];
    }

    /**
     * Fix All Prices.
     */
    public function fixAll(): void
    {
        $this->bundles = $this->getBundles();
        $pos = 0;

        foreach ($this->bundles as $bundleCode => $bundle) {
            $progress = (string) (++$pos).'/'.(string) \count($this->bundles);
            $this->fixBundle($bundleCode, $progress);
        }
    }
}
