<?php

/* 
 * Object to represent a filter for loading logs.
 */

class LogFilter
{
    private $m_alert_levels;
    private $m_min_age;
    private $m_max_age;
    private $m_minimum_id;
    private $m_maximum_id;
    private $m_search_text;
    
    
    public function __construct() 
    {
        $this->m_alert_levels = array();
        $this->m_min_age = null;
        $this->m_max_age = null;
        $this->m_minimum_id = null;
        $this->m_maximum_id = null;
        $this->m_search_text = null;
    }
    
    
    public function get_where_statement() : string
    {       
        $where_statement = "";
        $statements = array();
        
        if ($this->m_maximum_id !== null && $this->m_maximum_id !== 0) {
            $statements[] = "`id` < '" . ($this->m_maximum_id + 1) . "'";
        }
        
        if ($this->m_minimum_id !== null && $this->m_minimum_id !== 0) {
            $statements[] = "`id` > '" . ($this->m_minimum_id - 1) . "'";
        }
        
        if ($this->m_search_text !== null && $this->m_search_text !== "") {
            $escapedMessage = SiteSpecific::getDb()->escape_string($this->m_search_text);
            $statements[] = "`message` LIKE '%{$escapedMessage}%'";
        }
        
        if (count($this->m_alert_levels) > 0) {
            $quoted_alert_levels = iRAP\CoreLibs\ArrayLib::wrapElements($this->get_alert_levels(), "'");
            $statements[] = "`priority` IN (" . implode(', ', $quoted_alert_levels) . ")";
        }
        
        // We use 'date("Y-m-d H:i:s", time())' instead of just CURRENT_TIMESTAMP so that the deployer
        // doesn't have to remember to set the timezone on the host to UTC wherever the db is.
        if ($this->m_min_age != null) {          
            $statements[] = "TIMESTAMPDIFF(SECOND, `when`, '" . date("Y-m-d H:i:s", time()) . "') > " . $this->m_min_age;
        }
        
        if ($this->m_max_age != null) {                        
            $statements[] = "TIMESTAMPDIFF(SECOND, `when`, '" . date("Y-m-d H:i:s", time()) . "') < " . $this->m_max_age;
        }
        
        if (count($statements) > 0) {
            $where_statement = "WHERE " . implode($statements, " AND ");
        }
        
        return $where_statement;
    }
    
    
    /**
     * Set the log id to start the scan from.
     *
     * @param int $id
     */
    public function set_minimum_id($id)
    {
        $this->m_minimum_id = $id;
    }
    
    
    /**
     * Set the maximum possible log id to retrieve.
     *
     * @param int $id
     */
    public function set_maximum_id($id)
    {
        $this->m_maximum_id = $id;
    }
    
    
    /**
     * Specify an alert level that we wish to fetch from the logs
     *
     * @param int $level
     */
    public function enable_alert_level($level)
    {
        $this->m_alert_levels[$level] = 1;
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
        if (isset($this->m_alert_levels[$level])) {
            unset($this->m_alert_levels[$level]);
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
     * Load the current filter from the session.
     *
     * @return LogFilter
     */
    public static function load()
    {
        $logFilter = new LogFilter();
        
        if (isset($_SESSION['log_filter'])) {
            $logFilter = $_SESSION['log_filter']; 
        }
        
        return $logFilter;
    }
    
    
    public function set_max_age($seconds)
    {
        $this->m_max_age = $seconds; 
    }
    public function set_min_age($seconds)
    {
        $this->m_min_age = $seconds; 
    }
    
        
    // Accessors
    public function get_max_age()      
    {
        return $this->m_max_age; 
    }
    public function get_min_age()      
    {
        return $this->m_min_age; 
    }
    public function get_alert_levels() 
    {
        return array_keys($this->m_alert_levels); 
    }
}