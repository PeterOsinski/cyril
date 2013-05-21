<?php
namespace Application\Config;

class Routes
{

    private $routes;

    public function __construct()
    {
        $routes = new \Core\Router();
        
        $routes->add('/index', 'default::index');
        $routes->add('/add-product', 'default::addProduct');
        
        $routes->add('/_proxy/index_component', 'index_component', 'DefaultController::index2Action');
        
        $this->routes = $routes;
    }
    
    public function getRoutes(){
        return $this->routes;
    }

}