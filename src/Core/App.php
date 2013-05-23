<?php

namespace Core;


use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpFoundation\Session\Session;

final class App {
    
    private $debug;
    private $container;
    private $cacheDir;
    
    public function getRootDir(){
        return __DIR__.'/..';
    }
    
    public function __construct($debug = false){
        $this->debug = $debug;
        
        //load default components
        $builder = new Container($this->debug);
        $this->container = $builder->getContainer();
        
        $this->setDirs();
        $this->registerRouting();
        
        $request = Request::createFromGlobals();
        $this->container->set('request', $request);
        
        $this->registerSession();
        $this->registerTwig();
        $this->loadPrimaryConfig();
        $this->loadAppConfig();
        $this->registerDoctrine();
        
        $response = $this->container->get('framework')->handle($request);
        
        $response->send();
        $this->container->get('framework')->terminate($this->container->get('request'), $response);
    }
    
    private function registerSession()
    {
        $fileHandler = 'Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler';
        $sessionStorage = 'Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage';
        
        $this->container->register('session.file_handler', $fileHandler)
                ->setArguments(array('%cache_dir%' . '/Session'));
        $this->container->register('session.storage', $sessionStorage)
                ->setArguments(array(array(), new Reference('session.file_handler')));
        $this->container->set('session', new Session($this->container->get('session.storage')));
        //attach session to current request
        $this->container->get('request')->setSession($this->container->get('session'));
    }
    
    private function loadPrimaryConfig(){
        //register core config, basic listeners and twig extensions
        AppConfig::setConfig($this->container);
    }
    
    private function loadAppConfig(){
        //register user app config
        $userConfig = new \Application\Config\AppConfig(new Configurator($this->container));
        $userConfig->load();
    }
    
    private function registerRouting(){
        $registeredControllers = \Application\Config\AppConfig::registerController();
        
        //register special controller that has routes for debug panel;
        if($this->debug)
            $registeredControllers[] = 'Core\Controller\DebugController';
        
        $routeAnnotations = new Route\RouteAnnotationParser($registeredControllers);
        $routeAnnotations->parseControllers();
        
        $routes = new \Application\Config\Routes();
        $routeCollection = $routes->getRoutes();
        $routeCollection->addCollection($routeAnnotations->getCollection());
        $this->container->set('routes', $routeCollection);
    }
    
    private function registerTwig(){
        \Twig_Autoloader::register();

        $loader = new \Twig_Loader_Filesystem($this->getRootDir().'/Application/View');
        $twig = new \Twig_Environment($loader, array(
                    'cache' => $this->cacheDir.'/Twig',
                    'debug' => $this->debug
                ));
        
        $this->container->set('twig', $twig);
    }
    
    private function registerDoctrine() {
        $definition = new Definition('Doctrine\ORM\EntityManager');
        $entityDir = $this->getRootDir() . '/Application/Entity';
        $dbParams = $this->container->getParameter('db_params');
        
        $factory = new Doctrine\DoctrineFactory($this->debug, $this->cacheDir, $entityDir, $dbParams);
        $definition->setFactoryClass($factory)
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