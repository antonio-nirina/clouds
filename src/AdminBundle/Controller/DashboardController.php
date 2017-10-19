<?php
// src/AdminBundle/Controller/DashboardController.php

namespace AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/admin/dashboard")
 */
class DashboardController extends Controller
{
    /**
     * @Route("/", name="admin_dashboard_kpi")
     */
    public function kpiAction()
    {
        return $this->render('AdminBundle:Dashboard:kpi.html.twig', array());
    }
    
}