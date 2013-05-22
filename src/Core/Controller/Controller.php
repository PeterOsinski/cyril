<?php

namespace Core\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Controller extends ContainerAware
{
    
    /**
     * @var \Core\FrameworkContainerBuilder
     */
    protected $container;

    public function generateUrl($route, $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        return $this->container->get('url_generator')->generate($route, $parameters, $referenceType);
    }

    public function redirect($url, $status = 302)
    {
        return new RedirectResponse($url, $status);
    }

    public function createNotFoundException($message = 'Not Found', \Exception $previous = null)
    {
        return new NotFoundHttpException($message, $previous);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        if (!$this->container->get('session')->isStarted())
            $this->container->get('session')->start();

        return $this->container->get('request');
    }

    /**
     * Fetches user object from session
     */
    public function getUser()
    {
        if ($this->getRequest()->hasSession()
                && $this->getRequest()->getSession()->has('user')) {
            return $this->getRequest()->getSession()->get('user');
        } else {
            return false;
        }
    }

    /**
     * Sets user object in session
     */
    public function setUser($user)
    {
        $this->getRequest()->getSession()->set('user', $user);
    }
    
    public function unsetUser(){
        $this->getRequest()->getSession()->remove('user');
    }
    
    /**
     * Checks if user is authenticated
     */
    public function isAuthenticated(){
        if ($this->getRequest()->hasSession()
                && $this->getRequest()->getSession()->has('is_authenticated')) {
            return $this->getRequest()->getSession()->get('is_authenticated');
        } else {
            return false;
        }
    }
    
    /**
     * Sets user authentication
     */
    public function setIsAuthenticated($bool){
        if(is_bool($bool))
            $this->getRequest()->getSession()->set('is_authenticated', $bool);
    }
    
    /**
     * Sets user roles
     */
    public function setUserRoles(array $roles){
        $this->getRequest()->getSession()->set('user_roles', $roles);
    }
    
    /**
     * Get authenticated user roles
     */
    public function getUserRoles(){
        $session = $this->getRequest()->getSession();
        
        if(!$session->has('user'))
            throw new \Exception('There is no user object in session, set it first then authenticate the user!');
        if(!$session->has('is_authenticated') || 
                ($session->has('is_authenticated') && !$session->get('is_authenticated')))
            throw new \Exception('User is not authenticated!');
        
        if ($this->getRequest()->hasSession()
                && $this->getRequest()->getSession()->has('user_roles')) {
            return $this->getRequest()->getSession()->get('user_roles');
        }else{
            return false;
        }
    }

    /**
     * @return \Symfony\Component\EventDispatcher\EventDispatcher
     */
    public function getEventDispatcher()
    {
        return $this->container->has('dispatcher') ? $this->container->get('dispatcher') : null;
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getDoctrine()
    {
        return $this->container->get('doctrine');
    }

}

?>
