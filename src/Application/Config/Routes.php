<?php
namespace Application\Config;

class Routes
{

    private $routes;

    public function __construct()
    {
        $this->routes = new \Symfony\Component\Routing\RouteCollection();
        
//        $this->routes->add($name, new \Symfony\Component\Routing\Route(
//                        $path,
//                        array('_controller' => $this->baseControllerPath.$controller))
//        );
        
//        $this->routes = $routes;
    }
    
    /**
     * @return \Symfony\Component\Routing\RouteCollection
     */
    public function getRoutes(){
        return $this->routes;
    }

}