<?php

namespace Silvanus\FirewallRulesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;

use Silvanus\FirewallRulesBundle\Entity\FirewallRules;
use Silvanus\ChainsBundle\Entity\Chain;
use Silvanus\SyncBundle\Entity\Sync;

use Silvanus\FirewallRulesBundle\Form\FirewallRulesType;
use Silvanus\FirewallRulesBundle\Form\FirewallRulesCreateType;
use Silvanus\FirewallRulesBundle\Form\FirewallRulesUpdateType;

/**
 * FirewallRules controller.
 *
 */
class FirewallRulesController extends Controller
{

    /**
     * Lists all FirewallRules entities.
     *
     */
    public function indexAction(Request $request,$chain_id,$message)
    {

		try{		

			$em = $this->getDoctrine()->getManager();

			$chain_entity = $em->getRepository('SilvanusChainsBundle:Chain')->findOneBy(array('id'=>$chain_id));
		
			
		
			//form request
			if($request->getMethod()=='POST'){
				
				$formFilter = $this->createRuleFilterForm();
				$formFilter->submit($request);
				
				$formData = $formFilter->getData();
				
				//$firewallRepository = $em->getRepository('SilvanusFirewallRulesBundle:FirewallRules');
				$builder = $em->getRepository('SilvanusFirewallRulesBundle:FirewallRules')->createQueryBuilder('f');
				$builder->where('f.chain = :chain_id');
				$builder->setParameter(':chain_id',$chain_id);
	
	
				if($formData['rule']!=''){
					
					$builder->andWhere($builder->expr()->like('f.rule', ':rule'));
					$builder->setParameter(':rule','%'.$formData['rule'].'%');
					
				}

				if(isset($formData['sync_error'])){
					

					if($formData['sync_error']!=''){

						if($formData['sync_error']=='true'){	$sync_error=true;	}
						if($formData['sync_error']=='false'){	$sync_error=false;	}
							
						$builder->andWhere($builder->expr()->eq('f.syncError',':sync_error'));
						$builder->setParameter(':sync_error',$sync_error);
					}
					
				}


				$builder->orderBy('f.'.$formData['sort_by'],$formData['sort_direction']);
				
				$query = $builder->getQuery();
				
				$entities = $query->getResult();

				
				
			//no form request
			}else{

				
				$builder = $em->getRepository('SilvanusFirewallRulesBundle:FirewallRules')->createQueryBuilder('f');
				$builder->where('f.chain = :chain_id');
				$builder->setParameter(':chain_id',$chain_id);
				

				$query = $builder
					->orderBy('f.priority','asc')
					->getQuery();
				
				$entities = $query->getResult();

				
				$formFilter = $this->createRuleFilterForm();
				
				
				
			}
			
			//erase special char for mode escape
			for($n=0;$n<count($entities);$n++){
				
			
				if(strpos($entities[$n]->getRule(),'[')!==false){
					
					$entities[$n]->setRule(str_replace("[","",$entities[$n]->getRule()));
					$entities[$n]->setRule(str_replace("]","",$entities[$n]->getRule()));
					
				}
				
			}

			return $this->render('SilvanusFirewallRulesBundle:FirewallRules:index.html.twig', array(
				'entities' 			=> $entities,
				'filter_form' 		=> $formFilter->createView(),         
				'chain_id'			=> $chain_id,
				'chain_entity'		=> $chain_entity,
				'message'			=> $message,
			));

		}catch(Exception $e){
			
			echo $e->getLine()." - ".$e->getMessage();
			
		}

    }

/*	CREATE	*/

    /**
     * Displays a form to create a new FirewallRules entity.
     *
     */
    public function newAction($chain_id)
    {
		
		$em 			= $this->getDoctrine()->getManager();
		$chainEntity 	= $em->getRepository('SilvanusChainsBundle:Chain')->find($chain_id);
		
        $entity = new FirewallRules();
        $form   = $this->createForm(new FirewallRulesCreateType(), $entity);
		$form->get('destination')->setData($chainEntity->getHost());

        return $this->render('SilvanusFirewallRulesBundle:FirewallRules:new.html.twig', array(
			'form'   	=> $form->createView(),
            'chain_id' 	=> $chain_id,
        ));
    }

