<?
namespace Silvanus\FirewallRulesBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Silvanus\FirewallRulesBundle\Entity\TransportProtocol;

class TransportProtocolData implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {

		$transportProtocol = new TransportProtocol();
		$transportProtocol->setName('');		
		$manager->persist($transportProtocol);

		$transportProtocol = new TransportProtocol();
		$transportProtocol->setName('ALL');		
		$manager->persist($transportProtocol);

		$transportProtocol = new TransportProtocol();
		$transportProtocol->setName('TCP');		
		$manager->persist($transportProtocol);

		$transportProtocol = new TransportProtocol();
		$transportProtocol->setName('UDP');		
		$manager->persist($transportProtocol);

		$transportProtocol = new TransportProtocol();
		$transportProtocol->setName('UDPLITE');		
		$manager->persist($transportProtocol);

		$transportProtocol = new TransportProtocol();
		$transportProtocol->setName('ICMP');		
		$manager->persist($transportProtocol);

		$transportProtocol = new TransportProtocol();
		$transportProtocol->setName('ESP');		
		$manager->persist($transportProtocol);

		$transportProtocol = new TransportProtocol();
		$transportProtocol->setName('AH');		
		$manager->persist($transportProtocol);

		$transportProtocol = new TransportProtocol();
		$transportProtocol->setName('SCTP');		
		$manager->persist($transportProtocol);

		$manager->flush();

    }
}
