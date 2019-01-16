<?php

/* 
 * This is like the rabbitmq_purger script except that this does not bother trying to log to the 
 * database. This script is to be used when the queue gets too large and you just want a fast
 * way to clear it.
 * You will probably want to run many threads of this script in parallel. Tested as high as 60
 * with roughly 2.5k acks per second.
 */

require_once __DIR__ . '/../../bootstrap.php';

class RabbitmqPurger
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
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
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

$fetcher = new RabbitMqPurger();
$fetcher->run();
