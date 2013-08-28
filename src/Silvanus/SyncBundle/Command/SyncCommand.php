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
			
			echo '#'.$chainEntity->getId().' '.$chainEntity->getName()."\n";
			
			//delete and create test chain
			exec('iptables -F silvanus_test_chain 2>&1',$trash);
			exec('iptables -X silvanus_test_chain 2>&1',$trash);
			exec('iptables -N silvanus_test_chain');
			
			//load and test rules for this chain
			$builder 	= $this->em->getRepository('SilvanusFirewallRulesBundle:FirewallRules')->createQueryBuilder('f');
			$query 		= $builder
							->where('f.chain_id = :id_chain')
							->setParameter(':id_chain',$chainEntity->getId())
							->orderBy('f.priority','asc')
							->getQuery();
			
			$firewallRulesEntities = $query->getResult();
			
			$errorHandle = false;
			
			//test loop
			foreach($firewallRulesEntities as $firewallRulesEntity){
				
				$rule = "iptables -I silvanus_test_chain ".$firewallRulesEntity->getPriority()." ".$firewallRulesEntity->getRule()." 2>&1 ";
				
				if($chainEntity->getHost()!=''){
				
					$rule = str_replace('/host/',' '.$chainEntity->getHost().' ',$rule);
				
				}
				
				unset($output);
				exec($rule,$output);
				
				//error found
				if(count($output)>0){
					echo ' [error]	';
					$errorHandle = true;
				//no error found	
				}else{
					echo ' [ok]		';
				}
				
				echo $rule;
				
				if($errorHandle){
					print_r($output);
				}
					
				echo "\n";

				
			}

			exec('iptables -F silvanus_test_chain 2>&1',$trash);
			exec('iptables -X silvanus_test_chain 2>&1',$trash);

			
			//no errors found
			if(!$errorHandle){
			
				//delete and create test chain
				exec('iptables -F '.$chainEntity->getName().' 2>&1',$trash);
				exec('iptables -X '.$chainEntity->getName().' 2>&1',$trash);
				exec('iptables -N '.$chainEntity->getName());
				
				//set the rules to chain
				foreach($firewallRulesEntities as $firewallRulesEntity){
				
					$rule = "iptables -I ".$chainEntity->getName()." ".$firewallRulesEntity->getPriority()." ".$firewallRulesEntity->getRule()." 2>&1 ";
				
					exec($rule);
					
				}
			
				//delete the petition
				$this->em->remove($syncEntity);
				
				
			}else{
				
				$syncEntity->setError(true);
				$this->em->persist($syncEntity);
				
			}
				
		}
		
		$this->em->flush();

	}

}





















