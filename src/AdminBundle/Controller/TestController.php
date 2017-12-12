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

    /**
     * @Route("/test/upper-rank")
     */
    public function testUpperRankAction()
    {
        $em = $this->getDoctrine()->getManager();
        $program = $this->container->get('admin.program')->getCurrent();
        dump($program);
        $result = $em->getRepository('AdminBundle:Role')
            ->findHigherRank($program, 3);
        $result1 = $em->getRepository('AdminBundle:Role')
            ->findByProgram($program);
        $container = $this->container->get("AdminBundle\Service\PointAttribution\SalesPointAttribution");
        $date = new \DateTime();
        $data = \DateTime::createFromFormat('m', 1);
        dump(date_format($data, 'F'));
        dump(date_format($date, 'l jS F'));
        dump($container);
        dump($result);
        dump($result1);

        return new Response('<html><body>Finished!!</body></html>');
    }
}
