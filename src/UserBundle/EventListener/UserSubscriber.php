<?php

namespace UserBundle\EventListener;

use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserSubscriber implements EventSubscriberInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * ResettingListener constructor.
     *
     * @param UrlGeneratorInterface $router
     * @param int $tokenTtl
     */
    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::RESETTING_RESET_SUCCESS => 'onSuccessResettingReset',
        );
    }

    /**
     * @param GetResponseUserEvent $event
     */
    public function onSuccessResettingReset(FormEvent $event)
    {
        $event->setResponse(new RedirectResponse($this->router->generate('admin_dashboard_kpi')));
    }
}
