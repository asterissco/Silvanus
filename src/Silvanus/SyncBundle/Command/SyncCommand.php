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
		$this->test_chain = $this->getContainer()->getParameter('test_chain');
		
	 }
	 

    protected function execute(InputInterface $input, OutputInterface $output)
    {
		

		$syncEntities = $this->em->getRepository('SilvanusSyncBundle:Sync')->findAll();
	
		foreach($syncEntities as $syncEntity){
		
			unset($trash);
		
			//delete chain
			if($syncEntity->getAction()=='d'){
				
				exec('iptables -D INPUT -j '.$syncEntity->getChainName().' 2>&1',$trash);
				exec('iptables -F '.$syncEntity->getChainName().' 2>&1',$trash);
				exec('iptables -X '.$syncEntity->getChainName().' 2>&1',$trash);
				$this->em->remove($syncEntity);
				
				
			}
			
			if($syncEntity->getAction()=='c' or $syncEntity->getAction()=='u'){
				
			
				$chainEntity = $this->em->getRepository('SilvanusChainsBundle:Chain')->findOneBy(array('id'=>$syncEntity->getChainId()));
				
				echo '#'.$chainEntity->getId().' '.$chainEntity->getName()."\n";
				
				//delete and create test chain
				exec('iptables -F '.$this->test_chain.' 2>&1',$trash);
				exec('iptables -X '.$this->test_chain.' 2>&1',$trash);
				exec('iptables -N '.$this->test_chain.' 2>&1',$trash);
				
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
					
					$rule = "iptables -I ".$this->test_chain." ".$firewallRulesEntity->getPriority()." ".$firewallRulesEntity->getRule()." 2>&1 ";
					
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
					
					$rule = "iptables -I ".$chainEntity->getName()." ".$firewallRulesEntity->getPriority()." ".$firewallRulesEntity->getRule()." 2>&1 ";
					$rule = str_replace('/host/',' '.$chainEntity->getHost().' ',$rule);
					echo $rule;
					
					if($errorHandle){
						print_r($output);
					}
						
					echo "\n";

					
				}

				exec('iptables -F '.$this->test_chain.' 2>&1',$trash);
				exec('iptables -X '.$this->test_chain.' 2>&1',$trash);

				
				//no errors found
				if(!$errorHandle){
				
					//delete and create test chain
					exec('iptables -F '.$chainEntity->getName().' 2>&1',$trash);
					exec('iptables -X '.$chainEntity->getName().' 2>&1',$trash);
					exec('iptables -N '.$chainEntity->getName());
					
					//set the rules to chain
					foreach($firewallRulesEntities as $firewallRulesEntity){
					
						$rule = "iptables -I ".$chainEntity->getName()." ".$firewallRulesEntity->getPriority()." ".$firewallRulesEntity->getRule()." 2>&1 ";
						$rule = str_replace('/host/',' '.$chainEntity->getHost().' ',$rule);
										
						exec($rule.' 2>&1',$trash);
						
					}
				
					//delete the petition
					$this->em->remove($syncEntity);
					
					
				}else{
					
					$syncEntity->setError(true);
					$this->em->persist($syncEntity);
					
				}
				
				//if create a new chain, add trusted host
				if($syncEntity->getAction()=='c'){
			
					exec('iptables -A INPUT -j '.$chainEntity->getName().' 2>&1',$trash);
					
				}	
			
			}

		}
		
		$this->em->flush();

	}

}
