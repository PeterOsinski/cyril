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
 
        $this->queries[$lastKey]['time'] = (round($sqlDuration, 5) * 1000).' ms';
        
        $this->count++;
        $this->totalTime += $sqlDuration;
        $this->totalTime = round($this->totalTime, 5);
    }
    
    public function getMiniLog(){
        return array(
            'totalTime'=>($this->totalTime * 1000).' ms',
            'count'=>$this->count
        );
    }
    
    public function getFullLog(){
        return $this->queries;
    }
    
}
 