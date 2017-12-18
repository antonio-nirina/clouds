<?php
namespace AdminBundle\Controller;

use AdminBundle\Controller\AdminController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/admin/communication")
 */
class CommunicationController extends AdminController
{
    const SIDEBAR_VIEW = 'AdminBundle:Communication:menu_sidebar_communication.html.twig';

    public function __construct()
    {
        $this->active_menu_index = 3;
        $this->sidebar_view = self::SIDEBAR_VIEW;
    }

    /**
     * @Route("/edito", name="admin_communication_editorial")
     */
    public function editorialAction()
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        return $this->render('AdminBundle:Communication:edito.html.twig');
    }
}

