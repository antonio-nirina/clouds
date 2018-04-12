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
    private $service_container;

    /**
     * RegistrationConfirmListener constructor.
     * @param UrlGeneratorInterface $router
     * @param ContainerInterface $service_container
     */
    public function __construct(UrlGeneratorInterface $router, ContainerInterface $service_container)
    {
        $this->router = $router;
        $this->service_container = $service_container;
    }

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
        $Contact = $this->service_container->get('AdminBundle\Service\MailJet\MailjetContactList');
        //TODO : à supprimer si non utilisé
        $responseCreateContact = $Contact->createContactByMail($user);
    }
}
