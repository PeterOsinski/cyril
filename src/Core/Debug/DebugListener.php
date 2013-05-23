<?php

namespace Core\Debug;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

use Core\Debug\DebugToolbar;

class DebugListener implements EventSubscriberInterface
{

    protected $container;
    protected $appendix;
    
    protected $startTime;

    public function __construct(\Symfony\Component\DependencyInjection\Container $container)
    {
        $this->container = $container;
        $this->toolbar = new DebugToolbar();
    }

    public function onKernelResponse(FilterResponseEvent $filterResponseEvent)
    {
        if ($filterResponseEvent->getRequestType() == HttpKernelInterface::MASTER_REQUEST) {
            $this->toolbar->addWidget($this->getTotalTime(), 100);
            $this->toolbar->addWidget($this->getMiniSQLLogger($filterResponseEvent), 100, 'cyril-full-sql');
            $this->toolbar->addWidget($this->getUserParams(), null, 'cyril-session');
            $this->toolbar->addWidget($this->getCache());
            $this->toolbar->addWidget($this->getMemoryPeakUsage(), 80);
            $this->toolbar->addWidget($this->getPhpVersion());
            $this->toolbar->addWidget($this->getController());
            $this->toolbar->addWidget($this->getClearCache());
            
            $this->toolbar->addWindow($this->getFullSQLLogger(), 'cyril-full-sql');
            $this->toolbar->addWindow($this->getSession(), 'cyril-session');
            
            $this->appendToolbar($filterResponseEvent);
            $this->getSession();
        }
    }
    
    public function onKernelRequest(GetResponseEvent $getResponseEvent){
        if ($getResponseEvent->getRequestType() == HttpKernelInterface::MASTER_REQUEST) {
            $this->startTime = microtime(true);
        }
    }
    
    private function getSession(){
        $sessionParams = $this->container->get('session')->all();
        $output = '<ul class="cyril-session-list">';
        
        foreach($sessionParams as $name => $value){
            $val = is_object($value) ? get_class($value).' '.json_encode($value) : json_encode($value);
            
            $output .= '
                <li>
                    <span class="cyril-session-name">'.$name.': </span>
                    <span class="cyril-session-value">'.$val.'</span>
                </li>
                    ';
        }
        
        $output .= '</ul>';
        return $output;        
    }
    
    private function getClearCache(){
        $generator = $this->container->get('url_generator');
        
        $link = '<a href="'.$generator->generate('debug_cc_dev').'">Cache dev</a> ';
        $link .= '<a href="'.$generator->generate('debug_cc_prod').'">Cache prod</a>';
        
        return $link;
    }
    
    private function getController(){
        $request = $this->container->get('request');
        return preg_replace('/(Application)(\\\\)(Controller)(\\\\)/is', '', $request->get('_controller'));
    }
    
    private function getPhpVersion(){
        return '<a href="'.$this->container->get('url_generator')->generate('debug_phpinfo').'">PHP '.phpversion().'</a>';
    }
    
    private function getMemoryPeakUsage(){
        return 'Mem: '.(memory_get_peak_usage(true)/1024/1024).' mb / '.ini_get('memory_limit');
    }
    
    private function getTotalTime(){
        return 'T: '.(round(microtime(true) - $this->startTime, 5)*1000).' ms';
    }
    
    private function getCache(){
        if($this->container->has('cache')){
            $cacheClass = explode('\\', get_class($this->container->get('cache')));
            return '<a href="/apc.php">'.array_pop($cacheClass).'</a>';
        }else
            return false;
    }
    
    private function getUserParams()
    {
        $session = $this->container->get('request')->getSession();
        $user = $session->get('user') !== null ? 'green' : 'red';
        $isAuthenticated = $session->get('is_authenticated') ? 'green' : 'red';
        return '<span class="cyril-session cyril-'.$user.'">Usr</span> <span class="cyril-session cyril-'.$isAuthenticated.'">Auth</span>';
    }
    
    private function getFullSQLLogger(){
        $doctrine = $this->container->get('doctrine');
        $logger = $doctrine->getConfiguration()->getSQLLogger();
        $log = $logger->getFullLog();
        $output = '<ul class="cyril-sql-log">';
        
        foreach($log as $k=> $query){
            
            $params = array();
            if(is_array($query['params']))
                foreach($query['params'] as $param)
                        $params[] = is_array($param) ?'['.implode(',', $param).']':$param;
            
            $output .= '
                <li>
                    <p class="cyril-query">'.(1+$k).'. '.$query['sql'].'<p>
                    <p class="cyril-params">Paramters: {'.implode(',',$params).'}</p>
                    <p class="cyril-time">Time: '.$query['time'].'</p>
                </li>
                ';
        }
        $output .= '</ul>';
        
        return $output;
    }

    private function getMiniSQLLogger()
    {
        $doctrine = $this->container->get('doctrine');
        $logger = $doctrine->getConfiguration()->getSQLLogger();
        $sql = $logger->getMiniLog();
        return 'DB: (' . $sql['count'] . ') ' . $sql['totalTime'];
    }

    private function appendToolbar(FilterResponseEvent $filterResponseEvent)
    {
        $response = $filterResponseEvent->getResponse();
        
        /*
         * think about it for a second, maybe its an ajax request?
         */
        if($response->headers->get('Content-Type') != 'application/json'){
            $content = $response->getContent();

            $content .= $this->toolbar->renderToolbar();

            $response->setContent($content);
            $filterResponseEvent->setResponse($response);
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            'kernel.response' => 'onKernelResponse',
            'kernel.request' => 'onKernelRequest'
        );
    }

}

?>
