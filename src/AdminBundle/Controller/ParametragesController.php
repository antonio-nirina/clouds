<?php
// AdminBundle/Controller/ParametragesController.php

namespace AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/admin/parametrages")
 */
class ParametragesController extends Controller
{

    public function sidebarAction()
    {
        $em = $this->getDoctrine()->getManager();

        $url = 'cloud-rewards.peoplestay.com';

        $program = $em->getRepository('AdminBundle:Program')->findByUrl($url);
        $level = $program[0]->getParamLevel();

        return $this->render('AdminBundle:Parametrages:menu-sidebar-parametrages.html.twig', array(
                                    'level' => $level
                                ));
    }

    /**
     * @Route("/", name="admin_parametrages_programme")
     * @Method({"GET","POST"})
     */
    public function programmeAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $url = 'cloud-rewards.peoplestay.com';

        $program = $em->getRepository('AdminBundle:Program')->findByUrl($url);

        if (empty($program)) {//redirection si program n'existe pas
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $program = $program[0];
        $current_program = $program->getType();
        $program_type_repo = $em->getRepository('AdminBundle:ProgramType');
        $all_program_type = $program_type_repo->findAll();

        if ("POST" === $request->getMethod()) {
            $type = (int) $request->get('program_type');
            $is_multi_operation = ((int) $request->get('challenge_mode'))?true:false;

            $changed_type = true;
            if ($type === $program->getType()->getId()) {
                $changed_type = false;
            } else {
                $new_type = $program_type_repo->find($type);
                $program->setType($new_type);
            }

            if ($is_multi_operation != $program->getIsMultiOperation()) {
                $program->setIsMultiOperation($is_multi_operation);
            }

            if (($program->getParamLevel() < 1) || $changed_type) { //remettre au level 1
                $program->setParamLevel(1);
            }

            $em->flush();

            return $this->redirectToRoute('admin_parametrages_inscriptions');
        }

        return $this->render('AdminBundle:Parametrages:Programme.html.twig', array(
                                    'all_program_type' => $all_program_type,
                                    'program' => $program
                                ));
    }

    /**
     * @Route("/inscriptions", name="admin_parametrages_inscriptions")
     */
    public function inscriptionsAction()
    {
        return $this->render('AdminBundle:Parametrages:Inscriptions.html.twig', array());
    }

    /**
     * @Route("/imports", name="admin_parametrages_imports")
     */
    public function importsAction()
    {
        return $this->render('AdminBundle:Parametrages:Imports.html.twig', array());
    }
}
