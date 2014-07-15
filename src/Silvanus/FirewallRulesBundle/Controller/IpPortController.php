<?php

namespace Silvanus\FirewallRulesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\Extension\Core\Type\FileType;

use Silvanus\FirewallRulesBundle\Entity\IpPort;
use Silvanus\FirewallRulesBundle\Entity\TransportProtocol;

use Silvanus\FirewallRulesBundle\Form\IpPortType;

/**
 * IpPort controller.
 *
 */
class IpPortController extends Controller
{

	public function ajaxSearchPortAction(Request $request, $service){
	
		$em 	= $this->getDoctrine()->getManager();
		
		$dql	= "SELECT DISTINCT p.service AS p_service, p.number AS p_number, t.name AS t_name FROM Silvanus\FirewallRulesBundle\Entity\IpPort p ";
		$dql   .= "INNER JOIN Silvanus\FirewallRulesBundle\Entity\TransportProtocol t WHERE p.service LIKE ?1 AND p.protocol = t";
		
		$quey 	= $em->createQuery($dql);
		$quey->setParameter(1, "%".$service."%");
		
		$arr = array();
		foreach($quey->getResult() as $entity){
						
			$arr[] = $entity['p_service']." (".$entity['p_number'].") [".$entity['t_name']."]";
			
		}
		
		
		
		
		
		return $this->render('SilvanusFirewallRulesBundle:IpPort:ajaxsearchport.html.twig', array(
			'arr'	=> 	$arr,
		));
		
			
	}

    /**
     * Lists all IpPort entities.
     *
     */
    public function indexAction($page, $message=null)
    {
        $em = $this->getDoctrine()->getManager();

		$paginator  = $this->get('knp_paginator');
        //$entities = $em->getRepository('SilvanusFirewallRulesBundle:IpPort')->findAll();

		$pagination = $paginator->paginate($em->createQuery('SELECT p FROM Silvanus\FirewallRulesBundle\Entity\IpPort p'),  $this->get('request')->query->get('page', $page), 60);

		$form_iana = $this->createIanaForm();

        return $this->render('SilvanusFirewallRulesBundle:IpPort:index.html.twig', array(
            //'entities' 	=> $entities,
            'pagination' 	=> $pagination,
            'message'		=> $message,
            'form_iana'		=> $form_iana->createView(),
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
    
    /**
     * Insert iana ports from file to database
     * 
     * 
     * */
    public function ianaAction(Request $request){
	
		$form = $this->createIanaForm();
		$form->handleRequest($request);
		
		//get the temp file path
		foreach($request->files as $file){
			
			if(isset($file['attachment'])){			
				$f = $file['attachment'];
			}			
		}

		$em = $this->getDoctrine()->getManager();

		//truncate tables
		$connection = $em->getConnection();
		$platform   = $connection->getDatabasePlatform();
		$connection->executeUpdate($platform->getTruncateTableSQL('IpPort', true /* whether to cascade */));		
		$connection->executeUpdate($platform->getTruncateTableSQL('TransportProtocol', true /* whether to cascade */));		
		
		$n=0;
		$fields=array();
		$protocols=array();
		$handle = @fopen($f->getPathname(), "r");
		if ($handle) {
			while (($buffer = fgets($handle, 4096)) !== false) {
				$arrLine=explode(",",$buffer);
				if($n==0){

					for($y=0;$y<count($arrLine);$y++){
						$fields[$arrLine[$y]]=$y;						
					}
					
				}else{
					
					if(count($arrLine)>3 and strpos($arrLine[0],"IANA assigned this well-formed service name as a replacement for")===false){
					
						if(!in_array($arrLine[$fields['Transport Protocol']],$protocols)){
						
							$protocol=$em->getRepository('SilvanusFirewallRulesBundle:TransportProtocol')->findBy(array('name'=>strtoupper($arrLine[$fields['Transport Protocol']])));
							
							if(!$protocol){
								
								$protocol = new TransportProtocol();
								$protocol->setName(strtoupper($arrLine[$fields['Transport Protocol']]));							
								$em->persist($protocol);
								
								$protocols[]=$arrLine[$fields['Transport Protocol']];
								
							}
						
							$entProtocol[$arrLine[$fields['Transport Protocol']]]=$protocol;
						
						}
						
						
						$entity = new IpPort();
						
						if(isset($arrLine[$fields['Service Name']])){
							$entity->setService($arrLine[$fields['Service Name']]);
						}
						
						if(isset($arrLine[$fields['Port Number']])){
							$entity->setNumber($arrLine[$fields['Port Number']]);
						}
						
						if(isset($arrLine[$fields['Description']])){
							$entity->setDescription($arrLine[$fields['Description']]);
						}
						
						if(isset($arrLine[$fields['Reference']])){
							$entity->setReference($arrLine[$fields['Reference']]);
						}
						
						
						$entity->setProtocol($entProtocol[$arrLine[$fields['Transport Protocol']]]);
										
						$em->persist($entity);
						
					}					
				}
				$n++;
			}
	
			if (!feof($handle)) {
				echo "Error: unexpected fgets() fail\n";
			}
			fclose($handle);
		}		

		$em->flush();
		
		return $this->redirect($this->generateUrl('ipport', array('message'=>'Import success')));
		//return new Response ('<br><br>OK');
		 
	}
    
    /**
     * Import Iana file Form
     * 
     * */
	private function createIanaForm(){

        return $this->createFormBuilder()
            ->setAction($this->generateUrl('ipport_iana'))
            ->setMethod('POST')
            ->add('attachment', 'file', array('required' => true, 'data_class' => null, 'mapped'=>false))
            ->getForm();
				
		
	}

}
