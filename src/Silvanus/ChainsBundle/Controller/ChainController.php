<?php

namespace Silvanus\ChainsBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Silvanus\ChainsBundle\Entity\Chain;
use Silvanus\ChainsBundle\Entity\StackChain;
use Silvanus\ChainsBundle\Form\ChainType;
use Silvanus\ChainsBundle\Form\ChainSpecialType;

use Silvanus\SyncBundle\Entity\Sync;

/**
 * Chain controller.
 *
 * @Route("/chain")
 */
class ChainController extends Controller
{

    /**
     * Lists all Chain entities.
     *
     * @Route("/", name="chains")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request, $message)
    {

		$em = $this->getDoctrine()->getManager();

		
		/* form filter */
		if($request->getMethod()=='POST'){
		
			$filter_form = $this->createFilterForm();
			
			$filter_form->submit($request);
		
			
			if($filter_form->isValid()){

				$formData=$filter_form->getData();

				$builder = $em->getRepository('SilvanusChainsBundle:Chain')->createQueryBuilder('c');
				
				if($formData['name']!=''){
				
					$builder->where($builder->expr()->like('c.name',':name'));
					$builder->setParameter(':name',$formData['name']);
				
				}

				if($formData['host']!=''){
				
					$builder->where($builder->expr()->like('c.host',':host'));
					$builder->setParameter(':host',$formData['host']);
				
				}

				if($formData['type']!=''){
				
					$builder->where($builder->expr()->like('c.type',':type'));
					$builder->setParameter(':type',$formData['type']);
				
				}



				$builder->orderBy('c.'.$formData['sort_by'],$formData['sort_direction']);
				
				$entities=$builder->getQuery()->getResult();
				
				
				
			}else{
				
				$entities = $em->getRepository('SilvanusChainsBundle:Chain')->findAll();
				
			}
			
		}else{


			$entities = $em->getRepository('SilvanusChainsBundle:Chain')->findAll();

			$filter_form = $this->createFilterForm();

			
		}
		

