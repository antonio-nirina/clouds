<?php
namespace AppBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class HttpHeaderCustomizer
{
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();
        $response->headers->set('Cache-control', 'no-cache, must-revalidate, no-store');
        $response->headers->set('max-age', 0);
        $event->setResponse($response);
    }
}