	/*
	 * Create a new rule
	 * 
	 * */
	public function createAction(Request $request, $chain_id){
		
		$arrForm	= $this->get('request')->request->get('silvanus_firewallrulesbundle_firewallrulestype');
		$form = $this->createForm(new FirewallRulesCreateType());
		$form->submit($request);
		$em =  $this->getDoctrine()->getManager();

		if(isset($arrForm['priority'])){
			if($arrForm['priority']<1){
				$form->get('priority')->addError(new FormError('Priority must be more than 0'));
			}
		}

		if(!empty($arrForm['source_port'])){
			if(!is_numeric($arrForm['source_port'])){
				if(!preg_match('/([a-z])\w+ \([0-9]\w+\) \[([A-Z])\w+\]$/',$arrForm['source_port'])){
					$form->get('source_port')->addError(new FormError('Select a port o type a number'));
				}
			}	
		}
		if(!empty($arrForm['destination_port'])){
			if(!is_numeric($arrForm['destination_port'])){
				if(!preg_match('/([a-z])\w+ \([0-9]\w+\) \[([A-Z])\w+\]$/',$arrForm['destination_port'])){
					$form->get('destination_port')->addError(new FormError('Select a port o type a number'));
				}
			}	
		}

		if($form->get('append')->getData()){
		
			$lastPriority	= $em->getRepository('SilvanusFirewallRulesBundle:FirewallRules')->getLastPriority($chain_id);
			$priority		= $lastPriority;
															
		}else{
		
			$priorityExists	= $em->getRepository('SilvanusFirewallRulesBundle:FirewallRules')->checkPriorityExistsByChain($chain_id,$form->get('priority')->getData());
			
			if($form->get('force')->getData()){

				$em->getRepository('SilvanusFirewallRulesBundle:FirewallRules')->fixPriorityOffset($chain_id,$form->get('priority')->getData());
				$priority = $form->get('priority')->getData();
				
				
			}else{
				
				if($priorityExists){
					$form->get('priority')->addError(new FormError("This priority already used"));
				}else{
					$priority = $form->get('priority')->getData();
				}
				
			}
		
		}

		
		if($form->isValid()){

			$chainEntity 	= $em->getRepository('SilvanusChainsBundle:Chain')->find($chain_id);
			$rule			= $this->contructFirewallRule($arrForm);
	
						
			//create firewallrule
			$ruleEntity = new FirewallRules();
			$ruleEntity->setRule($rule);
			$ruleEntity->setChain($chainEntity);
			$ruleEntity->setPriority($priority);
			$ruleEntity->setSyncStatus($em->getRepository('SilvanusFirewallRulesBundle:RulesSyncStatus')->findOneBy(array('name'=>'No sync')));
			
			$em->persist($ruleEntity);
			$em->flush();

			$syncEntity = $em->getRepository('SilvanusSyncBundle:Sync')->findBy(array('chainId'=>$chain_id));
			if(!$syncEntity and $ruleEntity->getChain()->getActive()){

				$syncEntity = new sync();
				$syncEntity->setChainId($chain_id);
				$syncEntity->setTime(new \DateTime('now'));
				$syncEntity->setError(false);
				$syncEntity->setAction('u');
				$em->persist($syncEntity);
				$em->flush();

			}

			$em->getRepository('SilvanusFirewallRulesBundle:FirewallRules')->fixPriorityIndex($chain_id);

			return $this->redirect($this->generateUrl('firewallrules', 
				array(	'chain_id' => $chain_id, 
						'message'=> 'Create successful',
				)));
						
		}

        return $this->render('SilvanusFirewallRulesBundle:FirewallRules:new.html.twig', array(
			'form'   	=> $form->createView(),
            'chain_id' 	=> $chain_id,
        ));
		
		
	}

/*	UPDATE	*/

