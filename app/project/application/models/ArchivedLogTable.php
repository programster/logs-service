<?php

/* 
 * Class for interfacing with the logs table.
 */

class ArchivedLogTable extends \iRAP\MysqlObjects\AbstractUuidTable
{
    public function getDb(): \mysqli
    {
        return SiteSpecific::get_mysqli_db();
    }
    
    
    public function getFieldsThatAllowNull(): array
    {
        return array('uuid');
    }
    
    
    public function getFieldsThatHaveDefaults(): array
    {
        return array('when');
    }
    
    
    /**
     * Create an archived log from a log object.
     * This will create a row in the table and return the created object. However it does not
     * delete the original log. You may wish to do this.
     * @param Log $log
     * @return type
     */
    public function createFromLog(Log $log) : ArchivedLog
    {
        $archivedLog = $this->create(array(
            'message'  => $log->get_message(), 
            'context'  => $log->get_context(), 
            'priority' => $log->get_priority(), 
            'when'     => $log->get_when()
        ));
        
        /* @var $archivedLog ArchivedLog */
        return $archivedLog;
    }
    
    
    public function getObjectClassName() { return 'ArchivedLog'; }
    
    
    public function getRowObjectConstructorWrapper(): callable
    {
        $objectClassName = $this->getObjectClassName();
        
        $constructor = function($row, $row_field_types=null) use($objectClassName){ 
            return new $objectClassName($row, $row_field_types); 
        };
        
        return $constructor;
    } 
    
    
    public function getTableName() 
    {
        return 'logs_archive';
    }
    
    
    public function validateInputs(array $data): array 
    {
        return $data;
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
    public function load_filter($offset, $limit, LogFilter $filter_object)
    {
        $query = 
            "SELECT * FROM `" . $this->getTableName() . "` " .
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
        
        $db = $this->getDb();
        $result = $db->query($query);
        
        return $this->convertMysqliResultToObjects($result);
    }
}
