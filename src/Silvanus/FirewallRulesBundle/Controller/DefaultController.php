<?php

namespace Silvanus\FirewallRulesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('SilvanusFirewallRulesBundle:Default:index.html.twig', array('name' => $name));
    }
}
