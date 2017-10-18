<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class AdminDashboardController extends Controller
{
    /**
     * @Route("/admin/dashboard", name="admin_show_dashboard")
     */
    public function showAction()
    {
        return new Response("<html><body>Admin dashboard</body></html>");
    }
}