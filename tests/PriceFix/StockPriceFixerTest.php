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

namespace Test\AbraFlexi\PriceFix;

use AbraFlexi\PriceFix\StockPriceFixer;

class StockPriceFixerTest extends \PHPUnit\Framework\TestCase
{
    protected StockPriceFixer $object;

    protected function setUp(): void
    {
        $this->object = new StockPriceFixer();
    }

    protected function tearDown(): void
    {
    }

    /**
     * @covers \AbraFlexi\PriceFix\StockPriceFixer::fixAll
     */
    public function testfixAll(): void
    {
        // fixAll() requires a live AbraFlexi connection — mark as incomplete for unit runs
        $this->markTestIncomplete('Requires live AbraFlexi connection.');
    }
}
