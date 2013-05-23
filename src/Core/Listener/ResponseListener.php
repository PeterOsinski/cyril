<?php

namespace Core\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpFoundation\Response;

class ResponseListener implements EventSubscriberInterface {

    private $twig;
    private $matcher;

    public function __construct(\Twig_Environment $twig, \Symfony\Component\Routing\Matcher\UrlMatcher $matcher) {
        $this->twig = $twig;
        $this->matcher = $matcher;
    }

    public function onView(GetResponseForControllerResultEvent $event) {
        $response = $event->getControllerResult();
        $matchedRoute = $this->matcher->match($this->matcher->getContext()->getPathInfo());
        
        if ($response instanceof Response)
            $event->setResponse($response);

        if (is_array($response) && $matchedRoute) {
            $templatePath = $this->extractPathForTemplate($matchedRoute);
            $content = $this->twig->render($templatePath, $response);
            $response = new Response($content);
            $response->headers->set('Content-Type', 'text/html');
            $event->setResponse($response);
        }
    }
    
    private function extractPathForTemplate($route){
        $sub = preg_replace('/(Application)(\\\\)(Controller)(\\\\)/is', '', $route['_controller']);
        $sub = preg_replace('/(Controller)/is', '', $sub);
        $sub = substr($sub, 0 ,-6);
        return preg_replace('/(::)/is','/', $sub).'.html.twig';
    }
    
    static function getSubscribedEvents() {
        return array(
            'kernel.view' => 'onView'
            );
    }

}