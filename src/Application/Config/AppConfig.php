<?php

namespace Application\Config;

use Core\Configurator;
use Application\Twig\MyExtension;
use Application\Listener\IndexListener;

class AppConfig{
    
    public function __construct(Configurator $configurator)
    {
        $this->configurator = $configurator;
        $this->container = $configurator->getContainer();
    }
    
    public static function registerController(){
        return array(
          'DefaultController'  
        );
    }
    
    public function load(){
        $this->configurator->addTwigExtension(new MyExtension($this->container));
        $this->configurator->addEventListener('indexEvent', array(new IndexListener(), 'onIndex'));
    }
    
}