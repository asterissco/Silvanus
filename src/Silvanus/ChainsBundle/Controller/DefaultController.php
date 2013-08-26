<?php

namespace Silvanus\ChainsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('SilvanusChainsBundle:Default:index.html.twig', array('name' => $name));
    }
}
