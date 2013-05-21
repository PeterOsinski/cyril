<?php
 
namespace Core\Doctrine;
 
use Doctrine\DBAL\Logging\SQLLogger;
 
class Logger implements SQLLogger
{
 
    protected $queries = array();
    public $count = 0;
    public $totalTime = 0;
 
    public function startQuery($sql, array $params = null, array $types = null)
    {
        $this->queries[] = array(
            'sql' => $sql,
            'params' => $params,
            'time' => microtime(true)
        );
    }
 
    public function stopQuery()
    {
        $lastKey = key(array_slice($this->queries, -1, 1, TRUE));
 
        $sqlDuration = microtime(true) - $this->queries[$lastKey]['time'];
 
        $this->queries[$lastKey]['time'] = $sqlDuration;
        
        $this->count++;
        $this->totalTime += $sqlDuration;
        $this->totalTime = round($this->totalTime, 5).' s';
    }
    
    public function getWebLog(){
        return '<div style="position:absolute; bottom: 10px">('.$this->count.') '.$this->totalTime.'</div>';
    }
    
}
 