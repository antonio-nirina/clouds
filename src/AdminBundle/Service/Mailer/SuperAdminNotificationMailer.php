<?php
namespace AdminBundle\Service\Mailer;

use Doctrine\ORM\EntityManager;
use UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SuperAdminNotificationMailer
{
    private $mailer;
    private $em;
    private $twig;
    private $container;
    private $error_list;

    public function __construct(
        \Swift_Mailer $mailer,
        EntityManager $em,
        \Twig_Environment $twig,
        ContainerInterface $container
    ) {
        $this->mailer = $mailer;
        $this->em = $em;
        $this->twig = $twig;
        $this->container = $container;
    }

    public function getErrorList()
    {
        return $this->error_list;
    }

    private function retrieveRecipientEmailList()
    {
        $recipient_list = $this->em->getRepository('UserBundle\Entity\User')
            ->findSuperAdmin();
        $recipient_email_list = array();
        if (!empty($recipient_list)) {
            foreach ($recipient_list as $recipient) {
                array_push($recipient_email_list, $recipient->getEmail());
            }
        }
        return $recipient_email_list;
    }

    public function sendBeContactedNotification(User $sender)
    {
        $recipient_email_list = $this->retrieveRecipientEmailList();
        if (!empty($recipient_email_list)) {
            $email_subject = $this->container->getParameter('be_contacted_email_subject');
            foreach ($recipient_email_list as $recipient_email) {
                $message = (new \Swift_Message($email_subject))
                    ->setFrom($sender->getEmail())
                    ->setTo($recipient_email)
                    ->setBody(
                        $this->twig->render(
                            'AdminBundle:Emails/SuperAdminNotification:be_contacted.html.twig',
                            array(
                            'sender_email' => $sender->getEmail()
                            )
                        ),
                        'text/html'
                    );
                $failures = null;
                if (!$this->mailer->send($message, $failures)) {
                    $this->error_list = $failures;
                }
            }
        }

        return;
    }
}
