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

        $table_network = $program->getSiteTableNetworkSetting();
        $has_network = false;
        if ($table_network->getHasFacebook() || $table_network->getHasLinkedin() || $table_network->getHasTwitter()) {
            $has_network = true;
        }

        $background_link = '';
        if ($background = $program->getSiteDesignSetting()->getBodyBackground()) {
            $background_link = $this->container->getParameter('background_path').'/'.$program->getId().'/'.$background;
        }
                
        $em = $this->getDoctrine()->getManager();
        $ordered_slide_list = $em->getRepository('AdminBundle\Entity\HomePageSlide')
            ->findByHomePageDataOrdered($home_page_data);

        return $this->render('BeneficiaryBundle:Page:home.html.twig', array(
            'editorial' => $editorial,
            'has_network' => $has_network,
            'table_network' => $table_network,
            'slide_list' => $ordered_slide_list,
            'background_link' => $background_link
        ));
    }
}