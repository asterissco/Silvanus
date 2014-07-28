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
		 
	
		$this->em 				= $this->getContainer()->get('doctrine')->getManager();
		$this->test_chain 		= $this->getContainer()->getParameter('test_chain');
		$this->iptables_path 	= $this->getContainer()->getParameter('iptables_path');
		
	 }
	 

    protected function execute(InputInterface $input, OutputInterface $output)
    {
		

		$syncEntities 		= $this->em->getRepository('SilvanusSyncBundle:Sync')->findAll();
		$trustedEntities 	= $this->em->getRepository('SilvanusChainsBundle:Trusted')->findAll();
		
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
			
			if($syncEntity->getAction()=='c' or $syncEntity->getAction()=='u'){
				
			
				$chainEntity = $this->em->getRepository('SilvanusChainsBundle:Chain')->findOneBy(array('id'=>$syncEntity->getChainId()));
				
				echo '#'.$chainEntity->getId().' '.$chainEntity->getName()."\n";
				
				//delete and create test chain
				exec($this->iptables_path.'  -F '.$this->test_chain.' 2>&1',$trash);
				exec($this->iptables_path.'  -X '.$this->test_chain.' 2>&1',$trash);
				exec($this->iptables_path.'  -N '.$this->test_chain.' 2>&1',$trash);
				
				//load and test rules for this chain
				$builder 	= $this->em->getRepository('SilvanusFirewallRulesBundle:FirewallRules')->createQueryBuilder('f');
				$query 		= $builder
								->where('f.chain_id = :id_chain')
								->setParameter(':id_chain',$chainEntity->getId())
								->orderBy('f.priority','asc')
								->getQuery();
				
				$firewallRulesEntities = $query->getResult();
				
				$errorHandle = false;

				for($n=0;$n<count($firewallRulesEntities);$n++){
				
					if(strpos($firewallRulesEntities[$n]->getRule(),'[')!==false){
						
						$firewallRulesEntities[$n]->setRule(str_replace("[","",$firewallRulesEntities[$n]->getRule()));
						$firewallRulesEntities[$n]->setRule(str_replace("]","",$firewallRulesEntities[$n]->getRule()));
						
					}
					
				}

				
				//test loop
				foreach($firewallRulesEntities as $firewallRulesEntity){
					
					$rule = $this->iptables_path." -I ".$this->test_chain." ".$firewallRulesEntity->getPriority()." ".$firewallRulesEntity->getRule()." 2>&1 ";
					
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
					
					$rule = $this->iptables_path." -I ".$chainEntity->getName()." ".$firewallRulesEntity->getPriority()." ".$firewallRulesEntity->getRule()." 2>&1 ";
					$rule = str_replace('/host/',' '.$chainEntity->getHost().' ',$rule);
					echo $rule;
					
					if($errorHandle){
						$firewallRulesEntity->setSyncStatus($this->em->getRepository('SilvanusFirewallRulesBundle:RulesSyncStatus')->findOneBy(array('name'=>'Sync ERROR')));
						$firewallRulesEntity->setSyncErrorMessage(serialize($output));
						$this->em->persist($firewallRulesEntity);
					}
						
					echo "\n";

					
				}

				exec($this->iptables_path.'  -F '.$this->test_chain.' 2>&1',$trash);
				exec($this->iptables_path.'  -X '.$this->test_chain.' 2>&1',$trash);

				
				//no errors found, inset rules into chain and chain into trusted chain
				if(!$errorHandle){

					foreach($trustedEntities as $trustedEntity){
					
						exec($this->iptables_path.'  -D '.$trustedEntity->getName().' -j '.$chainEntity->getName().' 2>&1',$trash);
						
					}

					$chainEntity->setError(false);
				
					//delete and create test chain
					exec($this->iptables_path.'  -F '.$chainEntity->getName().' 2>&1',$trash);
					exec($this->iptables_path.'  -X '.$chainEntity->getName().' 2>&1',$trash);
					exec($this->iptables_path.'  -N '.$chainEntity->getName().' 2>&1',$trash);
					
					//set the rules to chain
					foreach($firewallRulesEntities as $firewallRulesEntity){
					
						$rule = $this->iptables_path." -I ".$chainEntity->getName()." ".$firewallRulesEntity->getPriority()." ".$firewallRulesEntity->getRule()." 2>&1 ";
						$rule = str_replace('/host/',' '.$chainEntity->getHost().' ',$rule);
						
						
						$firewallRulesEntity->setSyncStatus($this->em->getRepository('SilvanusFirewallRulesBundle:RulesSyncStatus')->findOneBy(array('name'=>'Sync OK')));
						$firewallRulesEntity->setSyncErrorMessage('');
						
						$this->em->persist($firewallRulesEntity);				
										
						exec($rule.' 2>&1',$trash);
						
					}
				
					//delete the petition
					$this->em->remove($syncEntity);

					foreach($chainEntity->getTrusted() as $trusted){
					
						exec($this->iptables_path.'  -A '.$trusted.' -j '.$chainEntity->getName().' 2>&1',$trash);
						
					}
					
					
				}else{
					
					$chainEntity->setError(true);
					$syncEntity->setError(true);
					$this->em->persist($syncEntity);
					
				}
				
				
				$this->em->persist($chainEntity);	
				
				

			
			}

		}
		
		$this->em->flush();

	}

}
