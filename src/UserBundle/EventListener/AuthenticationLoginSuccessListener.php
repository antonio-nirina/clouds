<?php

namespace UserBundle\EventListener;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Routing\RouterInterface;

class AuthenticationLoginSuccessListener implements AuthenticationSuccessHandlerInterface
{

    private $router;


    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param InteractiveLoginEvent $event
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $token = $event->getAuthenticationToken();
        $request = $event->getRequest();
        $this->onAuthenticationSuccess($request, $token);
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @return RedirectResponse
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $roles = array();

        foreach ($token->getRoles() as $value) {
            $roles[] = $value->getRole();
        }

        if (in_array("ROLE_ADMIN", $roles)) {
            return new RedirectResponse($this->router->generate('admin_dashboard_kpi'));
        } else {
            return new RedirectResponse($this->router->generate('beneficiary_home'));
        }
    }
}
