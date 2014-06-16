<?php

namespace Silvanus\FirewallRulesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Silvanus\FirewallRulesBundle\Entity\IpPort;
use Silvanus\FirewallRulesBundle\Form\IpPortType;

/**
 * IpPort controller.
 *
 */
class IpPortController extends Controller
{

    /**
     * Lists all IpPort entities.
     *
     */
    public function indexAction($message=null)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SilvanusFirewallRulesBundle:IpPort')->findAll();

        return $this->render('SilvanusFirewallRulesBundle:IpPort:index.html.twig', array(
            'entities' 	=> $entities,
            'message'	=> $message,
		));
    }
    /**
     * Creates a new IpPort entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new IpPort();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('ipport', array('message'=>'Create successful')));
        }

        return $this->render('SilvanusFirewallRulesBundle:IpPort:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
    * Creates a form to create a IpPort entity.
    *
    * @param IpPort $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(IpPort $entity)
    {
        $form = $this->createForm(new IpPortType(), $entity, array(
            'action' => $this->generateUrl('ipport_create'),
            'method' => 'POST',
        ));

       

        return $form;
    }

    /**
     * Displays a form to create a new IpPort entity.
     *
     */
    public function newAction()
    {
        $entity = new IpPort();
        $form   = $this->createCreateForm($entity);

        return $this->render('SilvanusFirewallRulesBundle:IpPort:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a IpPort entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SilvanusFirewallRulesBundle:IpPort')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find IpPort entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SilvanusFirewallRulesBundle:IpPort:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing IpPort entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SilvanusFirewallRulesBundle:IpPort')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find IpPort entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SilvanusFirewallRulesBundle:IpPort:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a IpPort entity.
    *
    * @param IpPort $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(IpPort $entity)
    {
        $form = $this->createForm(new IpPortType(), $entity, array(
            'action' => $this->generateUrl('ipport_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        

        return $form;
    }
    /**
     * Edits an existing IpPort entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SilvanusFirewallRulesBundle:IpPort')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find IpPort entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            
            $em->flush();
			return $this->redirect($this->generateUrl('ipport', array('message'=>'Update successful')));

        }

        return $this->render('SilvanusFirewallRulesBundle:IpPort:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a IpPort entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {

		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('SilvanusFirewallRulesBundle:IpPort')->find($id);

		if (!$entity) {
			throw $this->createNotFoundException('Unable to find IpPort entity.');
		}

		$em->remove($entity);
		$em->flush();

       return $this->redirect($this->generateUrl('ipport', array('message'=>'Delete successful')));
    }

    /**
     * Creates a form to delete a IpPort entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('ipport_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
