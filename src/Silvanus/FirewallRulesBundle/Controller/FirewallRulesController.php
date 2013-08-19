<?php

namespace Silvanus\FirewallRulesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Silvanus\FirewallRulesBundle\Entity\FirewallRules;
use Silvanus\FirewallRulesBundle\Form\FirewallRulesType;

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
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SilvanusFirewallRulesBundle:FirewallRules')->findAll();

        return $this->render('SilvanusFirewallRulesBundle:FirewallRules:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new FirewallRules entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity  = new FirewallRules();
        $form = $this->createForm(new FirewallRulesType(), $entity);
        $form->submit($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('firewallrules_show', array('id' => $entity->getId())));
        }

        return $this->render('SilvanusFirewallRulesBundle:FirewallRules:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to create a new FirewallRules entity.
     *
     */
    public function newAction()
    {
        $entity = new FirewallRules();
        $form   = $this->createForm(new FirewallRulesType(), $entity);

        return $this->render('SilvanusFirewallRulesBundle:FirewallRules:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
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
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing FirewallRules entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SilvanusFirewallRulesBundle:FirewallRules')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FirewallRules entity.');
        }

        $editForm = $this->createForm(new FirewallRulesType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SilvanusFirewallRulesBundle:FirewallRules:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing FirewallRules entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SilvanusFirewallRulesBundle:FirewallRules')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FirewallRules entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new FirewallRulesType(), $entity);
        $editForm->submit($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('firewallrules_edit', array('id' => $id)));
        }

        return $this->render('SilvanusFirewallRulesBundle:FirewallRules:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a FirewallRules entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
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
}
