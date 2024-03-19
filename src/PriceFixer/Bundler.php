<?php

/**
 * AbraFlexi Price Fixer
 *
 * This file is part of the AbraFlexi Price Fixer package.
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */

namespace AbraFlexi\PriceFixer;

class Bundler extends \AbraFlexi\Cenik
{

    /**
     * Load Product
     * 
     * @param string $code
     */
    public function overallPrice()
    {
        $subproductHelper = new Cenik();

        $subitemsPrice = 0;
        
        foreach ($this->getDataValue('sady-a-komplety') as $item) {
            $subproductHelper->loadFromAbraFlexi( \AbraFlexi\Functions::code($item['cenik']));
            $subitemsPrice += $subproductHelper->getDataValue('cenaZakl');
        }
        return $subitemsPrice;
    }

    /**
     * Save Bundle Price
     * 
     * @return boolean
     */
    public function saveBundlePrice($price)
    {
        $supplier = new \AbraFlexi\RW([
            'cenik' => $this->getRecordCode(),
            'firma' => 'code:' . \Ease\Shared::cfg('ABRAFLEXI_PROVIDER','code:PRICEFIXER'),
            'kodIndi' => $this->getDataValue('kod'),
            'primarni' => true,
            'nakupCena' => $price,
                ], ['evidence' => 'dodavatel']);
        return $supplier->lastCurlResponseCode == 201;
    }
}