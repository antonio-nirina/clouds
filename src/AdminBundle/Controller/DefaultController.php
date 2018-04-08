<?php

namespace AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('AdminBundle:Default:index.html.twig');
    }

    /**
     * @Route("/test/email/")
     */
    public function sendEmailAction()
    {
        $mailer = $this->get('swiftmailer.mailer.default');
        // dump($mailer);die;
        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('tendryclouds@gmail.com')
            ->setTo('lemospy@gmail.com')
            ->setBody('You should see me from the profiler!');

        $failures = 'failure';

        if (!$mailer->send($message, $failures)) {
            echo "Failures:";
            throw new NotFoundHttpException('Erreur envoie mail');
        }

        return new Response('<html><body>Admin dashboard!!</body></html>');
    }
}
