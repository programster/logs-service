<?php

/* 
 * Object to represent a filter for loading logs.
 */

class LogFilter
{
    private $m_alertLevels;
    private $m_minAge;
    private $m_maxAge;
    private $m_minimumId;
    private $m_maximumId;
    private $m_search_text;
    
    
    public function __construct() 
    {
        $this->m_alertLevels = array();
        $this->m_minAge = null;
        $this->m_maxAge = null;
        $this->m_minimumId = null;
        $this->m_maximumId = null;
        $this->m_search_text = null;
    }
    
    
    /**
     * 
     * @return string
     */
    public function generateWhereStatement() : string
    {       
        $whereStatement = "";
        $statements = array();
        
        if ($this->m_maximumId !== null && $this->m_maximumId !== 0) 
        {
            $statements[] = "`id` < '" . ($this->m_maximumId + 1) . "'";
        }
        
        if ($this->m_minimumId !== null && $this->m_minimumId !== 0) 
        {
            $statements[] = "`id` > '" . ($this->m_minimumId - 1) . "'";
        }
        
        if ($this->m_search_text !== null && $this->m_search_text !== "") 
        {
            $escapedMessage = SiteSpecific::getDb()->escape_string($this->m_search_text);
            $statements[] = "`message` LIKE '%{$escapedMessage}%'";
        }
        
        if (count($this->m_alertLevels) > 0) 
        {
            $quoted_alert_levels = iRAP\CoreLibs\ArrayLib::wrapElements($this->getAlertLevels(), "'");
            $statements[] = "`priority` IN (" . implode(', ', $quoted_alert_levels) . ")";
        }
        
        // We use 'date("Y-m-d H:i:s", time())' instead of just CURRENT_TIMESTAMP so that the deployer
        // doesn't have to remember to set the timezone on the host to UTC wherever the db is.
        if ($this->m_minAge != null) 
        {          
            $statements[] = "TIMESTAMPDIFF(SECOND, `when`, '" . date("Y-m-d H:i:s", time()) . "') > " . $this->m_minAge;
        }
        
        if ($this->m_maxAge != null) 
        {                        
            $statements[] = "TIMESTAMPDIFF(SECOND, `when`, '" . date("Y-m-d H:i:s", time()) . "') < " . $this->m_maxAge;
        }
        
        if (count($statements) > 0) 
        {
            $whereStatement = "WHERE " . implode($statements, " AND ");
        }
        
        return $whereStatement;
    }
    
    
    /**
     * Set the log id to start the scan from.
     *
     * @param int $id
     */
    public function setMinId($id)
    {
        $this->m_minimumId = $id;
    }
    
    
    /**
     * Set the maximum possible log id to retrieve.
     *
     * @param int $id
     */
    public function setMaxId($id)
    {
        $this->m_maximumId = $id;
    }
    
    
    /**
     * Specify an alert level that we wish to fetch from the logs
     *
     * @param int $level
     */
    public function enable_alert_level($level)
    {
        $this->m_alertLevels[$level] = 1;
    }
    
    
    public function setSearchText($text)
    {
        $this->m_search_text = $text;
    }
    
    
    /**
     * Specify an alert level that we wish to ignore from the logs
     *
     * @param int $level
     */
    public function disable_alert_level($level)
    {
        if (isset($this->m_alertLevels[$level])) 
        {
            unset($this->m_alertLevels[$level]);
        }
    }
    
    
    /**
     * Save the filter to the session for the user.
     * This will override anything that is currently in place.
     */
    public function save()
    {
        $_SESSION['log_filter'] = $this; 
    }
    
    
    /**
     * Load the current filter from the session. If one doesnt exist, then create one.
     * @return LogFilter
     */
    public static function load() : LogFilter
    {
        if (isset($_SESSION['log_filter'])) 
        {
            $logFilter = $_SESSION['log_filter']; 
        }
        else
        {
            $logFilter = new LogFilter();
        }
        
        return $logFilter;
    }
    
    
    public function set_max_age($seconds)
    {
        $this->m_maxAge = $seconds; 
    }
    
    
    public function set_min_age($seconds){ $this->m_minAge = $seconds; }
    
        
    // Accessors
    public function get_max_age() : int { return $this->m_maxAge; }
    public function getMinAge() : int { return $this->m_minAge; }
    public function getAlertLevels() { return array_keys($this->m_alertLevels); }
}