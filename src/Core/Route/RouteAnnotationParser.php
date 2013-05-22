<?php

namespace Core\Route;

use Doctrine\Common\Annotations\AnnotationRegistry;

class RouteAnnotationParser {

    private $controllers;
    private $routes;
    private $collection;

    public function __construct(array $controllers) {
        AnnotationRegistry::registerFile(__DIR__ . '/RouteAnnotation.php');
        $this->controllers = $controllers;
    }

    public function parseControllers() {
        $this->collection = new \Symfony\Component\Routing\RouteCollection();
        foreach ($this->controllers as $controllerNamespace) {
//            $controllerNamespace = 'Application\Controller\\' . $controller;
            $reader = new \Doctrine\Common\Annotations\AnnotationReader($controllerNamespace);
            $reflection = new \ReflectionClass($controllerNamespace);

            foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                $reflection = new \ReflectionMethod($controllerNamespace, $method->name);
                $annotation = $reader->getMethodAnnotations($reflection);
                if (!empty($annotation)) {
                    foreach ($annotation as $route) {
                        $defaults = $route->getDefaults();
                        $defaults['_controller'] = $controllerNamespace . '::' . $method->name;
                        
                        $routeObj = $route->getRouteObj();
                        $routeObj->setDefaults($defaults);
                        
                        $this->collection->add($route->getName(), $routeObj);
                    }
                }
            }
        }
    }
    
    /**
     * @return \Symfony\Component\Routing\RouteCollection
     */
    public function getCollection(){
        return $this->collection;
    }
    
    public function serializeRoutes() {
        return serialize($this->routes);
    }

}
?>

