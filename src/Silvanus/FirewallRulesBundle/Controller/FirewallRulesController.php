<?php

namespace Silvanus\FirewallRulesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;

use Silvanus\FirewallRulesBundle\Entity\FirewallRules;
use Silvanus\ChainsBundle\Entity\Chain;
use Silvanus\SyncBundle\Entity\Sync;


use Silvanus\FirewallRulesBundle\Form\FirewallRulesType;
use Silvanus\FirewallRulesBundle\Form\FirewallRulesCreateType;
use Silvanus\FirewallRulesBundle\Form\FirewallRulesUpdateType;
use Silvanus\FirewallRulesBundle\Form\FirewallRulesEasyCreateType;

/**
 * FirewallRules controller.
 *
 */
class FirewallRulesController extends Controller
{

    /**
     * Creates a form interface to add new rule in simple mode
     *
     */
    public function easynewAction(Request $request, $id_chain)
    {

		$em 			= $this->getDoctrine()->getManager();
		$entityChain	= $em->getRepository('SilvanusChainsBundle:Chain')->find($id_chain);

		$form = $this->createForm(new FirewallRulesEasyCreateType());

		$form->get('destination')->setData($entityChain->getHost());

		return $this->render('SilvanusFirewallRulesBundle:FirewallRules:easynew.html.twig', array(
			'form'		=> $form->createView(),
			'id_chain'	=> $id_chain,
		));

    }

    /**
     * Creates a form interface to add new rule in simple mode
     *
     */
    public function easycreateAction(Request $request, $id_chain)
    {
		
		$arrForm	= $this->get('request')->request->get('silvanus_firewallrulesbundle_firewallrulestype');			
		$form 		= $this->createForm(new FirewallRulesEasyCreateType());
		
		//$form->handleRequest($request);
		$form->submit($request);

		if($arrForm['priority']<1){
			$form->get('priority')->addError(new FormError('Priority must be more than 0'));
		}

		if($form->isValid()){

			$em 		= $this->getDoctrine()->getManager();
			
			$rule="";
			if(!empty($arrForm['source'])){
				$rule.=" -s ".$arrForm['source']." ";
			}
			if(!empty($arrForm['destination'])){
				$rule.=" -d ".$arrForm['destination']." ";
			}
			if(!empty($arrForm['protocol'])){
				$rule.=" -p ".$arrForm['protocol']." ";
			}
			if(!empty($arrForm['source_port'])){
				if(is_numeric($arrForm['source_port'])){
					$rule.=" --sport ".$arrForm['source_port']." ";
				}else{
					$ap=strpos($arrForm['source_port'],"(")+1;
					$cp=strpos($arrForm['source_port'],")")-strlen($arrForm['source_port']);
					$rule.=" --sport ".substr($arrForm['source_port'],$ap,$cp)." ";	
				}
			}
			if(!empty($arrForm['destination_port'])){
				if(is_numeric($arrForm['destination_port'])){
					$rule.=" --dport ".$arrForm['destination_port']." ";
				}else{
					$ap=strpos($arrForm['destination_port'],"(")+1;
					$cp=strpos($arrForm['destination_port'],")")-strlen($arrForm['destination_port']);
					$rule.=" --dport ".substr($arrForm['destination_port'],$ap,$cp)." ";	
				}
			}
			if(!empty($arrForm['interface_input'])){
				$rule.=" -i ".$arrForm['interface_input']." ";
			}
			if(!empty($arrForm['interface_output'])){
				$rule.=" -o ".$arrForm['interface_output']." ";
			}
			if(!empty($arrForm['action'])){
				$rule.=" -j ".$arrForm['action']." ";
			}
			
			//echo $rule;

			$entity = new FirewallRules();
			$entity->setChainId($id_chain);
			$entity->setRule($rule);
			
			if(isset($arrForm['priority'])){
				$entity->setPriority($arrForm['priority']);	
			}
			
			/*
			 * Force option
			 * 
			 * */			
			if(isset($arrForm['append'])){
			
			
				//get last priority
				$firewallRepository	= $this->getDoctrine()->getRepository('SilvanusFirewallRulesBundle:FirewallRules');
				
				$builder	= $firewallRepository->createQueryBuilder('f');
				
				$query = $builder 
					->where($builder->expr()->eq('f.chain_id',':id_chain'))
					->setParameter(':id_chain',$id_chain)
					->orderBy('f.priority','desc')
					->setFirstResult( 0 )
					->setMaxResults( 1 )
					->getQuery();
			
				$lastEntity = $query->getResult();


				if(!isset($lastEntity[0])){
					
					$entity->setPriority(1);
					
				}else{
				
					$lastEntity = $lastEntity[0];
					$entity->setPriority($lastEntity->getPriority()+1);
					
				}
						
			}
         
         
			$entity->setSyncStatus($em->getRepository('SilvanusFirewallRulesBundle:RulesSyncStatus')->findOneBy(array('id'=>1)));
         
            $em->persist($entity);
            $em->flush();
            
            $this->fixPriority($id_chain);

			/* add sync petition */
			$syncEntity = $em->getRepository('SilvanusSyncBundle:Sync')->findBy(array('chainId'=>$id_chain));
			if(!$syncEntity){

				$syncEntity = new sync();
				$syncEntity->setChainId($id_chain);
				$syncEntity->setTime(new \DateTime('now'));
				$syncEntity->setError(false);
				$syncEntity->setAction('u');
				$em->persist($syncEntity);
				$em->flush();

			}

            return $this->redirect($this->generateUrl('firewallrules', 
				array(	'id_chain' => $id_chain, 
						'message'=> 'Create successful',
				)	
			));			
			
		}
		
		return $this->render('SilvanusFirewallRulesBundle:FirewallRules:easynew.html.twig', array(
			'form'		=> $form->createView(),
			'id_chain'	=> $id_chain,
		));
		

    }


