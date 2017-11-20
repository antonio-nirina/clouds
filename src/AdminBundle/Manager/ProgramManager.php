<?php

namespace AdminBundle\Manager;

use AdminBundle\Entity\Program;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RequestStack;

class ProgramManager
{
    private $em;

    public function __construct(EntityManager $em, RequestStack $request_stack)
    {
        $this->em = $em;
        $this->request_stack = $request_stack;
    }

    public function getCurrent($request = null)
    {
        $url_program = $this->request_stack->getCurrentRequest()->server->get('HTTP_HOST');
        $program = $this->em->getRepository('AdminBundle:Program')->findByUrl($url_program);

        if (!empty($program)) {
            return $program[0];
        } else {
            return $program;
        }
    }
}
