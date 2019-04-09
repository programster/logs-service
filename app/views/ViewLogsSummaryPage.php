<?php

/* 
 * 
 */

class ViewLogsSummaryPage extends AbstractView
{
    private $m_title;
    private $m_filter;
    private $m_body;
    
    public function __construct(string $title, LogFilter $filter)
    {
        new ViewSearchLogs($logFilter) .
        $paginationView .
        new ViewLogsTable($logs) .
        $paginationView;
        
        $body = print 
            new ViewSearchLogs($logFilter) .
            $paginationView .
            new ViewLogsTable($logs) .
            $paginationView;
    }
    
    
    protected function renderContent() 
    {
        print new ViewTemplate($this->m_title, $this->m_body);
    }
}