    /**
     * Displays a form to edit FirewallRules entity.
     *
     */
    public function editAction($id,$chain_id)
    {
		
		$em 			= $this->getDoctrine()->getManager();
		$chainEntity 	= $em->getRepository('SilvanusChainsBundle:Chain')->find($chain_id);
		
        $entity = $em->getRepository('SilvanusFirewallRulesBundle:FirewallRules')->find($id);
        $form   = $this->createForm(new FirewallRulesCreateType(), $entity);
		$form->get('destination')->setData($chainEntity->getHost());

		$arrRule = $this->deconstructFirewallRule($entity->getRule());

		//~ echo "<pre>";
		//~ print_r($arrRule);
		//~ echo "</pre>";
		//~ exit();

		foreach($arrRule as $key => $val){

			if($key==='protocol'){
				$entityProtocol = $em->getRepository('SilvanusFirewallRulesBundle:TransportProtocol')->findOneBy(array('id'=>$val));
				$form->get($key)->setData($entityProtocol);
			}else{
				$form->get($key)->setData($val);
			}
			
		}

	
        return $this->render('SilvanusFirewallRulesBundle:FirewallRules:edit.html.twig', array(
			'edit_form' => $form->createView(),
            'chain_id' 	=> $chain_id,
            'entity'	=> $entity,
        ));
    }

	public function updateAction(Request $request,$id,$chain_id){
	
	
		$arrForm	= $this->get('request')->request->get('silvanus_firewallrulesbundle_firewallrulestype');
		$form = $this->createForm(new FirewallRulesCreateType());
		$form->submit($request);
		$em =  $this->getDoctrine()->getManager();

		if(isset($arrForm['priority'])){
			if($arrForm['priority']<1){
				$form->get('priority')->addError(new FormError('Priority must be more than 0'));
			}
		}

		if(!empty($arrForm['source_port'])){
			if(!is_numeric($arrForm['source_port'])){
				if(!preg_match('/([a-z])\w+ \([0-9]\w+\) \[([A-Z])\w+\]$/',$arrForm['source_port'])){
					$form->get('source_port')->addError(new FormError('Select a port o type a number'));
				}
			}	
		}
		if(!empty($arrForm['destination_port'])){
			if(!is_numeric($arrForm['destination_port'])){
				if(!preg_match('/([a-z])\w+ \([0-9]\w+\) \[([A-Z])\w+\]$/',$arrForm['destination_port'])){
					$form->get('destination_port')->addError(new FormError('Select a port o type a number'));
				}
			}	
		}

		if($form->get('append')->getData()){
		
			$lastPriority	= $em->getRepository('SilvanusFirewallRulesBundle:FirewallRules')->getLastPriority($chain_id);
			$priority		= $lastPriority;
															
		}else{
		
			$priorityExists	= $em->getRepository('SilvanusFirewallRulesBundle:FirewallRules')->checkPriorityExistsByChain($chain_id,$form->get('priority')->getData(),$id);
			
			if($form->get('force')->getData()){

				$em->getRepository('SilvanusFirewallRulesBundle:FirewallRules')->fixPriorityOffset($chain_id,$form->get('priority')->getData());
				$priority = $form->get('priority')->getData();
				
				
			}else{
				
				if($priorityExists){
					$form->get('priority')->addError(new FormError("This priority already used"));
				}else{
					$priority = $form->get('priority')->getData();
				}
				
			}
		
		}

		
		if($form->isValid()){

			$chainEntity 	= $em->getRepository('SilvanusChainsBundle:Chain')->find($chain_id);
			$rule			= $this->contructFirewallRule($arrForm);
				
			//create firewallrule
			$ruleEntity = $em->getRepository('SilvanusFirewallRulesBundle:FirewallRules')->find($id);
			$ruleEntity->setRule($rule);
			$ruleEntity->setChain($chainEntity);
			$ruleEntity->setPriority($priority);
			$ruleEntity->setSyncStatus($em->getRepository('SilvanusFirewallRulesBundle:RulesSyncStatus')->findOneBy(array('name'=>'No sync')));
			
			$em->persist($ruleEntity);
			$em->flush();

			$syncEntity = $em->getRepository('SilvanusSyncBundle:Sync')->findBy(array('chainId'=>$chain_id));
			if(!$syncEntity and $ruleEntity->getChain()->getActive()){

				$syncEntity = new sync();
				$syncEntity->setChainId($chain_id);
				$syncEntity->setTime(new \DateTime('now'));
				$syncEntity->setError(false);
				$syncEntity->setAction('u');
				$em->persist($syncEntity);
				$em->flush();

			}

			$em->getRepository('SilvanusFirewallRulesBundle:FirewallRules')->fixPriorityIndex($chain_id);
			
			return $this->redirect($this->generateUrl('firewallrules', 
				array(	'chain_id' => $chain_id, 
						'message'=> 'Create successful',
				)));
						
		}

        return $this->render('SilvanusFirewallRulesBundle:FirewallRules:new.html.twig', array(
			'form'   	=> $form->createView(),
            'chain_id' 	=> $chain_id,
        ));
		
	}

