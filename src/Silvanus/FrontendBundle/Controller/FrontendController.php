<?php

namespace Silvanus\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FrontendController extends Controller
{
    public function indexAction()
    {
        return $this->render('SilvanusFrontendBundle:Frontend:index.html.twig', array());
    }
}