    /**
     * Lists all FirewallRules entities.
     *
     */
    public function indexAction(Request $request,$id_chain,$message)
    {

        $em = $this->getDoctrine()->getManager();

		$chain_entity = $em->getRepository('SilvanusChainsBundle:Chain')->find($id_chain);
	
		//form request
		if($request->getMethod()=='POST'){
			
			$formFilter = $this->createRuleFilterForm();
			$formFilter->submit($request);
			
			$formData = $formFilter->getData();
			
			$firewallRepository = $em->getRepository('SilvanusFirewallRulesBundle:FirewallRules');
			
			$builder = $firewallRepository->createQueryBuilder('f');

			if($formData['rule']!=''){
				
				$builder->where($builder->expr()->like('f.rule', ':rule'));
				$builder->setParameter(':rule','%'.$formData['rule'].'%');
				
			}

			if($formData['sync_error']!=''){

				if($formData['sync_error']=='true'){	$sync_error=true;	}
				if($formData['sync_error']=='false'){	$sync_error=false;	}
					
				$builder->andWhere($builder->expr()->eq('f.syncError',':sync_error'));
				$builder->setParameter(':sync_error',$sync_error);
				
			}



			$builder->andWhere($builder->expr()->like('f.chain_id', ':chain_id'));
			$builder->setParameter(':chain_id',$id_chain);


			$builder->orderBy('f.'.$formData['sort_by'],$formData['sort_direction']);
			
			$query = $builder->getQuery();
			
			$entities = $query->getResult();

			
			
		//no form request
		}else{

			$builder = $em->getRepository('SilvanusFirewallRulesBundle:FirewallRules')->createQueryBuilder('f');

			$query = $builder
				->where('f.chain_id = :id_chain')
				->orderBy('f.priority','asc')
				->setParameter(':id_chain',$id_chain)
				->getQuery();

			$entities = $query->getResult();
			
			$formFilter = $this->createRuleFilterForm();
			
			
		}
		
		

        return $this->render('SilvanusFirewallRulesBundle:FirewallRules:index.html.twig', array(
            'entities' 			=> $entities,
            'filter_form' 		=> $formFilter->createView(),         
            'id_chain'			=> $id_chain,
            'chain_entity'		=> $chain_entity,
            'message'			=> $message,
        ));
    }
    /**
     * Creates a new FirewallRules entity.
     *
     */
    public function createAction(Request $request,$id_chain)
    {
		
		//get form values
		$arrForm=$this->get('request')->request->get('silvanus_firewallrulesbundle_firewallrulestype');
		
        $entity  = new FirewallRules();
        $entity->setChainId($id_chain);      
        
        $form = $this->createForm(new FirewallRulesCreateType(), $entity);
        $form->submit($request);

		$em = $this->getDoctrine()->getManager();

        if ($form->isValid()) {

			/*
			 * Force option
			 * 
			 * */			
			if(isset($arrForm['append'])){
			
			
				//get last priority
				$firewallRepository	= $this->getDoctrine()->getRepository('SilvanusFirewallRulesBundle:FirewallRules');
				
				$builder	= $firewallRepository->createQueryBuilder('f');
				
				$query = $builder 
					->where($builder->expr()->eq('f.chain_id',':id_chain'))
					->setParameter(':id_chain',$id_chain)
					->orderBy('f.priority','desc')
					->setFirstResult( 0 )
					->setMaxResults( 1 )
					->getQuery();
			
				$lastEntity = $query->getResult();


				if(!isset($lastEntity[0])){
					
					$entity->setPriority(1);
					
				}else{
				
					$lastEntity = $lastEntity[0];
					$entity->setPriority($lastEntity->getPriority()+1);
					
				}
						
			}
         
         
			$entity->setSyncStatus($em->getRepository('SilvanusFirewallRulesBundle:RulesSyncStatus')->findOneBy(array('id'=>1)));
         
            $em->persist($entity);
            $em->flush();
            
            $this->fixPriority($id_chain);

			/* add sync petition */
			$syncEntity = $em->getRepository('SilvanusSyncBundle:Sync')->findBy(array('chainId'=>$id_chain));
			if(!$syncEntity){

				$syncEntity = new sync();
				$syncEntity->setChainId($id_chain);
				$syncEntity->setTime(new \DateTime('now'));
				$syncEntity->setError(false);
				$syncEntity->setAction('u');
				$em->persist($syncEntity);
				$em->flush();

			}

            return $this->redirect($this->generateUrl('firewallrules', 
				array(	'id_chain' => $id_chain, 
						'message'=> 'Create successful',
				)	
			));
        
        }else{

			/*
			 * Force option mechanism
			 * 
			 * */
			if(isset($arrForm['force'])){

				/*
				 * Get the priority errors
				 * 
				 * */
				foreach ($form->get('priority')->getErrors() as $err){
					
					$priorityError=$err->getMessageTemplate();
					
				}
		
					
				/*
				 * If is true this condition, move the priority value of rules +1
				 * 
				 * */
				if($priorityError=='This value is already used.' 
					and count($form->get('rule')->getErrors())==0 
					and count($form->get('priority')->getErrors())==1
					){

					$firewallRepository = $this->getDoctrine()->getRepository('SilvanusFirewallRulesBundle:FirewallRules');
				
					$builder 	= $firewallRepository->createQueryBuilder('f');
				
				
					$query		= $builder
						->where('f.priority >= :priority')
						//->andWhere('f.id != :id')
						->andWhere('f.chain_id = :id_chain')
						->setParameter(':priority',$arrForm['priority'])
						//->setParameter(':id',$entity->getId())
						->setParameter(':id_chain',$id_chain)
						->orderBy('f.priority','desc')
						->getQuery();

					$fixPrio	= $query->getResult();
					
					foreach($fixPrio as $fix){
					
						$fix->setPriority($fix->getPriority()+1);
						$em->persist($fix);
						
					}
					
					$entity->setSyncStatus($em->getRepository('SilvanusFirewallRulesBundle:RulesSyncStatus')->findOneBy(array('id'=>1)));
												
					$em->persist($entity);
					$em->flush();
					
					$this->fixPriority($id_chain);
										
					/* add sync petition */
					$syncEntity = $em->getRepository('SilvanusSyncBundle:Sync')->findBy(array('chainId'=>$id_chain));
					if(!$syncEntity){

						$syncEntity = new sync();
						$syncEntity->setChainId($id_chain);
						$syncEntity->setTime(new \DateTime('now'));
						$syncEntity->setError(false);
						$syncEntity->setAction('u');
						$em->persist($syncEntity);
						$em->flush();

					}


					return $this->redirect($this->generateUrl('firewallrules', 
						array(	'id_chain' => $id_chain, 
								'message'=> 'Create successful',
						)	
					));
												
				}

			}

			
		}

        return $this->render('SilvanusFirewallRulesBundle:FirewallRules:new.html.twig', array(
            'entity' 	=> $entity,
            'form'   	=> $form->createView(),
            'id_chain'	=> $id_chain,
        ));
    }

