<?php

namespace Silvanus\FirewallRulesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
    public function indexAction(Request $request,$id_chain)
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

			$builder->where($builder->expr()->like('f.chain_id', ':chain_id'));
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
			 * Force option mechanism
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
         
         
            $em->persist($entity);
            $em->flush();

			$syncEntity = new sync();
			$syncEntity->setChainId($id_chain);
			$syncEntity->setTime(new \DateTime('now'));
			$syncEntity->setError(false);
			$em->persist($syncEntity);
			$em->flush();

            return $this->redirect($this->generateUrl('firewallrules', array('id_chain' => $id_chain)));
        
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
												
					$em->persist($entity);
					$em->flush();
					
					$this->fixPriority($id_chain);
										
					$syncEntity = new sync();
					$syncEntity->setChainId($id_chain);
					$syncEntity->setTime(new \DateTime('now'));
					$syncEntity->setError(false);
					$em->persist($syncEntity);
					$em->flush();



					return $this->redirect($this->generateUrl('firewallrules', array('id_chain' => $id_chain)));
												
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
			
				$em->persist($entity);
				$em->flush();
	
				$syncEntity = new sync();
				$syncEntity->setChainId($id_chain);
				$syncEntity->setTime(new \DateTime('now'));
				$syncEntity->setError(false);
				$em->persist($syncEntity);
				$em->flush();

				
				return $this->redirect($this->generateUrl('firewallrules', array('id_chain' => $id_chain)));			
        
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
			
				$em->persist($entity);
				$em->flush();

				$this->fixPriority($id_chain);

				$syncEntity = new sync();
				$syncEntity->setChainId($id_chain);
				$syncEntity->setTime(new \DateTime('now'));
				$syncEntity->setError(false);
				$em->persist($syncEntity);
				$em->flush();


				return $this->redirect($this->generateUrl('firewallrules', array('id_chain' => $id_chain)));
											
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

	/*
        $form = $this->createDeleteForm($id);
        $form->submit($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SilvanusFirewallRulesBundle:FirewallRules')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find FirewallRules entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('firewallrules'));
    
    */
 
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('SilvanusFirewallRulesBundle:FirewallRules')->find($id);

		$id_chain=$entity->getChainId();

		if (!$entity) {
			throw $this->createNotFoundException('Unable to find FirewallRules entity.');
		}

		$em->remove($entity);
		$em->flush();

		$this->fixPriority($id_chain);

		$syncEntity = new sync();
		$syncEntity->setChainId($id_chain);
		$syncEntity->setTime(new \DateTime('now'));
		$syncEntity->setError(false);
		$em->persist($syncEntity);
		$em->flush();


        return $this->redirect($this->generateUrl('firewallrules', array('id_chain'=>$id_chain)));
 
 
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

}