    /**
     * Deletes a FirewallRules entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
 
		$em 		= $this->getDoctrine()->getManager();
		$entity 	= $em->getRepository('SilvanusFirewallRulesBundle:FirewallRules')->find($id);
		$chain_id	= $entity->getChain()->getId();

		if (!$entity) {
			throw $this->createNotFoundException('Unable to find FirewallRules entity.');
		}

		$em->remove($entity);
		$em->flush();

		/* add sync petition */
		$syncEntity = $em->getRepository('SilvanusSyncBundle:Sync')->findBy(array('chainId'=>$chain_id));
		if(!$syncEntity){

			$syncEntity = new sync();
			$syncEntity->setChainId($chain_id);
			$syncEntity->setTime(new \DateTime('now'));
			$syncEntity->setError(false);
			$syncEntity->setAction('u');
			$em->persist($syncEntity);
			$em->flush();

		}

		$em->getRepository('SilvanusFirewallRulesBundle:FirewallRules')->fixPriorityIndex($chain_id);

		return $this->redirect($this->generateUrl('firewallrules', 
			array(	'chain_id' => $chain_id, 
					'message'=> 'Delete successful',
			)	
		));
 
 
    }


	/**
     * Switch active/inactive rule
     *
     *
     */
	public function activeSwitchAction(Request $request,$chain_id,$id,$action){
		
		$em		= $this->getDoctrine()->getManager();
		$entity	= $em->getRepository('SilvanusFirewallRulesBundle:FirewallRules')->find($id);
		
		if($action=='set_active'){			
			$entity->setActive(true);			
			$message = 'Set active successful';
		}else{
			$entity->setActive(false);
			$message = 'Set inactive successful';			
		}
		
		$em->persist($entity);
		$em->flush();

		/* add sync petition */
		$syncEntity = $em->getRepository('SilvanusSyncBundle:Sync')->findBy(array('chainId'=>$chain_id));
		if(!$syncEntity){

			$syncEntity = new sync();
			$syncEntity->setChainId($chain_id);
			$syncEntity->setTime(new \DateTime('now'));
			$syncEntity->setError(false);
			$syncEntity->setAction('u');
			$em->persist($syncEntity);
			$em->flush();

		}

		
		return $this->redirect($this->generateUrl('firewallrules', 
			array(	'chain_id' => $chain_id, 
					'message'=> $message,
			)	
		));
		
	}

