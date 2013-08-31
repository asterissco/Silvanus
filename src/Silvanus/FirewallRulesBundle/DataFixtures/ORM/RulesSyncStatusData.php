<?
namespace Silvanus\FirewallRulesBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Silvanus\FirewallRulesBundle\Entity\RulesSyncStatus;

class LoadRulesSyncStatusData implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {

		$syncStatus = new RulesSyncStatus();
		$syncStatus->setName('No sync');
		
		$manager->persist($syncStatus);

		$syncStatus = new RulesSyncStatus();
		$syncStatus->setName('Sync OK');
		
		$manager->persist($syncStatus);

		$syncStatus = new RulesSyncStatus();
		$syncStatus->setName('Sync ERROR');
		
		$manager->persist($syncStatus);

		$manager->flush();

    }
}
