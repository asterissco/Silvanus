<?
namespace Silvanus\ChainsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Silvanus\ChainsBundle\Entity\Trusted;

class LoadTrustedData implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {

        $trusted = new Trusted();
        $trusted->setName('INPUT');
        $manager->persist($trusted);

        $trusted = new Trusted();
        $trusted->setName('FORWARD');
        $manager->persist($trusted);

        $trusted = new Trusted();
        $trusted->setName('OUTPUT');
        $manager->persist($trusted);


        $manager->flush();
        
    }

}
