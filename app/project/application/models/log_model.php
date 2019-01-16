<?php

/*
 * Old-school CI way of interfacing with the logs table. 
 * Please don't use this class and instead use the Log and LogTable classes instead.
 */

class Log_model extends CI_Model
{
    private $m_id; # the UID of the log
    private $m_message; # the main error/log message
    private $m_context; # contextual information
    private $m_priority; # int priority (larger number = higher priority)
    private $m_when; # mysql timestamp field when log was recorded.
    
    # derived members
    private $m_context_object;
    
    protected static function getTable() { return "logs"; }
    
    
    /**
     * Get the ID of the last log in the system.
     * Useful if we only want to run table scans beyond this point later on.
     * @return int
     */
    public static function get_last_log_id()
    {
        $last_id = 0;
        
        $query = "SELECT `id` FROM `" . self::getTable() . "` ORDER BY `id` DESC LIMIT 1";
        $db = SiteSpecific::get_ci_db();
        $active_record = $db->query($query);
        $rows = $active_record->result_array();
        
        if (count($rows) > 0)
        {
            $last_id = $rows[0]['id'];
        }
        
        return $last_id;
    }
    
    
    /**
     * Try to load a log from the specified ID
     * @param int $id
     * @return Array<Log_model> - array of relevant logs (1 or 0)
     */
    public static function load_id($id)
    {
        $query = "SELECT * FROM `" . self::getTable() . "` WHERE `id`='" . $id . "'";
        $db = SiteSpecific::get_ci_db();
        $active_record = $db->query($query);
        $rows = $active_record->result_array();
        
        foreach ($rows as $row)
        {
            $log = new Log_model();
            
            $log->m_id       = $row['id'];
            $log->m_message  = $row['message'];
            $log->m_context  = $row['context'];
            $log->m_priority = $row['priority'];
            $log->m_when     = $row['when'];
            
            $log->m_context_object = json_decode($log->m_context);

            $logs[] = $log;
        }

        return $logs;
    }
    
    
    /**
     * Load all of the logs in the entire db.
     * You probably don't want to use this as there is probably a lot of logs.
     * @return Array <\LogModel>
     */
    public static function load_all()
    {
        $logs = array();
        
        $query = "SELECT * FROM `" . self::getTable() . "`";
        $db = SiteSpecific::get_ci_db();
        $active_record = $db->query($query);
        $rows = $active_record->result_array();
        
        foreach ($rows as $row)
        {
            $log = new LogModel();
            
            $log->m_id       = $row['id'];
            $log->m_message  = $row['message'];
            $log->m_context  = $row['context'];
            $log->m_priority = $row['priority'];
            $log->m_when     = $row['when'];
            
            $log->m_context_object = json_decode($log->m_context);

            $logs[] = $log;
        }

        return $logs;
    }
    
    
    /**
     * Get the number of logs there are for a specified filter
     * @param LogFilter $json_filter_obj
     * @return type
     */
    public static function get_num_logs(LogFilter $log_filter)
    {
        $count = 0;
        
        $query = 
            "SELECT count(*) as count FROM `" . self::getTable() . "` " .
            $log_filter->get_where_statement();
        
        $db = SiteSpecific::get_ci_db();
        $active_record = $db->query($query);
        
        $rows = $active_record->result_array();
        $row = $rows[0];
        $count = $row['count'];
        return $count;
    }
    
    
    /**
     * 
     * @param int $offset - the offset on the mysql query
     * @param int $limit - the limit of the mysql query.
     * @param int $min_age - minimum age in minutes (can be null to not specify)
     * @param int $max_age - max age in minutes (can be null to not specify)
     * @param int $min_priority - the minimum priority - can be null to not specify
     * @param int $max_priority - the max priority - can be null to not specify.
     */
    public static function load_filter($offset, $limit, LogFilter $filter_object)
    {
        $logs = array();
        
        $query = 
            "SELECT * FROM `" . self::getTable() . "` " .
            $filter_object->get_where_statement() .
            " ORDER BY `ID` DESC";

        if ($limit !== null)
        {
            $query .= " limit " . $limit;
        }

        if ($offset !== null && $offset > 0)
        {
            $query .= " offset " . $offset;
        }


        $db = SiteSpecific::get_ci_db();
        $active_record = $db->query($query);

        foreach ($active_record->result() as $row)
        {
            $log_obj = new Log_model();

            $log_obj->m_context  = $row->context;
            $log_obj->m_id       = $row->id;
            $log_obj->m_message  = $row->message;
            $log_obj->m_priority = $row->priority;
            $log_obj->m_when     = $row->when;

            $logs[] = $log_obj;
        }
        
        return $logs;
    }
    
    
    /**
     * Returns the timestamp of this object in a human readable format, rather than its raw form in the db
     * @return String - the timestamp in human readable form
     */
    public function get_human_readable_timestamp()
    {
        return date("D, d M y H:i:s",strtotime($this->m_when));
    }
    
    public function get_id()             { return $this->m_id; }
    public function get_message()        { return $this->m_message; }
    public function get_context_object() { return $this->m_context_object; }
    public function get_priority()       { return $this->m_priority; }
    public function get_when()           { return $this->m_when; }
}