<?php

class ViewSingleLog extends AbstractView
{
    private $m_log;
    
    
    public function __construct(Log $log) 
    {
        $this->m_log = $log;
    }
    
    
    protected function renderContent() 
    {
        /* @var $this->m_log Log */
    
?>


<style type="text/css">
    dd {
        position: relative;
    }
    .log-container-resize {
        display: none;
        position: absolute;
        right: 0;
        top: 0;
        visibility: hidden;
    }
    dd:hover .log-container-resize {
        display: block;
    }
</style>

<h2>Log: <?=  $this->m_log->get_id(); ?></h2>

<h3>Message</h3>
<p><?php echo $this->m_log->getMessage() ?></p>

<h3>Priority: <?= $this->m_log->getPriority(); ?></h3>

<h3>When: <?= $this->m_log->getHumanReadableTimestamp(); ?></h3>

<h3>Context</h3>
<pre><?php echo htmlentities(print_r($this->m_log->getContext(), true)); ?></pre>


<?php
    }
}