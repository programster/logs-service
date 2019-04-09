<?php


final class LogCollection extends ArrayObject
{
    public function __construct(Log ...$logs)
    {
        parent::__construct($logs);
    }


    public function append($value) 
    {
        if ($value instanceof Log)
        {
            parent::append($value);
        }
        else
        {
            throw new Exception("Cannot append non Log to a " . __CLASS__);
        }
    }


    public function offsetSet($index, $newval) 
    {
        if ($newval instanceof Log)
        {
            parent::offsetSet($index, $newval);
        }
        else
        {
            throw new Exception("Cannot add a non Log value to a " . __CLASS__);
        }
    }
}