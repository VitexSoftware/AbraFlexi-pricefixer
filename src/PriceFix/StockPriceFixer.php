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
use AbraFlexi\SkladovaKarta;

/**
 * Updates nakupCena in cenik from posledCena on skladova-karta.
 * Skips SADA bundles (handled by Bundler) and zero/missing last prices.
 */
class StockPriceFixer extends Cenik
{
    /**
     * Copy posledCena → nakupCena for all non-SADA warehouse products.
     */
    public function fixAll(): void
    {
        $group = \AbraFlexi\Functions::code(
            \Ease\Shared::cfg('ABRAFLEXI_GROUP', 'code:SADA')
        );
        $warehouseCode = \Ease\Shared::cfg('ABRAFLEXI_WAREHOUSE', 'code:SKLAD-DS');

        $items = $this->getColumnsFromAbraFlexi(
            ['id', 'kod', 'nakupCena', 'skupZboz'],
            ['skladove' => 'true', 'limit' => 0]
        ) ?: [];

        $sklad = new SkladovaKarta();
        $pos   = 0;
        $total = \count($items);

        foreach ($items as $item) {
            $progress = (string) (++$pos) . '/' . (string) $total;

            if ($item['skupZboz'] === $group) {
                continue;
            }

            $stockRows = $sklad->getColumnsFromAbraFlexi(
                ['posledCena'],
                ['cenik' => $item['id'], 'sklad' => $warehouseCode]
            );

            if (empty($stockRows)) {
                continue;
            }

            $lastPrice = (float) ($stockRows[0]['posledCena'] ?? 0);

            if ($lastPrice <= 0) {
                continue;
            }

            $currentPrice = (float) ($item['nakupCena'] ?? 0);

            if ((string) $currentPrice === (string) $lastPrice) {
                $this->addStatusMessage(
                    $progress . ' ' . $item['kod'] . ' = ' . (string) $lastPrice . ' — no change',
                    'info'
                );

                continue;
            }

            $this->insertToAbraFlexi(['id' => $item['id'], 'nakupCena' => $lastPrice]);
            $this->addStatusMessage(
                $progress . ' ' . $item['kod'] . ': ' . (string) $currentPrice . ' → ' . (string) $lastPrice,
                $this->lastResponseCode === 201 ? 'success' : 'error'
            );
        }
    }
}
