<?php

namespace Application\Listener;

class IndexListener
{
    
    public function onIndex(\Application\Event\IndexEvent $event){
        var_dump("Listener zareagował: ".$event->getString());
    }
}

?>
