<?php
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
     * @Route("/", name="admin_dashboard_show")
     */
    public function showAction()
    {
        return new Response("<html><body>Admin dashboard!!</body></html>");
    }
}