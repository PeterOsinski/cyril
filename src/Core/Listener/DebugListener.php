<?php

namespace Core\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class DebugListener implements EventSubscriberInterface {

    protected $container;

    public function __construct(\Symfony\Component\DependencyInjection\Container $container) {
        $this->container = $container;
    }

    public function onKernelResponse(FilterResponseEvent $filterResponseEvent) {
        if ($filterResponseEvent->getRequestType() == HttpKernelInterface::MASTER_REQUEST) {
            $appendix = '';
            $appendix .= $this->getSQLLogger($filterResponseEvent);
            $appendix .= $this->getSession();
            
            $this->appendToolbar($filterResponseEvent, $appendix);
        }
    }

    private function getSession() {
        if ($this->container->get('request')->hasSession()) {
            return $this->container->get('request')->getSession()->all();
        }
    }
    
    private function getIsAuthenticated(){
        if ($this->container->get('request')->hasSession()) {
            $session = $this->container->get('request')->getSession();
            $user = $session->has('user') ? $session->get('user') : null;

        }
    }

    private function getSQLLogger() {
        $doctrine = $this->container->get('doctrine');
        $logger = $doctrine->getConfiguration()->getSQLLogger();
        return $logger->getWebLog();
    }

    private function appendToolbar(FilterResponseEvent $filterResponseEvent, $appendix) {
        $response = $filterResponseEvent->getResponse();
        $content = $response->getContent();

        $content .= $appendix;

        $response->setContent($content);
        $filterResponseEvent->setResponse($response);
    }

    public static function getSubscribedEvents() {
        return array(
            'kernel.response' => 'onKernelResponse'
        );
    }

}

?>
