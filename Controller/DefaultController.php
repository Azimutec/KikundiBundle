<?php

namespace Azimutec\KikundiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('AzimutecKikundiBundle:Default:index.html.twig');
    }
}
