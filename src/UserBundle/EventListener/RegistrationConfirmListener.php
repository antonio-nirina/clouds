<?php
namespace UserBundle\EventListener;

use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RegistrationConfirmListener implements EventSubscriberInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    private $router;
    private $serviceContainer;

    /**
     * RegistrationConfirmListener constructor.
     * @param UrlGeneratorInterface $router
     * @param ContainerInterface $serviceContainer
     */
    public function __construct(UrlGeneratorInterface $router, ContainerInterface $serviceContainer)
    {
        $this->router = $router;
        $this->serviceContainer = $serviceContainer;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            FOSUserEvents::REGISTRATION_SUCCESS => [
                ['onRegistrationSuccess', -10],
            ],
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function onRegistrationSuccess(FormEvent $event)
    {
        $user = $event->getForm()->getData();

        //Set Default role
        $rolesDefault = array('ROLE_COMMERCIAL');
        $user->setRoles($rolesDefault);

        //Create accoumpt in Mailjet > Contact
        $Contact = $this->serviceContainer->get('AdminBundle\Service\MailJet\MailjetContactList');
        //TODO : à supprimer si non utilisé
        $responseCreateContact = $Contact->createContactByMail($user);
    }
}
