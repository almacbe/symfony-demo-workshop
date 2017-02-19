<?php

namespace AppBundle\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class MaintenanceListener
{
    /**
     * @var bool
     */
    private $enabled;

    /**
     * MaintainerListener constructor.
     */
    public function __construct($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        if (!$this->enabled) {
            return;
        }

        $response = new Response('Estamos en mantenimiento');

        $event->setResponse($response);
    }
}
