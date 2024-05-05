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
     * calculate the selling price from the purchase prices of the price set 
     * items
     * 
     * @return float
     */
    public function overallPrice() {
        $subproductHelper = new Cenik();
        $subitemsPrice = 0;
        foreach ($this->getDataValue('sady-a-komplety') as $item) {
            $subproductHelper->loadFromAbraFlexi(\AbraFlexi\Functions::code($item['cenik']));
            $subitemsPrice += ($subproductHelper->getDataValue('nakupCena') * floatval($item['mnozMj']));
        }
        return $subitemsPrice;
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
}
