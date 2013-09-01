<?php

namespace Silvanus\SyncBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SyncController extends Controller
{
    public function indexAction()
    {
		
		$em = $this->getDoctrine()->getManager();
		$entities=$em->getRepository('SilvanusSyncBundle:Sync')->findAll();
	
		
        return $this->render('SilvanusSyncBundle:Sync:index.html.twig', array(
			'entities'=>$entities,
		));
    }
}
