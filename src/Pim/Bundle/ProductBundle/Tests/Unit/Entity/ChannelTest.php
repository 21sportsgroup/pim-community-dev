<?php

namespace Pim\Bundle\ProductBundle\Tests\Unit\Entity;

use Pim\Bundle\ProductBundle\Entity\Category;
use Pim\Bundle\ProductBundle\Entity\Locale;
use Pim\Bundle\ProductBundle\Entity\Currency;
use Pim\Bundle\ProductBundle\Entity\Channel;

/**
 * Test related class
 *
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ChannelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Channel
     */
    protected $channel;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->channel = new Channel();
    }

    /**
     * Test related method
     */
    public function testConstruct()
    {
        $this->assertEntity($this->channel);
    }

    /**
     * Test getter/setter for id property
     */
    public function testGetSetId()
    {
        $this->assertEmpty($this->channel->getId());

        // change value and assert new
        $newId = 5;
        $this->assertEntity($this->channel->setId($newId));
        $this->assertEquals($newId, $this->channel->getId());
    }

    /**
     * Test getter/setter for code property
     */
    public function testGetSetCode()
    {
        $this->assertEmpty($this->channel->getCode());

        // change value and assert new
        $newCode = 'ecommerce';
        $this->assertEntity($this->channel->setCode($newCode));
        $this->assertEquals($newCode, $this->channel->getCode());
    }

    /**
     * Test getter/setter for name property
     */
    public function testGetSetName()
    {
        $this->assertEmpty($this->channel->getName());

        // change value and assert new
        $newName = 'E-Commerce';
        $this->assertEntity($this->channel->setName($newName));
        $this->assertEquals($newName, $this->channel->getName());
    }

    /**
     * Test getter/setter for category property
     */
    public function testGetSetCategory()
    {
        $this->assertNull($this->channel->getCategory());

        $expectedCategory = $this->createCategory('test-tree');
        $this->assertEntity($this->channel->setCategory($expectedCategory));
        $this->assertEquals($expectedCategory, $this->channel->getCategory());
    }

    /**
     * Create a category for testing
     *
     * @param string $code
     *
     * @return \Pim\Bundle\ProductBundle\Entity\Category
     */
    protected function createCategory($code)
    {
        $category = new Category();
        $category->setCode($code);

        return $category;
    }

    /**
     * Test getter/add/remove for currency property
     */
    public function testGetAddRemoveCurrency()
    {
        $this->assertCount(0, $this->channel->getCurrencies());

        // assert adding the right entity
        $expectedCurrencyEUR = $this->createCurrency('EUR');
        $this->assertEntity($this->channel->addCurrency($expectedCurrencyEUR));
        $this->assertCount(1, $this->channel->getCurrencies());

        $currency = $this->channel->getCurrencies()->first();
        $this->assertEquals($expectedCurrencyEUR, $currency);

        // assert removing the right entity
        $expectedCurrencyUSD = $this->createCurrency('USD');
        $this->channel->addCurrency($expectedCurrencyUSD);
        $this->assertCount(2, $this->channel->getCurrencies());

        $this->assertEntity($this->channel->removeCurrency($expectedCurrencyEUR));
        $this->assertCount(1, $this->channel->getCurrencies());
        $currency = $this->channel->getCurrencies()->first();
        $this->assertEquals($expectedCurrencyUSD, $currency);
    }

    /**
     * Create a currency for testing
     *
     * @param string $code
     *
     * @return \Pim\Bundle\ProductBundle\Entity\Currency
     */
    protected function createCurrency($code)
    {
        $currency = new Currency();
        $currency->setCode($code);

        return $currency;
    }

    /**
     * Test getter/add/remove for locale property
     */
    public function testGetAddRemoveLocale()
    {
        $this->assertCount(0, $this->channel->getLocales());

        // assert adding the right entity
        $expectedLocaleFR = $this->createLocale('fr_FR');
        $this->assertEntity($this->channel->addLocale($expectedLocaleFR));
        $this->assertCount(1, $this->channel->getLocales());

        $locale = $this->channel->getLocales()->first();
        $this->assertEquals($expectedLocaleFR, $locale);

        // assert removing the right entity
        $expectedLocaleEN = $this->createLocale('en_US');
        $this->channel->addLocale($expectedLocaleEN);
        $this->assertCount(2, $this->channel->getLocales());

        $this->assertEntity($this->channel->removeLocale($expectedLocaleFR));
        $this->assertCount(1, $this->channel->getLocales());
        $locale = $this->channel->getLocales()->first();
        $this->assertEquals($expectedLocaleEN, $locale);
    }

    /**
     * Create a locale for testing
     *
     * @param string $code
     *
     * @return \Pim\Bundle\ProductBundle\Tests\Unit\Entity\Locale
     */
    protected function createLocale($code)
    {
        $locale = new Locale();
        $locale->setCode($code);

        return $locale;
    }

    /**
     * Assert an entity
     *
     * @param Channel $entity
     */
    protected function assertEntity($entity)
    {
        $this->assertInstanceOf('Pim\Bundle\ProductBundle\Entity\Channel', $entity);
    }
}