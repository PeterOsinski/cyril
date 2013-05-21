<?php

namespace Core;

class Router {

    protected $routes;
    protected $baseControllerPath = 'Application\\Controller\\';

    public function __construct() {
        $this->routes = new \Symfony\Component\Routing\RouteCollection();
    }
    
    /**
     * 
     * @param type $name
     * @param type $path
     * @param type $controller if ommited $name will be used as controller name ex: 
     *              $name = 'default::index'
     *              $controller will be set as 'DefaultController::indexAction'
     */
    public function add($path, $name, $controller = null){
        
        if(is_null($controller)){
            $controller = explode('::', $name);
            $controller = ucfirst($controller[0]).'Controller::'.$controller[1].'Action';
        }
        
        $this->routes->add($name, new \Symfony\Component\Routing\Route(
                        $path,
                        array('_controller' => $this->baseControllerPath.$controller))
        );
    }
    
    public function getRoutes(){
        return $this->routes;
    }

}

?>