        return array(
            'entities' 		=> $entities,
            'filter_form'	=> $filter_form->createView(),
            'message'		=> $message,
        );
    }
    /**
     * Creates a new Chain entity.
     *
     * @Route("/", name="chains_create")
     * @Method("POST")
     * @Template("SilvanusChainsBundle:Chain:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Chain();
        $form = $this->createForm(new ChainType(), $entity);
        $form->submit($request);
		

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

			if($entity->getType()=='normal'){
				$stackChain = new StackChain();
				$stackChain->setChainParent($entity);
				$stackChain->setChainChildren($entity);
				$stackChain->setPriority(1);
				$stackChain->setActive(true);
				$em->persist($stackChain);
				$em->flush();
			}


			$this->createSync($entity->getId(),"c");

            //return $this->redirect($this->generateUrl('chains_show', array('id' => $entity->getId())));
            return $this->redirect($this->generateUrl('chains',array('message'=>'Create successful: '.$entity->getName())));
        
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            
        );
    }

    /**
     * Displays a form to create a new Chain entity.
     *
     * @Route("/new", name="chains_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Chain();
        $form   = $this->createForm(new ChainType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Chain entity.
     *
     * @Route("/{id}", name="chains_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SilvanusChainsBundle:Chain')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Chain entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Chain entity.
     *
     * @Route("/{id}/edit", name="chains_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SilvanusChainsBundle:Chain')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Chain entity.');
        }

		if($entity->getType()==='normal'){
			$editForm = $this->createForm(new ChainType(), $entity);
			$editForm->remove('type');
		}else{
			$editForm = $this->createForm(new ChainSpecialType(), $entity);
		}
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Chain entity.
     *
     * @Route("/{id}", name="chains_update")
     * @Method("PUT")
     * @Template("SilvanusChainsBundle:Chain:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SilvanusChainsBundle:Chain')->find($id);

		$type  = $entity->getType();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Chain entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new ChainType(), $entity);        
        $editForm->submit($request);
		

        if ($editForm->isValid()) {
			
			$entity->setType($type);
            $em->persist($entity);
            $em->flush();

			$this->createSync($entity->getId(),"u");

			//~ if($entity->getType()==='prototype'){
				//~ $id = $id;
			//~ }

            //return $this->redirect($this->generateUrl('chains_edit', array('id' => $id)));
			return $this->redirect($this->generateUrl('chains', array('message'=>'Update successful: '.$entity->getName())));

        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Force sync
     *
     * @Route("/{id}", name="chains_force_sync")
     */
    public function forceSyncAction(Request $request, $id)
    {

		$em 			= $this->getDoctrine()->getManager();

		$chainEntity 	= $em->getRepository('SilvanusChainsBundle:Chain')->findOneBy(array('id'=>$id));		
		$syncEntity 	= $em->getRepository('SilvanusSyncBundle:Sync')->findOneBy(array('chainId'=>$id));
		
		if(!$syncEntity){

			$syncEntity = new sync();
			$syncEntity->setChainId($chainEntity->getId());
			$syncEntity->setTime(new \DateTime('now'));
			$syncEntity->setError(false);
			$syncEntity->setAction('u');
			$syncEntity->setChainName($chainEntity->getName());
			$em->persist($syncEntity);
			$em->flush();

		}

		return $this->redirect($this->generateUrl('chains',array('message'=>'Force petition successful: '.$chainEntity->getName())));

	}

    /**
     * Deletes a Chain entity.
     *
     * @Route("/{id}", name="chains_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
		
		$em 	= $this->getDoctrine()->getManager();
		$entity	= $em->getRepository('SilvanusChainsBundle:Chain')->find($id);
		
		if(!$entity){
		
			throw $this->createNotFoundException('Uneable to find Chain entity');
			
		}
		
		
		//remove rules
		foreach($entity->getRules() as $firewalRulesEntity){
			$em->remove($firewalRulesEntity);
		}
		
		//remove relantions with other chains
		$stackChainEntity 	= $em->getRepository('SilvanusChainsBundle:StackChain')->findBy(array('chainParent'=>$entity->getId()));
		
		foreach($stackChainEntity as $stackChainE){
			$em->remove($stackChainE);
			$em->flush();
		}
		
		if($entity->getType()=='normal'){
			$stackChainEntities = $em->getRepository('SilvanusChainsBundle:StackChain')->findBy(array("chainParent"=>$entity->getId()));
			
			foreach($stackChainEntities as $stackChainEntity){
				$em->remove($stackChainEntity);
			}
		}

		if($entity->getType()=='stack'){
			$stackChainEntities = $em->getRepository('SilvanusChainsBundle:StackChain')->findBy(array("chainChildren"=>$entity->getId()));
			
			foreach($stackChainEntities as $stackChainEntity){
				$em->remove($stackChainEntity);
			}
		}
		
		
		$this->createSync($entity->getId(),"d");

		$em->remove($entity);		
		$em->flush();


        return $this->redirect($this->generateUrl('chains',array('message'=>'Delete successful: '.$entity->getName())));
    }

	/* 
	 * create syncronization petition 
	 * 
	 * */
	private function createSync($chain_id,$action){

		$em 			= $this->getDoctrine()->getManager();
		$chainEntity	= $em->getRepository('SilvanusChainsBundle:Chain')->find($chain_id);

		if($chainEntity->getType()=='normal'){

			$syncEntity = $em->getRepository('SilvanusSyncBundle:Sync')->findOneBy(array('chainId'=>$chainEntity->getId()));
			
			if(!$syncEntity){

				$syncEntity = new sync();
				
			}

			if($chainEntity->getActive()){
			
				$syncEntity->setChainId($chainEntity->getId());
				$syncEntity->setChainName($chainEntity->getName());
				$syncEntity->setTime(new \DateTime('now'));
				$syncEntity->setError(false);
				if($action=='d'){
					$syncEntity->setAction('d');
				}else{
					if($chainEntity->getActive()){
						$syncEntity->setAction($action);
					}else{
						$syncEntity->setAction('d');
					}
				}
				$syncEntity->setAction($action);
				$em->persist($syncEntity);
				$em->flush();
			
			}
			
		}
		
		if($chainEntity->getType()=='stack'){
		
			$stackChainEntities = $em->getRepository('SilvanusChainsBundle:StackChain')->findBy(array('chainChildren'=>$chainEntity->getId()));
			
			foreach($stackChainEntities as $stackChainEntity){
				
				unset($syncEntity);
				$syncEntity = $em->getRepository('SilvanusSyncBundle:Sync')->findOneBy(array('chainId'=>$stackChainEntity->getChainParent()->getId()));				

				if(!$syncEntity){

					$syncEntity = new sync();
					
				}

				if($stackChainEntity->getChainParent()->getActive()){
				
					$syncEntity->setChainId($stackChainEntity->getChainParent()->getId());
					$syncEntity->setChainName($stackChainEntity->getChainParent()->getName());
					$syncEntity->setTime(new \DateTime('now'));
					$syncEntity->setError(false);
					$syncEntity->setAction("u");
					$em->persist($syncEntity);
					$em->flush();
				
				}

				
			}

			
		}
		
	}


    /**
     * Creates a form to delete a Chain entity by id.
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
     * Creates a form to filter list
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createFilterForm()
    {
        return $this->createFormBuilder(array())
            ->add('sort_by','choice', array(
				'label' 	=> 		'Sort by',
				'choices' 	=> 		array(
					'id'=>'ID',
					'name'=>'Name',
					)
				))
            ->add('sort_direction','choice',array(
				'choices'=>array(
						'ASC'=>'Asc',
						'DESC'=>'Desc',
					),
				'label'=>'Sort direction',	
				))				
            ->add('name','text', array(
				'label' 	=> 		'Name',
				'required'	=> 		false		
				))
            ->add('host','text', array(
				'label' 	=> 		'Host',
				'required'	=> 		false		
				))
            ->add('type','choice', array(
				'label' 	=> 		'Type',
				'required'	=> 		false,
				'choices' 	=> 		array(
					'normal' => 'Normal',
					'stack' => 'Stack',
					'prototype' => 'Prototype',
					)
						
				))

            ->getForm()
        ;
    }



}
