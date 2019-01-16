<?php

/* 
 * Check the rabbitmq logging queue for logs and put them into our database.
 * One day in the future we may merge this script with the alerter so that all new log alerts are 
 * immediately sent out from here, rather than going into the database and then loaded from there.
 * 
 * This script is meant to be managed by supervisor so that it is always running and there is only
 * ever one instance running.
 */

require_once __DIR__ . '/../../bootstrap.php';

class RabbitmqFetcher
{
    public function __construct()
    {
        
    }
    
    
    public function run()
    {
        $connection = new \PhpAmqpLib\Connection\AMQPStreamConnection(
            RABBITMQ_HOST, 
            5672, 
            RABBITMQ_USER, 
            RABBITMQ_PASSWORD
        );
        
        $channel = $connection->channel();
        
        // Create the queue if it doesn't already exist.
        $channel->queue_declare(
            $queue = RABBITMQ_LOG_QUEUE,
            $passive = false,
            $durable = true,
            $exclusive = false,
            $auto_delete = false,
            $nowait = false,
            $arguments = null,
            $ticket = null
        );
        
        
        $callback = function ($msg) {
            
            $log = json_decode($msg->body, $assocForm = true);
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
            
            $insertArray = array();
            $insertArray['message'] = $log['message'];
            $insertArray['priority'] = $log['level'];
            
            if (isset($log['context'])) {
                $insertArray['context'] = json_encode($log['context'], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
            }
            else
            {
                $insertArray['context'] = '';
            }
            
            if (isset($log['timestamp'])) {
                $insertArray['when'] = date("Y-m-d H:i:s", $log['timestamp']);
            }
            else
            {
                $insertArray['when'] = date("Y-m-d H:i:s", time());
            }
            
            print "fetched log: " . print_r($log, true) . PHP_EOL;
            $db = SiteSpecific::getDb();
            
            $query = 'INSERT INTO `logs` SET ' . \iRAP\CoreLibs\MysqliLib::generateQueryPairs(
                $insertArray, 
                $db
            );
            
            $result = $db->query($query);
            
            if ($result === false) {
                print "Failed to insert log." . PHP_EOL . $query . PHP_EOL . $db->error . PHP_EOL;
            }
        };

        $channel->basic_qos(null, 1, null);

        $channel->basic_consume(
            $queue = RABBITMQ_LOG_QUEUE,
            $consumer_tag = '',
            $no_local = false,
            $no_ack = false,
            $exclusive = false,
            $nowait = false,
            $callback
        );

        // This is the best I could find for a non-blocking wait. Unfortunately one has to have
        // a timeout (for now), and simply setting nonBlocking=true on its own appears do to nothing.
        // An exception is thrown when the timout is reached, breaking the loop, and you should catch it
        // to exit gracefully.
        try
        {
            while (count($channel->callbacks)) 
            {
                $channel->wait($allowed_methods = null, $nonBlocking = true, $timeout = 1);
            }
        }
        catch (Exception $e)
        {
            print "There are no more tasks in the queue." . PHP_EOL;
        }

        $channel->close();
        $connection->close();
    }
}

$fetcher = new RabbitmqFetcher();
$fetcher->run();
sleep(3);
