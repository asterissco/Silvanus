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
		$syncStatus->setName('PENDING');
		
		$manager->persist($syncStatus);

		$syncStatus = new RulesSyncStatus();
		$syncStatus->setName('OK ');
		
		$manager->persist($syncStatus);

		$syncStatus = new RulesSyncStatus();
		$syncStatus->setName('ERROR');

		$syncStatus = new RulesSyncStatus();
		$syncStatus->setName('DISPATCH');

		
		$manager->persist($syncStatus);

		$manager->flush();

    }
}
