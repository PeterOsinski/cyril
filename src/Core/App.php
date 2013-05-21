<?php

namespace Core;


use Symfony\Component\DependencyInjection\Definition;

final class App {
    
    private $debug;
    private $container;
    private $cacheDir;
    
    public function getRootDir(){
        return __DIR__.'/..';
    }
    
    public function __construct($debug = false){
        $this->debug = $debug;
        
        $builder = new Container($this->debug);
        $this->container = $builder->getContainer();
        
        $this->setDirs();
        
        $this->registerRouting();
        
        $request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
        $this->container->register('request', $request);
        
        $this->registerTwig();
        $this->loadPrimaryConfig();
        $this->loadAppConfig();
        $this->registerDoctrine();
        
        $response = $this->container->get('framework')->handle($request);
        
        $response->send();
        $this->container->get('framework')->terminate($this->container->get('request'), $response);
    }
    
    private function loadPrimaryConfig(){
        //register core config, session, basic listeners and twig extensions
        AppConfig::setConfig($this->container);
    }
    
    private function loadAppConfig(){
        //register user app config
        if(class_exists('\Application\Config\AppConfig')){
            $userConfig = new \Application\Config\AppConfig(new Configurator($this->container));
            $userConfig->load();
        }
    }
    
    private function registerRouting(){
        $routeAnnotations = new Route\RouteAnnotationParser(\Application\Config\AppConfig::registerController());
        $routeAnnotations->parseControllers();
        
        $routes = new \Application\Config\Routes();
        $routeCollection = $routes->getRoutes();
        $routeCollection->addCollection($routeAnnotations->getCollection());
        $this->container->setParameter('routes', $routeCollection);
    }
    
    private function registerTwig(){
        \Twig_Autoloader::register();

        $loader = new \Twig_Loader_Filesystem($this->getRootDir().'/Application/View');
        $twig = new \Twig_Environment($loader, array(
                    'cache' => $this->cacheDir.'/Twig',
                    'debug' => $this->debug
                ));
        
        $this->container->setParameter('twig', $twig);
    }
    
    private function registerDoctrine(){
        $definition = new Definition('Doctrine\ORM\EntityManager');
       $definition->setFactoryClass(new Doctrine\DoctrineFactory($this->debug, $this->cacheDir, $this->getRootDir().'/Application/Entity'))
               ->setFactoryMethod('get');
       
       $this->container->setDefinition('doctrine', $definition);
    }
    
    private function setDirs(){
        $this->container->setParameter('root_dir', $this->getRootDir());

        $env = $this->debug ? 'dev' : 'prod';
        $this->cacheDir = $this->container->getParameter('root_dir').'/Cache/'.$env;
        $this->container->setParameter('cache_dir', $this->cacheDir);
    }
        
}