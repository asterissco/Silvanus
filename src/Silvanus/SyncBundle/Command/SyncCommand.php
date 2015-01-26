<?

namespace Silvanus\SyncBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Silvanus\ChainsBundle\Entity\Chain;

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
		 
	
		$this->em 				= $this->getContainer()->get('doctrine')->getManager();
		$this->test_chain 		= $this->getContainer()->getParameter('test_chain');
		$this->iptables_path 	= $this->getContainer()->getParameter('iptables_path');
		
	 }
	 

    protected function execute(InputInterface $input, OutputInterface $output)
    {
		

		$syncEntities 		= $this->em->getRepository('SilvanusSyncBundle:Sync')->findAll();
		$trustedEntities 	= $this->em->getRepository('SilvanusChainsBundle:Trusted')->findAll();
		$testChain 			= new Chain();
		$testChain->setName($this->test_chain);		
		
		foreach($syncEntities as $syncEntity){
		
			unset($trash);
		
			//delete chain
			if($syncEntity->getAction()=='d'){
				
				exec($this->iptables_path.'  -D INPUT -j '.$syncEntity->getChainName().' 2>&1',$trash);
				exec($this->iptables_path.'  -D FORWARD -j '.$syncEntity->getChainName().' 2>&1',$trash);
				exec($this->iptables_path.'  -D OUTPUT -j '.$syncEntity->getChainName().' 2>&1',$trash);
				
				exec($this->iptables_path.'  -F '.$syncEntity->getChainName().' 2>&1',$trash);
				exec($this->iptables_path.'  -X '.$syncEntity->getChainName().' 2>&1',$trash);
				$this->em->remove($syncEntity);
				
				
			}
			
			//create or update chain
			if($syncEntity->getAction()=='c' or $syncEntity->getAction()=='u'){
				
			
				$chainEntity = $this->em->getRepository('SilvanusChainsBundle:Chain')->findOneBy(array('id'=>$syncEntity->getChainId()));
				
				echo '#'.$chainEntity->getId().' '.$chainEntity->getName()."\n";
				
				//delete and create test chain
				exec($this->iptables_path.'  -F '.$this->test_chain.' 2>&1',$trash);
				exec($this->iptables_path.'  -X '.$this->test_chain.' 2>&1',$trash);
				exec($this->iptables_path.'  -N '.$this->test_chain.' 2>&1',$trash);
				
				//load and test rules for this chain				
				//get firewall stack
				$stackEntity = $this->em->getRepository('SilvanusChainsBundle:StackChain')->findBy(array('chainParent'=>$chainEntity->getId()),array('priority'=>'ASC'));
				
				//get normal chain into the stack
				foreach($stackEntity as $stackChainEntity){

					if($stackChainEntity->getChainChildren()->getType()=='normal'){
						$parentChain = $stackChainEntity->getChainChildren();
					}

				}


				
				$errorHandle = false;				
				foreach($stackEntity as $stackChainEntity){

					$builder 	= $this->em->getRepository('SilvanusFirewallRulesBundle:FirewallRules')->createQueryBuilder('f');
					$query 		= $builder
									->where('f.chain = :chain_id')
									->setParameter(':chain_id',$stackChainEntity->getChainChildren()->getId())
									->orderBy('f.priority','asc')
									->getQuery();
					
					$firewallRulesEntities = $query->getResult();

					//~ foreach($firewallRulesEntities as $e){
						//~ echo $e->getRule()."\n";
					//~ }
					
					$testChain->setHost($parentChain->getHost());
					$errorHandle = $this->addRulesToChain($firewallRulesEntities, $testChain, false);
					if($errorHandle){
						break;
					}

				}
									
				//delete test chain
				exec($this->iptables_path.'  -F '.$this->test_chain.' 2>&1',$trash);
				exec($this->iptables_path.'  -X '.$this->test_chain.' 2>&1',$trash);
				
				//no errors found, insert rules into chain and chain into trusted chain
				if(!$errorHandle){

					foreach($trustedEntities as $trustedEntity){
					
						exec($this->iptables_path.'  -D '.$trustedEntity->getName().' -j '.$parentChain->getName().' 2>&1',$trash);
						
					}

					$parentChain->setError(false);
				
					//delete and create test chain
					exec($this->iptables_path.'  -F '.$parentChain->getName().' 2>&1',$trash);
					exec($this->iptables_path.'  -X '.$parentChain->getName().' 2>&1',$trash);
					exec($this->iptables_path.'  -N '.$parentChain->getName().' 2>&1',$trash);

					foreach($stackEntity as $stackChainEntity){

						$builder 	= $this->em->getRepository('SilvanusFirewallRulesBundle:FirewallRules')->createQueryBuilder('f');
						$query 		= $builder
										->where('f.chain = :chain_id')
										->setParameter(':chain_id',$stackChainEntity->getChainChildren()->getId())
										->orderBy('f.priority','asc')
										->getQuery();
						
						$firewallRulesEntities = $query->getResult();

						$errorHandle = $this->addRulesToChain($firewallRulesEntities, $parentChain, true);

					}
					
				
					//delete the petition
					$this->em->remove($syncEntity);

					foreach($chainEntity->getTrusted() as $trusted){
					
						exec($this->iptables_path.'  -A '.$trusted.' -j '.$parentChain->getName().' 2>&1',$trash);
						
					}
					
					
				}else{
					
					$chainEntity->setError(true);
					$syncEntity->setError(true);
					$this->em->persist($syncEntity);
					
				}
				
				$this->em->persist($parentChain);	
				
			
			}

		}
		
		$this->em->flush();

	}
	
	//private function addRulesToChain(\Silvanus\FirewallRulesBundle\FirewallRules $firewallRulesEntities, $chainName){
	private function addRulesToChain($firewallRulesEntities, $chainEntity, $log){

		foreach($firewallRulesEntities as $firewallRulesEntity){

			$errorHandle = false;

			//echo $firewallRulesEntity->getRule()."\n";

			$fixed_rule=str_replace("["," ",$firewallRulesEntity->getRule());
			$fixed_rule=str_replace("]"," ",$fixed_rule);
								
			$rule = $this->iptables_path." -I ".$chainEntity->getName()." ".$firewallRulesEntity->getPriority()." ".$fixed_rule." 2>&1 ";
	
			if($chainEntity->getHost()!=''){			
				$rule = str_replace('/host/',' '.$chainEntity->getHost().' ',$rule);			
			}


			unset($output);
			exec($rule,$output);
			
			//error found
			if(count($output)>0){
				if($log){
					echo ' [error]	';
				}
				$errorHandle = true;
			//no error found	
			}else{
				if($log){
					echo ' [ok]		';
				}
				
			}
			
			$rule = $this->iptables_path." -I ".$chainEntity->getName()." ".$firewallRulesEntity->getPriority()." ".$firewallRulesEntity->getRule()." 2>&1 ";
			$rule = str_replace('/host/',' '.$chainEntity->getHost().' ',$rule);
			if($log){
				echo $rule;			
				echo "\n";
			}
			
			if($errorHandle){
				$firewallRulesEntity->setSyncStatus($this->em->getRepository('SilvanusFirewallRulesBundle:RulesSyncStatus')->findOneBy(array('name'=>'Sync ERROR')));
				$firewallRulesEntity->setSyncErrorMessage(serialize($output));					
				$this->em->persist($firewallRulesEntity);
				return true;				
			}else{
				$firewallRulesEntity->setSyncStatus($this->em->getRepository('SilvanusFirewallRulesBundle:RulesSyncStatus')->findOneBy(array('name'=>'Sync OK')));
				//$firewallRulesEntity->setSyncErrorMessage(serialize($output));									
			}
			
		}
		
		return false;
							
	}
	

}
