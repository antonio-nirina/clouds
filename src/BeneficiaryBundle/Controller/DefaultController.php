<?php

namespace BeneficiaryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{

    /**
     *
     * @return type
     */
    public function indexAction()
    {
        return $this->render('BeneficiaryBundle:Default:index.html.twig');
    }
}