    /**
     * Displays a form to create a new FirewallRules entity.
     *
     */
    public function newAction($id_chain)
    {
        $entity = new FirewallRules();
        $form   = $this->createForm(new FirewallRulesCreateType(), $entity);

        return $this->render('SilvanusFirewallRulesBundle:FirewallRules:new.html.twig', array(
            'entity' 	=> $entity,
			'form'   	=> $form->createView(),
            'id_chain' 	=> $id_chain,
        ));
    }

    /**
     * Finds and displays a FirewallRules entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SilvanusFirewallRulesBundle:FirewallRules')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FirewallRules entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SilvanusFirewallRulesBundle:FirewallRules:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        
			'id_chain'	  => $entity->getChainId(),	
            ));
    
    }

    /**
     * Displays a form to edit an existing FirewallRules entity.
     *
     */
    public function editAction($id, $id_chain)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SilvanusFirewallRulesBundle:FirewallRules')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FirewallRules entity.');
        }

        $editForm = $this->createForm(new FirewallRulesUpdateType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SilvanusFirewallRulesBundle:FirewallRules:edit.html.twig', array(
            'entity'      	=> $entity,
            'edit_form'   	=> $editForm->createView(),
            'delete_form' 	=> $deleteForm->createView(),
            'id_chain'		=> $id_chain,
        ));
    }

    /**
     * Edits an existing FirewallRules entity.
     *
     */
    public function updateAction(Request $request, $id, $id_chain)
    {
		
		$arrForm=$this->get('request')->request->get('silvanus_firewallrulesbundle_firewallrulestype');
		
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SilvanusFirewallRulesBundle:FirewallRules')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FirewallRules entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new FirewallRulesUpdateType(), $entity);
       
        $editForm->submit($request);

		
        if ($editForm->isValid()) {

				$entity->setSyncStatus($em->getRepository('SilvanusFirewallRulesBundle:RulesSyncStatus')->findOneBy(array('id'=>1)));
			
				$em->persist($entity);
				$em->flush();
	
				/* add sync petition */
				$syncEntity = $em->getRepository('SilvanusSyncBundle:Sync')->findBy(array('chainId'=>$id_chain));
				if(!$syncEntity){

					$syncEntity = new sync();
					$syncEntity->setChainId($id_chain);
					$syncEntity->setTime(new \DateTime('now'));
					$syncEntity->setError(false);
					$syncEntity->setAction('u');
					$em->persist($syncEntity);
					$em->flush();

				}


				$this->fixPriority($id_chain);
				
				return $this->redirect($this->generateUrl('firewallrules', 
					array(	'id_chain' => $id_chain, 
							'message'=> 'Update successful',
					)	
				));
        
        }else{
		
		
			/*
			 * Get the priority errors
			 * 
			 * */
			foreach ($editForm->get('priority')->getErrors() as $err){
				
				$priorityError=$err->getMessageTemplate();
				
			}
		
			
			/*
			 * If is true this condition, move the priority value of rules +1
			 * 
			 * */
			if($priorityError=='This value is already used.' 
				and count($editForm->get('rule')->getErrors())==0 
				and count($editForm->get('priority')->getErrors())==1
				and isset($arrForm['force'])
				){

				$firewallRepository = $this->getDoctrine()->getRepository('SilvanusFirewallRulesBundle:FirewallRules');
			
				$builder = $firewallRepository->createQueryBuilder('f');
			
				$query		= $builder
					->where('f.priority >= :priority')
					->andWhere('f.id != :id')
					->andWhere('f.chain_id = :id_chain')
					->setParameter(':priority',$arrForm['priority'])
					->setParameter(':id',$entity->getId())
					->setParameter(':id_chain',$id_chain)
					->orderBy('f.priority','desc')
					->getQuery();

				$fixPrio	= $query->getResult();
				
				foreach($fixPrio as $fix){
					
					$fix->setPriority($fix->getPriority()+1);
					$em->persist($fix);
					
				}
			
				$entity->setSyncStatus($em->getRepository('SilvanusFirewallRulesBundle:RulesSyncStatus')->findOneBy(array('id'=>1)));
			
				$em->persist($entity);
				$em->flush();

				$this->fixPriority($id_chain);

				/* add sync petition */
				$syncEntity = $em->getRepository('SilvanusSyncBundle:Sync')->findBy(array('chainId'=>$id_chain));
				if(!$syncEntity){

					$syncEntity = new sync();
					$syncEntity->setChainId($id_chain);
					$syncEntity->setTime(new \DateTime('now'));
					$syncEntity->setError(false);
					$syncEntity->setAction('u');
					$em->persist($syncEntity);
					$em->flush();

				}


				return $this->redirect($this->generateUrl('firewallrules', 
					array(	'id_chain' => $id_chain, 
							'message'=> 'Update successful',
					)	
				));
											
			}
		
		}
		

        return $this->render('SilvanusFirewallRulesBundle:FirewallRules:edit.html.twig', array(
            'entity'      	=> $entity,
            'edit_form'   	=> $editForm->createView(),
            'delete_form' 	=> $deleteForm->createView(),
            'id_chain'	 	=> $id_chain,
        ));
    }
    /**
     * Deletes a FirewallRules entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
 
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('SilvanusFirewallRulesBundle:FirewallRules')->find($id);

		$id_chain=$entity->getChainId();

		if (!$entity) {
			throw $this->createNotFoundException('Unable to find FirewallRules entity.');
		}

		$em->remove($entity);
		$em->flush();

		$this->fixPriority($id_chain);

		/* add sync petition */
		$syncEntity = $em->getRepository('SilvanusSyncBundle:Sync')->findBy(array('chainId'=>$id_chain));
		if(!$syncEntity){

			$syncEntity = new sync();
			$syncEntity->setChainId($id_chain);
			$syncEntity->setTime(new \DateTime('now'));
			$syncEntity->setError(false);
			$syncEntity->setAction('u');
			$em->persist($syncEntity);
			$em->flush();

		}


		return $this->redirect($this->generateUrl('firewallrules', 
			array(	'id_chain' => $id_chain, 
					'message'=> 'Delete successful',
			)	
		));
 
 
    }

####################################################################################################### aqui

    /**
     * Fixed the priority in all chain
     * 
     * @param mixed $id_chain The chain id
     * 
	 */
	private function fixPriority($id_chain){
	
		$em = $this->getDoctrine()->getManager();
		
		$builder = $em->getRepository('SilvanusFirewallRulesBundle:FirewallRules')->createQueryBuilder('f');
		
		$query = $builder
			->where('f.chain_id = :id_chain ')
			->setParameter(':id_chain',$id_chain)
			->orderBy('f.priority','asc')
			->getQuery();
		
		$entities = $query->getResult();
		
		$n=1;
		foreach($entities as $entity){
		
			$entity->setPriority($n);
			$em->persist($entity);
			$n++;
		}
	
		$em->flush();
	}


    /**
     * Creates a form to delete a FirewallRules entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }

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

}











