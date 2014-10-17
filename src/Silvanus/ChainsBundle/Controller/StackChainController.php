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

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormError;

use Silvanus\SyncBundle\Entity\Sync;

/**
 * Chain controller.
 *

 */
class StackChainController extends Controller
{

    /**
     * Lists all StackChain entities.
     *
     */
    public function indexAction(Request $request, $id, $message)
    {

		$em 		= $this->getDoctrine()->getManager();
		$entities 	= $em->getRepository('SilvanusChainsBundle:StackChain')->findBy(array('chainParent'=>$id),array('priority'=>'ASC'));

		$em->getRepository('SilvanusChainsBundle:StackChain')->fixPriorityIndex($id);
		
		return $this->render('SilvanusChainsBundle:StackChain:index.html.twig', array(
			'message'		=> $message,
			'entities'		=> $entities,
			'id'			=> $id,
		));

    }

    /**
     * Create form to add new chain
     *
     */
    public function addAction(Request $request, $id)
    {

		$form	= $this->createNewForm($id);
		
		return $this->render('SilvanusChainsBundle:StackChain:add.html.twig', array(
			'form'			=> $form->createView(),
			'id'			=> $id,	
		));

    }

    /**
     * Add new chain to Main Chain
     *
     */
    public function createAction(Request $request, $id)
    {

		$form		= $this->createNewForm($id);		
		$form->submit($request);
		$em 		= $this->getDoctrine()->getManager();
		$entity 	= $em->getRepository('SilvanusChainsBundle:StackChain')->findOneBy(
			array(
				'chainParent'=>$id,
				'chainChildren'=>$form->get('chainChildren')->getData(),
			
		));
		
		
		$isValid = true;
		if($entity!=null){
			$form->get('chainChildren')->addError(new FormError("This chain already exists"));
			$isValid = false;
		}
		
		if(!$form->get('append')->getData() and $form->get('priority')->getData()==""){

			$form->get('priority')->addError(new FormError("This value is required"));
			$isValid = false;
			
		}
		
		if($form->get('append')->getData()){
			$priority = $em->getRepository('SilvanusChainsBundle:StackChain')->getLastPriorityByChain($id);
		}else{
		
			if($form->get('force')->getData()){
				$em->getRepository('SilvanusChainsBundle:StackChain')->fixPriorityOffset($id,$form->get('priority')->getData());
				$priority	= $form->get('priority')->getData();
			}else{
				
				$exists = $em->getRepository('SilvanusChainsBundle:StackChain')->checkPriorityExists($id,$form->get('priority')->getData());
				
				if($exists){
					$form->get('priority')->addError(new FormError("This priority already exists"));
					$isValid = false;					
				}else{
					$priority = $form->get('priority')->getData();		
				}
					
			}
			
		}
		
		if($isValid){
			if($priority<=0){
				$form->get('priority')->addError(new FormError("Priority must be > 0 and integer"));
				$isValid = false;					
			}			
		}			

		if($isValid){
		
			$entity = new StackChain();
			$entity->setChainParent($em->getRepository('SilvanusChainsBundle:Chain')->findOneBy(array('id'=>$id)));
			$entity->setChainChildren($em->getRepository('SilvanusChainsBundle:Chain')->findOneBy(array('id'=>$form->get('chainChildren')->getData())));
			$entity->setActive($form->get('active')->getData());
			$entity->setPriority($priority);
			$em->persist($entity);
			$em->flush();
			
			$entities 	= $em->getRepository('SilvanusChainsBundle:StackChain')->findBy(array('chainParent'=>$id));
			return $this->redirect(
				$this->generateUrl('stack_chains',
					array(
						'message'		=> 'Chain add to stack',
						'entities'		=> $entities,
						'id'			=> $id,						
					)
			));


		}

		return $this->render('SilvanusChainsBundle:StackChain:add.html.twig', array(
			'form'			=> $form->createView(),
			'id'			=> $id,	
		));


    }

    /**
     * Load form up update chain
     *
     */
    public function modifyAction(Request $request, $chain_parent, $chain_children)
    {

		$em 			= $this->getDoctrine()->getManager();
		$entity 		= $em->getRepository('SilvanusChainsBundle:StackChain')->findOneBy(array(
			'chainParent'=>$chain_parent,
			'chainChildren'=>$chain_children,
		));

		$form 			= $this->createModifyForm();
		$form->get('active')->setData($entity->getActive());
		$form->get('priority')->setData($entity->getPriority());
		
		return $this->render('SilvanusChainsBundle:StackChain:modify.html.twig', array(
			'form'		=> $form->createView(),
			'entity'	=> $entity,
		));

    }

