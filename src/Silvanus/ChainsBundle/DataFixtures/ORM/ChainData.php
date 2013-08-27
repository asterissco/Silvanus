<?
namespace Silvanus\ChainsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Silvanus\ChainsBundle\Entity\Chain;

class LoadUserData implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {

        $chain = new Chain();
        $chain->setName('client1');
        $chain->setHost('10.25.54.144');
        $chain->setPolicy('ACCEPT');
        $manager->persist($chain);

        $chain = new Chain();
        $chain->setName('client2');
        $chain->setHost('192.168.100.25');
        $chain->setPolicy('ACCEPT');
        $manager->persist($chain);

        $chain = new Chain();
        $chain->setName('client3');
        $chain->setHost('85.65.44.212');
        $chain->setPolicy('ACCEPT');
        $manager->persist($chain);

        $chain = new Chain();
        $chain->setName('client4');
        $chain->setHost('25.125.254.36');
        $chain->setPolicy('ACCEPT');
        $manager->persist($chain);

        $manager->flush();
        
    }
}
