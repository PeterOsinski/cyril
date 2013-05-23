<?php

namespace Core\Controller;

use Symfony\Component\HttpFoundation\Request;
use Core\Route\Route;
use Symfony\Component\HttpFoundation\Response;

class DebugController extends Controller
{

    /**
     * @Route("/_debug/phpinfo", name="debug_phpinfo")
     */
    public function debugAction()
    {
        phpinfo();
        // :) hihi
        exit;
    }
    
    /**
     * @Route("/_debug/cc/dev", name="debug_cc_dev", defaults={"env"="dev"})
     * @Route("/_debug/cc/prod", name="debug_cc_prod", defaults={"env"="prod"})
     */
    public function clearCache($env)
    {
        $this->getRequest()->getSession()->invalidate();
        $rootDir = $this->container->getParameter('root_dir');
        $cacheDir = $rootDir.'/Cache/'.$env;
        $this->rmDirRecursive($cacheDir);
        
        $referer = $this->getRequest()->headers->get('referer');
        
        return new \Symfony\Component\HttpFoundation\RedirectResponse($referer);
    }
    
    private function rmDirRecursive($dir){
        $files = glob($dir . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (substr($file, -1) == '/')
                $this->rmDirRecursive($file);
            else
                unlink($file);
        }
        if(file_exists($dir))
            rmdir($dir);
    }

}

?>
