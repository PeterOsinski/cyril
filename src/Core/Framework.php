<?php 

namespace Core;

use Symfony\Component\HttpKernel;

class Framework extends HttpKernel\HttpKernel {
    
    public function getRootDir()
    {
        return __DIR__;
    }
    
}