<?

namespace Silvanus\SyncBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SyncCommand extends ContainerAwareCommand
{
	
    protected function configure()
    {

       $this
            ->setName('silvanus:sync')
            ->setDescription('Silvanus sincronizator')
        ;

		  

    }

	 protected function initialize(InputInterface $input, OutputInterface $output){
		 
	
		$this->em = $this->getContainer()->get('doctrine')->getManager();
		 
	 }
	 

    protected function execute(InputInterface $input, OutputInterface $output)
    {

		$syncEntities = $this->em->getRepository('SilvanusSyncBundle:Sync')->findAll();
	
		foreach($syncEntities as $syncEntity){
			
			$chainEntity = $this->em->getRepository('SilvanusChainsBundle:Chain')->findOneBy(array('id'=>$syncEntity->getChainId()));
			
			echo '#'.$chainEntity->getId().' '.$chainEntity->getName().' '.$chainEntity->getPolicy()."\n";
			
			//create test chain
			exec('iptables -N silvanus_test_chain');
			
		}

	}
}





















