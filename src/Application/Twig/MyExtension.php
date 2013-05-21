<?php

namespace Application\Twig;

class MyExtension extends \Twig_Extension
{
    protected $container;
    
    public function __construct(\Symfony\Component\DependencyInjection\Container $container){
        $this->container = $container;
    }
    
    public function getFunctions()
    {
        return array(
            'hello' => new \Twig_Function_Method($this, 'hello'),
            new \Twig_SimpleFunction('closure', function(){
                return 'funkcja anonimowa Twiga';
            })
        );
    }
    
    public function hello(){
        return 'to jest metoda Twiga';
    }
    
    public function getGlobals()
    {
        return array(
            'text' => 'to jest zmienna globalna',
        );
    }
    
    public function getName()
    {
        return 'myExtension';
    }
}
