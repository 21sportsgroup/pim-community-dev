<?php
namespace Pim\Bundle\InstallerBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Pim\Bundle\ConfigBundle\Entity\Channel;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Config\FileLocator;

/**
 * Load fixtures for channels
 *
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2012 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class LoadChannelData extends AbstractInstallerFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $configuration = Yaml::parse(realpath($this->getFilePath()));

        foreach ($configuration['channels'] as $data) {
            $channel = $this->createChannel($data['code'], $data['label']);
            $manager->persist($channel);
        }

        $manager->flush();
    }

    /**
     * Create a channel
     * @param string $code Channel code
     * @param string $name Channel name
     *
     * @return \Pim\Bundle\ConfigBundle\Entity\Channel
     */
    protected function createChannel($code, $name)
    {
        $channel = new Channel();
        $channel->setCode($code);
        $channel->setName($name);
        $this->setReference('channel.'. $code, $channel);

        return $channel;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return 'channels';
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 1;
    }
}
