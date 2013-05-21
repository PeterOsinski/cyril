<?php

namespace Core\Route;
use Doctrine\Common\Annotations\AnnotationRegistry;
class RouteAnnotationParser
{
    
    private $controllers;
    private $routes;
    
    public function __construct(array $controllers)
    {
        AnnotationRegistry::registerFile(__DIR__.'/RouteAnnotation.php');
        $this->controllers = $controllers;
    }
    
    public function parseControllers(){
        
        foreach($this->controllers as $controller){
            $reader = new \Doctrine\Common\Annotations\AnnotationReader('\Application\Controller\\'.$controller);
            $reflection = new \ReflectionClass('\Application\Controller\\'.$controller);
            
            foreach($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method){
                $reflection = new \ReflectionMethod('\Application\Controller\\'.$controller, $method->name);
                $annotation = $reader->getMethodAnnotations($reflection);
                if(!empty($annotation))
                    $this->routes[] = $annotation;
            }
        }
    }
    
    public function serializeRoutes(){
        return serialize($this->routes);
    }
    
}

?>

