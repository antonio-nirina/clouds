<?php
namespace BeneficiaryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class PageController extends Controller
{
    /**
     * @Route("/", name="beneficiary_home")
     */
    public function homePageAction()
    {
        return $this->render('BeneficiaryBundle:Page:home.html.twig');
    }
}