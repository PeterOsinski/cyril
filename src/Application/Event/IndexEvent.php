<?php

namespace Application\Event;

use Symfony\Component\EventDispatcher\Event;

class IndexEvent extends Event
{
    
    protected $string;
    
    public function __construct($string){
        $this->string = $string;
    }
    
    public function getString(){
        return $this->string;
    }
}

?>
