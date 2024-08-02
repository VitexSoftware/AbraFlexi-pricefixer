<?php

/**
 * AbraFlexi Price Fixer
 *
 * This file is part of the AbraFlexi Price Fixer package.
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */

namespace AbraFlexi\PriceFix;

use AbraFlexi\Cenik;

class Bundler extends \AbraFlexi\Cenik {

    /**
     * 
     * @var array
     */
    private $bundles = [];

    /**
     * Get Bundles
     * 
     * @return array
     */
    public function getBundles() {
        $completor = new \AbraFlexi\PriceFix\SadyAKomplety();
        $complets = $completor->getColumnsFromAbraFlexi('*', ['limit' => 0]);

        $bundles = [];
        foreach ($complets as $complet) {
            $bundles[strval($complet['cenikSada'])][] = [strval($complet['cenik']) => $complet['mnozMj']];
        }

        return $bundles;
    }

    /**
     * calculate the selling price from the purchase prices of the price set 
     * items
     * 
     * @return float
     */
    public function overallPrice() {
        $overAllPrice = 0;
        $items = [];
        foreach ($this->getDataValue('sady-a-komplety') as $item) {
            $overAllPrice += $this->getSubItemPrice($item['cenik']) * floatval($item['mnozMj']);
            $items[] = $item['cenik'];
        }
        if ($overAllPrice == 0) {
            $this->addStatusMessage($this->getRecordCode(). ' Overall price: 0! : [' . implode(',', $items) . ']', 'warning');
        }
        return $overAllPrice;
    }

    /**
     * get purchase price for price list item
     * 
     * @param string $pricelistCode
     * 
     * @return float|null
     */
    public function getSubItemPrice($pricelistCode) {
        $subproductHelper = new Cenik();
        $item = $this->getColumnsFromAbraFlexi(['nakupCena', 'sada', 'skupZboz'], ['id' => \AbraFlexi\Functions::code($pricelistCode)]);
        if ($item[0]['nakupCena'] == 0) {
            $this->addStatusMessage('Item ' . $pricelistCode . ' without purchase price  ', 'debug');
        }
        return empty($item) ? null : $item[0]['nakupCena'];
    }

    /**
     * Save Bundle Price
     * 
     * @param float $price to save
     *
     * @return array
     */
    public function saveBundlePrice($price) {
        return $this->insertToAbraFlexi([
                    'id' => $this->getRecordIdent(),
                    'nakupCena' => $price,
                    'cena2' => $price,
                    'cena3' => $price,
                    'cena4' => $price,
                    'cena5' => $price
                        //"cenaZaklBezDph" ?
                        //"cenaZaklVcDph" ?
                        //"cenaZakl" ?
        ]);
    }

    /**
     * Create Supplier Price provided by PriceFix
     *
     * @param float|int $price
     *
     * @return boolean
     */
    public function saveProviderPrice($price) {
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
     * Fix All Prices
     */
    public function fixAll() {
        $group = \AbraFlexi\Functions::code(\Ease\Shared::cfg('ABRAFLEXI_GROUP', 'code:SADA'));
        $this->bundles = $this->getBundles();
        $pos = 0;
        foreach ($this->bundles as $bundleCode => $bundle) {
            $progress = strval(++$pos) . '/' . strval(count($this->bundles));
            $this->loadFromAbraFlexi($bundleCode);

            if ($this->getDataValue('skupZboz') != $group) {
                $this->insertToAbraFlexi(['id' => \AbraFlexi\Functions::code($bundleCode), 'skupZboz' => $group]);
                $this->addStatusMessage($progress . ' ' . sprintf(_('%s: Fixing Group to %s '), $bundleCode, $group), $this->lastResponseCode == 201 ? 'success' : 'error');
            }

            $bundlePrice = $this->overallPrice();
            $this->addStatusMessage($progress . ' 📦 ' . \AbraFlexi\Functions::uncode($bundleCode) . '  = 💰 ' . strval($bundlePrice) . ' 💶', $this->saveBundlePrice($bundlePrice) ? 'success' : 'error');
        }
    }
}
