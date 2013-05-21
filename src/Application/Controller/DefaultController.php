<?php

namespace Application\Controller;

use Symfony\Component\HttpFoundation\Request;
use Core\Controller\Controller;

use Application\Entity\Product;

class DefaultController extends Controller
{

    public function indexAction(Request $request)
    {
        $content = 'index!';
        
        
        $products = $this->getDoctrine()->getRepository('\Application\Entity\Product')->findAll();
        $this->getRequest()->getSession()->set('sa', 'pa');
        $event = new \Application\Event\IndexEvent('Event odpalany przez listener');
        $this->getEventDispatcher()->dispatch('indexEvent', $event);

        return compact('content');
    }
    
    public function addProductAction(){
        
        $product = new Product();
        $product->setName('Produkt'. uniqid());
        
        $em = $this->getDoctrine();
        $em->persist($product);
        $em->flush();
        
        return $this->redirect('index');
    }
    
    public function index2Action(Request $request)
    {
        $content = 'to jest renderowany komponent';
        return compact('content');
    }

}