<?php

namespace Core\Doctrine;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Cache\ApcCache;

class DoctrineFactory {

    protected $debug = false;

    public function __construct($debug) {
        $this->debug = $debug;
    }

    public function get() {
        $config = Setup::createAnnotationMetadataConfiguration(array(\Core\App::getRootDir().'/Application/Entity'), $this->debug);

        $proxyDir = \Core\App::getRootDir().'/Cache/doctrine';
        $config->setProxyDir($proxyDir);
        $config->setProxyNamespace('dc2_proxy');

        if (!$this->debug) {
            $config->setAutoGenerateProxyClasses(false);
            
            $queryCache = new ApcCache();
            $queryCache->setNamespace('db_query::' . md5($proxyDir) . '::');
            $config->setQueryCacheImpl($queryCache);

            $metadataCache = new ApcCache();
            $metadataCache->setNamespace('metadata::' . md5($proxyDir) . '::');
            $config->setMetadataCacheImpl($metadataCache);

            $resultCache = new ApcCache();
            $resultCache->setNamespace('results::' . md5($proxyDir) . '::');
            $config->setResultCacheImpl($resultCache);
        }else{
            $config->setSQLLogger(new Logger());
            $config->setAutoGenerateProxyClasses(true);
        }
        
        $conn = array(
            'driver' => 'pdo_pgsql',
            'user' => 'postgres',
            'password' => 'postgres',
//            'user'     => '2012',
//            'password' => 'galileo',
            'dbname' => 'foo',
        );
        $em = EntityManager::create($conn, $config);

        return $em;
    }

}

?>
