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

use AbraFlexi\PriceFix\Bundler;

class BundlerTest extends \PHPUnit\Framework\TestCase
{
    public function testFixAllUsesSingleBundleOperation(): void
    {
        $bundler = new class() extends Bundler {
            /**
             * @var list<array{bundle: string, progress: string}>
             */
            public array $fixed = [];

            public function getBundles()
            {
                return [
                    'code:BUNDLE-1' => [],
                    'code:BUNDLE-2' => [],
                ];
            }

            public function fixBundle(string $bundleCode, string $progress = ''): array
            {
                $this->fixed[] = ['bundle' => $bundleCode, 'progress' => $progress];

                return ['bundle' => $bundleCode, 'changed' => false, 'price' => 0.0, 'success' => true];
            }
        };

        $bundler->fixAll();

        self::assertSame([
            ['bundle' => 'code:BUNDLE-1', 'progress' => '1/2'],
            ['bundle' => 'code:BUNDLE-2', 'progress' => '2/2'],
        ], $bundler->fixed);
    }

    public function testFixOneReportsAffectedBundles(): void
    {
        $bundler = new class() extends Bundler {
            /**
             * @var list<array{bundle: string, progress: string}>
             */
            public array $fixed = [];

            public function getBundlesForItem(string $pricelistCode): array
            {
                \PHPUnit\Framework\TestCase::assertSame('code:COMPONENT', $pricelistCode);

                return ['code:BUNDLE-1', 'code:BUNDLE-2', 'code:BUNDLE-3'];
            }

            public function fixBundle(string $bundleCode, string $progress = ''): array
            {
                $this->fixed[] = ['bundle' => $bundleCode, 'progress' => $progress];

                return match ($bundleCode) {
                    'code:BUNDLE-1' => ['bundle' => $bundleCode, 'changed' => true, 'price' => 12.5, 'success' => true],
                    'code:BUNDLE-2' => ['bundle' => $bundleCode, 'changed' => false, 'price' => 8.0, 'success' => true],
                    default => ['bundle' => $bundleCode, 'changed' => true, 'price' => 3.0, 'success' => false],
                };
            }
        };

        $result = $bundler->fixOne('code:COMPONENT');

        self::assertSame(3, $result['matched']);
        self::assertSame(1, $result['updated']);
        self::assertSame(1, $result['unchanged']);
        self::assertSame(1, $result['failed']);
        self::assertSame([
            ['bundle' => 'code:BUNDLE-1', 'progress' => '1/3'],
            ['bundle' => 'code:BUNDLE-2', 'progress' => '2/3'],
            ['bundle' => 'code:BUNDLE-3', 'progress' => '3/3'],
        ], $bundler->fixed);
    }
}
