<?php

namespace Test\AbraFlexi\PriceFix;

use AbraFlexi\PriceFix\Bundler;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2024-11-27 at 18:35:40.
 */
class BundlerTest extends \PHPUnit\Framework\TestCase {

    /**
     * @var Bundler
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void {
        $this->object = new Bundler();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void {
        
    }

    /**
     * @covers AbraFlexi\PriceFix\Bundler::getBundles
     * @todo   Implement testgetBundles().
     */
    public function testgetBundles() {
        $this->assertEquals('', $this->object->getBundles());
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers AbraFlexi\PriceFix\Bundler::overallPrice
     * @todo   Implement testoverallPrice().
     */
    public function testoverallPrice() {
        $this->assertEquals('', $this->object->overallPrice());
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers AbraFlexi\PriceFix\Bundler::getSubItemPrice
     * @todo   Implement testgetSubItemPrice().
     */
    public function testgetSubItemPrice() {
        $this->assertEquals('', $this->object->getSubItemPrice());
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers AbraFlexi\PriceFix\Bundler::saveBundlePrice
     * @todo   Implement testsaveBundlePrice().
     */
    public function testsaveBundlePrice() {
        $this->assertEquals('', $this->object->saveBundlePrice());
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers AbraFlexi\PriceFix\Bundler::saveProviderPrice
     * @todo   Implement testsaveProviderPrice().
     */
    public function testsaveProviderPrice() {
        $this->assertEquals('', $this->object->saveProviderPrice());
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers AbraFlexi\PriceFix\Bundler::fixAll
     * @todo   Implement testfixAll().
     */
    public function testfixAll() {
        $this->assertEquals('', $this->object->fixAll());
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete('This test has not been implemented yet.');
    }
}
