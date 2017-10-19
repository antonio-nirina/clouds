<?php
// AdminBundle/Controller/ParametragesController.php

namespace AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/admin/parametrages")
 */
class ParametragesController extends Controller
{
    /**
     * @Route("/", name="admin_parametrages_programme")
     */
    public function ProgrammeAction()
    {
        return $this->render('AdminBundle:Parametrages:Programme.html.twig', array());
    }
	
	/**
     * @Route("/inscriptions", name="admin_parametrages_inscriptions")
     */
    public function InscriptionsAction()
    {
        return $this->render('AdminBundle:Parametrages:Inscriptions.html.twig', array());
    }
	
	/**
     * @Route("/imports", name="admin_parametrages_imports")
     */
    public function ImportsAction()
    {
        return $this->render('AdminBundle:Parametrages:Imports.html.twig', array());
    }
}