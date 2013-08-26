<?php

namespace Silvanus\ChainsBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Silvanus\ChainsBundle\Entity\Chain;
use Silvanus\ChainsBundle\Form\ChainType;

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
     * @Route("/", name="chain")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SilvanusChainsBundle:Chain')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Chain entity.
     *
     * @Route("/", name="chain_create")
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

            return $this->redirect($this->generateUrl('chain_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Chain entity.
     *
     * @Route("/new", name="chain_new")
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
     * @Route("/{id}", name="chain_show")
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
     * @Route("/{id}/edit", name="chain_edit")
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

        $editForm = $this->createForm(new ChainType(), $entity);
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
     * @Route("/{id}", name="chain_update")
     * @Method("PUT")
     * @Template("SilvanusChainsBundle:Chain:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SilvanusChainsBundle:Chain')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Chain entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new ChainType(), $entity);
        $editForm->submit($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('chain_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Chain entity.
     *
     * @Route("/{id}", name="chain_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->submit($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SilvanusChainsBundle:Chain')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Chain entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('chain'));
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
}
