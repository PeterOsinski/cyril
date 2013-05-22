<?php
namespace Core;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
class Container
{
    
    private $container;

    public function __construct($debug)
    {
        $container = new ContainerBuilder();
        
        $container->setParameter('debug', $debug);
        
        $container->register('context', 'Symfony\Component\Routing\RequestContext');

        $container->register('matcher', 'Symfony\Component\Routing\Matcher\UrlMatcher')
                ->setArguments(array(new Reference('routes'), new Reference('context')));

        $container->register('resolver', 'Core\Controller\ControllerResolver')
                ->addArgument($container);

        $container->register('url_generator', 'Symfony\Component\Routing\Generator\UrlGenerator')
                ->setArguments(array(new Reference('routes'), new Reference('context')));
        
        $container->register('listener.response', 'Symfony\Component\HttpKernel\EventListener\ResponseListener')
                ->setArguments(array('UTF-8'));
        
        $container->register('listener.router', 'Symfony\Component\HttpKernel\EventListener\RouterListener')
                ->setArguments(array(new Reference('matcher')));

        $container->register('dispatcher', 'Symfony\Component\EventDispatcher\EventDispatcher')
                ->addMethodCall('addSubscriber', array(new Reference('listener.router')));

        $container->register('framework', 'Core\Framework')
                ->setArguments(array(new Reference('dispatcher'), new Reference('resolver')));

        $container->register('listener.response', 'Core\Listener\ResponseListener')
                ->setArguments(array(new Reference('twig'), new Reference('matcher')));
        
        if($container->getParameter('debug')){
            $container->register('listener.debug', 'Core\Listener\DebugListener')
                ->addArgument($container);
            $container->getDefinition('dispatcher')
                ->addMethodCall('addSubscriber', array(new Reference('listener.debug')));
        }
        
        $container->getDefinition('dispatcher')
                ->addMethodCall('addSubscriber', array(new Reference('listener.response')));
        
        $this->container = $container;
    }
    
    public function getContainer(){
        return $this->container;
    }

}
