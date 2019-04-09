<?php

class ArchivedLog extends iRAP\MysqlObjects\AbstractUuidTableRowObject
{
    protected $m_message;
    protected $m_context;
    protected $m_priority;
    protected $m_when;
    
    
    public function __construct($row, $row_field_types=null)
    {
        $this->initializeFromArray($row, $row_field_types);
    }
    
    
    /**
     * Create an archived log object from a log object.
     * WARNING - this does not automatically save to the database.
     *
     * @param  Log $log
     * @return \ArchivedLog
     */
    public static function createFromLog(Log $log)
    {
        $archivedLog = new ArchivedLog(
            array(
                'message' => $log->getMessage(),
                'context' => $log->getContext(),
                'priority' => $log->getPriority(),
                'when' => $log->getWhen()
            )
        );
        
        return $archivedLog;
    }
    
    
    
    protected function getAccessorFunctions(): array
    {
        return array(
            'message'  => function () { return $this->m_message; },
            'context'  => function () { return $this->m_context; },
            'priority' => function () { return $this->m_priority; },
            'when'     => function () { return $this->m_when; }
        );
    }
    
    
    protected function getSetFunctions(): array
    {
        return array(
            'message'  => function ($x) { $this->m_message = $x; },
            'context'  => function ($x) { $this->m_context = $x; },
            'priority' => function ($x) { $this->m_priority = $x; },
            'when'     => function ($x) { $this->m_when = $x; }
        );
    } 
    
    
    public function getTableHandler(): \iRAP\MysqlObjects\TableInterface 
    {
        return ArchivedLogTable::getInstance();
    }
    
    
    // Accessors
    public function getMessage() : string  { return $this->m_message; }
    public function getContext() { return $this->m_context; }
    public function getPriority() : int { return $this->m_priority; }
    public function getWhen() : int { return $this->m_when; }
}