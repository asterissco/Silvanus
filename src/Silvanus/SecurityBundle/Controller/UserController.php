<?php

namespace Silvanus\SecurityBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;

use Silvanus\SecurityBundle\Entity\User;
use Silvanus\SecurityBundle\Form\UserCreateType;
use Silvanus\SecurityBundle\Form\UserUpdateType;
use Silvanus\SecurityBundle\Form\UserChangePasswordType;

/**
 * User controller.
 *
 */
class UserController extends Controller
{

    /**
     * Lists all User entities.
     *
     */
    public function indexAction($message)
    {
		

        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('SilvanusSecurityBundle:User')->findAll();

        return $this->render('SilvanusSecurityBundle:User:index.html.twig', array(
            'entities' 		=> $entities,
            'message'		=> $message,
        ));
    }
    /**
     * Creates a new User entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new User();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            
            $em = $this->getDoctrine()->getManager();
            
			$factory        = $this->get('security.encoder_factory');
			$encoder        = $factory->getEncoder($entity);
			$password       = $encoder->encodePassword($entity->getPassword(), $entity->getSalt());
			$entity->setPassword($password);
                        
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('user', array('message' => 'Create successful: '.$entity->getUsername())));
        
        }

        return $this->render('SilvanusSecurityBundle:User:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
    * Creates a form to create a User entity.
    *
    * @param User $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(User $entity)
    {
        $form = $this->createForm(new UserCreateType(), $entity, array(
            'action' => $this->generateUrl('user_create'),
            'method' => 'POST',
        ));

        //$form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new User entity.
     *
     */
    public function newAction()
    {
		
        $entity = new User();
        $form   = $this->createCreateForm($entity);

        return $this->render('SilvanusSecurityBundle:User:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    
    }

    /**
     * Finds and displays a User entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SilvanusSecurityBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SilvanusSecurityBundle:User:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SilvanusSecurityBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SilvanusSecurityBundle:User:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a User entity.
    *
    * @param User $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(User $entity)
    {
        $form = $this->createForm(new UserUpdateType(), $entity, array(
            'action' => $this->generateUrl('user_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        //$form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing User entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SilvanusSecurityBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm 	= $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

			return $this->redirect($this->generateUrl('user', array('message' => 'Updated successful: '.$entity->getUsername())));
            
        }

        return $this->render('SilvanusSecurityBundle:User:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a User entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {


		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('SilvanusSecurityBundle:User')->find($id);

		if (!$entity) {
			throw $this->createNotFoundException('Unable to find User entity.');
		}

		$em->remove($entity);
		$em->flush();

        return $this->redirect($this->generateUrl('user', array('message' => 'Delete successful')));
    }

    /**
     * Creates a form to delete a User entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('user_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }


	public function updatePasswordAction(Request $request, $id){
	
		$em 			= $this->getDoctrine()->getManager();
		
		$entity			= $em->getRepository('SilvanusSecurityBundle:User')->find($id);


		if (!$entity) {
			throw $this->createNotFoundException('Unable to find User entity.');
		}


		$arrForm	=	$request->request->get('silvanus_securitybundle_user');

		$form = $this->changePasswordCreateFormAction($entity->getId());
		$form->handleRequest($request);

			
		if ($arrForm['password']['first']==$arrForm['password']['second']) {
		
			$factory        = $this->get('security.encoder_factory');
			$encoder        = $factory->getEncoder($entity);
			$password       = $encoder->encodePassword($arrForm['password']['first'], $entity->getSalt());
			$entity->setPassword($password);
						
			$em->persist($entity);
			$em->flush();

			return $this->redirect($this->generateUrl('user', array('message' => 'Update password: '.$entity->getUsername())));

		}
		
		$form->get('password')->get('second')->addError(new FormError('The password fields must match.'));

        return $this->render('SilvanusSecurityBundle:User:changepassword.html.twig', array(
            'edit_form'   	=> $form->createView(),
            'entity'		=> $entity
        ));
	
	
	}

    /** 
     * Create form to change password
     * 
     * 
     * */
	public function changePasswordAction($id){

		$em 			= $this->getDoctrine()->getManager();
		
		$entity			= $em->getRepository('SilvanusSecurityBundle:User')->find($id);
        $edit_form   	= $this->changePasswordCreateFormAction($id);

        return $this->render('SilvanusSecurityBundle:User:changepassword.html.twig', array(
            'edit_form'   	=> $edit_form->createView(),
            'entity'		=> $entity
        ));

		 
	}

    
    /** 
     * Create form to change password
     * 
     * 
     * */
	public function changePasswordCreateFormAction($id){

        $form = $this->createForm(new UserChangePasswordType(), null, array(
            'action' => $this->generateUrl('user_update_password', array('id'=>$id)),
            'method' => 'POST',
        ));

        return $form;
		 
	}

}
