<?php
namespace Application\Entity;

/**
 * @Entity(repositoryClass="Application\Entity\ProductRepository") 
 * @Table(name="products")
 **/
class Product
{

    /**
     * @Id 
     * @Column(type="integer")
     * @GeneratedValue 
     **/
    protected $id;

    /**
     * @Column(type="string") 
     **/
    protected $name;

    public function getId()
    {
        return $this->id;
    }
    
    public function getName(){
        return $this->name;
    }
    
    public function setName($name){
        $this->name = $name;
        
        return $this;
    }

}