<?php
namespace UserBundle\EventListener;

use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Listener responsible to change the redirection at the end of the password resetting
 */
class RegistrationConfirmListener implements EventSubscriberInterface
{
    private $router;

    /**
     * RegistrationConfirmListener constructor.
     * @param UrlGeneratorInterface $router
     */
    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::REGISTRATION_CONFIRM  => 'onRegistrationConfirm'
        );
    }

    /**
     * @param GetResponseUserEvent $event
     */
    public function onRegistrationConfirm(GetResponseUserEvent $event)
    {
        $url = $this->router->generate('rsWelcomeBundle_check_full_register');

        $event->setResponse(new RedirectResponse($url));
    }
}
