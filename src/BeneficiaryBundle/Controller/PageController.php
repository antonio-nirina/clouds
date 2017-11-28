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
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $home_page_data = $program->getHomePageData();
        if (is_null($home_page_data)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $editorial = $home_page_data->getEditorial();
        if (is_null($editorial)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        return $this->render('BeneficiaryBundle:Page:home.html.twig', array(
            'editorial' => $editorial,
        ));
    }
}