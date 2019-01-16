<?php

/*
 * This script will move outdated logs into the archived logs table
 * This script expects to be managed by supervisord
 * We archive logs rather than delete, just in case we want to use them for some sort of reporting
 * or analysis later.
 */

require_once __DIR__ . '/../../bootstrap.php';

// specify the number of records to try and archive at a time.
// unfortunately this is rather small, but working within 512 mb of RAM and some logs are massive.
define('BATCH_SIZE', 50);


function main()
{
    $fetchResults = true;
    
    while ($fetchResults)
    {
        print "fetching logs to archive..." . PHP_EOL;
        $mysqli = SiteSpecific::getDb();
        
        $oldLogsQuery = 
            "SELECT * FROM `logs`" . 
            " WHERE `when` < (NOW() - INTERVAL " . ARCHIVE_AGE . " SECOND)" . 
            " LIMIT " . BATCH_SIZE;
        
        $result = $mysqli->query($oldLogsQuery);
        
        if ($result === false) {
            // log the error
            LogTable::getInstance()->create(
                "Logs archiver process - failed to fetch logs", 
                "", 
                \iRAP\Logging\LogLevel::ERROR
            );
            
            $fetchResults = false;
        }
        else 
        {
            /* @var $result mysqli_result */
            if ($result->num_rows > 0) {
                // there were results found, so keep fetching again
                $fetchResults = true;
                $insertData = array();
                $logsToDelete = array();
                
                while (($logRow = $result->fetch_assoc()) !== null)
                {
                    $log = new Log($logRow);
                    /* @var $archivedLogTable ArchivedLogTable */
                    $archivedLog = ArchivedLog::createFromLog($log); // this creates row in table.
                    $arrayForm = $archivedLog->getArrayForm();
                    $arrayForm['uuid'] = iRAP\MysqlObjects\UuidLib::convertHexToBinary($arrayForm['uuid']);
                    $insertData[] = $arrayForm;
                    $logsToDelete[] = $log->get_id();
                }
                
                $batchDeleteQuery = 
                    "DELETE FROM `" . LogTable::getInstance()->getTableName() . 
                    "` WHERE `id` IN(" . implode(",", $logsToDelete) . ")";
                
                $batchInsertQuery = \iRAP\CoreLibs\MysqliLib::generateBatchInsertQuery(
                    $insertData, 
                    ArchivedLogTable::getInstance()->getTableName(), 
                    $mysqli
                );
                
                $transaction = new \iRAP\MultiQuery\Transaction($mysqli, 5);
                $transaction->addQuery($batchInsertQuery);
                $transaction->addQuery($batchDeleteQuery);
                $transaction->run();
                                
                if ($transaction->getStatus() !== \iRAP\MultiQuery\Transaction::STATE_SUCCEEDED) {
                    throw new Exception("Failed to move logs to archive table");
                }
            }
            else
            {
                // selected no results, can stop now.
                $fetchResults = false;
            }
        }
    }
}

main();
sleep(3); // must sleep or will eat entire CPU with supervisor launching it immediately


