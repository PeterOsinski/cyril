<?php

namespace Core;

class Configurator
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    protected $container;
    protected $dbParams;
    
    public function __construct($container){
        $this->container = $container;
    }
    
    public function addTwigExtension(\Twig_ExtensionInterface $extension){
        if($this->container->has('twig'))
            $this->container->get('twig')->addExtension($extension);
    }
    
    public function addEventListener($eventName, $listener){
        if($this->container->has('dispatcher'))
            $this->container->get('dispatcher')->addListener($eventName, $listener);
    }
    
    public function setDBParameters(array $params){
        $this->container->setParameter('db_params', $params);
    }
    
    /**
     * @return \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    public function getContainer(){
        return $this->container;
    }
    
}

?>
