<?php

/* 
 * Class for interfacing with the logs table.
 */

class LogTable extends \iRAP\MysqlObjects\AbstractTable
{
    public function getDb(): \mysqli
    {
        return SiteSpecific::get_mysqli_db();
    }
    
    
    public function getFieldsThatAllowNull(): array
    {
        return array();
    }
    
    
    public function getFieldsThatHaveDefaults(): array
    {
        return array('when');
    }
    
    
    public function getObjectClassName() { return 'Log'; }
    
    
    public function getRowObjectConstructorWrapper(): callable
    {
        $objectClassName = $this->getObjectClassName();
        
        $constructor = function($row, $row_field_types=null) use($objectClassName){ 
            return new $objectClassName($row, $row_field_types); 
        };
        
        return $constructor;
    } 
    
    
    public function getTableName() { return 'logs'; }
    
    
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
            " ORDER BY `id` DESC";

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
