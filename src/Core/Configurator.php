<?php

namespace Core;

class Configurator
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    protected $container;
    
    public function __construct($container){
        $this->container = $container;
    }
    
    public function addTwigExtension(\Twig_ExtensionInterface $extension){
        if($this->container->hasParameter('twig'))
            $this->container->getParameter('twig')->addExtension($extension);
    }
    
    public function addEventListener($eventName, $listener){
        if($this->container->has('dispatcher'))
            $this->container->get('dispatcher')->addListener($eventName, $listener);
    }
    
    /**
     * @return \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    public function getContainer(){
        return $this->container;
    }
    
}

?>
