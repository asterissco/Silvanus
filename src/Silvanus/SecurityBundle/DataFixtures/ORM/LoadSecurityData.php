<?
namespace Silvanus\SecurityBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Silvanus\SecurityBundle\Entity\Role;
use Silvanus\SecurityBundle\Entity\User;

class LoadSecurityData implements FixtureInterface, ContainerAwareInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }	
	
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {

		//generate a new role
		$role = new Role();
		$role->setRole('ROLE_ADMIN');
		$role->setDescription('Unlimited access to all application');
		$manager->persist($role);
		$manager->flush();
		
		//generate a new user
		$user = new User();
		$user->setName('Default User');
		$user->setUsername('admin');		
		$user->setEmail('admin@mydomain');
		$encoder        = $this->container->get('security.encoder_factory')->getEncoder($user);
		$password       = $encoder->encodePassword("admin", $user->getSalt());
		$user->setPassword($password);
		$user->addRole($role);
		$manager->persist($user);
		$manager->flush();
		
		
        
    }
}
