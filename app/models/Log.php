<?php

class Log extends iRAP\MysqlObjects\AbstractTableRowObject
{
    private $m_message;
    private $m_context;
    private $m_priority;
    private $m_when;
    private $m_recieved;
    
    
    public function __construct($row, $row_field_types=null)
    {
        $this->initializeFromArray($row, $row_field_types);
    }
    
    
    /**
     * Create a new Log in the database.
     * @param string $message
     * @param string $context
     * @param int $priority
     * @param int $when
     * @return \Log
     */
    public static function createNew(string $message, string $context, int $priority, int $when) : Log
    {
        $log = new Log([
            'message' => $message,
            'context' => $context,
            'priority' => $priority,
            'when' => $when,
            'recieved' => time(),
        ]);
        
        $log->save();
        return $log;
    }
    
    
    protected function getAccessorFunctions(): array
    {
        return array(
            'message'  => function() { return $this->m_message; },
            'context'  => function() { return json_encode($this->m_context); },
            'priority' => function() { return $this->m_priority; },
            'when'     => function() { return $this->m_when; },
            'recieved' => function() { return $this->m_recieved; },
        );
    }
    
    
    protected function getSetFunctions(): array
    {
        $contextFunction = function($x) { 
            $context = json_decode($x, true);
            
            if ($context === null)
            {
                $context = [$x];
            }
            
            $this->m_context = $context;
        };
        
        return array(
            'message'  => function($x) { $this->m_message = $x; },
            'context'  => $contextFunction,
            'priority' => function($x) { $this->m_priority = $x; },
            'when'     => function($x) { $this->m_when = $x; },
            'recieved' => function($x) { $this->m_recieved = $x; },
        );
    } 
    
    
    public function getTableHandler(): \iRAP\MysqlObjects\TableInterface 
    {
        return LogTable::getInstance();
    }
    
    
    // Accessors
    public function getMessage() : string { return $this->m_message; }
    public function getContext() : array { return $this->m_context; }
    public function getPriority() : int { return $this->m_priority; }
    public function getWhen() : int { return $this->m_when; }
    public function getHumanReadableTimestamp() : string { return date("H:i:s d-m-Y", $this->m_when); }

}