/*  PRIVATE CONTEXT */

	/**
     * Creates a form to filter rules list
     *
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createRuleFilterForm()
    {
        return $this->createFormBuilder(array())
            ->add('sort_by', 'choice',array(
				'choices'=>array(
						'priority'=>'Priority',
						'rule'=>'Rule',						
						'id'=>'Id',
					),
				'label'=>'Sort by',	
				))
            ->add('sort_direction', 'choice',array(
				'choices'=>array(
						'asc'=>'Asc',
						'desc'=>'Desc',
					),
				'label'=>'Sort direction',	
				))
            //~ ->add('show_free_rules', 'choice',array(
				//~ 'choices'=>array(
						//~ '0'=>'No',
						//~ '1'=>'Yes',
					//~ ),
				//~ 'label'=>'Show free rules',	
				//~ ))
            ->add('rule', 'text',array(
					'label'=>'Rule',
					'required' => false	
				))
            ->getForm()
        ;
    }

	/*
	 * Get rule of array
	 * 
	 * */
	private function contructFirewallRule($arr){
		
		$em = $this->getDoctrine()->getManager();
		
		$rule="";
		if(!empty($arr['source'])){
			$rule.=" -s ".$arr['source']." ";
		}
		if(!empty($arr['destination'])){
			$rule.=" -d ".$arr['destination']." ";
		}
		if(!empty($arr['protocol'])){
			
			$entity = $em->getRepository('SilvanusFirewallRulesBundle:TransportProtocol')->find($arr['protocol']);
			if($entity->getName()!==''){
				$rule.=" -p ".$entity->getName()." ";
			}			
		}
		if(!empty($arr['source_port'])){
			if(is_numeric($arr['source_port'])){
				$rule.=" --sport ".$arr['source_port']." ";
			}else{
				if(preg_match('/([a-z])\w+ \([0-9]\w+\) \[([A-Z])\w+\]$/',$arr['source_port'])){
					$ap=strpos($arr['source_port'],"(")+1;
					$cp=strpos($arr['source_port'],")")-strlen($arr['source_port']);
					$rule.=" --sport ".substr($arr['source_port'],$ap,$cp)." ";	
				}
			}
		}
		if(!empty($arr['destination_port'])){
			if(is_numeric($arr['destination_port'])){
				$rule.=" --dport ".$arr['destination_port']." ";
			}else{
				if(preg_match('/([a-z])\w+ \([0-9]\w+\) \[([A-Z])\w+\]$/',$arr['destination_port'])){
					$ap=strpos($arr['destination_port'],"(")+1;
					$cp=strpos($arr['destination_port'],")")-strlen($arr['destination_port']);
					$rule.=" --dport ".substr($arr['destination_port'],$ap,$cp)." ";	
				}
			}
		}
		if(!empty($arr['interface_input'])){
			$rule.=" -i ".$arr['interface_input']." ";
		}
		if(!empty($arr['interface_output'])){
			$rule.=" -o ".$arr['interface_output']." ";
		}
		if(!empty($arr['more'])){
			$rule.=" [ ".$arr['more']." ] ";
		}
		if(!empty($arr['action'])){
			$rule.=" -j ".$arr['action']." ";
		}
				
		return $rule;
		
		
	}

	/*
	 * Get fields of rule in array from rule
	 * 
	 * */
	private function deconstructFirewallRule($rule){
		
		$rule=explode(" ",$rule);
		$em = $this->getDoctrine()->getManager();
		$arr = array();

		$flag	= false;
		$more	= "";
		$srule	= array();
		for($y=0;$y<count($rule);$y++){
				
			if($flag===true){
				
				if(strpos($rule[$y],']')!==false){
					
					$more.=" ".trim(substr($rule[$y],0,-1));
					$flag = false;
					
				}else{
					
					$more.=" ".trim($rule[$y]);
						
				}
							
			}

			if(strpos($rule[$y],'[')!==false){

				$more.=trim(substr($rule[$y],1));
				$flag=true;

			}
			
			if($flag===false){
			
				$srule[] = $rule[$y];
				
			}

		}

		if(count($srule)>0){
		
			$rule 				= $srule;
			$arr['more']		= $more;
						
		}
		
		for($n=0;$n<count($rule);$n++){
			
			if($rule[$n]=="-s"){		$arr['source']=$rule[$n+1];						}
			if($rule[$n]=="-d"){		$arr['destination']=$rule[$n+1];				}
			if($rule[$n]=="-p"){	
				$entity = $em->getRepository('SilvanusFirewallRulesBundle:TransportProtocol')->findOneBy(array('name'=>$rule[$n+1]));										
				$arr['protocol']=$entity->getId();
			}
			if($rule[$n]=="--sport"){		$arr['source_port']=$rule[$n+1];			}
			if($rule[$n]=="--dport"){		$arr['destination_port']=$rule[$n+1];		}
			if($rule[$n]=="-i")		{		$arr['interface_input']=$rule[$n+1];		}
			if($rule[$n]=="-o")		{		$arr['interface_output']=$rule[$n+1];		}
			if($rule[$n]=="-j")		{		$arr['action']=$rule[$n+1];					}
			
		}
		
		return $arr;
		
	}


}
