<?php
namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class TestController extends Controller
{
    /**
     * @Route("/test/slide-max-order")
     */
    public function testQueryAction()
    {
        $em = $this->getDoctrine()->getManager();
        $program = $this->container->get('admin.program')->getCurrent();
        dump($program);
        $login_portal_data = $program->getLoginPortalData();
        dump($login_portal_data);
        $max = $em->getRepository('AdminBundle\Entity\LoginPortalData')
            ->retrieveMaxSlideOrderByLoginPortalData($login_portal_data);
        dump($max);

        return new Response('<html><body>Finished!!</body></html>');
    }
}