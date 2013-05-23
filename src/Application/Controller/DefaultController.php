<?php

namespace Application\Controller;

use Symfony\Component\HttpFoundation\Request;
use Core\Controller\Controller;
use Core\Route\Route;
use Application\Entity\Product;

class DefaultController extends Controller
{

    /**
     * @Route("/index", name="index")
     */
    public function indexAction(Request $request)
    {
        $content = 'index!';

//        $products = $this->getDoctrine()->getRepository('\Application\Entity\Product')
//                        ->findBy(array('id'=>array(1,2)));
//        $products2 = $this->getDoctrine()->getRepository('\Application\Entity\Product')
//                        ->find(3);
        $products = $this->getDoctrine()->getRepository('\Application\Entity\Product')
                ->createQueryBuilder('a')
                ->where('a.id = 2')
                ->getQuery()
                ->useQueryCache(true, 100, '123')
                ->setResultCacheId('')
                ->getResult();
        
        
//        $products = $this->getDoctrine()->getRepository('\Application\Entity\Product')->findAll();
//        $this->setUser(new \stdClass());

        $event = new \Application\Event\IndexEvent('Event odpalany przez listener');
        $this->getEventDispatcher()->dispatch('indexEvent', $event);
        return compact('content', 'products');
    }
    
    /**
     * @Route("/add", name="add_product")
     */
    public function addProductAction(){
        $em = $this->getDoctrine();
        
        for($i = 0; $i<=1;$i++){
            $product = new Product();
            $product->setName('Produkt'. uniqid());

            $em->persist($product);
        }
        $em->flush();
        
        return $this->redirect('index');
    }
    
    /**
     * @Route("_proxy/index_component", name="index_component")
     */
    public function index2Action(Request $request)
    {
        $content = 'to jest renderowany komponent';
        return compact('content');
    }

}