    /**
     * Update 
     *
     */
    public function updateAction(Request $request, $chain_parent, $chain_children)
    {


		$form 			= $this->createModifyForm();	
		$form->submit($request);
		$em 			= $this->getDoctrine()->getManager();
		$entity 		= $em->getRepository('SilvanusChainsBundle:StackChain')->findOneBy(array(
			'chainParent'=>$chain_parent,
			'chainChildren'=>$chain_children,
		));

		
		$isValid = true;
		$priority = $form->get('priority')->getData();				
		if(!$form->get('append')->getData() and $form->get('priority')->getData()==""){

			$form->get('priority')->addError(new FormError("This value is required"));
			$isValid = false;
			
		}
		
		if($form->get('append')->getData()){
			$priority = $em->getRepository('SilvanusChainsBundle:StackChain')->getLastPriorityByChain($chain_parent);
		}else{
		
			if($form->get('force')->getData()){
				$em->getRepository('SilvanusChainsBundle:StackChain')->fixPriorityOffset($chain_parent,$form->get('priority')->getData());
				$priority	= $form->get('priority')->getData();
			}else{
				
				$exists = $em->getRepository('SilvanusChainsBundle:StackChain')->checkPriorityExists($chain_parent,$form->get('priority')->getData());
				
				if($exists){
					
					if($form->get('priority')->getData()!=$entity->getPriority()){					
						$form->get('priority')->addError(new FormError("This priority already exists"));
						$isValid = false;					
					}
					
				}else{
					$priority = $form->get('priority')->getData();		
				}
					
			}
			
		}

		if($isValid){			
			if($priority<=0){
				$form->get('priority')->addError(new FormError("Priority must be > 0 and integer"));
				$isValid = false;					
			}			
		}			
							
		if($isValid){
		
			
			$entity->setChainParent($em->getRepository('SilvanusChainsBundle:Chain')->findOneBy(array('id'=>$chain_parent)));
			$entity->setChainChildren($em->getRepository('SilvanusChainsBundle:Chain')->findOneBy(array('id'=>$chain_children)));
			$entity->setActive($form->get('active')->getData());
			$entity->setPriority($form->get('priority')->getData());
			$em->persist($entity);
			$em->flush();
			
			$entities 	= $em->getRepository('SilvanusChainsBundle:StackChain')->findBy(array('chainParent'=>$chain_parent));
			return $this->redirect(
				$this->generateUrl('stack_chains',
					array(
						'message'		=> 'Update succefull',
						'entities'		=> $entities,
						'id'			=> $chain_parent,						
					)
			));


		}
		
		return $this->render('SilvanusChainsBundle:StackChain:modify.html.twig', array(
			'form'			=> $form->createView(),
			'entity'		=> $entity,	
		));

    }
    
    /*
     * Remove chain of stack
     * 
     * */
    public function removeAction(Request $request, $chain_parent, $chain_children){

		$em = $this->getDoctrine()->getManager();
		$entity 		= $em->getRepository('SilvanusChainsBundle:StackChain')->findOneBy(array(
			'chainParent'=>$chain_parent,
			'chainChildren'=>$chain_children,
		));

		$em->remove($entity);
		$em->flush();

		$em->getRepository('SilvanusChainsBundle:StackChain')->fixPriorityIndex($chain_parent);

		$entities 	= $em->getRepository('SilvanusChainsBundle:StackChain')->findBy(array('chainParent'=>$chain_parent));
		return $this->redirect(
			$this->generateUrl('stack_chains',
				array(
					'message'		=> 'Update succefull',
					'entities'		=> $entities,
					'id'			=> $chain_parent,						
				)
		));
	
	}


    /**
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createNewForm($id)
    {
		
		$em 		= $this->getDoctrine()->getManager();
		$entities	= $em->getRepository('SilvanusChainsBundle:Chain')->findBy(array('type'=>'stack'));
		
		foreach($entities as $entity){
			$arr[$entity->getId()]=$entity->getName();
		}
		
		
        return $this->createFormBuilder()
			->add('chainChildren','choice',array(
				'label' => 'Chain to add',
				'choices'=>$arr,
            ))
            ->add('active','checkbox',array(
				'label'=>'Active',
				'required'=>false,
				'data'=>true
            ))
            ->add('priority','integer',array(
				'label'=>'Priority',
				'required'=>false,
            ))
            ->add('force','checkbox',array(
				'label'=>'Force',
				'required'=>false,
            ))
            ->add('append','checkbox',array(
				'label'=>'Append',
				'required'=>false,
            ))
            ->getForm()
        ;
    }

    /**
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createModifyForm()
    {
			
        return $this->createFormBuilder()
            ->add('active','checkbox',array(
				'label'=>'Active',
				'required'=>false,
				'data'=>true
            ))
            ->add('priority','integer',array(
				'label'=>'Priority',
				'required'=>false,
            ))
            ->add('force','checkbox',array(
				'label'=>'Force',
				'required'=>false,
            ))
            ->add('append','checkbox',array(
				'label'=>'Append',
				'required'=>false,
            ))
            ->getForm()
        ;
    }


}
