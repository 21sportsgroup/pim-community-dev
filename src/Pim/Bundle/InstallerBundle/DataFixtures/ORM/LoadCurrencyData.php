<?php
namespace Pim\Bundle\InstallerBundle\DataFixtures\ORM;

use Pim\Bundle\ConfigBundle\Entity\Currency;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Yaml\Yaml;

/**
 * Load fixtures for currencies
 *
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2012 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 */
class LoadCurrencyData extends AbstractInstallerFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $allCurrencies = $this->container->getParameter('pim_config.currencies');
        $activatedCurrencies = Yaml::parse(realpath($this->getFilePath()));

        foreach ($allCurrencies['currencies'] as $currencyCode => $currencyName) {
            $activated = in_array($currencyCode, $activatedCurrencies['currencies']);
            $currency = $this->createCurrency($currencyCode, $activated);
            $this->setReference('currency.'. $currencyCode, $currency);
            $manager->persist($currency);
        }

        $manager->flush();
    }

    /**
     * Create currency entity and persist it
     * @param string  $code      Currency code
     * @param boolean $activated Define if currency is activated or not
     *
     * @return \Pim\Bundle\ConfigBundle\Entity\Currency
     */
    protected function createCurrency($code, $activated = false)
    {
        $currency = new Currency();
        $currency->setCode($code);
        $currency->setActivated($activated);

        return $currency;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return 'currencies';
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 2;
    }
}
