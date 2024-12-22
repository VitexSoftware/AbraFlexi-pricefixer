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

class Bundler extends Cenik
{
    /**
     * Bundles.
     * @var array<string, list<array<string, mixed>>> $bundles
     */
    private array $bundles = [];

    /**
     * Get Bundles.
     *
     * @return array<string, list<array<string, mixed>>>
     */
    public function getBundles()
    {
        $completor = new \AbraFlexi\PriceFix\SadyAKomplety();
        $complets = $completor->getColumnsFromAbraFlexi('*', ['limit' => 0]);

        $bundles = [];

        foreach ($complets as $complet) {
            $bundles[(string) $complet['cenikSada']][] = [(string) $complet['cenik'] => $complet['mnozMj']];
        }

        return $bundles;
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
        $item = $this->getColumnsFromAbraFlexi(['nakupCena', 'sada', 'skupZboz'], ['id' => \AbraFlexi\Functions::code($pricelistCode)]);

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
     * @return array
     */
    public function saveBundlePrice($price)
    {
        return $this->insertToAbraFlexi([
            'id' => $this->getRecordIdent(),
            'nakupCena' => $price,
            'cena2' => $price,
            'cena3' => $price,
            'cena4' => $price,
            'cena5' => $price,
            // "cenaZaklBezDph" ?
            // "cenaZaklVcDph" ?
            // "cenaZakl" ?
        ]);
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
     * Fix All Prices.
     */
    public function fixAll(): void
    {
        $group = \AbraFlexi\Functions::code(\Ease\Shared::cfg('ABRAFLEXI_GROUP', 'code:SADA'));
        $this->bundles = $this->getBundles();
        $pos = 0;

        foreach ($this->bundles as $bundleCode => $bundle) {
            $progress = (string) (++$pos).'/'.(string) \count($this->bundles);
            $this->loadFromAbraFlexi($bundleCode);
            $pprice = $this->getDataValue('nakupCena');

            if ($this->getDataValue('skupZboz') !== $group) {
                $this->insertToAbraFlexi(['id' => \AbraFlexi\Functions::code($bundleCode), 'skupZboz' => $group]);
                $this->addStatusMessage($progress.' '.sprintf(_('%s: Fixing Group to %s '), $bundleCode, $group), $this->lastResponseCode === 201 ? 'success' : 'error');
            }

            $bundlePrice = $this->overallPrice();

            if ((string) $pprice !== (string) $bundlePrice) {
                $this->addStatusMessage($progress.' 📦 '.\AbraFlexi\Functions::uncode($bundleCode).' '.$pprice.' ➟ 💰 '.(string) $bundlePrice.' 💶', $this->saveBundlePrice($bundlePrice) ? 'success' : 'error');
            } else {
                $this->addStatusMessage($progress.' 📦 '.\AbraFlexi\Functions::uncode($bundleCode).'  = 💰 '.(string) $bundlePrice.' 💶 - no change', 'info');
            }
        }
    }
}
