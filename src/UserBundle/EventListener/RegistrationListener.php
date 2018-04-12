<?php
namespace UserBundle\EventListener;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Listener responsible for adding the default user role at registration
 */
class RegistrationAddRoleDefaultListener implements EventSubscriberInterface
{

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
        FOSUserEvents::REGISTRATION_CONFIRMED => [
        ['onRegistrationSuccess', -10],
        ],
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function onRegistrationSuccess(FormEvent $event)
    {
        $rolesArr = array('ROLE_USER');
        $user = $event->getForm()->getData();
        $user->setRoles($rolesArr);
    }
}
