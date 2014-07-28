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
use Silvanus\FirewallRulesBundle\Form\FirewallRulesEasyUpdateType;

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

		if(isset($arrForm['priority'])){
			if($arrForm['priority']<1){
				$form->get('priority')->addError(new FormError('Priority must be more than 0'));
			}
		}

		if($form->isValid()){

			$em 		= $this->getDoctrine()->getManager();
			
			$entity = new FirewallRules();
			$entity->setChainId($id_chain);
			$entity->setRule($this->contructFirewallRule($arrForm));
			
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
     * Displays a form to edit an existing FirewallRules entity.
     *
     */
    public function easyeditAction($id, $id_chain)
    {
        $em = $this->getDoctrine()->getManager();

        //$entity = $em->getRepository('SilvanusFirewallRulesBundle:FirewallRules')->findOneBy(array('id'=>$id));
        $entity = $em->getRepository('SilvanusFirewallRulesBundle:FirewallRules')->find($id);
		$arrRule = $this->deconstructFirewallRule($entity->getRule());
		
		
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FirewallRules entity.');
        }

		if(isset($arrRule['protocol'])){	$defaults['protocol']=$arrRule['protocol'];				}

		

		$editForm = $this->createForm(new FirewallRulesEasyUpdateType(),$entity);
		
		foreach($arrRule as $key => $val){

			if($key==='protocol'){
				$entityProtocol = $em->getRepository('SilvanusFirewallRulesBundle:TransportProtocol')->findOneBy(array('id'=>$val));
				$editForm->get($key)->setData($entityProtocol);
			}else{
				$editForm->get($key)->setData($val);
			}
			
		}

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SilvanusFirewallRulesBundle:FirewallRules:easyedit.html.twig', array(
            'entity'      	=> $entity,
            'edit_form'   	=> $editForm->createView(),
            'delete_form' 	=> $deleteForm->createView(),
            'id_chain'		=> $id_chain,
        ));
    }

	/*
	 * Update a rule in simple mode
	 * 
	 * */
	public function easyupdateAction(Request $request,$id,$id_chain){

		$arrForm	= $this->get('request')->request->get('silvanus_firewallrulesbundle_firewallrulestype');			
			
		$em 		= $this->getDoctrine()->getManager();
		$entity 	= $em->getRepository('SilvanusFirewallRulesBundle:FirewallRules')->findOneBy(array('id'=>$id));
		
	
		$editForm=$this->createForm(new FirewallRulesEasyUpdateType(),$entity);
		$editForm->submit($request);
		
		if($editForm->isValid()){
		
			$rule = $this->contructFirewallRule($arrForm);			
			$entity->setRule($rule);

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

			
		}else{

			$priorityError="";
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
				 
		$deleteForm = $this->createDeleteForm($id);
	
		return $this->render('SilvanusFirewallRulesBundle:FirewallRules:easyedit.html.twig',array(
			'entity'		=> $entity,
			'edit_form'		=> $editForm->createView(),
			'delete_form'	=> $deleteForm->createView(),
			'id_chain'		=> $id_chain,
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
		
		for($n=0;$n<count($entities);$n++){
		
			if(strpos($entities[$n]->getRule(),'[')!==false){
				
				$entities[$n]->setRule(str_replace("[","",$entities[$n]->getRule()));
				$entities[$n]->setRule(str_replace("]","",$entities[$n]->getRule()));
				
			}
			
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
		
			$priorityError="";
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
				$ap=strpos($arr['source_port'],"(")+1;
				$cp=strpos($arr['source_port'],")")-strlen($arr['source_port']);
				$rule.=" --sport ".substr($arr['source_port'],$ap,$cp)." ";	
			}
		}
		if(!empty($arr['destination_port'])){
			if(is_numeric($arr['destination_port'])){
				$rule.=" --dport ".$arr['destination_port']." ";
			}else{
				$ap=strpos($arr['destination_port'],"(")+1;
				$cp=strpos($arr['destination_port'],")")-strlen($arr['destination_port']);
				$rule.=" --dport ".substr($arr['destination_port'],$ap,$cp)." ";	
			}
		}
		if(!empty($arr['interface_input'])){
			$rule.=" -i ".$arr['interface_input']." ";
		}
		if(!empty($arr['interface_output'])){
			$rule.=" -o ".$arr['interface_output']." ";
		}
		if(!empty($arr['module'])){
			$rule.=" [".$arr['module']."] ";
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
		$mode	= "";
		$srule	= array();
		for($y=0;$y<count($rule);$y++){
				
			if($flag===true){
				
				if(strpos($rule[$y],']')!==false){
					
					$mode.=trim(substr($rule[$y],0,-1));
					$flag = false;
					
				}else{
					
					$mode.=" ".trim($rule[$y]);
						
				}
							
			}

			if(strpos($rule[$y],'[')!==false){

				$mode.=trim(substr($rule[$y],1));
				$flag=true;

			}
			
			if($flag===false){
			
				$srule[] = $rule[$y];
				
			}

		}

		if(count($srule)>0){
		
			$rule 				= $srule;
			$arr['module']		= $mode;
						
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
