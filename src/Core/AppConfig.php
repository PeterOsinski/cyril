<?php

namespace Core;

class AppConfig
{
    public static function setConfig(\Symfony\Component\DependencyInjection\Container $container)
    {
        $renderer = new \Symfony\Component\HttpKernel\Fragment\InlineFragmentRenderer($container->get('framework'));
        $handler = new \Symfony\Component\HttpKernel\Fragment\FragmentHandler(array($renderer));
        $handler->setRequest($container->get('request'));
        
        $container->getParameter('twig')->addExtension(new Twig\HttpKernelExtension($handler));
        
        $container->getParameter('twig')->addExtension(new Twig\RoutingExtension($container->get('url_generator')));
    }
}

?>
