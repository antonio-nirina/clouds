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
    
    public static function getSubscribedEvents()
    {
        return [
        FOSUserEvents::REGISTRATION_CONFIRMED => [
        ['onRegistrationSuccess', -10],
        ],
        ];
    }

    public function onRegistrationSuccess(FormEvent $event)
    {
        $rolesArr = array('ROLE_USER');

        /**
 * @var $user \FOS\UserBundle\Model\UserInterface 
*/
        $user = $event->getForm()->getData();
        $user->setRoles($rolesArr);
    }
}