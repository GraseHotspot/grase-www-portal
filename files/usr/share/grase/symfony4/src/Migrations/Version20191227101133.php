<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\Setting;
use App\Util\SettingsUtils;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Move old networkoptions serialized option to individual options
 */
final class Version20191227101133 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /** @var EntityManagerInterface */
    private $em;

    /** @var EntityRepository */
    private $settingsRepository;

    /**
     * @param Schema $schema
     *
     */
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE settings CHANGE setting setting VARCHAR(30) NOT NULL');
    }

    /**
     * @param Schema $schema
     *
     */
    public function postUp(Schema $schema): void
    {
        $this->em = $this->container->get('doctrine.orm.entity_manager');

        $this->settingsRepository = $this->em->getRepository(Setting::class);

        /** @var Setting $oldNetworkOptionsSetting */
        $oldNetworkOptionsSetting = $this->settingsRepository->find('networkoptions');
        if (!$oldNetworkOptionsSetting) {
            // We have already converted the option, don't try to do it again
            return;
        }
        $oldNetworkOptions = unserialize($oldNetworkOptionsSetting->getRawValue());

        $this->setAndCreateSetting(Setting::NETWORK_LAN_INTERFACE, $oldNetworkOptions['lanif']);
        $this->setAndCreateSetting(Setting::NETWORK_LAN_IP, $oldNetworkOptions['lanipaddress']);
        $this->setAndCreateSetting(Setting::NETWORK_LAN_MASK, $oldNetworkOptions['networkmask']);
        $this->setAndCreateSetting(Setting::NETWORK_WAN_INTERFACE, $oldNetworkOptions['wanif']);
        $this->setAndCreateSetting(Setting::NETWORK_DNS_SERVERS, (array) $oldNetworkOptions['dnsservers']);
        $this->setAndCreateSetting(Setting::NETWORK_BOGUS_NX, (array) $oldNetworkOptions['bogusnx']);

        $this->setAndCreateSetting(Setting::NETWORK_LAST_CHANGED, time());

        // delete networkoptions setting
        $this->em->remove($oldNetworkOptionsSetting);

        $this->em->flush();
    }

    private function setAndCreateSetting($name, $value)
    {
        $setting = $this->settingsRepository->find($name);
        if (!$setting) {
            $setting = new Setting($name);
        }
        $setting->setValue($value);

        $this->em->persist($setting);

        return $setting;
    }

    /**
     * @param Schema $schema
     *
     * @throws \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE settings CHANGE setting setting VARCHAR(20) NOT NULL');
    }
}
